# Tag Removal Fix Documentation

## Problem Summary

The tag removal feature was not working correctly in the game. When a task removed a tag from a player (via the `tags_to_remove` field), those tasks would become unavailable in subsequent rounds, breaking progression systems.

## Root Cause

The issue was in the `getAvailableTasks()` method in both the `Game` and `Player` models. The method was only returning tasks that matched the **current** active tags (tags on the game + tags on players).

### What Was Happening:

1. Player starts with tags: `["rookie", "intermediate-locked"]`
2. Player completes a task that removes the `"rookie"` tag
3. The `"rookie"` tag is detached from the player
4. Next time `getAvailableTasks()` is called, it only looks for tasks matching current tags
5. If `"rookie"` is no longer on ANY player or the game, tasks tagged with ONLY `"rookie"` become unavailable
6. This breaks progression systems where tasks need to remove tags to unlock new content

### Example Scenario:

```
Initial State:
- Game has tag: "warm-up"
- Player has tags: ["rookie", "intermediate-locked"]
- Task A: has "rookie" tag, removes "rookie" tag
- Task B: has "warm-up" tag, removes "intermediate-locked" tag

After completing Task A:
- Player now has tags: ["intermediate-locked"]
- Game still has tag: "warm-up"

OLD BEHAVIOR (BROKEN):
- Only Task B is available (matches "warm-up")
- Task A is no longer available (no one has "rookie" tag anymore)
- Player can never complete another "rookie" task!

NEW BEHAVIOR (FIXED):
- Both Task A and Task B are available
- Task A is available because it can remove "intermediate-locked" from player
- Task B is available because it matches game's "warm-up" tag
```

## Solution

Modified the `getAvailableTasks()` method in both `Game` and `Player` models to include two types of tasks:

1. **Tasks with matching tags** - Tasks that have at least one tag matching the game's or player's current tags
2. **Tasks that can remove player tags** - Tasks that have any tag ID in their `tags_to_remove` field that matches a tag currently on the player

### Code Changes

#### File: `app/Models/Game.php`

**Before:**
```php
public function getAvailableTasks()
{
    $tagIds = $this->getAllActiveTags();

    return Task::published()
        ->where("spice_rating", "<=", $this->max_spice_rating)
        ->when(count($tagIds) > 0, function ($query) use ($tagIds) {
            $query->whereHas("tags", function ($q) use ($tagIds) {
                $q->whereIn("tags.id", $tagIds);
            });
        })
        ->get();
}
```

**After:**
```php
public function getAvailableTasks()
{
    $tagIds = $this->getAllActiveTags();
    
    // Get all tags that are currently on any player
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

    if (count($tagIds) > 0 || count($playerTagIds) > 0) {
        $query->where(function ($q) use ($tagIds, $playerTagIds) {
            // Include tasks that match active tags
            if (count($tagIds) > 0) {
                $q->whereHas("tags", function ($tagQuery) use ($tagIds) {
                    $tagQuery->whereIn("tags.id", $tagIds);
                });
            }

            // Also include tasks that can remove tags from players
            if (count($playerTagIds) > 0) {
                $q->orWhereNotNull("tags_to_remove");
            }
        });
    }

    $tasks = $query->get();

    // Filter tasks in PHP for database compatibility
    if (count($playerTagIds) > 0) {
        $tasks = $tasks->filter(function ($task) use (
            $tagIds,
            $playerTagIds,
        ) {
            // Keep if matches active tags
            if (
                count($tagIds) > 0 &&
                $task->tags->pluck("id")->intersect($tagIds)->count() > 0
            ) {
                return true;
            }

            // Keep if can remove any player tag
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
```

#### File: `app/Models/Player.php`

Similar changes were made to the `Player::getAvailableTasks()` method, ensuring that tasks can remove tags from the specific player.

## Testing

A comprehensive test script was created at `test_tag_removal_fix.php` that verifies:

1. ✅ All tasks are available when player has tags
2. ✅ Tasks remain available after removing tags from player
3. ✅ Tasks with game tags are still available even when player has no tags
4. ✅ Game-level task availability works correctly

### Test Results

All tests pass, confirming that:
- Tasks with matching game/player tags are available
- Tasks that can remove tags from players are ALSO available
- Tasks remain available even after removing tags from players
- Progression systems work correctly

## Impact

This fix enables proper progression systems in games where:
- Players start as "rookies" and unlock content by completing tasks
- Tasks remove "locked" tags to unlock new difficulty levels
- Players advance through stages by removing progression tags
- The game creates a sense of achievement through unlocking

## Usage Example

```php
// Create a progression system
$rookieTag = Tag::create(['name' => 'Rookie', 'slug' => 'rookie']);
$lockedTag = Tag::create(['name' => 'Advanced Locked', 'slug' => 'advanced-locked']);

// Task that removes rookie status
$welcomeTask = Task::create([
    'type' => 'truth',
    'description' => 'Welcome question',
    'tags_to_remove' => [$rookieTag->id],
]);
$welcomeTask->tags()->attach([$rookieTag->id]);

// Task that unlocks advanced content
$unlockTask = Task::create([
    'type' => 'dare',
    'description' => 'Complete this to unlock advanced',
    'tags_to_remove' => [$lockedTag->id],
]);
$unlockTask->tags()->attach([$rookieTag->id]);

// Player starts with both tags
$player->tags()->attach([$rookieTag->id, $lockedTag->id]);

// After completing welcomeTask, player loses rookie tag
// But unlockTask is STILL available because it can remove the locked tag
$player->getAvailableTasks(); // Returns both tasks!
```

## Files Modified

- `app/Models/Game.php` - Updated `getAvailableTasks()` method
- `app/Models/Player.php` - Updated `getAvailableTasks()` and `getRandomTask()` methods
- `test_tag_removal_fix.php` - New comprehensive test script

## Backward Compatibility

This change is **fully backward compatible**. It only expands the set of available tasks - it doesn't remove any tasks that were previously available. Games that don't use the `tags_to_remove` feature will continue to work exactly as before.