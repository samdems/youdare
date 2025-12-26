# Can't Have Tags - Quick Start Guide

## What Is It?

The "Can't Have Tags" feature lets you hide tasks from players who have specific tags. It's the opposite of regular tags:
- **Regular tags**: Player must HAVE the tag to see the task
- **Can't have tags**: Player must NOT HAVE the tag to see the task

## Quick Example

```php
// Create a task that only non-beginners can see
$task = Task::create([
    'type' => 'dare',
    'description' => 'Advanced challenge',
    'spice_rating' => 4,
    'cant_have_tags' => [5]  // Hide from players with tag ID 5
]);
```

## Common Use Cases

### 1. Beginner vs Advanced Content
```php
$beginnerTag = Tag::where('slug', 'beginner')->first();

// Advanced task - hidden from beginners
Task::create([
    'type' => 'dare',
    'description' => 'Expert level challenge',
    'cant_have_tags' => [$beginnerTag->id]
]);
```

### 2. Progressive Unlocking
```php
$lockedTag = Tag::where('slug', 'locked')->first();

// Unlock task - removes the locked tag
$unlockTask = Task::create([
    'description' => 'Complete this to unlock new content',
    'tags_to_remove' => [$lockedTag->id]
]);

// Hidden task - appears after locked tag is removed
$hiddenTask = Task::create([
    'description' => 'Secret unlocked content',
    'cant_have_tags' => [$lockedTag->id]
]);
```

### 3. Exclusive Groups
```php
$teamATag = Tag::where('slug', 'team-a')->first();

// Only visible to players NOT on Team A
Task::create([
    'description' => 'Team B exclusive mission',
    'cant_have_tags' => [$teamATag->id]
]);
```

## API Usage

### Creating Tasks via API
```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "type": "dare",
    "description": "Advanced players only",
    "spice_rating": 3,
    "cant_have_tags": [5, 7]
  }'
```

### Updating Tasks
```bash
curl -X PUT http://localhost:8000/api/tasks/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "cant_have_tags": [5, 7]
  }'
```

## How It Works

1. Player requests available tasks
2. System gets all tasks matching player's regular tags
3. System filters OUT tasks where player has ANY of the cant_have_tags
4. Player only sees tasks they're allowed to see

## Important Rules

✅ **Empty or null = No restrictions** - Task visible to everyone
```php
'cant_have_tags' => null    // No restrictions
'cant_have_tags' => []      // No restrictions
```

✅ **Multiple tags = OR logic** - Player can't have ANY of them
```php
'cant_have_tags' => [1, 2, 3]  // Hide if player has tag 1 OR 2 OR 3
```

✅ **Automatic filtering** - Just use `$player->getAvailableTasks()`
```php
$tasks = $player->getAvailableTasks();  // Already filtered!
```

## Code Examples

### Check if task is available for a player
```php
if ($task->isAvailableForPlayer($player)) {
    echo "Player can see this task";
} else {
    echo "Task is hidden from player";
}
```

### Get the restricted tags for a task
```php
$restrictedTags = $task->getCantHaveTags();
foreach ($restrictedTags as $tag) {
    echo "Players with {$tag->name} can't see this task\n";
}
```

### Get tasks for a specific player
```php
// This automatically filters by cant_have_tags
$availableTasks = $player->getAvailableTasks();
```

## Combining Regular Tags and Can't Have Tags

You can use both together for precise control:

```php
$adultTag = Tag::where('slug', 'adults-only')->first();
$sensitiveTag = Tag::where('slug', 'sensitive')->first();

$task = Task::create([
    'description' => 'Adult content, not sensitive',
    'spice_rating' => 4,
    'cant_have_tags' => [$sensitiveTag->id]
]);

// Attach required tag
$task->tags()->attach($adultTag->id);
```

This task requires:
- ✓ Player HAS "adults-only" tag (regular tag)
- ✓ Player DOES NOT have "sensitive" tag (cant_have_tags)

## Testing Your Setup

Run the test script:
```bash
php test_cant_have_tags.php
```

Or test manually:
```php
// Create test scenario
$restrictedTag = Tag::create(['name' => 'Restricted']);
$player = Player::find(1);
$player->tags()->attach($restrictedTag->id);

$task = Task::create([
    'description' => 'Hidden task',
    'cant_have_tags' => [$restrictedTag->id]
]);

// Should return false
var_dump($task->isAvailableForPlayer($player));

// Remove tag
$player->tags()->detach($restrictedTag->id);

// Should return true
var_dump($task->isAvailableForPlayer($player));
```

## Troubleshooting

**Q: Task not appearing for anyone?**
- Check if cant_have_tags contains valid tag IDs
- Verify the task has regular tags that match the game/player

**Q: Task appearing when it shouldn't?**
- Confirm cant_have_tags is an array: `var_dump($task->cant_have_tags)`
- Check player's tags: `$player->tags()->pluck('id')->toArray()`

**Q: Custom queries not filtering correctly?**
- Always use `$player->getAvailableTasks()` for proper filtering
- Don't query tasks directly unless you apply the filter manually

## Need More Info?

See the full documentation: `CANT_HAVE_TAGS.md`
