# Tag Removal on Task Completion

## Overview

Tasks can now automatically remove specific tags from players when completed. This powerful feature enables progressive difficulty systems, one-time challenges, tutorial systems, and unlock mechanics.

## How It Works

When creating or editing a task, you can specify which tags should be removed from a player when they complete that task. The system will:

1. Check if the player has any of the specified tags
2. Remove those tags from the player
3. Increment the player's score
4. Return updated player data with new tag list

## Use Cases

### 1. Progressive Difficulty
Remove beginner tags as players advance:
- Task: "Share your most embarrassing moment"
- Removes: `beginner` tag
- Result: Player no longer sees beginner-level tasks

### 2. One-Time Challenges
Prevent tasks from being repeated:
- Task: "First dare - introduce yourself creatively"
- Removes: `first-time` tag
- Result: Task won't appear again for this player

### 3. Tutorial/Onboarding
Guide new players through initial tasks:
- Task: "Complete your first truth question"
- Removes: `tutorial` tag
- Result: Player graduates from tutorial tasks

### 4. Unlock Systems
Gate content behind achievements:
- Task: "Complete 3 physical challenges"
- Removes: `physical-locked` tag
- Result: Player unlocks new physical challenges

### 5. Category Completion
Track progress through content categories:
- Task: "Final social challenge"
- Removes: `social-challenges` tag
- Result: Player has completed all social tasks

## Creating Tasks with Tag Removal

### Via Web Interface

1. Navigate to **Create Task** or **Edit Task**
2. Fill in standard task details (type, description, spice rating)
3. Select tags for the task (what tags this task requires)
4. Scroll to **"Tags to Remove on Completion"** section
5. Select tags to remove when completed (marked with 🗑️ icon)
6. Save the task

### Via API

```bash
POST /api/tasks
Content-Type: application/json

{
  "type": "truth",
  "spice_rating": 2,
  "description": "Share an embarrassing story from your childhood",
  "draft": false,
  "tags": [1, 2, 3],              // Task requires these tags
  "tags_to_remove": [1]            // Remove tag ID 1 when completed
}
```

## Completing Tasks with Tag Removal

### Via API Endpoint

Use the new `complete-task` endpoint:

```bash
POST /api/players/{player_id}/complete-task
Content-Type: application/json

{
  "task_id": 5,
  "points": 1
}
```

**Response:**
```json
{
  "success": true,
  "message": "Task completed successfully and removed 1 tag(s)",
  "data": {
    "player": {
      "id": 1,
      "name": "Alice",
      "score": 5,
      "tags": [
        {
          "id": 2,
          "name": "Intermediate",
          "slug": "intermediate"
        }
      ]
    },
    "task": {
      "id": 5,
      "type": "truth",
      "description": "Share an embarrassing story",
      "tags_to_remove": [1]
    },
    "removed_tags_count": 1,
    "removed_tags": [
      {
        "id": 1,
        "name": "Beginner",
        "slug": "beginner"
      }
    ]
  }
}
```

### Programmatically

```php
// Get player and task
$player = Player::find($playerId);
$task = Task::find($taskId);

// Remove tags from player
$removedTagIds = $task->removeTagsFromPlayer($player);

// Increment score
$player->incrementScore($points);

// Check what was removed
if (count($removedTagIds) > 0) {
    $removedTags = Tag::whereIn('id', $removedTagIds)->get();
    echo "Removed tags: " . $removedTags->pluck('name')->join(', ');
}
```

## Database Structure

### Migration
```php
Schema::table('tasks', function (Blueprint $table) {
    $table->json('tags_to_remove')
        ->nullable()
        ->comment('Tags that should be removed from player when task is completed');
});
```

### Task Model
```php
protected $fillable = [
    'type',
    'spice_rating',
    'description',
    'tags_to_remove',  // Array of tag IDs
    'draft',
    'user_id',
];

protected $casts = [
    'tags_to_remove' => 'array',
];
```

## Model Methods

### Task Model

#### `removeTagsFromPlayer(Player $player)`
Removes the specified tags from the player.
```php
$removedTagIds = $task->removeTagsFromPlayer($player);
// Returns array of removed tag IDs
```

#### `getRemovableTags()`
Gets the Tag models for tags that will be removed.
```php
$tags = $task->getRemovableTags();
// Returns Collection of Tag models
```

