# Tag Removal on Task Completion

**Status:** ✅ Production Ready | **Version:** 1.0.0 | **Date:** 2025-12-26

## Quick Overview

Tasks can now automatically remove specific tags from players when completed. This enables:

- 🎯 Progressive difficulty systems
- 🔒 Content unlocking mechanics
- 📚 Tutorial/onboarding flows
- 🏆 Achievement-based progression
- ⭐ One-time challenges

## What's New

### Database
- New `tags_to_remove` JSON column on `tasks` table
- Stores array of tag IDs to remove when task is completed

### API
- New endpoint: `POST /api/players/{player}/complete-task`
- Handles task completion + tag removal + score increment
- Returns updated player data with removed tags list

### UI
- "Tags to Remove on Completion" section in task create/edit forms
- Visual distinction with 🗑️ icon and red theme
- Display of removable tags in task show view

### Features
- Automatic tag removal on task completion
- Multiple tags can be removed per task
- Safe: only removes if player has the tag
- Backward compatible with existing tasks

## Quick Start

### 1. Create a Task with Tag Removal

**Web Interface:**
1. Go to Create Task or Edit Task
2. Fill in task details (type, description, spice rating)
3. Select task tags (who can see this task)
4. Scroll to "Tags to Remove on Completion"
5. Check tags to remove (marked with 🗑️)
6. Save

**API:**
```bash
POST /api/tasks
Content-Type: application/json

{
  "type": "dare",
  "spice_rating": 2,
  "description": "Complete your first challenge",
  "tags": [1, 2],              # Task requires these tags
  "tags_to_remove": [1]         # Remove tag 1 on completion
}
```

### 2. Complete a Task

**API:**
```bash
POST /api/players/5/complete-task
Content-Type: application/json

{
  "task_id": 10,
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
      "id": 5,
      "name": "Alice",
      "score": 7,
      "tags": [...]
    },
    "task": {...},
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

## Common Use Cases

### Progressive Difficulty
Remove beginner tags as players advance:
```php
// Welcome task removes rookie tag
$task->tags()->attach([$rookieTag->id]);
$task->tags_to_remove = [$rookieTag->id];
```

### One-Time Challenges
Prevent tasks from being repeated:
```php
// First dare removes first-time tag
$task->tags()->attach([$firstTimeTag->id]);
$task->tags_to_remove = [$firstTimeTag->id];
```

### Tutorial System
Guide players through onboarding:
```php
// Tutorial task graduates player
$task->tags()->attach([$tutorialTag->id]);
$task->tags_to_remove = [$tutorialTag->id];
```

### Content Unlocking
Gate advanced content:
```php
// Achievement unlocks new content
$task->tags()->attach([$intermediateTag->id]);
$task->tags_to_remove = [$lockedTag->id];
```

## Code Examples

### Basic Task Completion
```php
$player = Player::find($playerId);
$task = Task::find($taskId);

// Remove tags and increment score
$removedTagIds = $task->removeTagsFromPlayer($player);
$player->incrementScore();

// Check what was removed
if (count($removedTagIds) > 0) {
    echo "Removed " . count($removedTagIds) . " tags";
}
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

## Model Methods

### Task Model

#### `removeTagsFromPlayer(Player $player): array`
Removes the specified tags from the player.
```php
$removedTagIds = $task->removeTagsFromPlayer($player);
// Returns: array of removed tag IDs
```

#### `getRemovableTags(): Collection`
Gets the Tag models for tags that will be removed.
```php
$tags = $task->getRemovableTags();
// Returns: Collection of Tag models
```

## API Endpoints

### Complete Task (Recommended)
```
POST /api/players/{player}/complete-task
```

**Body:**
```json
{
  "task_id": 5,        // required
  "points": 1          // optional, default: 1
}
```

**Response:**
- Player data with updated tags
- Task data
- Count of removed tags
- List of removed tags

### Legacy Score Endpoint
```
POST /api/players/{player}/score
```
⚠️ **Note:** Does NOT handle tag removal. Use `complete-task` instead.

## Real-World Example

```php
// Setup: Progressive difficulty system
$beginner = Tag::create(['name' => 'Beginner', 'slug' => 'beginner']);
$intermediate = Tag::create(['name' => 'Intermediate', 'slug' => 'intermediate']);

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
$player->refresh()->load('tags');

// Result: Player is now intermediate
echo $player->tags->pluck('name'); // ['Intermediate']
```

## Testing

### Run Test Script
```bash
php test_task_completion.php
```

This creates a complete test scenario with:
- 3 test tags (Beginner, Intermediate, Advanced)
- 3 test tasks with different removal rules
- 1 test player progressing through levels
- Demonstrates full flow

