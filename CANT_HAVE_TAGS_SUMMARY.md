# Can't Have Tags Feature - Implementation Summary

## Overview

The "Can't Have Tags" feature has been successfully implemented. This feature allows tasks to specify tags that players must **NOT** have for the task to be visible to them. It provides negative filtering, complementing the existing positive tag filtering system.

## What Was Implemented

### 1. Database Changes

**Migration:** `2025_12_26_204721_add_cant_have_tags_to_tasks_table.php`

- Added `cant_have_tags` column to `tasks` table
- Type: JSON (nullable)
- Stores array of tag IDs that players must NOT have
- Positioned after `tags_to_remove` column

**Run migration:**
```bash
php artisan migrate
```

### 2. Model Updates

**Task Model (`app/Models/Task.php`)**

Added to fillable array:
- `cant_have_tags`

Added to casts:
- `cant_have_tags` => `array`

New methods:
- `getCantHaveTags()` - Returns Tag models for cant_have_tags
- `isAvailableForPlayer(Player $player)` - Checks if task is available for a specific player

**Player Model (`app/Models/Player.php`)**

Updated methods:
- `getAvailableTasks()` - Now filters out tasks where player has any cant_have_tags

**Game Model (`app/Models/Game.php`)**

New methods:
- `getAvailableTasksForPlayer(Player $player)` - Gets tasks for specific player with cant_have_tags filtering

### 3. Controller Updates

**TaskController (`app/Http/Controllers/TaskController.php`)**

Updated validation in `store()` and `update()`:
```php
"cant_have_tags" => "array",
"cant_have_tags.*" => "exists:tags,id",
```

**API TaskController (`app/Http/Controllers/Api/TaskController.php`)**

Updated validation in `store()` and `update()`:
```php
"cant_have_tags" => "array",
"cant_have_tags.*" => "exists:tags,id",
```

### 4. View Updates

**Create Task Form (`resources/views/tasks/create.blade.php`)**

Added new section: "Can't Have Tags (Negative Filter)"
- Displays all available tags
- Uses warning color scheme (🚫 icon)
- Clear description of functionality
- Checkbox selection interface

**Edit Task Form (`resources/views/tasks/edit.blade.php`)**

Added new section: "Can't Have Tags (Negative Filter)"
- Pre-selects existing cant_have_tags
- Same interface as create form
- Preserves old input on validation errors

**Show Task View (`resources/views/tasks/show.blade.php`)**

Added display section:
- Shows which tags will prevent task from appearing
- Warning alert with clear messaging
- Links to tag details
- Only displays if cant_have_tags are present

### 5. Documentation

Created comprehensive documentation:

1. **CANT_HAVE_TAGS.md** - Full documentation
   - Detailed explanation of how it works
   - Database schema
   - Model methods
   - API usage examples
   - Use cases and examples
   - Troubleshooting guide

2. **CANT_HAVE_TAGS_QUICKSTART.md** - Quick reference
   - Quick examples
   - Common use cases
   - API usage
   - Testing instructions
   - Troubleshooting tips

3. **Updated README.md**
   - Added feature to features list
   - Added to tagging system section
   - Added documentation links

### 6. Testing

**Test Script:** `test_cant_have_tags.php`

Comprehensive test suite that validates:
- ✓ Tag creation and assignment
- ✓ Task filtering for different player types
- ✓ `isAvailableForPlayer()` method
- ✓ `getCantHaveTags()` method
- ✓ Multiple cant_have_tags per task
- ✓ Proper exclusion logic

**Run tests:**
```bash
php test_cant_have_tags.php
```

All tests pass successfully!

## How It Works

### Logic Flow

1. Player requests available tasks via `$player->getAvailableTasks()`
2. System fetches tasks matching regular tags (positive filtering)
3. System applies cant_have_tags filter (negative filtering)
4. For each task with cant_have_tags:
   - Get player's tag IDs
   - Check if player has ANY of the cant_have_tags
   - If yes: Hide task from player
   - If no: Show task to player
5. Return filtered task collection

### Example Scenario

```php
// Create tags
$beginnerTag = Tag::create(['name' => 'Beginner']);
$advancedTag = Tag::create(['name' => 'Advanced']);

// Create player with beginner tag
$player = Player::create(['name' => 'New Player']);
$player->tags()->attach($beginnerTag->id);

// Create advanced task (hidden from beginners)
$task = Task::create([
    'description' => 'Expert challenge',
    'cant_have_tags' => [$beginnerTag->id]
]);

// This returns FALSE - task hidden from beginner
$task->isAvailableForPlayer($player);

// Remove beginner tag
$player->tags()->detach($beginnerTag->id);

// This returns TRUE - task now visible
$task->isAvailableForPlayer($player);
```

## API Usage

### Creating Task with Can't Have Tags

```bash
POST /api/tasks
Content-Type: application/json

{
  "type": "dare",
  "description": "Advanced players only",
  "spice_rating": 4,
  "cant_have_tags": [5, 7]
}
```

