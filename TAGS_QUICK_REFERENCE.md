# Tags Quick Reference

## What Are Tags?

Tags are **categories for organizing tasks**. They help users browse and filter tasks by topic, theme, or difficulty.

- ✅ Tags categorize tasks
- ✅ All users see all tasks
- ✅ Tags are for organization only
- ❌ No user preferences
- ❌ No personalized filtering

---

## Setup

```bash
# Run migrations
php artisan migrate

# Seed default tags
php artisan db:seed --class=TagSeeder
```

---

## Default Tags (10)

| ID | Name | Slug |
|----|------|------|
| 1 | Adults Only | adults-only |
| 2 | Family Friendly | family-friendly |
| 3 | Party Mode | party-mode |
| 4 | Romantic | romantic |
| 5 | Extreme | extreme |
| 6 | Funny | funny |
| 7 | Physical | physical |
| 8 | Mental | mental |
| 9 | Creative | creative |
| 10 | Social | social |

---

## Quick Code Examples

### Create Task with Tags
```php
$task = Task::create([
    'type' => 'dare',
    'spice_rating' => 2,
    'description' => 'Do 20 jumping jacks',
    'draft' => false
]);

// Attach tags
$task->tags()->attach([6, 7]); // Funny, Physical
```

### Get Tasks by Tag
```php
$tag = Tag::where('slug', 'physical')->first();
$tasks = $tag->tasks()->get();
```

### Update Task Tags
```php
$task = Task::find(1);

// Add tags
$task->tags()->attach([1, 2]);

// Replace all tags
$task->tags()->sync([3, 4, 5]);

// Remove tags
$task->tags()->detach([1]);
```

### Find Tasks with Specific Tags
```php
// Tasks with ANY of these tags
$tasks = Task::withTags([1, 2, 3])->get();

// Tasks with ALL of these tags
$tasks = Task::withAllTags([1, 2, 3])->get();

// Tasks with no tags
$tasks = Task::doesntHave('tags')->get();
```

---

## Web Routes

| URL | Purpose |
|-----|---------|
| `/tags` | Browse all tags |
| `/tags/create` | Create new tag |
| `/tags/{id}` | View tag details |
| `/tags/{id}/edit` | Edit tag |
| `/tasks/create` | Create task (with tag selection) |
| `/tasks/{id}` | View task (shows tags) |

---

## API Endpoints

### Tags
```bash
GET    /api/tags              # List all tags
POST   /api/tags              # Create tag
GET    /api/tags/{id}         # Get tag
PUT    /api/tags/{id}         # Update tag
DELETE /api/tags/{id}         # Delete tag
```

### Tasks with Tags
```bash
GET    /api/tasks             # List tasks (with tags)
POST   /api/tasks             # Create task with tags
GET    /api/tasks/{id}        # Get task with tags
PUT    /api/tasks/{id}        # Update task tags
```

---

## API Usage Examples

### Create Tagged Task
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

### Get All Tags
```bash
curl http://localhost/api/tags
```

### Create New Tag
```bash
curl -X POST http://localhost/api/tags \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Outdoor Activities",
    "description": "Tasks that take place outdoors"
  }'
```

---

## Model Relationships

### Tag
```php
$tag->tasks()  // Get all tasks with this tag
```

### Task
```php
$task->tags()  // Get all tags for this task
```

---

## Common Queries

```php
// Get tag with task count
$tag = Tag::withCount('tasks')->find(1);

// Get all tags ordered by popularity
$tags = Tag::withCount('tasks')
    ->orderBy('tasks_count', 'desc')
    ->get();

// Get untagged tasks
$tasks = Task::doesntHave('tags')->get();

// Get published tasks with specific tag
$tasks = Task::where('draft', false)
    ->whereHas('tags', function($q) {
        $q->where('tags.id', 1);
    })->get();
```

---

## UI Features

- ✅ Visual tag selection (checkbox cards)
- ✅ Tag badges on task cards
- ✅ Clickable tags (navigate to tag page)
- ✅ Auto-slug generation from name
- ✅ Tag task count display
- ✅ Tag detail pages with task preview
- ✅ Responsive grid layouts

---

## Remember

🏷️ **Tags = Categories**
- Tags organize tasks into categories
- All users see all tasks
- No personalized filtering
- Simple and straightforward

---

## Need More Help?

- Full Guide: `TAGGING_SIMPLIFIED.md`
- Changes Made: `CHANGES_SUMMARY.md`
- Code Examples: Check controllers and models