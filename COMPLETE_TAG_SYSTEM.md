# Complete Tag System Overview

## Introduction

The YouDare application features a comprehensive four-part tagging system that provides sophisticated content filtering, progression mechanics, achievement systems, and player customization. This document provides a complete overview of all tag features working together.

## The Four Tag Systems

### 1. Regular Tags (Positive Filtering)
**Purpose:** Control which tasks players can see

**How it works:** Players must have at least one matching tag to see a task.

**Example:**
```php
$task = Task::create(['description' => 'Party challenge']);
$task->tags()->attach($partyModeTag->id);

// Only players with "Party Mode" tag can see this task
```

**Use cases:**
- Content categorization
- Age-appropriate filtering
- Theme-based filtering
- Interest-based content delivery

### 2. Tags to Remove (Completion Actions)
**Purpose:** Remove tags from players when they complete a task

**How it works:** Specified tags are detached from player on task completion.

**Example:**
```php
$task = Task::create([
    'description' => 'Graduate from beginner',
    'tags_to_remove' => [$beginnerTag->id]
]);

// Removes "Beginner" tag when completed
```

**Use cases:**
- One-time content (remove after viewing)
- Progression (remove old rank)
- Temporary status removal
- Tutorial completion

### 3. Can't Have Tags (Negative Filtering)
**Purpose:** Hide tasks from players with specific tags

**How it works:** Players with ANY of these tags cannot see the task.

**Example:**
```php
$task = Task::create([
    'description' => 'Advanced challenge',
    'cant_have_tags' => [$beginnerTag->id]
]);

// Hidden from players with "Beginner" tag
```

**Use cases:**
- Exclusive content
- Progressive unlocking
- Content restrictions
- Skill-gated challenges

### 4. Tags to Add (Rewards & Progression)
**Purpose:** Add tags to players when they complete a task

**How it works:** Specified tags are attached to player on task completion.

**Example:**
```php
$task = Task::create([
    'description' => 'Earn veteran badge',
    'tags_to_add' => [$veteranTag->id]
]);

// Adds "Veteran" tag when completed
```

**Use cases:**
- Achievement badges
- Rank progression
- Content unlocking
- Milestone tracking

## Visual Reference

### UI Icons & Colors

| Feature | Icon | Color | Section Name |
|---------|------|-------|--------------|
| Regular Tags | 🏷️ | Primary (Blue) | "Tags" |
| Tags to Remove | 🗑️ | Error (Red) | "Tags to Remove on Completion" |
| Can't Have Tags | 🚫 | Warning (Yellow) | "Can't Have Tags (Negative Filter)" |
| Tags to Add | ✨ | Success (Green) | "Tags to Add on Completion" |

## Complete System Workflow

### Task Visibility Flow

```
1. Player requests available tasks
   ↓
2. Filter: Does player have at least one regular tag?
   - No → Task hidden
   - Yes → Continue
   ↓
3. Filter: Does player have any cant_have_tags?
   - Yes → Task hidden
   - No → Task visible
   ↓
4. Task shown to player
```

### Task Completion Flow

```
1. Player completes task
   ↓
2. Remove tags_to_remove from player
   ↓
3. Add tags_to_add to player
   ↓
4. Player's tags updated
   ↓
5. Available tasks automatically recalculated
```

## Combining All Features

### Example 1: Simple Progression System

```php
// Beginner → Veteran progression
$promotionTask = Task::create([
    'description' => 'Complete promotion challenge',
    'tags_to_remove' => [$beginnerTag->id],    // Remove old rank
    'tags_to_add' => [$veteranTag->id]         // Add new rank
]);
$promotionTask->tags()->attach($beginnerTag->id);  // Only beginners see it

// Workflow:
// 1. Beginner player sees task (has beginner tag)
// 2. Completes task
// 3. Loses "Beginner" tag
// 4. Gains "Veteran" tag
// 5. Now sees veteran-level tasks
```

### Example 2: Unlockable Content