### Updating Can't Have Tags

```bash
PUT /api/tasks/1
Content-Type: application/json

{
  "cant_have_tags": [5, 7, 9]
}
```

### Response Format

```json
{
  "id": 1,
  "type": "dare",
  "description": "Advanced players only",
  "spice_rating": 4,
  "cant_have_tags": [5, 7],
  "tags": [...]
}
```

## UI Features

### Visual Design

- **Icon:** 🚫 (no entry sign)
- **Color Scheme:** Warning (yellow/orange)
- **Clear Labels:** "Can't Have Tags (Negative Filter)"
- **Helpful Description:** Explains negative filtering concept

### User Experience

- Checkbox interface (consistent with other tag selections)
- Pre-filled on edit forms
- Displays on task detail page
- Clear warning alert when cant_have_tags are present
- Links to tag details for more information

## Use Cases

### 1. Progressive Content
```php
// Hide advanced content until players complete basics
$task->cant_have_tags = [$beginnerTag->id];
```

### 2. Exclusive Groups
```php
// Content for Team A only (not Team B)
$task->cant_have_tags = [$teamBTag->id];
```

### 3. Content Restrictions
```php
// Hide drinking tasks from non-drinkers
$task->cant_have_tags = [$nonDrinkerTag->id];
```

### 4. Unlockable Content
```php
// Unlock task removes "locked" tag
$unlockTask->tags_to_remove = [$lockedTag->id];

// Hidden content appears after unlock
$hiddenTask->cant_have_tags = [$lockedTag->id];
```

## Important Notes

### Filtering Priority

1. First: Match regular tags (player must have at least one)
2. Then: Check cant_have_tags (player must have none)
3. Result: Task shown only if both conditions pass

### Empty Values

- `null` = No restrictions
- `[]` = No restrictions
- Both are treated the same way

### OR Logic

Multiple cant_have_tags use OR logic:
- `[1, 2, 3]` means player can't have tag 1 OR 2 OR 3
- If player has ANY of them, task is hidden

### Automatic Filtering

Always use `$player->getAvailableTasks()` - it handles all filtering automatically:
```php
// ✓ CORRECT - automatic filtering
$tasks = $player->getAvailableTasks();

// ✗ WRONG - bypasses filtering
$tasks = Task::published()->get();
```

## Files Modified/Created

### Created
- `database/migrations/2025_12_26_204721_add_cant_have_tags_to_tasks_table.php`
- `CANT_HAVE_TAGS.md`
- `CANT_HAVE_TAGS_QUICKSTART.md`
- `CANT_HAVE_TAGS_SUMMARY.md`
- `test_cant_have_tags.php`

### Modified
- `app/Models/Task.php`
- `app/Models/Player.php`
- `app/Models/Game.php`
- `app/Http/Controllers/TaskController.php`
- `app/Http/Controllers/Api/TaskController.php`
- `resources/views/tasks/create.blade.php`
- `resources/views/tasks/edit.blade.php`
- `resources/views/tasks/show.blade.php`
- `README.md`

## Verification

### Test Results
```
✓ Test 1 PASSED: Beginner player sees 3 tasks
✓ Test 2 PASSED: Experienced player sees 4 tasks
✓ Test 3 PASSED: VIP player sees 3 tasks
✓ Test 4 PASSED: Beginner cannot see advanced task
✓ Test 5 PASSED: Experienced cannot see beginner-only task
✓ Test 6 PASSED: VIP cannot see non-VIP task
✓ Test 7 PASSED: Task 5 only available to experienced player

All tests PASSED!
```

### Manual Testing

1. Create a task via UI with cant_have_tags selected
2. View task detail page - see cant_have_tags displayed
3. Edit task - see cant_have_tags pre-selected
4. Test via API - send cant_have_tags in JSON
5. Verify filtering works in game play

## Next Steps

### Optional Enhancements

1. Add bulk operations for cant_have_tags
2. Add cant_have_tags statistics to task statistics endpoint
3. Add UI indicators showing which tasks are hidden due to cant_have_tags
4. Add admin dashboard showing cant_have_tags usage
5. Add export/import functionality for tasks with cant_have_tags

### Monitoring

- Track cant_have_tags usage in analytics
- Monitor filter performance with large datasets
- Collect user feedback on feature clarity

## Support

For questions or issues:
1. Check **CANT_HAVE_TAGS_QUICKSTART.md** for quick examples
2. Read **CANT_HAVE_TAGS.md** for full documentation
3. Run **test_cant_have_tags.php** to verify installation
4. Check model methods: `isAvailableForPlayer()`, `getCantHaveTags()`

## Conclusion

The Can't Have Tags feature is fully implemented and tested. It provides powerful negative filtering capabilities that complement the existing tag system, enabling more sophisticated content delivery strategies.

The feature is:
- ✅ Fully functional in backend
- ✅ Integrated in UI (create, edit, show)
- ✅ Available via API
- ✅ Thoroughly tested
- ✅ Comprehensively documented
- ✅ Ready for production use