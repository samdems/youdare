# Simplified Tagging System Documentation

## Overview

The tagging system provides a way to **categorize and organize tasks** into different categories. Tags are simple labels that help users browse and filter content by topic, difficulty, or theme.

## Key Concepts

- **Tags**: Categories for organizing tasks (e.g., "Family Friendly", "Physical", "Funny")
- **Task Tags**: Tasks can have multiple tags to indicate what category they belong to
- **Organization**: Tags help organize large numbers of tasks into manageable categories
- **No User Filtering**: Tags are purely organizational - all users see all tasks regardless of tags

## What's Different from Original Design

This is a **simplified version** that removes user-based filtering:

- ❌ Users don't have tags
- ❌ No personalized content filtering
- ❌ No user tag preferences
- ✅ Tags are purely for organization
- ✅ All users see all tasks
- ✅ Simpler to understand and use

## How It Works

### 1. Tags Exist as Categories
```
Tags Table:
- Family Friendly
- Adults Only
- Party Mode
- Physical
- Mental
- Creative
```

### 2. Tasks Can Be Tagged
```
Task: "Do 20 jumping jacks"
Tags: Physical, Family Friendly

Task: "Tell an embarrassing story"
Tags: Adults Only, Party Mode
```

### 3. Browse by Category
Users can:
- View all tasks
- Filter by specific tags
- See which tags a task has
- Navigate to tag pages to see all tasks in that category

## Database Structure

### Tables
- `tags` - Stores tag information
- `task_tag` - Links tasks to tags (many-to-many)

### Relationships
```
Tag → has many Tasks
Task → has many Tags
```

## Default Tags (10 seeded)

1. **Adults Only** - Content for 18+
2. **Family Friendly** - All ages content
3. **Party Mode** - Social gathering tasks
4. **Romantic** - Couples content
5. **Extreme** - High intensity tasks
6. **Funny** - Humorous content
7. **Physical** - Physical activity tasks
8. **Mental** - Mind challenges
9. **Creative** - Imagination tasks
10. **Social** - Social interaction

## API Endpoints

### Tag Management
```
GET    /api/tags              - List all tags
POST   /api/tags              - Create new tag
GET    /api/tags/{id}         - Get single tag
PUT    /api/tags/{id}         - Update tag
DELETE /api/tags/{id}         - Delete tag
```

### Tasks with Tags
```
GET    /api/tasks             - List all tasks (with tags)
POST   /api/tasks             - Create task with tags
GET    /api/tasks/{id}        - Get task with tags
PUT    /api/tasks/{id}        - Update task tags
```

## Web Routes

### Tag Pages
```
GET    /tags                  - Browse all tags
GET    /tags/create           - Create new tag form
POST   /tags                  - Store new tag
GET    /tags/{id}             - View tag details
GET    /tags/{id}/edit        - Edit tag form
PUT    /tags/{id}             - Update tag
DELETE /tags/{id}             - Delete tag
```

## Usage Examples

### Creating a Tagged Task

**Via Web:**
1. Go to `/tasks/create`
2. Fill in task details
3. Check tags you want (e.g., "Physical", "Funny")
4. Click "Create Task"

**Via API:**
```bash
curl -X POST http://localhost/api/tasks \
  -H "Content-Type: application/json" \
  -d '{
    "type": "dare",
    "spice_rating": 2,
    "description": "Do a silly dance",
    "tags": [6, 7]
  }'
```

**Via Code:**
```php
$task = Task::create([
    'type' => 'dare',
    'spice_rating' => 2,
    'description' => 'Do a silly dance',
    'draft' => false
]);

// Attach tags
$task->tags()->attach([6, 7]); // Funny, Physical
```

### Browsing by Tag

**Via Web:**
1. Visit `/tags` to see all tags
2. Click on a tag (e.g., "Physical")
3. See all tasks with that tag

**Via Code:**
```php
// Get a tag
$physicalTag = Tag::where('slug', 'physical')->first();

// Get all tasks with this tag
$tasks = $physicalTag->tasks()->published()->get();
```

### Managing Tags

**Create a Tag:**
```php
$tag = Tag::create([
    'name' => 'Outdoor Activities',
    'slug' => 'outdoor-activities',
    'description' => 'Tasks that take place outdoors'
]);
```

**Update Task Tags:**
```php
$task = Task::find(1);

// Add tags
$task->tags()->attach([1, 2]);

// Replace all tags
$task->tags()->sync([3, 4, 5]);

// Remove specific tags
$task->tags()->detach([1]);

// Remove all tags
$task->tags()->detach();
```

## Features