### Run Party Game Example
```bash
php example_tag_removal_usage.php
```

This shows a realistic party game with:
- Progressive difficulty levels
- Multiple players at different stages
- Complete progression story
- API usage examples

## Best Practices

### ✅ Do This
- Use descriptive tag names (`beginner-level`, `first-time`)
- Test progression flow before deployment
- Use `complete-task` endpoint for full functionality
- Document which tasks remove which tags
- Combine with task tags for smart content filtering

### ❌ Avoid This
- Using legacy `score` endpoint (no tag removal)
- Removing tags without testing impact
- Unclear tag names (`tag1`, `temp`)
- Removing tags that break game progression

## Important Notes

### Safety
- Only removes tags if player has them (no errors)
- Only affects the completing player
- Empty/null `tags_to_remove` = no action
- Tag removal happens before score increment

### Compatibility
- ✅ Backward compatible (existing tasks unchanged)
- ✅ Web UI fully supports feature
- ✅ API fully supports feature
- ✅ Works with existing tag system

### Migration
Run migration on existing installations:
```bash
php artisan migrate
```

## Troubleshooting

### Tags Not Being Removed
1. ✓ Check task has `tags_to_remove` set
2. ✓ Verify player has those tags
3. ✓ Use `complete-task` endpoint, not `score`
4. ✓ Check logs: `storage/logs/laravel.log`

### Tasks Not Appearing
1. ✓ Player may have had tags removed
2. ✓ Check `$player->getAllAvailableTags()`
3. ✓ Verify task tags match player tags

### API Errors
1. ✓ Ensure `task_id` exists
2. ✓ Verify `player_id` is valid
3. ✓ Check authentication if required
4. ✓ Review Laravel logs

## Files Modified

### Backend
- `database/migrations/2025_12_26_194052_add_tags_to_remove_to_tasks_table.php` (new)
- `app/Models/Task.php` (updated)
- `app/Http/Controllers/TaskController.php` (updated)
- `app/Http/Controllers/Api/TaskController.php` (updated)
- `app/Http/Controllers/Api/PlayerController.php` (updated)
- `routes/api.php` (updated)

### Frontend
- `resources/views/tasks/create.blade.php` (updated)
- `resources/views/tasks/edit.blade.php` (updated)
- `resources/views/tasks/show.blade.php` (updated)

### Documentation
- `TAG_REMOVAL_ON_COMPLETION.md` (new - full docs)
- `TAG_REMOVAL_QUICKSTART.md` (new - quick reference)
- `TAG_REMOVAL_SUMMARY.md` (new - implementation summary)
- `TAG_REMOVAL_README.md` (new - this file)

### Testing
- `test_task_completion.php` (new - test script)
- `example_tag_removal_usage.php` (new - party game example)

## Documentation

### Comprehensive Guides
- **TAG_REMOVAL_ON_COMPLETION.md** - Complete documentation with examples, API reference, best practices
- **TAG_REMOVAL_QUICKSTART.md** - Quick reference guide for common tasks
- **TAG_REMOVAL_SUMMARY.md** - Implementation details and changes
- **TAG_REMOVAL_README.md** - This file (overview)

### Test Files
- **test_task_completion.php** - Basic test demonstrating tag removal
- **example_tag_removal_usage.php** - Realistic party game scenario

## Future Enhancements

Possible additions:
- Tag addition on completion (opposite of removal)
- Conditional tag removal (based on score, time, etc.)
- Tag replacement (remove X, add Y)
- Analytics on tag removal patterns
- Bulk tag management operations
- Tag removal history/audit log

## Support

### Need Help?
1. Check documentation files listed above
2. Run test scripts to see examples
3. Review API examples in docs
4. Check Laravel logs for errors

### Report Issues
Document any issues with:
- Steps to reproduce
- Expected vs actual behavior
- Relevant log entries
- Laravel version and environment

## Version History

### v1.0.0 (2025-12-26)
- ✅ Initial release
- ✅ Database migration
- ✅ Model methods
- ✅ API endpoints
- ✅ UI updates
- ✅ Full documentation
- ✅ Test scripts
- ✅ Examples

## Summary

The tag removal feature is fully implemented, tested, and documented. It provides a powerful mechanism for creating dynamic, progressive gameplay experiences where player capabilities and available content evolve based on their actions.

**Ready for production use!** 🚀

---

For detailed information, see:
- Full Documentation: `TAG_REMOVAL_ON_COMPLETION.md`
- Quick Reference: `TAG_REMOVAL_QUICKSTART.md`
- Implementation Details: `TAG_REMOVAL_SUMMARY.md`