```php
// Unlock challenge
$unlockTask = Task::create([
    'description' => 'Unlock advanced content',
    'tags_to_add' => [$advancedUnlockedTag->id]
]);
$unlockTask->tags()->attach($anyPlayerTag->id);

// Advanced content (only after unlock)
$advancedTask = Task::create([
    'description' => 'Advanced challenge',
    'cant_have_tags' => []  // No restrictions
]);
$advancedTask->tags()->attach($advancedUnlockedTag->id);  // Requires unlock

// Workflow:
// 1. Player completes unlock task
// 2. Receives "Advanced Unlocked" tag
// 3. Can now see tasks tagged with "Advanced Unlocked"
```

### Example 3: Complete Feature Showcase

```php
$masterTask = Task::create([
    'description' => 'Master Challenge',
    
    // Must have intermediate rank to see this
    // Set via: $task->tags()->attach($intermediateTag->id)
    
    'tags_to_remove' => [$intermediateTag->id],  // Remove intermediate rank
    'tags_to_add' => [$masterTag->id],           // Add master rank
    'cant_have_tags' => [$beginnerTag->id]       // No beginners allowed
]);

// Workflow:
// 1. Only intermediate players see it (has intermediate tag)
// 2. Beginners can't see it (cant_have_tags filter)
// 3. On completion: loses intermediate, gains master
// 4. Now sees master-level content
```

## Common Patterns

### Pattern 1: Linear Progression

```php
// Create ranks
$ranks = [
    Tag::create(['name' => 'Newbie']),
    Tag::create(['name' => 'Regular']),
    Tag::create(['name' => 'Veteran']),
    Tag::create(['name' => 'Legend'])
];

// Create progression tasks
for ($i = 0; $i < count($ranks) - 1; $i++) {
    $task = Task::create([
        'description' => "Advance to {$ranks[$i+1]->name}",
        'tags_to_remove' => [$ranks[$i]->id],
        'tags_to_add' => [$ranks[$i+1]->id]
    ]);
    $task->tags()->attach($ranks[$i]->id);
}
```

### Pattern 2: Achievement System

```php
// Achievement badges
$badges = [
    'first_dare' => Tag::create(['name' => 'First Dare Badge']),
    'brave_soul' => Tag::create(['name' => 'Brave Soul Badge']),
    'social_butterfly' => Tag::create(['name' => 'Social Butterfly'])
];

// Tasks award badges
$firstDareTask = Task::create([
    'description' => 'Complete your first dare',
    'tags_to_add' => [$badges['first_dare']->id]
]);

// Prevent re-earning
$firstDareTask->cant_have_tags = [$badges['first_dare']->id];
```

### Pattern 3: Temporary Status

```php
$powerUpTag = Tag::create(['name' => 'Power Up Active']);

// Gain power-up
$gainTask = Task::create([
    'description' => 'Activate power-up',
    'tags_to_add' => [$powerUpTag->id]
]);

// Use power-up (expires)
$useTask = Task::create([
    'description' => 'Use your power-up',
    'tags_to_remove' => [$powerUpTag->id]
]);
$useTask->tags()->attach($powerUpTag->id);  // Only if active
```

### Pattern 4: Exclusive Groups

```php
$teamA = Tag::create(['name' => 'Team A']);
$teamB = Tag::create(['name' => 'Team B']);

// Team A exclusive
$teamATask = Task::create([
    'description' => 'Team A mission',
    'cant_have_tags' => [$teamB->id]
]);
$teamATask->tags()->attach($teamA->id);

// Team B exclusive
$teamBTask = Task::create([
    'description' => 'Team B mission',
    'cant_have_tags' => [$teamA->id]
]);
$teamBTask->tags()->attach($teamB->id);
```

## Database Schema

### Tasks Table

```sql
CREATE TABLE tasks (
    id INTEGER PRIMARY KEY,
    type VARCHAR(255),              -- 'truth' or 'dare'
    description TEXT,
    spice_rating INTEGER,
    draft BOOLEAN,
    tags_to_remove JSON,           -- [1, 2, 3] - IDs to remove
    cant_have_tags JSON,           -- [4, 5] - Player can't have these
    tags_to_add JSON,              -- [6, 7] - IDs to add on completion
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Relationships

```sql
-- Regular tags (many-to-many)
task_tag (task_id, tag_id)