## Example Flow

### Setup
```php
// Create progression tags
$beginner = Tag::create(['name' => 'Beginner', 'slug' => 'beginner']);
$intermediate = Tag::create(['name' => 'Intermediate', 'slug' => 'intermediate']);
$advanced = Tag::create(['name' => 'Advanced', 'slug' => 'advanced']);

// Create task that removes beginner tag
$task = Task::create([
    'type' => 'truth',
    'spice_rating' => 2,
    'description' => 'Tell us about your greatest achievement',
    'tags_to_remove' => [$beginner->id]
]);
$task->tags()->attach([$beginner->id, $intermediate->id]);

// Create player with beginner and intermediate tags
$player = Player::create(['name' => 'Alice', 'game_id' => $game->id]);
$player->tags()->attach([$beginner->id, $intermediate->id]);
```

### Completion
```php
// Player completes the task
$removedTags = $task->removeTagsFromPlayer($player);
$player->incrementScore();

// Check results
echo "Score: {$player->score}\n";
echo "Tags: " . $player->tags->pluck('name')->join(', ') . "\n";
// Output: Score: 1
//         Tags: Intermediate
```

## Important Notes

### Tag Removal Behavior
- Only removes tags if the player actually has them
- Does not throw errors if player doesn't have the tag
- Can remove multiple tags in one task completion
- Tag removal happens before score increment

### Available Tasks
- Players can still see tasks that would remove their tags
- Once tags are removed, related tasks may no longer appear
- Use this for progressive unlocking systems

### Safety
- Tags are only removed when explicitly specified
- Empty `tags_to_remove` array = no tags removed
- `null` `tags_to_remove` = no tags removed
- Only affects the completing player, not other players

## Best Practices

### 1. Clear Naming
Use descriptive tag names that indicate their purpose:
- ✅ `beginner-level`, `first-time`, `tutorial-active`
- ❌ `tag1`, `temp`, `remove-me`

### 2. Document Intent
Add descriptions to tags explaining when they're removed:
```php
Tag::create([
    'name' => 'Beginner',
    'description' => 'Removed after completing first challenge',
]);
```

### 3. Progressive Systems
Create clear progression paths:
```
Beginner → Intermediate → Advanced → Expert
```

### 4. Combine with Task Tags
Use task tags to control who sees the task, and `tags_to_remove` for progression:
```php
// Task requires beginner tag (so only beginners see it)
$task->tags()->attach([$beginnerTag->id]);

// But removes beginner tag when completed (so they progress)
$task->tags_to_remove = [$beginnerTag->id];
```

### 5. Test Thoroughly
Always test tag removal flow:
```bash
php test_task_completion.php
```

## API Routes

### Complete Task
```
POST /api/players/{player}/complete-task
```

**Parameters:**
- `task_id` (required): ID of the task being completed
- `points` (optional): Points to award (default: 1)

**Response:** Player data with updated tags and score

### Legacy Score Endpoint
```
POST /api/players/{player}/score
```
This endpoint still works but does NOT handle tag removal.
Use `complete-task` endpoint instead for full functionality.

## Testing

Run the test script:
```bash
php test_task_completion.php
```

This creates:
- 3 test tags (Beginner, Intermediate, Advanced)
- 3 test tasks with different tag removal rules
- 1 test player with all tags
- Demonstrates full completion flow

## Migration

To add this feature to existing database:
```bash
php artisan migrate
```

The migration adds the `tags_to_remove` column to the `tasks` table.

## Troubleshooting

### Tags Not Being Removed
1. Check task has `tags_to_remove` set correctly
2. Verify player actually has those tags
3. Ensure using `complete-task` endpoint, not legacy `score` endpoint
4. Check player tags before and after: `$player->tags()->pluck('name')`

### Tasks Not Appearing
1. Player may have had tags removed
2. Check player's current tags: `$player->getAllAvailableTags()`
3. Verify task's required tags match player's tags

### API Errors
1. Ensure `task_id` exists and is valid
2. Check player_id is correct
3. Verify authentication if required
4. Check Laravel logs: `storage/logs/laravel.log`

## Examples

See `test_task_completion.php` for a complete working example demonstrating:
- Tag creation
- Task creation with tag removal
- Player creation with tags
- Task completion flow
- Tag progression
- Available tasks filtering