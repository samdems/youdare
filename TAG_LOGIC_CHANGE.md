# Tag Logic Change: OR to AND

## Overview

Changed the tag filtering logic from **OR** (at least one) to **AND** (all required) for both task selection and {{someone}} player selection.

## What Changed

### 1. Task Selection for Players

**Previous Behavior (OR Logic):**
- A task with tags ["Adults Only", "Couples"] would show for players who had EITHER tag
- Player with just ["Adults Only"] → Task shows ✓
- Player with just ["Couples"] → Task shows ✓
- Player with both ["Adults Only", "Couples"] → Task shows ✓

**New Behavior (AND Logic):**
- A task with tags ["Adults Only", "Couples"] now ONLY shows for players who have BOTH tags
- Player with just ["Adults Only"] → Task does NOT show ✗
- Player with just ["Couples"] → Task does NOT show ✗
- Player with both ["Adults Only", "Couples"] → Task shows ✓

### 2. {{someone}} Player Selection

**Previous Behavior (OR Logic):**
- `someone_tags` with ["Male", "Single"] would match players with EITHER tag
- Player with just ["Male"] → Can be selected ✓
- Player with just ["Single"] → Can be selected ✓
- Player with both ["Male", "Single"] → Can be selected ✓

**New Behavior (AND Logic):**
- `someone_tags` with ["Male", "Single"] now ONLY matches players with BOTH tags
- Player with just ["Male"] → Cannot be selected ✗
- Player with just ["Single"] → Cannot be selected ✗
- Player with both ["Male", "Single"] → Can be selected ✓

## Code Changes

### File: `app/Models/Player.php`

**Changed:** `getAvailableTasks()` method

```php
// OLD: OR logic - player needs ANY tag
$q->orWhereHas("tags", function ($tagQuery) use ($tagIds) {
    $tagQuery->whereIn("tags.id", $tagIds);
});

// NEW: AND logic - player needs ALL tags
$taskTagIds = $task->tags->pluck("id")->toArray();
$hasAllTags = count(array_diff($taskTagIds, $tagIds)) === 0;
```

**Logic:** Uses `array_diff()` to check if any task tags are missing from player's tags. If the difference is empty, player has all required tags.

### File: `resources/js/stores/playerStore.js`

**Changed:** `processTaskDescription()` function - {{someone}} filtering

```javascript
// OLD: OR logic - player needs ANY tag
return task.someone_tags.some((tagId) =>
    playerTagIds.includes(tagId),
);

// NEW: AND logic - player needs ALL tags
return task.someone_tags.every((tagId) =>
    playerTagIds.includes(tagId),
);
```

**Logic:** Changed from `.some()` (at least one) to `.every()` (all).

### UI Updates

Updated descriptions in:
- `resources/views/tasks/create.blade.php`
- `resources/views/tasks/edit.blade.php`
- `resources/views/tasks/show.blade.php`

**Old text:** "The player must have at least one of these tags"
**New text:** "The player must have ALL of these tags"

## Use Cases

### Example 1: Age + Relationship Status
**Task:** "Kiss your partner"
**Tags:** ["Adults Only", "Couples"]

- **Before:** Any adult OR anyone in a couple could get this task (inappropriate for singles)
- **After:** ONLY adults who are in couples get this task ✓

### Example 2: Gender + Clothing
**Task:** "Remove your bra"
**Tags:** ["Female", "Bra"]

- **Before:** Any female OR anyone wearing a bra could get this (might show for someone not wearing one)
- **After:** ONLY females who have the "Bra" tag (wearing one) get this task ✓

### Example 3: {{someone}} Selection
**Task:** "{{someone}} must give you a lap dance"
**someone_tags:** ["Female", "Bold"]

- **Before:** Would select any female OR anyone bold (might select a shy female)
- **After:** ONLY selects females who are bold ✓

## Impact on Existing Tasks

### Tasks with Single Tag
**No Change** - Tasks with only one tag work exactly the same

### Tasks with Multiple Tags
**Behavior Change** - Now requires ALL tags instead of ANY tag

### Recommendation
Review existing multi-tag tasks to ensure they still work as intended. You may need to:
1. Remove some tags from tasks that were using "broad" OR matching
2. Add more specific tags to players
3. Create new tasks for different tag combinations

## Benefits

1. **More Precise Control** - Tasks appear only for the exact player types intended
2. **Prevents Edge Cases** - Reduces unexpected task appearances
3. **Better Gameplay** - More targeted and appropriate task distribution
4. **Clearer Intent** - Tag combinations clearly define exact requirements
5. **Flexible Filtering** - Can still achieve "OR" behavior by creating multiple tasks

## Technical Notes

- Tasks with **no tags** still show for all players (unchanged)
- The `cant_have_tags` filter still uses OR logic (if player has ANY of these, task is excluded)
- Empty tag arrays are treated as "no restriction" (all players eligible)
- Gender filter works independently and is applied after tag filtering

## Migration Notes

**No Database Migration Required** - This is a logic change only.

**What You Need to Do:**
1. Review multi-tag tasks in your database
2. Verify they still match intended players
3. Update task tags if needed
4. Test gameplay with new logic

## Testing Checklist

- [ ] Single-tag tasks still work correctly
- [ ] Multi-tag tasks only show for players with ALL tags
- [ ] {{someone}} with multiple tags only selects players with ALL tags
- [ ] Tasks with no tags still show for everyone
- [ ] cant_have_tags still excludes properly
- [ ] Gender filter works with tag filters
- [ ] Empty tag selections work (no restrictions)