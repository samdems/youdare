<?php

namespace App\Models;

use App\Services\TaskFilter;
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
    public function getAllAvailableTags(): array
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
    public function getAvailableTasks(): \Illuminate\Support\Collection
    {
        $tagIds = $this->getAllAvailableTags();
        $playerTagIds = $this->tags()->pluck("tags.id")->toArray();

        // Get base query for tasks within spice rating
        $tasks = $this->getTasksWithinSpiceRating();

        // Early exit: if no tags, only return tasks with no tags and no tags_to_remove
        if (empty($tagIds) && empty($playerTagIds)) {
            return $this->filterTasksForPlayerWithoutTags($tasks);
        }
        // Filter tasks based on tags using the TaskFilter service
        $taskFilter = new TaskFilter();
        $filteredTasks = $taskFilter->filterAvailableTasksForPlayer(
            $tasks,
            $this,
            $tagIds,
            $playerTagIds,
        );

        // Filter out tasks where the player has any of the cant_have_tags
        return $taskFilter->filterByCantHaveTags($filteredTasks, $this);
    }

    /**
     * Get published tasks within the game's max spice rating.
     */
    protected function getTasksWithinSpiceRating(): \Illuminate\Support\Collection
    {
        return Task::published()
            ->where("spice_rating", "<=", $this->game->max_spice_rating)
            ->with("tags")
            ->get();
    }

    /**
     * Filter tasks for players without any tags.
     */
    protected function filterTasksForPlayerWithoutTags(
        \Illuminate\Support\Collection $tasks,
    ): \Illuminate\Support\Collection {
        return $tasks->filter(function ($task) {
            return $task->tags->isEmpty() && empty($task->tags_to_remove);
        });
    }

    /**
     * Get a random task for this player.
     * Excludes tasks that have already been used by this player.
     * Automatically resets this player's history if all tasks have been used.
     * Other players can still receive tasks that this player has seen.
     *
     * @param  string|null  $type  Filter by task type ('truth' or 'dare')
     * @param  bool  $markAsUsed  Whether to mark the selected task as used
     * @return Task|null
     */
    public function getRandomTask(
        ?string $type = null,
        bool $markAsUsed = true,
    ): ?Task {
        $availableTasks = $this->game->getAvailableTasksForPlayer($this, true);

        if ($type) {
            $availableTasks = $availableTasks->where("type", $type);
        }

        $task = $availableTasks->shuffle()->first();

        if ($task && $markAsUsed) {
            $this->game->markTaskAsUsed($task, $this);
        }

        return $task;
    }

    /**
     * Increment the player's score.
     */
    public function incrementScore(int $points = 1): void
    {
        $this->increment("score", $points);
    }

    /**
     * Deactivate the player.
     */
    public function deactivate(): void
    {
        $this->update(["is_active" => false]);
    }

    /**
     * Activate the player.
     */
    public function activate(): void
    {
        $this->update(["is_active" => true]);
    }

    /**
     * Scope a query to only include active players.
     */
    public function scopeActive(
        \Illuminate\Database\Eloquent\Builder $query,
    ): \Illuminate\Database\Eloquent\Builder {
        return $query->where("is_active", true);
    }
}
