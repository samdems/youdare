# Task History Feature - No Repeating Tasks

## Overview

Implemented a task history system that prevents tasks from repeating until all available tasks have been used. Once all tasks have been shown, the history automatically resets and tasks can be used again.

## How It Works

### Automatic Tracking
- Every time a player gets a task, it's automatically marked as "used" for that game
- The system tracks which tasks have been shown in each game
- Tasks won't repeat until all available tasks have been exhausted

### Automatic Reset
- When no unused tasks remain, the system automatically clears the history
- All tasks become available again
- Players will see a message: "All tasks have been used! Starting over with fresh tasks."

### Per-Game Tracking
- Each game has its own independent task history
- Different games don't affect each other
- History is cleared when a game ends

## Database Structure

### New Table: `game_task_history`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| game_id | bigint | Foreign key to games table |
| task_id | bigint | Foreign key to tasks table |
| player_id | bigint (nullable) | Player who received the task |
| created_at | timestamp | When task was used |
| updated_at | timestamp | Last update |

**Unique Constraint:** `game_id + task_id` (prevents duplicate tracking)

## Code Changes

### 1. Migration
**File:** `database/migrations/2025_12_28_135959_create_game_task_history_table.php`
- Creates the `game_task_history` table
- Sets up foreign key relationships
- Adds unique constraint and indexes

### 2. Game Model
**File:** `app/Models/Game.php`

#### New Relationship
```php
public function usedTasks()
{
    return $this->belongsToMany(Task::class, 'game_task_history')
        ->withPivot('player_id')
        ->withTimestamps();
}
```

#### New Methods

**`markTaskAsUsed($task, $player = null)`**
- Records that a task has been used in this game
- Associates it with the player who received it
- Prevents duplicate entries

**`clearTaskHistory()`**
- Clears all task history for this game
- Called automatically when all tasks are exhausted
- Can be called manually to reset history

**`hasUsedTask($task)`**
- Checks if a specific task has been used in this game
- Returns boolean

**`getAvailableTasksForPlayer($player, $excludeUsed = true)`**
- Updated to exclude used tasks by default
- Automatically resets history if no unused tasks remain
- Can optionally include used tasks by setting `$excludeUsed = false`

### 3. Player Model
**File:** `app/Models/Player.php`

#### Updated Method: `getRandomTask($type = null, $markAsUsed = true)`
- Now uses game's task history system
- Automatically marks selected task as used (can be disabled)
- Excludes previously used tasks
- Auto-resets if all tasks exhausted

**Parameters:**
- `$type` (string|null): Filter by 'truth' or 'dare'
- `$markAsUsed` (bool): Whether to mark task as used (default: true)

## Usage Examples

### Getting a Random Task (Automatic)
```php
// In controller or API - already handled automatically
$task = $player->getRandomTask('truth');
// Task is automatically marked as used
// Won't appear again until all tasks are exhausted
```

### Getting Task Without Marking as Used
```php
// For preview purposes
$task = $player->getRandomTask('dare', false);
// Task is NOT marked as used
```

### Manually Clearing History
```php
// Reset all task history for a game
$game->clearTaskHistory();
// All tasks now available again
```

### Checking if Task Was Used
```php
if ($game->hasUsedTask($task)) {
    echo "This task was already used in this game";
}
```

### Getting Tasks Including Used Ones
```php
// Get all available tasks, even if used before
$tasks = $game->getAvailableTasksForPlayer($player, false);
```

## API Endpoints

### Get Random Task for Player
**Endpoint:** `GET /api/players/{player}/random-task?type=truth`

**Response when task found:**
```json
{
    "success": true,
    "data": {
        "id": 123,
        "type": "truth",
        "description": "What's your biggest secret?",
        ...
    }
}
```

**Response when all tasks exhausted (auto-reset):**
```json
{
    "success": true,
    "message": "All tasks exhausted - history reset",
    "data": {
        "id": 45,
        "type": "truth",
        ...
    }
}
```

## Benefits

✅ **No Repetition** - Players won't see the same task twice until all tasks are used
✅ **Automatic Management** - No manual intervention needed
✅ **Fair Distribution** - All tasks get equal opportunity to appear
✅ **Better Gameplay** - More variety and less predictability
✅ **Smart Reset** - Automatically starts over when needed
✅ **Per-Game Tracking** - Different games don't interfere with each other

## Migration Instructions

1. **Run the migration:**
```bash
php artisan migrate
```

2. **Existing games will start tracking from now on**
   - Old games won't have history (all tasks available)
   - New tasks will be tracked going forward

3. **No data loss** - This is a pure addition, no existing data is modified

## Testing Checklist

- [ ] Create a game with limited tasks available
- [ ] Play through all available tasks
- [ ] Verify tasks don't repeat
- [ ] Verify automatic reset when all tasks exhausted
- [ ] Test with different task types (truth/dare)
- [ ] Test with multiple games simultaneously
- [ ] Verify player-specific tag filtering still works
- [ ] Test manual history clear

## Performance Considerations

- **Indexed queries** - Fast lookups using game_id index
- **Unique constraints** - Prevents duplicate tracking
- **Minimal overhead** - One insert per task (already using database)
- **Efficient filtering** - Uses `whereNotIn` with array of IDs

## Edge Cases Handled

✅ **No available tasks** - Returns null gracefully
✅ **All tasks used** - Auto-resets and continues
✅ **Player with no matching tags** - Returns null (no infinite loop)
✅ **Duplicate marking** - Prevented by unique constraint
✅ **Game deletion** - Cascade deletes history
✅ **Task deletion** - Cascade deletes history entries

## Future Enhancements

Possible additions in the future:
- Admin panel to view task history
- Statistics on most/least used tasks
- Option to disable auto-reset (stop when exhausted)
- Weighted random selection (favor less-recently-used tasks)
- Task cooldown period instead of complete exclusion