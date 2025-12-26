# Tagging System Documentation

## Overview

The tagging system allows you to categorize tasks and users with tags. Tasks are only visible to users who have at least one matching tag. This creates a filtered, personalized experience for each user.

## Key Concepts

- **Tags**: Categories that can be applied to both tasks and users (e.g., "Adults Only", "Family Friendly", "Party Mode")
- **Task Tags**: Tasks can have multiple tags. A task with tags will only be shown to users who have at least one matching tag.
- **User Tags**: Users can have multiple tags representing their preferences/interests.
- **Filtering**: Tasks are automatically filtered based on the authenticated user's tags.
- **No Tags**: Users with no tags will only see tasks that have no tags. Tasks with no tags are considered "universal" content.

## Database Schema

### Tags Table
- `id` - Primary key
- `name` - Tag name (unique)
- `slug` - URL-friendly slug (unique, auto-generated)
- `description` - Optional description
- `created_at`, `updated_at` - Timestamps

### Pivot Tables
- `task_tag` - Links tasks to tags (task_id, tag_id)
- `tag_user` - Links users to tags (user_id, tag_id)

## Models

### Tag Model (`App\Models\Tag`)

**Relationships:**
```php
$tag->tasks();  // Get all tasks with this tag
$tag->users();  // Get all users with this tag
```

### Task Model (`App\Models\Task`)

**Relationships:**
```php
$task->tags();  // Get all tags for this task
```

**Scopes:**
```php
Task::withTags([1, 2, 3]);      // Tasks with any of these tags
Task::withAllTags([1, 2, 3]);   // Tasks with all of these tags
```

### User Model (`App\Models\User`)

**Relationships:**
```php
$user->tags();  // Get all tags for this user
```

**Helper Methods:**
```php
$user->hasTag(1);              // Check by ID
$user->hasTag('party-mode');   // Check by slug
$user->hasAnyTag([1, 2, 3]);   // Has at least one tag
$user->hasAllTags([1, 2, 3]);  // Has all tags
```

## Seeded Tags

The system comes with 10 pre-configured tags:

1. **Adults Only** (`adults-only`) - Content suitable for adults only (18+)
2. **Family Friendly** (`family-friendly`) - Content suitable for all ages
3. **Party Mode** (`party-mode`) - Tasks suitable for parties and social gatherings
4. **Romantic** (`romantic`) - Tasks for couples and romantic situations
5. **Extreme** (`extreme`) - Tasks for those who dare to go extreme
6. **Funny** (`funny`) - Humorous and entertaining tasks
7. **Physical** (`physical`) - Tasks that require physical activity
8. **Mental** (`mental`) - Tasks that challenge your mind
9. **Creative** (`creative`) - Tasks that require creativity and imagination
10. **Social** (`social`) - Tasks involving social interaction

## API Endpoints

### Tag Endpoints

#### List All Tags
```
GET /api/tags
```

**Query Parameters:**
- `search` - Search by name or slug
- `with_counts` - Include task and user counts (boolean)
- `sort_by` - Sort field (name, slug, created_at, updated_at)
- `sort_order` - Sort order (asc, desc)
- `per_page` - Items per page (1-100, default: 20)

**Example Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Adults Only",
      "slug": "adults-only",
      "description": "Content suitable for adults only (18+)",
      "created_at": "2025-12-26T15:31:36.000000Z",
      "updated_at": "2025-12-26T15:31:36.000000Z",
      "tasks_count": 15,
      "users_count": 8
    }
  ],
  "meta": { ... },
  "links": { ... }
}
```

#### Get Single Tag
```
GET /api/tags/{tag}
```

**Example Response:**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "name": "Adults Only",
    "slug": "adults-only",
    "description": "Content suitable for adults only (18+)",
    "tasks_count": 15,
    "users_count": 8
  }
}
```

#### Create Tag
```
POST /api/tags
```

**Request Body:**
```json
{
  "name": "New Tag",
  "slug": "new-tag",  // Optional, auto-generated from name
  "description": "Description of the tag"
}
```

