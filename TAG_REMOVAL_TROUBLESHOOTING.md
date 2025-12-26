# Tag Removal Troubleshooting Guide

## Quick Diagnostic Checklist

If tags are not being removed from players during gameplay, check these items:

### ✅ 1. Is the Task Configured Correctly?

```php
// Check the task has tags_to_remove field set
$task = Task::find($taskId);
var_dump($task->tags_to_remove); // Should be an array like [1, 2, 3]
```

**Common Issues:**
- ❌ `tags_to_remove` is `null` or empty array
- ❌ `tags_to_remove` contains invalid tag IDs
- ❌ Task is not published (`draft` is `true`)

**Solution:**
```php
$task->update([
    'tags_to_remove' => [$tagId1, $tagId2],
    'draft' => false,
]);
```

### ✅ 2. Does the Player Actually Have the Tag?

```php
// Check if player has the tag before removal
$player = Player::find($playerId);
$hasTag = $player->tags()->where('tag_id', $tagId)->exists();
echo "Player has tag: " . ($hasTag ? "YES" : "NO");
```

**Common Issues:**
- ❌ Tag was never attached to the player
- ❌ Tag was already removed in a previous task
- ❌ Wrong player or tag ID

**Solution:**
```php
// Attach tag to player if missing
$player->tags()->syncWithoutDetaching([$tagId]);
```

### ✅ 3. Is the Frontend Calling the Correct Endpoint?

**Check browser Network tab:**

❌ **WRONG:** `POST /api/players/{id}/score`
✅ **CORRECT:** `POST /api/players/{id}/complete-task`

**Request Body Should Include:**
```json
{
  "task_id": 5,
  "points": 1
}
```

**Common Issues:**
- ❌ Frontend calling wrong endpoint
- ❌ Missing `task_id` in request body
- ❌ CSRF token missing or invalid

**Solution:**
Edit `resources/js/components/GamePlay.vue`:
```javascript
const response = await fetch(
    `/api/players/${this.currentPlayerId}/complete-task`,
    {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
            task_id: this.currentTask.id,
            points: 1,
        }),
    }
);
```

### ✅ 4. Is the API Endpoint Returning Success?

**Check the API response:**

Expected response:
```json
{
  "success": true,
  "message": "Task completed successfully and removed 1 tag(s)",
  "data": {
    "player": { "id": 1, "tags": [...] },
    "removed_tags_count": 1,
    "removed_tags": [{ "id": 1, "name": "Rookie" }]
  }
}
```

**Common Issues:**
- ❌ `removed_tags_count` is 0 (tag wasn't removed)
- ❌ Response shows error message
- ❌ 404 or 422 status code

**Debug:**
```php
// Test the API endpoint directly
$task = Task::find($taskId);
$player = Player::find($playerId);
$removedTags = $task->removeTagsFromPlayer($player);
dd($removedTags); // Should show array of removed tag IDs
```

### ✅ 5. Is the Frontend Updating Player State?

**Check browser console:**

Should see:
```
Removed 1 tag(s) from player
```

**Common Issues:**
- ❌ Player object not updated after API call
- ❌ Tags not refreshed in UI
- ❌ JavaScript errors in console

**Solution:**
```javascript
if (data.success && data.data.player) {
    player.score = data.data.player.score;
    player.tags = data.data.player.tags; // Update tags!
}
```

## Quick Test Script

Run this to verify tag removal is working:

```bash
php debug_tag_removal.php
```

Expected output:
```
✅ SUCCESS: Tag was removed successfully!
   - removeTagsFromPlayer returned: [1]
   - Player now has 0 tags
   - Pivot table is clean
```

## Common Error Messages

### "Method 'removeTagsFromPlayer' not found"

**Cause:** Using old version of Task model

**Solution:** Verify `app/Models/Task.php` has the method:
```php
public function removeTagsFromPlayer(Player $player) { ... }
```

### "Task completed successfully" but removed_tags_count is 0

**Cause:** Player doesn't have the tag that task tries to remove

**Check:**
```php
$player->tags()->pluck('id'); // Current tags on player
$task->tags_to_remove;        // Tags task tries to remove
```

**Solution:** Make sure the tag IDs match

### "CSRF token mismatch"

**Cause:** Missing or invalid CSRF token in API request

**Solution:**
```javascript
"X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
```

### Tasks disappear after removing tag

**Cause:** Old version of `getAvailableTasks()` method

**Solution:** Verify `app/Models/Game.php` and `app/Models/Player.php` have the updated `getAvailableTasks()` method that includes tasks with `tags_to_remove`

## Verification Steps

### Step 1: Backend Test
```bash
php debug_tag_removal.php
```
Should show: ✅ SUCCESS

### Step 2: Database Check
```sql
-- Check player_tag pivot table
SELECT * FROM player_tag WHERE player_id = 1;

-- Before completion: should have the tag
-- After completion: tag should be removed
```

### Step 3: API Test
```bash
curl -X POST http://localhost/api/players/1/complete-task \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: <token>" \
  -d '{"task_id": 5, "points": 1}'
```

Should return `removed_tags_count > 0`

### Step 4: Frontend Test

1. Open browser DevTools (F12)
2. Go to Network tab
3. Complete a task in the game
4. Check the request to `/complete-task`
5. Verify response has `removed_tags` array
6. Check console for "Removed X tag(s) from player"

## Still Not Working?

### Full Reset Test

1. Create a fresh game:
```php
$game = Game::create(['code' => 'TEST123', 'max_spice_rating' => 5]);
```

2. Create a test tag:
```php
$tag = Tag::create(['name' => 'Test Tag', 'slug' => 'test-tag']);
```

3. Create a task that removes it:
```php
$task = Task::create([
    'type' => 'truth',
    'spice_rating' => 1,
    'description' => 'Test task',
    'tags_to_remove' => [$tag->id],
    'draft' => false,
]);
$task->tags()->attach([$tag->id]);
```

4. Create a player with the tag:
```php
$player = $game->players()->create(['name' => 'Test Player']);
$player->tags()->attach([$tag->id]);
```

5. Test removal:
```php
$removed = $task->removeTagsFromPlayer($player);
var_dump(count($removed)); // Should be 1
$player->refresh();
var_dump($player->tags->count()); // Should be 0
```

If this fails, there's a deeper issue with your Laravel installation or database.

## Need More Help?

Run the comprehensive test suite:
```bash
php test_e2e_tag_removal.php
```

This will test the entire flow and show exactly where it fails.

Check the detailed documentation:
- `TAG_REMOVAL_FIX.md` - Technical details
- `FIX_SUMMARY.md` - Overview of the fix
- `example_tag_removal_usage.php` - Working example