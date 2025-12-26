# Can't Have Tags Feature

## Overview

The "Can't Have Tags" feature allows you to specify tags that a player must **NOT** have for a task to be available to them. This is the opposite of regular task tags (which specify what tags players must have).

This feature is useful for creating exclusive content or preventing certain tasks from appearing to players with specific characteristics.

## Use Cases

1. **Exclusive Content**: Create tasks that only appear to players who don't have certain tags
   - Example: A "Mystery Box" task that only appears if the player doesn't have the "knows-secret" tag
   
2. **Progressive Gameplay**: Hide advanced tasks until players have completed prerequisites
   - Example: Hide "Advanced Challenge" tasks from players who still have the "beginner" tag
   
3. **Content Filtering**: Prevent certain tasks from appearing based on player attributes
   - Example: Don't show "drinking" tasks to players with the "non-drinker" tag
   
4. **Temporary Restrictions**: Combine with `tags_to_remove` to create tasks that unlock other tasks
   - Example: A task that removes the "first-timer" tag, which unlocks tasks that have "first-timer" in cant_have_tags

## Database Schema

### Tasks Table
- `cant_have_tags` - JSON array of tag IDs that players must NOT have for this task to appear

## How It Works

1. When fetching available tasks for a player, the system checks each task's `cant_have_tags` field
2. If the field is empty or null, the task is available (no restrictions)
3. If the field contains tag IDs, the system checks if the player has ANY of those tags
4. If the player has ANY of the restricted tags, the task is **hidden** from that player
5. If the player has NONE of the restricted tags, the task is **available**

## Model Methods

### Task Model

#### `getCantHaveTags()`
Get the Tag models for tags in the cant_have_tags array.

```php
$task = Task::find(1);
$restrictedTags = $task->getCantHaveTags();
// Returns Collection of Tag models
```

#### `isAvailableForPlayer(Player $player)`
Check if a task is available for a specific player based on cant_have_tags.

```php
$task = Task::find(1);
$player = Player::find(1);

if ($task->isAvailableForPlayer($player)) {
    // Task can be shown to this player
} else {
    // Task should be hidden from this player
}
```

### Player Model

The `getAvailableTasks()` method automatically filters out tasks where the player has any of the cant_have_tags.

```php
$player = Player::find(1);
$availableTasks = $player->getAvailableTasks();
// Only returns tasks that:
// 1. Match the player's tags (regular filtering)
// 2. The player doesn't have any of the cant_have_tags
```

### Game Model

Use `getAvailableTasksForPlayer()` to get tasks for a specific player, which includes cant_have_tags filtering.

```php
$game = Game::find(1);
$player = $game->players()->first();
$tasks = $game->getAvailableTasksForPlayer($player);
```

## API Usage

### Creating a Task with Can't Have Tags

When creating or updating a task via API, include the `cant_have_tags` field as an array of tag IDs:

```json
POST /api/tasks
{
  "type": "dare",
  "description": "Tell us about your most embarrassing moment",
  "spice_rating": 3,
  "cant_have_tags": [5, 7],
  "draft": false
}
```

This task will only appear to players who do NOT have tag IDs 5 or 7.

### Response Format

Tasks returned by the API will include the `cant_have_tags` field:

```json
{
  "id": 1,
  "type": "dare",
  "description": "Tell us about your most embarrassing moment",
  "spice_rating": 3,
  "cant_have_tags": [5, 7],
  "tags": [
    {
      "id": 1,
      "name": "Party Mode",
      "slug": "party-mode"
    }
  ]
}
```

## Examples

### Example 1: Beginner-Only Tasks

Create a task that only appears to players without the "experienced" tag:

```php
$experiencedTag = Tag::where('slug', 'experienced')->first();

$task = Task::create([
    'type' => 'dare',
    'description' => 'Welcome! This is your first challenge.',
    'spice_rating' => 1,
    'cant_have_tags' => [$experiencedTag->id]
]);
```

### Example 2: Progressive Unlocking

Create a beginner task that removes the "beginner" tag when completed, and an advanced task that requires the beginner tag to be removed:

```php
$beginnerTag = Tag::where('slug', 'beginner')->first();

// Beginner task that removes the beginner tag
$beginnerTask = Task::create([
    'type' => 'dare',
    'description' => 'Complete your first challenge!',
    'spice_rating' => 1,
    'tags_to_remove' => [$beginnerTag->id]
]);

// Advanced task that only appears after beginner tag is removed
$advancedTask = Task::create([
    'type' => 'dare',
    'description' => 'Advanced challenge for experienced players!',
    'spice_rating': 3,
    'cant_have_tags' => [$beginnerTag->id]
]);
```

