# Tags Quick Start Guide

This guide will get you up and running with the tagging system in 5 minutes.

## What Are Tags?

Tags are categories that filter which tasks users see. Tasks are **only visible to users who have matching tags**.

- User has "Family Friendly" tag → Sees tasks tagged "Family Friendly"
- User has "Adults Only" tag → Sees tasks tagged "Adults Only"
- User has NO tags → Sees only tasks with NO tags

## Quick Setup

### 1. Migrate and Seed

```bash
php artisan migrate
php artisan db:seed --class=TagSeeder
```

This creates 10 default tags:
- Adults Only
- Family Friendly
- Party Mode
- Romantic
- Extreme
- Funny
- Physical
- Mental
- Creative
- Social

### 2. Assign Tags to a User

**Via Code:**
```php
$user = User::find(1);
$familyTag = Tag::where('slug', 'family-friendly')->first();
$user->tags()->attach($familyTag->id);
```

**Via API:**
```bash
curl -X POST http://localhost/api/tags/1/attach \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 3. Create Tasks with Tags

**Via Code:**
```php
$task = Task::create([
    'type' => 'dare',
    'spice_rating' => 2,
    'description' => 'Do a silly dance',
    'draft' => false
]);
$task->tags()->attach([1, 6]); // Family Friendly + Funny
```

**Via API:**
```bash
curl -X POST http://localhost/api/tasks \
  -H "Content-Type: application/json" \
  -d '{
    "type": "dare",
    "spice_rating": 2,
    "description": "Do a silly dance",
    "tags": [1, 6]
  }'
```

## How Filtering Works

### Scenario 1: User with Tags
```
User has tags: [1, 6]  (Family Friendly, Funny)
Task has tags: [1]     (Family Friendly)
Result: ✅ User CAN see this task (1 matches 1)
```

### Scenario 2: No Match
```
User has tags: [1]     (Family Friendly)
Task has tags: [2]     (Adults Only)
Result: ❌ User CANNOT see this task (no match)
```

### Scenario 3: User with No Tags
```
User has tags: []
Task has tags: []
Result: ✅ User CAN see this task (both empty)
```

### Scenario 4: Multiple Tags
```
User has tags: [1, 3, 6]  (Family, Party, Funny)
Task has tags: [3, 5]     (Party, Extreme)
Result: ✅ User CAN see this task (3 matches 3)
```

## API Endpoints Cheat Sheet

### Get All Tags
```
GET /api/tags
```

### Get User's Tags (requires auth)
```
GET /api/tags/user
Authorization: Bearer TOKEN
```

### Add Tag to User (requires auth)
```
POST /api/tags/{tagId}/attach
Authorization: Bearer TOKEN
```

### Remove Tag from User (requires auth)
```
DELETE /api/tags/{tagId}/detach
Authorization: Bearer TOKEN
```

### Replace All User Tags (requires auth)
```
POST /api/tags/user/sync
Authorization: Bearer TOKEN
Content-Type: application/json

{
  "tag_ids": [1, 3, 6]
}
```

### Get Filtered Tasks (automatic based on auth)
```
GET /api/tasks
Authorization: Bearer TOKEN  (optional for guests)
```

### Get Random Task (filtered)
```
GET /api/tasks/random?type=dare&max_spice=3
Authorization: Bearer TOKEN  (optional for guests)
```

### Create Task with Tags
```
POST /api/tasks
Content-Type: application/json

{
  "type": "dare",
  "spice_rating": 3,
  "description": "Task description",
  "tags": [1, 6]
}
```

## Common Use Cases

### Use Case 1: Family App
- Create "Family Friendly" tag
- All users get this tag by default
- All content is tagged "Family Friendly"
- Result: Everyone sees everything (safe content)

### Use Case 2: Adult Party Game
- Create "Adults Only" and "Party Mode" tags
- Users select their preferences during onboarding
- Content is tagged appropriately
- Result: Adults see spicy content, families see clean content

### Use Case 3: Niche Communities
- Create specific tags: "Fitness", "Mental Health", "Dating"
- Users join communities by selecting tags
- Content is categorized
- Result: Users see content relevant to their interests

## Testing the System

Run the test suite:
```bash
php test_tag_filtering.php
```

This runs 28 tests to verify:
- Users see correct tasks based on their tags
- Users don't see tasks without matching tags
- Untagged users only see untagged tasks
- Helper methods work correctly

## User Helper Methods

```php
// Check if user has a tag
$user->hasTag(1);              // By ID
$user->hasTag('family-friendly'); // By slug

// Check for any matching tag
$user->hasAnyTag([1, 2, 3]);

// Check for all tags
$user->hasAllTags([1, 2, 3]);

// Get user's tags
$user->tags;
```

## Task Query Scopes

```php
// Tasks with any of these tags
Task::withTags([1, 2, 3])->get();

// Tasks with all of these tags
Task::withAllTags([1, 2, 3])->get();

// Tasks without tags
Task::whereDoesntHave('tags')->get();
```

## Pro Tips

1. **Default Tags**: Set default tags for new users in your registration flow
2. **Tag Onboarding**: Show tag selection during first login
3. **Universal Content**: Keep some tasks untagged for new users
4. **Tag Balance**: Ensure popular tags have enough content
5. **Test Coverage**: Test with users having 0, 1, and multiple tags

## Troubleshooting

### "I don't see any tasks"
- Check your tags: `GET /api/tags/user`
- If you have no tags, you'll only see untagged tasks
- If you have tags, you'll only see tasks with matching tags

### "I see tasks I shouldn't see"
- Verify task tags: `GET /api/tasks/{taskId}`
- Check your tags match the task tags
- Ensure filtering is enabled (check auth)

### "API returns empty results"
- Guest users only see untagged tasks
- Authenticated users need matching tags
- Check if tasks exist with your tag combination

## Next Steps

- Read full documentation: `TAGGING_SYSTEM.md`
- Run examples: `php example_tag_usage.php`
- Check API examples: `API_EXAMPLES.md`
- Explore routes: `php artisan route:list --path=tags`

## Important Notes

⚠️ **Breaking Change**: Existing tasks without tags will only be visible to users without tags. Assign tags to your existing content!

✅ **Security**: Tag filtering is enforced at the database query level, not just the UI.

🎯 **Performance**: Pivot tables are indexed for fast filtering.

📊 **Analytics**: Use `Tag::withCount(['tasks', 'users'])` to get usage stats.