-- Player tags (many-to-many)
player_tag (player_id, tag_id)
```

## API Usage

### Creating a Task with All Features

```bash
POST /api/tasks
Content-Type: application/json

{
  "type": "dare",
  "description": "Complete master challenge",
  "spice_rating": 4,
  "tags": [3],                    // Regular tags
  "tags_to_remove": [1, 2],       // Remove on completion
  "tags_to_add": [4, 5],          // Add on completion
  "cant_have_tags": [6, 7]        // Hide from these players
}
```

### Response Format

```json
{
  "id": 1,
  "type": "dare",
  "description": "Complete master challenge",
  "spice_rating": 4,
  "tags": [
    {"id": 3, "name": "Intermediate", "slug": "intermediate"}
  ],
  "tags_to_remove": [1, 2],
  "tags_to_add": [4, 5],
  "cant_have_tags": [6, 7],
  "draft": false
}
```

## Model Methods

### Task Model

```php
// Regular tags
$task->tags()                           // Relationship
$task->tags()->attach([1, 2, 3])       // Add tags
$task->tags()->sync([1, 2, 3])         // Replace all tags

// Tags to remove
$task->getRemovableTags()              // Get Tag models
$task->removeTagsFromPlayer($player)   // Execute removal

// Can't have tags
$task->getCantHaveTags()               // Get Tag models
$task->isAvailableForPlayer($player)   // Check if player can see

// Tags to add
$task->getAddableTags()                // Get Tag models
$task->addTagsToPlayer($player)        // Execute addition
```

### Player Model

```php
// Get available tasks (with all filters applied)
$player->getAvailableTasks()

// Get player tags
$player->tags()
$player->tags()->pluck('id')->toArray()
```

## Testing

### Test Scripts

```bash
# Test regular tag filtering
php test_tag_filtering.php

# Test tags to remove
php test_tag_removal_fix.php

# Test can't have tags
php test_cant_have_tags.php

# Test tags to add
php test_tags_to_add.php
```

### Manual Testing Workflow

```php
// 1. Create test tags
$beginner = Tag::create(['name' => 'Beginner']);
$veteran = Tag::create(['name' => 'Veteran']);

// 2. Create test player
$player = Player::create(['name' => 'Test Player']);
$player->tags()->attach($beginner->id);

// 3. Create progression task
$task = Task::create([
    'description' => 'Promotion test',
    'tags_to_remove' => [$beginner->id],
    'tags_to_add' => [$veteran->id]
]);
$task->tags()->attach($beginner->id);

// 4. Verify visibility
$availableTasks = $player->getAvailableTasks();
// Should contain the task

// 5. Complete task
$task->removeTagsFromPlayer($player);
$task->addTagsToPlayer($player);

// 6. Verify tag changes
$playerTags = $player->tags()->pluck('name')->toArray();
// Should be ['Veteran']

// 7. Verify task no longer visible
$availableTasks = $player->fresh()->getAvailableTasks();
// Should NOT contain the task (player no longer has beginner tag)
```

## Documentation Reference

### Quick Start Guides
- `TAGS_QUICKSTART.md` - Regular tags
- `TAG_REMOVAL_QUICKSTART.md` - Tags to remove
- `CANT_HAVE_TAGS_QUICKSTART.md` - Can't have tags
- `TAGS_TO_ADD_QUICKSTART.md` - Tags to add

### Complete Documentation
- `TAGGING_SYSTEM.md` - Regular tags full docs
- `TAG_REMOVAL_README.md` - Tags to remove full docs
- `CANT_HAVE_TAGS.md` - Can't have tags full docs
- `COMPLETE_TAG_SYSTEM.md` - This document

### Implementation Summaries
- `CANT_HAVE_TAGS_SUMMARY.md` - Can't have tags implementation
- `TAGS_TO_ADD_SUMMARY.md` - Tags to add implementation

## Best Practices

### 1. Tag Naming Conventions

```php
// Use clear, descriptive names
✓ Tag::create(['name' => 'Beginner', 'slug' => 'beginner'])
✓ Tag::create(['name' => 'First Dare Badge', 'slug' => 'first-dare-badge'])