#### Update Tag
```
PUT/PATCH /api/tags/{tag}
```

**Request Body:**
```json
{
  "name": "Updated Tag Name",
  "description": "Updated description"
}
```

#### Delete Tag
```
DELETE /api/tags/{tag}
```

### User Tag Management (Requires Authentication)

#### Get User's Tags
```
GET /api/tags/user
Authorization: Bearer {token}
```

**Example Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Adults Only",
      "slug": "adults-only",
      "description": "Content suitable for adults only (18+)"
    }
  ]
}
```

#### Attach Tag to User
```
POST /api/tags/{tag}/attach
Authorization: Bearer {token}
```

**Example Response:**
```json
{
  "status": "success",
  "message": "Tag added to your profile",
  "data": { ... }
}
```

#### Detach Tag from User
```
DELETE /api/tags/{tag}/detach
Authorization: Bearer {token}
```

#### Sync User Tags (Replace all tags)
```
POST /api/tags/user/sync
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "tag_ids": [1, 2, 3]
}
```

### Task Endpoints with Tags

#### List Tasks (Filtered by User Tags)
```
GET /api/tasks
Authorization: Bearer {token}  // Optional for guests
```

**Filtering Logic:**
- Authenticated users with tags: See tasks that have at least one matching tag
- Authenticated users without tags: See only tasks with no tags
- Guest users: See only tasks with no tags

**Query Parameters:**
- `type` - Filter by type (truth, dare)
- `draft` - Filter by draft status (true, false)
- `min_spice` - Minimum spice rating (1-5)
- `max_spice` - Maximum spice rating (1-5)
- All standard pagination parameters

#### Get Random Task (Filtered by User Tags)
```
GET /api/tasks/random
Authorization: Bearer {token}  // Optional for guests
```

**Query Parameters:**
- `type` - Filter by type (truth, dare)
- `max_spice` - Maximum spice rating
- `min_spice` - Minimum spice rating

#### Create Task with Tags
```
POST /api/tasks
```

**Request Body:**
```json
{
  "type": "dare",
  "spice_rating": 3,
  "description": "Do something fun!",
  "draft": false,
  "tags": [1, 2, 3]  // Array of tag IDs
}
```

#### Update Task Tags
```
PUT/PATCH /api/tasks/{task}
```

**Request Body:**
```json
{
  "tags": [1, 2, 3]  // Replaces all tags
}
```

#### Get Task with Tags
```
GET /api/tasks/{task}
```

**Example Response:**
```json
{
  "status": "success",
  "data": {
    "id": 1,
    "type": "dare",
    "spice_rating": 3,
    "description": "Do something fun!",
    "draft": false,
    "tags": [
      {
        "id": 1,
        "name": "Adults Only",
        "slug": "adults-only"
      }
    ],
    "spice_level": "Hot"
  }
}
```

## Web Routes

### Tag Routes
- `GET /tags` - List all tags
- `GET /tags/create` - Show create form
- `POST /tags` - Store new tag
- `GET /tags/{tag}` - Show tag details
- `GET /tags/{tag}/edit` - Show edit form
- `PUT/PATCH /tags/{tag}` - Update tag
- `DELETE /tags/{tag}` - Delete tag

### User Tag Management (Requires Authentication)
- `POST /tags/{tag}/attach` - Add tag to user
- `DELETE /tags/{tag}/detach` - Remove tag from user

### Task Routes (with Tag Filtering)
All task routes automatically filter based on authenticated user's tags.

## Usage Examples

### Example 1: User Signs Up and Selects Preferences

1. User registers/logs in
2. User browses available tags: `GET /api/tags`
3. User adds tags to their profile:
   ```
   POST /api/tags/1/attach  (Adults Only)
   POST /api/tags/3/attach  (Party Mode)
   POST /api/tags/6/attach  (Funny)
   ```
4. User now sees only tasks with these tags when browsing

### Example 2: Creating Content for Specific Audiences

1. Admin creates a new task:
   ```json
   POST /api/tasks
   {
     "type": "dare",
     "spice_rating": 4,
     "description": "Tell your most embarrassing story",
     "draft": false,
     "tags": [1, 3]  // Adults Only, Party Mode
   }
   ```
2. This task is only visible to users who have either "Adults Only" or "Party Mode" tags

### Example 3: Universal Content

1. Create a task without tags:
   ```json
   POST /api/tasks
   {
     "type": "truth",
     "spice_rating": 1,
     "description": "What's your favorite color?",
     "draft": false,
     "tags": []  // No tags
   }
   ```
2. This task is only visible to users who have NO tags (universal content)

### Example 4: Getting Personalized Random Task

```javascript
// Authenticated user with tags [1, 3, 6] requests a random task
fetch('/api/tasks/random?type=dare&max_spice=3', {
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN'
  }
})
// Returns: Random dare with spice ≤ 3 that has at least one tag matching [1, 3, 6]
```

## Best Practices

1. **Tag Strategy**: Create a clear tag taxonomy before launching
2. **User Onboarding**: Prompt users to select tags during registration
3. **Tag Management**: Regularly review and consolidate tags to avoid fragmentation
4. **Content Coverage**: Ensure popular tag combinations have sufficient content
5. **Universal Content**: Keep some tasks untagged for users who haven't set preferences
6. **Tag Descriptions**: Write clear descriptions to help users understand each tag
7. **Testing**: Test tag filtering with various user tag combinations

## Migration and Setup

To set up the tagging system in your database:

```bash
# Run migrations
php artisan migrate