### Example 3: Mutually Exclusive Content

Create tasks that are mutually exclusive based on player choices:

```php
$teamATag = Tag::where('slug', 'team-a')->first();
$teamBTag = Tag::where('slug', 'team-b')->first();

// Task only for Team A (not Team B)
$teamATask = Task::create([
    'type' => 'dare',
    'description' => 'Team A exclusive challenge',
    'spice_rating' => 2,
    'cant_have_tags' => [$teamBTag->id]
]);

// Task only for Team B (not Team A)
$teamBTask = Task::create([
    'type' => 'dare',
    'description' => 'Team B exclusive challenge',
    'spice_rating' => 2,
    'cant_have_tags' => [$teamATag->id]
]);
```

## Combining with Regular Tags

Tasks can have both regular tags (required) and cant_have_tags (forbidden):

```php
$adultTag = Tag::where('slug', 'adults-only')->first();
$sensitiveTag = Tag::where('slug', 'sensitive-content')->first();

$task = Task::create([
    'type' => 'dare',
    'description' => 'Adult content for non-sensitive players',
    'spice_rating' => 4,
]);

// Attach required tag
$task->tags()->attach($adultTag->id);

// Set forbidden tag
$task->update([
    'cant_have_tags' => [$sensitiveTag->id]
]);
```

This task will only appear to players who:
- HAVE the "adults-only" tag (regular tag requirement)
- DO NOT have the "sensitive-content" tag (cant_have_tags restriction)

## Important Notes

1. **Empty Array vs Null**: Both empty array `[]` and `null` mean no restrictions (task available to all)

2. **AND Logic**: If multiple tags are in cant_have_tags, the player must have NONE of them (not ANY)
   - cant_have_tags: [1, 2, 3] = Player can't have tag 1 OR 2 OR 3

3. **Filtering Priority**: cant_have_tags is checked AFTER regular tag matching
   - First: Check if player matches required tags (regular tags)
   - Then: Check if player has any forbidden tags (cant_have_tags)

4. **Performance**: Filtering is done in PHP after fetching tasks from database, so be mindful of large datasets

5. **Game vs Player**: The Player model's `getAvailableTasks()` automatically handles cant_have_tags filtering. Always use this method when getting tasks for a specific player.

## Migration

The feature was added via migration:

```bash
php artisan make:migration add_cant_have_tags_to_tasks_table
php artisan migrate
```

Migration adds:
- `cant_have_tags` column (JSON, nullable) to tasks table
- Column is positioned after `tags_to_remove`

## Testing

Test that tasks are properly filtered:

```php
// Create test data
$restrictedTag = Tag::create(['name' => 'Restricted', 'slug' => 'restricted']);
$player = Player::create(['name' => 'Test Player', 'game_id' => 1]);
$player->tags()->attach($restrictedTag->id);

// Create task with cant_have_tags
$task = Task::create([
    'type' => 'dare',
    'description' => 'Should not appear',
    'spice_rating' => 1,
    'cant_have_tags' => [$restrictedTag->id]
]);

// Test filtering
$availableTasks = $player->getAvailableTasks();
$this->assertFalse($availableTasks->contains($task));

// Remove restricted tag
$player->tags()->detach($restrictedTag->id);

// Test task now appears
$availableTasks = $player->getAvailableTasks();
$this->assertTrue($availableTasks->contains($task));
```

## Troubleshooting

### Task not appearing for any player

Check:
1. Is `cant_have_tags` set to a valid array of existing tag IDs?
2. Are you sure no players have those tags?
3. Does the task have other required tags that players match?

### Task appearing when it shouldn't

Check:
1. Is the `cant_have_tags` field properly saved as JSON array?
2. Verify the player actually has the restricted tags: `$player->tags()->pluck('id')->toArray()`
3. Use `$task->isAvailableForPlayer($player)` to debug specific cases

### Tasks not filtering in custom queries

If you're writing custom task queries, make sure to apply the cant_have_tags filter:

```php
// DON'T do this - bypasses cant_have_tags filtering
$tasks = Task::published()->where('spice_rating', '<=', 3)->get();

// DO this - uses proper filtering
$tasks = $player->getAvailableTasks()->where('spice_rating', '<=', 3);
```