// Avoid generic names
✗ Tag::create(['name' => 'Tag1', 'slug' => 'tag1'])
```

### 2. Progression Chains

```php
// Clear progression path
$ranks = ['Newbie', 'Regular', 'Veteran', 'Legend'];

// Each rank task removes previous, adds next
// Makes progression obvious to players
```

### 3. Achievement Design

```php
// Prevent re-earning achievements
$achievementTask->cant_have_tags = [$achievementBadgeTag->id];

// This ensures players can only earn it once
```

### 4. Content Gating

```php
// Use cant_have_tags for skill gates
$advancedTask->cant_have_tags = [$beginnerTag->id];

// Clear indication of skill requirements
```

### 5. Testing Strategy

```php
// Always test the complete flow
1. Create tags
2. Create player with starting tags
3. Verify task visibility
4. Complete task
5. Verify tag changes
6. Verify new task visibility
```

## Troubleshooting

### Issue: Task not visible to player

**Check:**
1. Does player have at least one regular tag matching the task?
2. Does player have any of the task's cant_have_tags?
3. Is the task published (draft = false)?
4. Is the task within the game's max_spice_rating?

**Debug:**
```php
$task = Task::find(1);
$player = Player::find(1);

// Check regular tags
$taskTags = $task->tags()->pluck('id')->toArray();
$playerTags = $player->tags()->pluck('id')->toArray();
$hasMatch = count(array_intersect($taskTags, $playerTags)) > 0;

// Check cant_have_tags
$available = $task->isAvailableForPlayer($player);

// Check draft status
$isPublished = !$task->draft;
```

### Issue: Tags not added/removed on completion

**Check:**
1. Are you calling the methods? `addTagsToPlayer()` / `removeTagsFromPlayer()`
2. Is the field properly formatted as JSON array?
3. Do the tag IDs exist in the database?

**Debug:**
```php
// Check field format
var_dump($task->tags_to_add);      // Should be array
var_dump($task->tags_to_remove);   // Should be array

// Manually test
$added = $task->addTagsToPlayer($player);
$removed = $task->removeTagsFromPlayer($player);

// Check player tags
$playerTags = $player->tags()->pluck('id')->toArray();
```

### Issue: Duplicate tags being added

**Solution:** The system prevents this automatically. If you see duplicates:

```php
// Check for manual direct insertions
// Use the model methods instead:
✓ $task->addTagsToPlayer($player);

// Don't do this:
✗ $player->tags()->attach($tagId);  // Can create duplicates
```

## Performance Considerations

### Caching Strategy

```php
// Cache player's available tasks
$cacheKey = "player_{$player->id}_tasks";
$tasks = Cache::remember($cacheKey, 3600, function() use ($player) {
    return $player->getAvailableTasks();
});

// Invalidate cache on tag changes
$player->tags()->attach($tagId);
Cache::forget("player_{$player->id}_tasks");
```

### Query Optimization

```php
// Eager load tags
$tasks = Task::with('tags')->get();

// Batch operations
$tagIds = [1, 2, 3, 4, 5];
$player->tags()->sync($tagIds);  // More efficient than loop
```

## Conclusion

The complete tag system provides a powerful, flexible framework for:

- ✅ Content filtering and personalization
- ✅ Achievement and badge systems
- ✅ Progressive difficulty and unlocking
- ✅ Player status and rank management
- ✅ Skill-based content gating
- ✅ Temporary buffs/debuffs
- ✅ Team and group mechanics
- ✅ One-time content delivery

All four systems work together seamlessly to create dynamic, engaging gameplay experiences that adapt to each player's journey.