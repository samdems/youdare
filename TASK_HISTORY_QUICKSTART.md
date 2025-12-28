# Task History Quick Start Guide

## What This Does

**Prevents tasks from repeating until all available tasks have been used.**

When a player gets a task, it won't appear again until every other available task has been shown. Once all tasks are exhausted, the history automatically resets and starts over.

## Installation

### Step 1: Run Migration
```bash
php artisan migrate
```

This creates the `game_task_history` table that tracks which tasks have been used in each game.

### Step 2: Done!
The feature is automatically active. No configuration needed.

## How It Works

### During Gameplay
1. Player requests a task (truth or dare)
2. System selects a random task that **hasn't been used yet**
3. Task is automatically marked as "used" for this game
4. Next time, that task won't appear until all others are used

### When All Tasks Are Used
1. System detects no unused tasks remain
2. **Automatically clears the history**
3. All tasks become available again
4. Gameplay continues seamlessly

## Key Features

✅ **Automatic** - No manual management required
✅ **Per-Game** - Each game has its own independent history
✅ **Auto-Reset** - Seamlessly starts over when exhausted
✅ **Fair Distribution** - Every task gets shown before repeating
✅ **Smart Filtering** - Still respects tags, spice level, gender filters, etc.

## Example Flow

**Game with 10 available tasks for a player:**

```
Round 1: Task #5 ← shown
Round 2: Task #2 ← shown
Round 3: Task #8 ← shown
...
Round 10: Task #1 ← shown (last unused task)
Round 11: Task #7 ← AUTO-RESET, all tasks available again!
```

## For Developers

### Task Selection (Automatic)
```php
// In your controller - already handled!
$task = $player->getRandomTask('truth');
// Task is automatically marked as used
```

### Manual History Management
```php
// Clear history manually (optional)
$game->clearTaskHistory();

// Check if task was used
if ($game->hasUsedTask($task)) {
    // ...
}

// Get task without marking as used (preview mode)
$task = $player->getRandomTask('dare', false);
```

### API Response
The API already handles this automatically:

**Request:**
```
GET /api/players/{player}/random-task?type=truth
```

**Response:**
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

## Important Notes

⚠️ **History is per-game** - Different games don't share history
⚠️ **Still respects all filters** - Tags, gender, spice level all still apply
⚠️ **Cascading deletes** - History is deleted when game ends
⚠️ **Existing games** - Will start tracking from now on (no retroactive history)

## Testing

To verify it's working:

1. Start a game with few available tasks (e.g., 3-5 tasks)
2. Keep requesting tasks
3. Note which tasks appear
4. After using all tasks, verify they start repeating
5. Confirm no task repeats before all others are shown

## Troubleshooting

**Q: Tasks are still repeating immediately**
- Make sure you ran the migration
- Check that `game_task_history` table exists
- Verify `getRandomTask()` is being called (not bypassed)

**Q: No tasks available**
- The player might have no matching tasks (check tags/filters)
- This is not a history issue - it's a filtering issue

**Q: Want to reset history for testing**
```php
$game->clearTaskHistory();
```

## Benefits for Players

🎮 **Better Experience** - More variety, less repetition
🎯 **Fair Play** - Everyone gets equal task distribution  
🔄 **Seamless Reset** - No interruption when cycling through tasks
🎲 **Still Random** - Order is random within unused tasks

## That's It!

The feature is now active and working automatically. Players will notice improved variety in their tasks without any additional setup required.

For more technical details, see `TASK_HISTORY_FEATURE.md`.