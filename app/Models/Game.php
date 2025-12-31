<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Game extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "name",
        "code",
        "status",
        "max_spice_rating",
        "settings",
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        "settings" => "array",
        "max_spice_rating" => "integer",
    ];

    /**
     * The possible game statuses.
     */
    const STATUS_WAITING = "waiting";
    const STATUS_ACTIVE = "active";
    const STATUS_COMPLETED = "completed";

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate game code if not provided
        static::creating(function ($game) {
            if (empty($game->code)) {
                $game->code = strtoupper(Str::random(6));
            }
        });
    }

    /**
     * Get the players for this game.
     */
    public function players()
    {
        return $this->hasMany(Player::class)->orderBy("order");
    }

    /**
     * Get the tags that are in play for this game.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, "game_tag");
    }

    /**
     * Get the tasks that have been used in this game.
     */
    public function usedTasks()
    {
        return $this->belongsToMany(Task::class, "game_task_history")
            ->withPivot("player_id")
            ->withTimestamps();
    }

    /**
     * Mark a task as used by a specific player in this game.
     *
     * @param  Task|int  $task
     * @param  Player|int  $player
     * @return void
     */
    public function markTaskAsUsed($task, $player)
    {
        $taskId = is_object($task) ? $task->id : $task;
        $playerId = is_object($player) ? $player->id : $player;

        // Only add if not already in history for this player
        if (
            !$this->usedTasks()
                ->wherePivot("player_id", $playerId)
                ->where("task_id", $taskId)
                ->exists()
        ) {
            $this->usedTasks()->attach($taskId, ["player_id" => $playerId]);
        }
    }

    /**
     * Clear the task history for this game (allows tasks to repeat).
     *
     * @return void
     */
    public function clearTaskHistory()
    {
        $this->usedTasks()->detach();
    }

    /**
     * Get tasks available for a specific player in this game.
     * Filters out tasks where the player has any of the cant_have_tags.
     * Also excludes tasks that have already been used by this player.
     *
     * @param  Player  $player
     * @param  bool  $excludeUsed  Whether to exclude previously used tasks by this player
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableTasksForPlayer(
        Player $player,
        $excludeUsed = true,
    ) {
        $tasks = $player->getAvailableTasks();

        if (!$excludeUsed) {
            return $tasks;
        }

        $usedTaskIds = $this->usedTasks()
            ->wherePivot("player_id", $player->id)
            ->pluck("task_id")
            ->toArray();

        $tasks = $tasks->filter(function ($task) use ($usedTaskIds) {
            return !in_array($task->id, $usedTaskIds);
        });

        if ($tasks->isEmpty()) {
            $this->clearTaskHistoryForPlayer($player);
            $tasks = $player->getAvailableTasks();
        }

        return $tasks;
    }

    /**
     * Clear the task history for a specific player in this game.
     *
     * @param  Player|int  $player
     * @return void
     */
    public function clearTaskHistoryForPlayer($player)
    {
        $playerId = is_object($player) ? $player->id : $player;
        $this->usedTasks()->wherePivot("player_id", $playerId)->detach();
    }

    /**
     * Check if a specific player has used a task in this game.
     *
     * @param  Task|int  $task
     * @param  Player|int  $player
     * @return bool
     */
    public function hasPlayerUsedTask($task, $player)
    {
        $taskId = is_object($task) ? $task->id : $task;
        $playerId = is_object($player) ? $player->id : $player;

        return $this->usedTasks()
            ->wherePivot("player_id", $playerId)
            ->where("task_id", $taskId)
            ->exists();
    }

    /**
     * Get all available tags for this game (game tags + player tags).
     */
    public function getAllActiveTags()
    {
        $gameTags = $this->tags()->pluck("tags.id")->toArray();
        $playerTags = $this->players()
            ->with("tags")
            ->get()
            ->pluck("tags")
            ->flatten()
            ->pluck("id")
            ->unique()
            ->toArray();

        return array_unique(array_merge($gameTags, $playerTags));
    }

    /**
     * Get tasks that match the game's tags and max spice rating.
     * This includes tasks that match current game/player tags OR tasks that can remove tags from players.
     * Note: This method does not filter by cant_have_tags. Use getAvailableTasksForPlayer() for player-specific filtering.
     */
    public function getAvailableTasks()
    {
        $tagIds = $this->getAllActiveTags();

        // Get all tags that are currently on any player (these can be removed by tasks)
        $playerTagIds = $this->players()
            ->with("tags")
            ->get()
            ->pluck("tags")
            ->flatten()
            ->pluck("id")
            ->unique()
            ->toArray();

        $query = Task::published()->where(
            "spice_rating",
            "<=",
            $this->max_spice_rating,
        );

        // If we have tags, filter tasks
        if (count($tagIds) > 0 || count($playerTagIds) > 0) {
            $query->where(function ($q) use ($tagIds, $playerTagIds) {
                // Include tasks that match active tags
                if (count($tagIds) > 0) {
                    $q->whereHas("tags", function ($tagQuery) use ($tagIds) {
                        $tagQuery->whereIn("tags.id", $tagIds);
                    });
                }

                // Also include tasks that can remove tags from players
                // Get all tasks and filter in PHP since JSON querying varies by database
                if (count($playerTagIds) > 0) {
                    // This will be further filtered after retrieval
                    $q->orWhereNotNull("tags_to_remove");
                }
            });
        }

        $tasks = $query->get();

        // Filter tasks that can remove player tags (done in PHP for database compatibility)
        if (count($playerTagIds) > 0) {
            $tasks = $tasks->filter(function ($task) use (
                $tagIds,
                $playerTagIds,
            ) {
                // Keep task if it matches active tags
                if (
                    count($tagIds) > 0 &&
                    $task->tags->pluck("id")->intersect($tagIds)->count() > 0
                ) {
                    return true;
                }

                // Keep task if it can remove any player tag
                if (
                    !empty($task->tags_to_remove) &&
                    is_array($task->tags_to_remove)
                ) {
                    return count(
                        array_intersect($task->tags_to_remove, $playerTagIds),
                    ) > 0;
                }

                return false;
            });
        }

        return $tasks;
    }

    /**
     * Start the game.
     */
    public function start()
    {
        $this->update(["status" => self::STATUS_ACTIVE]);
    }

    /**
     * Complete the game.
     */
    public function complete()
    {
        $this->update(["status" => self::STATUS_COMPLETED]);
    }

    /**
     * Check if the game is waiting.
     */
    public function isWaiting()
    {
        return $this->status === self::STATUS_WAITING;
    }

    /**
     * Check if the game is active.
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if the game is completed.
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Scope a query to only include active games.
     */
    public function scopeActive($query)
    {
        return $query->where("status", self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include waiting games.
     */
    public function scopeWaiting($query)
    {
        return $query->where("status", self::STATUS_WAITING);
    }

    /**
     * Find a game by its code.
     */
    public static function findByCode($code)
    {
        return static::where("code", strtoupper($code))->first();
    }
}