# Seed default tags
php artisan db:seed --class=TagSeeder
```

## Code Examples

### Programmatically Managing Tags in PHP

```php
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;

// Create a tag
$tag = Tag::create([
    'name' => 'New Category',
    'description' => 'Description here'
]);

// Attach tags to a task
$task = Task::find(1);
$task->tags()->attach([1, 2, 3]);

// Sync tags (replace all)
$task->tags()->sync([1, 2]);

// Detach specific tags
$task->tags()->detach([3]);

// Detach all tags
$task->tags()->detach();

// Add tags to a user
$user = User::find(1);
$user->tags()->attach([1, 2, 3]);

// Check if user has a tag
if ($user->hasTag('adults-only')) {
    // User has this tag
}

// Get tasks filtered by user's tags
$userTagIds = $user->tags()->pluck('tags.id')->toArray();
$tasks = Task::withTags($userTagIds)->get();
```

## Troubleshooting

### Tasks not appearing for users

1. Check if user has tags: `GET /api/tags/user`
2. Check if task has tags: `GET /api/tasks/{task}`
3. Verify at least one tag matches between user and task
4. If user has no tags, they can only see tasks with no tags

### User sees all tasks

1. Check if tag filtering is bypassed (e.g., admin role)
2. Verify authentication is working properly
3. Check if middleware is applied correctly

### Tags not syncing

1. Ensure tag IDs exist in database
2. Check validation errors in response
3. Verify authentication token is valid

## Security Considerations

1. **Tag Creation**: Restrict tag creation to admin users in production
2. **Tag Assignment**: Users can manage their own tags
3. **Content Filtering**: Tag filtering is enforced at query level
4. **API Authentication**: Use Sanctum tokens for authenticated endpoints
5. **Validation**: All tag operations validate tag existence and user permissions

## Performance Tips

1. Use eager loading when fetching tasks with tags: `Task::with('tags')->get()`
2. Cache frequently accessed tags
3. Index foreign keys in pivot tables (already configured)
4. Consider adding a unique constraint on pivot tables to prevent duplicates (already configured)
5. Use `withCount()` sparingly as it adds subqueries

## Future Enhancements

- Tag hierarchies/categories
- Tag popularity metrics
- User tag recommendations
- Automatic tag suggestions for tasks
- Tag-based analytics and reporting
- Tag weight/importance scoring
- Tag synonyms and aliases