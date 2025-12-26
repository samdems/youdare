# Tag Removal on Task Completion - Quick Reference

## Quick Start

### 1. Create a Task with Tag Removal

**Via Web UI:**
1. Go to **Create Task** or **Edit Task**
2. Fill in task details (type, description, spice rating)
3. Select task tags (what tags are needed to see this task)
4. Scroll to **"Tags to Remove on Completion"** section
5. Check tags to remove (marked with 🗑️ icon)
6. Save

**Via API:**
```bash
POST /api/tasks
Content-Type: application/json

{
  "type": "dare",
  "spice_rating": 2,
  "description": "Do 10 jumping jacks",
  "tags": [1, 2],           # Task requires these tags
  "tags_to_remove": [1]      # Remove tag 1 when completed
}
```

### 2. Complete a Task

**Via API:**
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
      "score": 5,
      "tags": [...]
    },
    "removed_tags_count": 1,
    "removed_tags": [...]
  }
}
```

## Common Use Cases

### Progressive Difficulty
```php
// Beginner task removes beginner tag
$task->tags()->attach([$beginnerTag->id]);
$task->tags_to_remove = [$beginnerTag->id];
```

### One-Time Challenges
```php
// First task removes first-time tag
$task->tags()->attach([$firstTimeTag->id]);
$task->tags_to_remove = [$firstTimeTag->id];
```

### Tutorial System
```php
// Tutorial task removes tutorial tag when complete
$task->tags()->attach([$tutorialTag->id]);
$task->tags_to_remove = [$tutorialTag->id];
```

### Unlock Content
```php
// Completing this unlocks new content
$task->tags()->attach([$intermediateTag->id]);
$task->tags_to_remove = [$lockedTag->id];
```

## Code Examples

### Remove Tags from Player
```php
$player = Player::find($playerId);
$task = Task::find($taskId);

// Remove tags specified in task
$removedTagIds = $task->removeTagsFromPlayer($player);

// Increment score
$player->incrementScore();

echo "Removed " . count($removedTagIds) . " tags";
```

### Get Removable Tags
```php
$task = Task::find($taskId);
$tags = $task->getRemovableTags();

foreach ($tags as $tag) {
    echo "Will remove: {$tag->name}\n";
}
```

### Check Player Tags
```php
$player = Player::with('tags')->find($playerId);
echo "Player tags: " . $player->tags->pluck('name')->join(', ');
```

## API Endpoints

### Complete Task (Recommended)
```
POST /api/players/{player}/complete-task
```
Handles tag removal + score increment.

### Legacy Score Endpoint
```
POST /api/players/{player}/score
```
⚠️ Does NOT handle tag removal. Use `complete-task` instead.

## Testing

Run test script:
```bash
php test_task_completion.php
```

This demonstrates:
- Creating tags and tasks
- Setting up tag removal
- Completing tasks
- Watching tags get removed
- Tag progression flow

## Important Notes

✅ **Do:**
- Use descriptive tag names (e.g., `beginner-level`, `first-time`)
- Test the flow before deploying
- Use `complete-task` endpoint for full functionality
- Document which tasks remove which tags

❌ **Don't:**
- Use the legacy `score` endpoint (doesn't remove tags)
- Remove tags that affect game balance without testing
- Forget to verify player has tags before showing tasks

## Quick Troubleshooting

**Tags not being removed?**
- Check task has `tags_to_remove` set
- Verify player has those tags
- Use `complete-task` endpoint, not `score`

**Tasks not appearing?**
- Player's tags may have been removed
- Check `$player->getAllAvailableTags()`
- Verify task tags match player tags

**API errors?**
- Verify `task_id` exists
- Check `player_id` is valid
- Review logs: `storage/logs/laravel.log`

## Real World Example

```php
// Setup: Create progression system
$beginner = Tag::create(['name' => 'Beginner']);
$intermediate = Tag::create(['name' => 'Intermediate']);

// Task that graduates beginners
$task = Task::create([
    'type' => 'truth',
    'description' => 'Share your most embarrassing moment',
    'spice_rating' => 2,
    'tags_to_remove' => [$beginner->id]
]);
$task->tags()->attach([$beginner->id, $intermediate->id]);

// Player starts as beginner
$player = Player::create(['name' => 'Alice', 'game_id' => $game->id]);
$player->tags()->attach([$beginner->id]);

// Complete task - removes beginner tag
$task->removeTagsFromPlayer($player);
$player->incrementScore();

// Player is now only intermediate
echo $player->tags->pluck('name'); // ['Intermediate']
```

## See Also

- Full documentation: `TAG_REMOVAL_ON_COMPLETION.md`
- Test script: `test_task_completion.php`
- API docs: `API_EXAMPLES.md`
