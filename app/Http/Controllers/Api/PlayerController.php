<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Player;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlayerController extends Controller
{
    /**
     * Display a listing of players for a specific game.
     */
    public function index(Game $game)
    {
        $players = $game->players()->with("tags")->get();

        return response()->json([
            "success" => true,
            "data" => $players,
        ]);
    }

    /**
     * Store a newly created player in a game.
     */
    public function store(Request $request, Game $game)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:255",
            "gender" => "nullable|in:male,female",
            "tag_ids" => "nullable|array",
            "tag_ids.*" => "exists:tags,id",
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "success" => false,
                    "errors" => $validator->errors(),
                ],
                422,
            );
        }

        // Get the next order number
        $nextOrder = $game->players()->max("order") + 1;

        \Log::info("Creating player", [
            "name" => $request->name,
            "gender" => $request->gender,
            "tag_ids" => $request->tag_ids,
        ]);

        $player = $game->players()->create([
            "name" => $request->name,
            "gender" => $request->gender,
            "order" => $nextOrder,
        ]);

        // Attach tags if provided
        if (
            $request->has("tag_ids") &&
            is_array($request->tag_ids) &&
            count($request->tag_ids) > 0
        ) {
            \Log::info("Attaching tags", [
                "tag_ids" => $request->tag_ids,
            ]);

            $player->tags()->sync($request->tag_ids);
        }

        $player->load("tags");

        \Log::info("Final player state", [
            "player_id" => $player->id,
            "gender" => $player->gender,
            "tags" => $player->tags->pluck("name", "slug")->toArray(),
        ]);

        return response()->json(
            [
                "success" => true,
                "message" => "Player added successfully",
                "data" => $player,
            ],
            201,
        );
    }

    /**
     * Display the specified player.
     */
    public function show(Player $player)
    {
        $player->load(["game", "tags"]);

        return response()->json([
            "success" => true,
            "data" => $player,
        ]);
    }

    /**
     * Update the specified player.
     */
    public function update(Request $request, Player $player)
    {
        $validator = Validator::make($request->all(), [
            "name" => "nullable|string|max:255",
            "gender" => "nullable|in:male,female",
            "score" => "nullable|integer|min:0",
            "is_active" => "nullable|boolean",
            "order" => "nullable|integer|min:0",
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "success" => false,
                    "errors" => $validator->errors(),
                ],
                422,
            );
        }

        $player->update(
            $request->only(["name", "gender", "score", "is_active", "order"]),
        );

        $player->load("tags");

        return response()->json([
            "success" => true,
            "message" => "Player updated successfully",
            "data" => $player,
        ]);
    }

    /**
     * Remove the specified player from storage.
     */
    public function destroy(Player $player)
    {
        $player->delete();

        return response()->json([
            "success" => true,
            "message" => "Player removed successfully",
        ]);
    }

    /**
     * Sync tags for the player (replaces all tags).
     */
    public function syncTags(Request $request, Player $player)
    {
        $validator = Validator::make($request->all(), [
            "tag_ids" => "required|array",
            "tag_ids.*" => "exists:tags,id",
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "success" => false,
                    "errors" => $validator->errors(),
                ],
                422,
            );
        }

        $player->tags()->sync($request->tag_ids);
        $player->load("tags");

        return response()->json([
            "success" => true,
            "message" => "Player tags updated successfully",
            "data" => $player,
        ]);
    }

    /**
     * Attach a tag to the player.
     */
    public function attachTag(Player $player, Tag $tag)
    {
        if ($player->tags()->where("tag_id", $tag->id)->exists()) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Tag already attached to player",
                ],
                400,
            );
        }

        $player->tags()->attach($tag->id);
        $player->load("tags");

        return response()->json([
            "success" => true,
            "message" => "Tag attached to player successfully",
            "data" => $player,
        ]);
    }

    /**
     * Detach a tag from the player.
     */
    public function detachTag(Player $player, Tag $tag)
    {
        $player->tags()->detach($tag->id);
        $player->load("tags");

        return response()->json([
            "success" => true,
            "message" => "Tag detached from player successfully",
            "data" => $player,
        ]);
    }

    /**
     * Get available tasks for the player.
     */
    public function availableTasks(Player $player)
    {
        $tasks = $player->getAvailableTasks();

        return response()->json([
            "success" => true,
            "data" => $tasks,
            "count" => $tasks->count(),
        ]);
    }

    /**
     * Get a random task for the player.
     */
    public function randomTask(Request $request, Player $player)
    {
        $validator = Validator::make($request->all(), [
            "type" => "nullable|in:truth,dare",
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "success" => false,
                    "errors" => $validator->errors(),
                ],
                422,
            );
        }

        $task = $player->getRandomTask($request->type);

        if (!$task) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "No tasks available for this player",
                ],
                404,
            );
        }

        return response()->json([
            "success" => true,
            "data" => $task,
        ]);
    }

    /**
     * Increment the player's score.
     */
    public function incrementScore(Request $request, Player $player)
    {
        $validator = Validator::make($request->all(), [
            "points" => "nullable|integer|min:1",
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "success" => false,
                    "errors" => $validator->errors(),
                ],
                422,
            );
        }

        $points = $request->points ?? 1;
        $player->incrementScore($points);
        $player->refresh();

        return response()->json([
            "success" => true,
            "message" => "Score updated successfully",
            "data" => $player,
        ]);
    }
}
