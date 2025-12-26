# Tags to Add - Quick Start Guide

## What Is It?

The "Tags to Add" feature lets you automatically give tags to players when they complete a task. It's perfect for:
- 🏆 Awarding achievements and badges
- 📈 Marking player progression
- 🔓 Unlocking new content tiers
- ⭐ Rewarding milestones

## Quick Example

```php
// Create a task that awards a "Veteran" tag when completed
$task = Task::create([
    'type' => 'dare',
    'description' => 'Complete your first challenge',
    'spice_rating' => 2,
    'tags_to_add' => [5]  // Add tag ID 5 to player on completion
]);
```

## Common Use Cases

### 1. Achievement System
```php
$achieverTag = Tag::where('slug', 'achiever')->first();

// Task awards achievement badge
Task::create([
    'description' => 'Unlock the Achiever badge',
    'tags_to_add' => [$achieverTag->id]
]);
```

### 2. Progression System
```php
$beginnerTag = Tag::where('slug', 'beginner')->first();
$veteranTag = Tag::where('slug', 'veteran')->first();

// Task promotes beginner to veteran
Task::create([
    'description' => 'Graduation challenge',
    'tags_to_remove' => [$beginnerTag->id],  // Remove beginner status
    'tags_to_add' => [$veteranTag->id]        // Add veteran status
]);
```

### 3. Unlocking Content
```php
$unlockedTag = Tag::where('slug', 'premium-unlocked')->first();

// Task unlocks premium content
Task::create([
    'description' => 'Complete special challenge',
    'tags_to_add' => [$unlockedTag->id]
]);

// Premium tasks require the unlocked tag
$premiumTask = Task::create([
    'description' => 'Premium exclusive challenge',
]);
$premiumTask->tags()->attach($unlockedTag->id);
```

### 4. Multiple Rewards
```php
$braveTag = Tag::where('slug', 'brave')->first();
$strongTag = Tag::where('slug', 'strong')->first();

// Task awards multiple tags
Task::create([
    'description' => 'Epic challenge',
    'tags_to_add' => [$braveTag->id, $strongTag->id]
]);
```

## How It Works

### Backend (PHP)

**Add tags when task is completed:**
```php
$task = Task::find(1);
$player = Player::find(1);

// Add tags to player
$addedTags = $task->addTagsToPlayer($player);
// Returns array of tag IDs that were added
```

**Get tags that will be added:**
```php
$task = Task::find(1);
$tagsToAdd = $task->getAddableTags();
// Returns Collection of Tag models
```

**Complete progression (remove + add):**
```php
// Remove old tags, add new tags
$task->removeTagsFromPlayer($player);
$task->addTagsToPlayer($player);
```

### UI (Web Interface)

When creating or editing a task, you'll see:
- **Tags to Add on Completion** section (✨ sparkles icon)
- Green success color scheme
- Checkbox selection for each tag
- Description: "Tags that will be added to the player when they complete this task"

## API Usage

### Creating Task with Tags to Add
```bash
POST /api/tasks
Content-Type: application/json

{
  "type": "dare",
  "description": "Earn your veteran badge",
  "spice_rating": 3,
  "tags_to_add": [5, 7]
}
```

### Updating Tags to Add
```bash
PUT /api/tasks/1
Content-Type: application/json

{
  "tags_to_add": [5, 7, 9]
}
```

### Response Format
```json
{
  "id": 1,
  "type": "dare",
  "description": "Earn your veteran badge",
  "spice_rating": 3,
  "tags_to_add": [5, 7],
  "tags_to_remove": [],
  "tags": [...]
}
```

## Important Features

✅ **Duplicate Prevention** - Tag won't be added if player already has it
```php
$task->addTagsToPlayer($player);  // Adds tag
$task->addTagsToPlayer($player);  // Skips (already has it)
```

✅ **Multiple Tags** - Add as many tags as needed
```php
'tags_to_add' => [1, 2, 3, 4, 5]
```

✅ **Combine with Remove** - Perfect for progression
```php
'tags_to_remove' => [1],  // Remove old status
'tags_to_add' => [2]       // Add new status
```

✅ **Empty = No Action** - Null or empty array means no tags added
```php
'tags_to_add' => null   // No tags added
'tags_to_add' => []     // No tags added
```

## Visual Design