### Tag Management Interface
- ✅ Browse all tags in a grid
- ✅ Create new tags with auto-slug generation
- ✅ Edit existing tags
- ✅ Delete tags (removes from all tasks)
- ✅ View tag details with task preview

### Task Interface
- ✅ Select multiple tags when creating tasks
- ✅ Visual checkbox-style tag selection
- ✅ Tags displayed on task cards
- ✅ Tags shown on task detail pages
- ✅ Clickable tag badges

### Organization
- ✅ Tasks can have multiple tags
- ✅ Tags can be applied to multiple tasks
- ✅ Easy navigation between tags and tasks
- ✅ Task counts per tag

## Models

### Tag Model
```php
// Relationships
$tag->tasks(); // Get tasks with this tag

// Example
$tag = Tag::find(1);
$taskCount = $tag->tasks()->count();
```

### Task Model
```php
// Relationships
$task->tags(); // Get all tags for this task

// Scopes
Task::withTags([1, 2, 3]); // Tasks with any of these tags
Task::withAllTags([1, 2, 3]); // Tasks with all of these tags

// Example
$task = Task::find(1);
$tagNames = $task->tags->pluck('name');
```

## Setup

### 1. Run Migrations
```bash
php artisan migrate
```

This creates:
- `tags` table
- `task_tag` pivot table

### 2. Seed Default Tags
```bash
php artisan db:seed --class=TagSeeder
```

This creates 10 default tags.

### 3. Start Using!
- Visit `/tags` to browse tags
- Visit `/tasks/create` to create tagged tasks

## Common Operations

### Get All Tasks in a Category
```php
$tag = Tag::where('slug', 'physical')->first();
$tasks = $tag->tasks()->published()->get();
```

### Get Tasks with Multiple Tags
```php
// Tasks with ANY of these tags
$tasks = Task::withTags([1, 2, 3])->get();

// Tasks with ALL of these tags
$tasks = Task::withAllTags([1, 2, 3])->get();
```

### Get Untagged Tasks
```php
$tasks = Task::doesntHave('tags')->get();
```

### Get Tag Statistics
```php
$tags = Tag::withCount('tasks')
    ->orderBy('tasks_count', 'desc')
    ->get();

foreach ($tags as $tag) {
    echo "{$tag->name}: {$tag->tasks_count} tasks\n";
}
```

## Best Practices

1. **Consistent Naming**: Use clear, descriptive tag names
2. **Not Too Many**: Keep tags focused and meaningful
3. **Avoid Duplicates**: Check if a tag exists before creating similar ones
4. **Use Descriptions**: Help users understand what each tag means
5. **Regular Review**: Periodically review and consolidate tags

## UI Features

### Tags Index Page (`/tags`)
- Grid of all tags
- Shows task count per tag
- Search and pagination
- Create new tag button

### Tag Detail Page (`/tags/{id}`)
- Tag information
- Preview of tagged tasks
- Edit and delete options
- Navigation to all tagged tasks

### Task Forms
- Visual tag selection with checkboxes
- Tag descriptions shown
- Multiple selection support
- Clear "no tags" option

### Task Display
- Tags shown as badges
- Clickable to view tag details
- Hover effects
- Clear indication when task has no tags

## Performance

- ✅ Indexed foreign keys in pivot table
- ✅ Eager loading support: `Task::with('tags')->get()`
- ✅ Efficient queries with scopes
- ✅ Pagination on all list views

## Security

- ✅ Input validation on all endpoints
- ✅ Cascade deletes for data integrity
- ✅ Unique constraints prevent duplicates
- ✅ XSS protection on outputs

## What's NOT Included (vs. Original Design)

This simplified version does **NOT** include:
- ❌ User tag preferences
- ❌ Personalized content filtering
- ❌ User-specific tag management
- ❌ Tag-based access control
- ❌ User tag assignment endpoints

Tags are purely for **organization and navigation**, not for **personalization or filtering**.

## When to Use This System

Use this simplified tagging system when you want:
- ✅ Simple task categorization
- ✅ Easy content organization
- ✅ Browse by topic/category
- ✅ All users see all content
- ✅ Minimal complexity

Don't use this if you need:
- ❌ Personalized content per user
- ❌ Access control based on preferences
- ❌ User-specific filtering
- ❌ Content recommendations

## Summary

The simplified tagging system provides **organizational structure** for tasks without the complexity of user-based filtering. It's perfect for:

- Organizing large task libraries
- Helping users find relevant content
- Categorizing by theme or difficulty
- Simple navigation and browsing

All users see all tasks - tags just help organize and find content more easily.

---

**For more details, see the code examples in the controllers and models.**