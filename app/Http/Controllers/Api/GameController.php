<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Game::with(["players", "tags"]);

        // Filter by status
        if ($request->has("status")) {
            $query->where("status", $request->status);
        }

        $games = $query->latest()->get();

        return response()->json([
            "success" => true,
            "data" => $games,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "nullable|string|max:255",
            "max_spice_rating" => "nullable|integer|min:1|max:5",
            "enable_group_tasks" => "nullable|boolean",
            "tag_ids" => "nullable|array",
            "tag_ids.*" => "exists:tags,id",
            "settings" => "nullable|array",
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

        $game = Game::create([
            "name" => $request->name,
            "max_spice_rating" => $request->max_spice_rating ?? 5,
            "enable_group_tasks" => $request->enable_group_tasks ?? true,
            "settings" => $request->settings ?? [],
        ]);

        // Attach tags if provided
        if ($request->has("tag_ids")) {
            $game->tags()->sync($request->tag_ids);
        }

        $game->load(["players", "tags"]);

        return response()->json(
            [
                "success" => true,
                "message" => "Game created successfully",
                "data" => $game,
            ],
            201,
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Game $game)
    {
        $game->load(["players.tags", "tags"]);

        return response()->json([
            "success" => true,
            "data" => $game,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Game $game)
    {
        $validator = Validator::make($request->all(), [
            "name" => "nullable|string|max:255",
            "status" => "nullable|in:waiting,active,completed",
            "max_spice_rating" => "nullable|integer|min:1|max:5",
            "enable_group_tasks" => "nullable|boolean",
            "settings" => "nullable|array",
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

        $game->update(
            $request->only([
                "name",
                "status",
                "max_spice_rating",
                "enable_group_tasks",
                "settings",
            ]),
        );

        $game->load(["players", "tags"]);

        return response()->json([
            "success" => true,
            "message" => "Game updated successfully",
            "data" => $game,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game)
    {
        $game->delete();

        return response()->json([
            "success" => true,
            "message" => "Game deleted successfully",
        ]);
    }

    /**
     * Find a game by its code.
     */
    public function findByCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "code" => "required|string|size:6",
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

        $game = Game::findByCode($request->code);

        if (!$game) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Game not found",
                ],
                404,
            );
        }

        $game->load(["players.tags", "tags"]);

        return response()->json([
            "success" => true,
            "data" => $game,
        ]);
    }

    /**
     * Start the game.
     */
    public function start(Game $game)
    {
        if (!$game->isWaiting()) {
            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        "Game can only be started when in waiting status",
                ],
                400,
            );
        }

        if ($game->players()->count() < 2) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Game needs at least 2 players to start",
                ],
                400,
            );
        }

        $game->start();
        $game->load(["players", "tags"]);

        return response()->json([
            "success" => true,
            "message" => "Game started successfully",
            "data" => $game,
        ]);
    }

    /**
     * Complete the game.
     */
    public function complete(Game $game)
    {
        if (!$game->isActive()) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Only active games can be completed",
                ],
                400,
            );
        }

        $game->complete();
        $game->load(["players", "tags"]);

        return response()->json([
            "success" => true,
            "message" => "Game completed successfully",
            "data" => $game,
        ]);
    }

    /**
     * Sync tags for the game (replaces all tags).
     */
    public function syncTags(Request $request, Game $game)
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

        $game->tags()->sync($request->tag_ids);
        $game->load(["tags"]);

        return response()->json([
            "success" => true,
            "message" => "Game tags updated successfully",
            "data" => $game,
        ]);
    }

    /**
     * Attach a tag to the game.
     */
    public function attachTag(Game $game, Tag $tag)
    {
        if ($game->tags()->where("tag_id", $tag->id)->exists()) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Tag already attached to game",
                ],
                400,
            );
        }

        $game->tags()->attach($tag->id);
        $game->load(["tags"]);

        return response()->json([
            "success" => true,
            "message" => "Tag attached to game successfully",
            "data" => $game,
        ]);
    }

    /**
     * Detach a tag from the game.
     */
    public function detachTag(Game $game, Tag $tag)
    {
        $game->tags()->detach($tag->id);
        $game->load(["tags"]);

        return response()->json([
            "success" => true,
            "message" => "Tag detached from game successfully",
            "data" => $game,
        ]);
    }

    /**
     * Get available tasks for the game.
     */
    public function availableTasks(Game $game)
    {
        $tasks = $game->getAvailableTasks();

        return response()->json([
            "success" => true,
            "data" => $tasks,
            "count" => $tasks->count(),
        ]);
    }

    /**
     * Get a random group task for the game.
     */
    public function getGroupTask(Game $game)
    {
        if (!$game->enable_group_tasks) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Group tasks are disabled for this game",
                ],
                400,
            );
        }

        $task = $game->getRandomGroupTask();

        if (!$task) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "No group tasks available",
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
     * Complete a group task and start a new round.
     */
    public function completeGroupTask(Game $game)
    {
        if (!$game->enable_group_tasks) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Group tasks are disabled for this game",
                ],
                400,
            );
        }

        if (!$game->isRoundComplete()) {
            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        "Cannot complete group task - round not complete",
                ],
                400,
            );
        }

        $game->startNewRound();
        $game->load(["players", "tags"]);

        return response()->json([
            "success" => true,
            "message" => "New round started",
            "data" => $game,
        ]);
    }

    /**
     * Advance to the next player in the round.
     */
    public function advancePlayer(Game $game)
    {
        $game->advanceToNextPlayer();
        $game->load(["players", "tags"]);

        return response()->json([
            "success" => true,
            "data" => $game,
        ]);
    }
}