- **Icon:** ✨ (sparkles)
- **Color:** Success green
- **Section:** "Tags to Add on Completion"
- **Shows on:** Create, Edit, and Show task pages

## Complete Example: Progression System

```php
// Step 1: Create progression tags
$newbie = Tag::create(['name' => 'Newbie', 'slug' => 'newbie']);
$regular = Tag::create(['name' => 'Regular', 'slug' => 'regular']);
$veteran = Tag::create(['name' => 'Veteran', 'slug' => 'veteran']);
$legend = Tag::create(['name' => 'Legend', 'slug' => 'legend']);

// Step 2: Create progression tasks
$task1 = Task::create([
    'description' => 'First Challenge',
    'tags_to_remove' => [$newbie->id],
    'tags_to_add' => [$regular->id]
]);

$task2 = Task::create([
    'description' => 'Veteran Challenge',
    'tags_to_remove' => [$regular->id],
    'tags_to_add' => [$veteran->id]
]);

$task3 = Task::create([
    'description' => 'Legendary Challenge',
    'tags_to_remove' => [$veteran->id],
    'tags_to_add' => [$legend->id]
]);

// Step 3: Player progresses
$player = Player::find(1);
$player->tags()->attach($newbie->id);  // Start as newbie

// Complete tasks to progress
$task1->removeTagsFromPlayer($player);
$task1->addTagsToPlayer($player);
// Player is now Regular

$task2->removeTagsFromPlayer($player);
$task2->addTagsToPlayer($player);
// Player is now Veteran

$task3->removeTagsFromPlayer($player);
$task3->addTagsToPlayer($player);
// Player is now Legend!
```

## Combining All Tag Features

You can use all three tag features together:

```php
Task::create([
    'description' => 'Ultimate Challenge',
    
    // Regular tags (must have to see task)
    // Set via $task->tags()->attach([...])
    
    // Tags to remove on completion
    'tags_to_remove' => [1, 2],
    
    // Tags to add on completion
    'tags_to_add' => [3, 4],
    
    // Can't have tags (hide from these players)
    'cant_have_tags' => [5, 6]
]);
```

Workflow:
1. Player must have regular tags to see the task
2. Player must NOT have cant_have_tags to see the task
3. On completion: Remove tags_to_remove
4. On completion: Add tags_to_add

## Testing Your Setup

Run the test script:
```bash
php test_tags_to_add.php
```

Or test manually:
```php
// Create test data
$rewardTag = Tag::create(['name' => 'Reward']);
$player = Player::find(1);

$task = Task::create([
    'description' => 'Test task',
    'tags_to_add' => [$rewardTag->id]
]);

// Test adding tags
$addedTags = $task->addTagsToPlayer($player);
var_dump($addedTags);  // Should contain reward tag ID

// Verify player has tag
var_dump($player->tags()->pluck('id')->toArray());
```

## Troubleshooting

**Q: Tags not being added?**
- Check if tags_to_add is an array: `var_dump($task->tags_to_add)`
- Verify tag IDs exist in database
- Make sure you're calling `addTagsToPlayer($player)`

**Q: Duplicate tags being added?**
- The system prevents duplicates automatically
- Check with: `$player->tags()->pluck('id')->toArray()`

**Q: Not seeing tags_to_add in UI?**
- Make sure you ran the migration: `php artisan migrate`
- Clear cache: `php artisan config:clear`
- Check browser console for errors

## Pro Tips

💡 **Use with cant_have_tags for progression:**
```php
// Task only appears after completing previous challenges
$task->cant_have_tags = [$beginnerTag->id];
$task->tags_to_add = [$advancedTag->id];
```

💡 **Create achievement milestones:**
```php
$task10 = Task::create([
    'description' => 'Complete 10 challenges',
    'tags_to_add' => [$milestone10Tag->id]
]);
```

💡 **Temporary buffs/debuffs:**
```php
$powerUpTask = Task::create([
    'description' => 'Gain power-up',
    'tags_to_add' => [$powerUpTag->id]
]);

$cooldownTask = Task::create([
    'description' => 'Power-up expired',
    'tags_to_remove' => [$powerUpTag->id]
]);
```

## Need More Info?

- **Full Documentation:** See `TAGS_TO_ADD.md` (when created)
- **API Reference:** Check API documentation
- **Test Script:** Run `test_tags_to_add.php`
- **Related Features:** `CANT_HAVE_TAGS.md`, `TAG_REMOVAL_README.md`
