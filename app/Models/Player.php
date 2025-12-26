<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "game_id",
        "name",
        "gender",
        "score",
        "is_active",
        "order",
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "is_active" => "boolean",
        "score" => "integer",
        "order" => "integer",
    ];

    /**
     * Get the game this player belongs to.
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Get the tags specific to this player.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, "player_tag");
    }

    /**
     * Get all tags available for this player (game tags + player tags).
     * Filters out tags that require a higher spice level than the game's max.
     */
    public function getAllAvailableTags()
    {
        $maxSpiceRating = $this->game->max_spice_rating;

        // Get game tags filtered by spice level
        $gameTags = $this->game
            ->tags()
            ->where("tags.min_spice_level", "<=", $maxSpiceRating)
            ->pluck("tags.id")
            ->toArray();

        // Get player tags filtered by spice level
        $playerTags = $this->tags()
            ->where("tags.min_spice_level", "<=", $maxSpiceRating)
            ->pluck("tags.id")
            ->toArray();

        return array_unique(array_merge($gameTags, $playerTags));
    }

    /**
     * Get tasks available for this player based on game and player tags.
     * This includes tasks that match current tags OR tasks that can remove tags from this player.
     * Excludes tasks where the player has any of the cant_have_tags.
     */
    public function getAvailableTasks()
    {
        $tagIds = $this->getAllAvailableTags();

        // Get tags currently on this player (these can be removed by tasks)
        $playerTagIds = $this->tags()->pluck("tags.id")->toArray();

        $query = Task::published()->where(
            "spice_rating",
            "<=",
            $this->game->max_spice_rating,
        );

        // If we have tags, filter tasks
        if (count($tagIds) > 0 || count($playerTagIds) > 0) {
            $query->where(function ($q) use ($tagIds, $playerTagIds) {
                // Include tasks with no tags (always available)
                $q->whereDoesntHave("tags");

                // Include tasks that match active tags
                if (count($tagIds) > 0) {
                    $q->orWhereHas("tags", function ($tagQuery) use ($tagIds) {
                        $tagQuery->whereIn("tags.id", $tagIds);
                    });
                }

                // Also include tasks that can remove tags from this player
                if (count($playerTagIds) > 0) {
                    // This will be further filtered after retrieval
                    $q->orWhereNotNull("tags_to_remove");
                }
            });

            $tasks = $query->get();

            // Filter tasks that can remove player tags (done in PHP for database compatibility)
            if (count($playerTagIds) > 0) {
                $tasks = $tasks->filter(function ($task) use (
                    $tagIds,
                    $playerTagIds,
                ) {
                    // Keep task if it has no tags (always available)
                    if ($task->tags->isEmpty()) {
                        return true;
                    }

                    // Keep task if it matches active tags
                    if (
                        count($tagIds) > 0 &&
                        $task->tags->pluck("id")->intersect($tagIds)->count() >
                            0
                    ) {
                        return true;
                    }

                    // Keep task if it can remove any tag from this player
                    if (
                        !empty($task->tags_to_remove) &&
                        is_array($task->tags_to_remove)
                    ) {
                        return count(
                            array_intersect(
                                $task->tags_to_remove,
                                $playerTagIds,
                            ),
                        ) > 0;
                    }

                    return false;
                });
            }
        } else {
            // No tags at all - return all tasks within spice rating
            $tasks = $query->get();
        }

        // Filter out tasks where the player has any of the cant_have_tags
        $tasks = $tasks->filter(function ($task) {
            return $task->isAvailableForPlayer($this);
        });

        return $tasks;
    }

    /**
     * Get a random task for this player.
     */
    public function getRandomTask($type = null)
    {
        $availableTasks = $this->getAvailableTasks();

        if ($type) {
            $availableTasks = $availableTasks->where("type", $type);
        }

        return $availableTasks->shuffle()->first();
    }

    /**
     * Increment the player's score.
     */
    public function incrementScore($points = 1)
    {
        $this->increment("score", $points);
    }

    /**
     * Deactivate the player.
     */
    public function deactivate()
    {
        $this->update(["is_active" => false]);
    }

    /**
     * Activate the player.
     */
    public function activate()
    {
        $this->update(["is_active" => true]);
    }

    /**
     * Scope a query to only include active players.
     */
    public function scopeActive($query)
    {
        return $query->where("is_active", true);
    }
}
