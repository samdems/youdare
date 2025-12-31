<?php

namespace App\Services;

use App\Models\Player;
use App\Models\Task;
use Illuminate\Support\Collection;

class TaskFilter
{
    /**
     * Filter tasks available for a player.
     *
     * @param  Collection  $tasks  The tasks to filter
     * @param  Player  $player  The player to filter tasks for
     * @param  array  $tagIds  Available tag IDs (game + player tags)
     * @param  array  $playerTagIds  Player-specific tag IDs
     * @return Collection
     */
    public function filterAvailableTasksForPlayer(
        Collection $tasks,
        Player $player,
        array $tagIds,
        array $playerTagIds,
    ): Collection {
        // Early exit: if no tags, only return tasks without tags or tags_to_remove
        if (empty($tagIds) && empty($playerTagIds)) {
            return $this->filterTasksWithoutTags($tasks);
        }
        return $tasks->filter(function ($task) use ($tagIds, $playerTagIds) {
            return $this->isTaskAvailable($task, $tagIds, $playerTagIds);
        });
    }

    /**
     * Filter tasks that have no tags and no tags_to_remove.
     *
     * @param  Collection  $tasks  The tasks to filter
     * @return Collection
     */
    protected function filterTasksWithoutTags(Collection $tasks): Collection
    {
        return $tasks->filter(function ($task) {
            return $task->tags->isEmpty() && empty($task->tags_to_remove);
        });
    }

    /**
     * Check if a task is available based on tags.
     *
     * @param  Task  $task  The task to check
     * @param  array  $tagIds  Available tag IDs (game + player tags)
     * @param  array  $playerTagIds  Player-specific tag IDs
     * @return bool
     */
    protected function isTaskAvailable(
        Task $task,
        array $tagIds,
        array $playerTagIds,
    ): bool {
        // Task with no tags and no tags_to_remove is always available
        if ($task->tags->isEmpty() && empty($task->tags_to_remove)) {
            return true;
        }

        // Task is available if player has all required tags
        if ($this->playerHasAllRequiredTags($task, $tagIds)) {
            return true;
        }

        return false;
    }

    /**
     * Check if player has all required tags for a task.
     *
     * @param  Task  $task  The task to check
     * @param  array  $tagIds  Available tag IDs (game + player tags)
     * @return bool
     */
    protected function playerHasAllRequiredTags(Task $task, array $tagIds): bool
    {
        if (empty($tagIds) || $task->tags->isEmpty()) {
            return false;
        }

        $taskTagIds = $task->tags->pluck("id")->toArray();

        return count(array_diff($taskTagIds, $tagIds)) === 0;
    }

    /**
     * Check if task can remove any tag from the player.
     *
     * @param  Task  $task  The task to check
     * @param  array  $playerTagIds  Player-specific tag IDs
     * @return bool
     */
    protected function taskCanRemovePlayerTag(
        Task $task,
        array $playerTagIds,
    ): bool {
        if (
            empty($task->tags_to_remove) ||
            !is_array($task->tags_to_remove) ||
            empty($playerTagIds)
        ) {
            return false;
        }

        return count(array_intersect($task->tags_to_remove, $playerTagIds)) > 0;
    }

    /**
     * Filter out tasks where the player has any of the cant_have_tags.
     *
     * @param  Collection  $tasks  The tasks to filter
     * @param  Player  $player  The player to check against
     * @return Collection
     */
    public function filterByCantHaveTags(
        Collection $tasks,
        Player $player,
    ): Collection {
        return $tasks->filter(function ($task) use ($player) {
            return $task->isAvailableForPlayer($player);
        });
    }
}
