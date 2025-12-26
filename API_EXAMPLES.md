# Task API Documentation

This API provides endpoints for managing Truth or Dare tasks with various spice ratings.

## Task Model Structure

Each task has the following fields:
- `id`: Unique identifier
- `type`: Either "truth" or "dare"
- `spice_rating`: Integer from 1-5 (1=Mild, 5=Extreme)
- `description`: The task description/question
- `draft`: Boolean indicating if the task is a draft
- `created_at`: Timestamp when created
- `updated_at`: Timestamp when last updated
- `spice_level`: (computed) Descriptive text for spice rating

## API Endpoints

### Base URL
```
http://localhost:8000/api
```

### 1. Get All Tasks
**GET** `/tasks`

Query Parameters:
- `type`: Filter by type ("truth" or "dare")
- `draft`: Include drafts (true/false, default: false)
- `min_spice`: Minimum spice rating (1-5)
- `max_spice`: Maximum spice rating (1-5)
- `search`: Search in description
- `sort_by`: Sort field (created_at, updated_at, spice_rating, type)
- `sort_order`: Sort order (asc, desc)
- `per_page`: Results per page (1-100, default: 15)

Example:
```bash
# Get all published truth tasks with spice rating 3 or higher
curl "http://localhost:8000/api/tasks?type=truth&min_spice=3"

# Get all tasks sorted by spice rating
curl "http://localhost:8000/api/tasks?sort_by=spice_rating&sort_order=desc"
```

### 2. Get Single Task
**GET** `/tasks/{id}`

Example:
```bash
curl http://localhost:8000/api/tasks/1
```

### 3. Create Task
**POST** `/tasks`

Request Body:
```json
{
    "type": "truth",
    "spice_rating": 3,
    "description": "What's the most embarrassing thing you've done?",
    "draft": false
}
```

Example:
```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -d '{
    "type": "dare",
    "spice_rating": 4,
    "description": "Call a random contact and sing happy birthday",
    "draft": false
  }'
```

### 4. Update Task
**PUT/PATCH** `/tasks/{id}`

Request Body (all fields optional for PATCH):
```json
{
    "type": "dare",
    "spice_rating": 5,
    "description": "Updated description",
    "draft": true
}
```

Example:
```bash
# Update only spice rating
curl -X PATCH http://localhost:8000/api/tasks/1 \
  -H "Content-Type: application/json" \
  -d '{"spice_rating": 4}'
```

### 5. Delete Task
**DELETE** `/tasks/{id}`

Example:
```bash
curl -X DELETE http://localhost:8000/api/tasks/1
```

### 6. Get Random Task
**GET** `/tasks/random`

Query Parameters:
- `type`: Filter by type ("truth" or "dare")
- `min_spice`: Minimum spice rating
- `max_spice`: Maximum spice rating

Example:
```bash
# Get random dare with max spice rating of 3
curl "http://localhost:8000/api/tasks/random?type=dare&max_spice=3"

# Get any random task
curl http://localhost:8000/api/tasks/random
```

### 7. Toggle Draft Status
**PATCH** `/tasks/{id}/toggle-draft`

Example:
```bash
curl -X PATCH http://localhost:8000/api/tasks/1/toggle-draft
```

### 8. Get Statistics
**GET** `/tasks/statistics`

Returns statistics about all tasks in the database.

Example:
```bash
curl http://localhost:8000/api/tasks/statistics
```

Response:
```json
{
    "status": "success",
    "data": {
        "total_tasks": 60,
        "published_tasks": 50,
        "draft_tasks": 10,
        "by_type": {
            "truth": 30,
            "dare": 30
        },
        "by_spice_rating": {
            "1": 10,
            "2": 10,
            "3": 15,
            "4": 15,
            "5": 10
        },
        "average_spice_rating": 3.2
    }
}
```

### 9. Bulk Update Tasks
**PATCH** `/tasks/bulk`

Update multiple tasks at once.

Request Body:
```json
{
    "task_ids": [1, 2, 3, 4],
    "updates": {
        "spice_rating": 4,
        "draft": false
    }
}
```

Example:
```bash
curl -X PATCH http://localhost:8000/api/tasks/bulk \
  -H "Content-Type: application/json" \
  -d '{
    "task_ids": [1, 2, 3],
    "updates": {"draft": true}
  }'
```

### 10. Bulk Delete Tasks
**DELETE** `/tasks/bulk`

Delete multiple tasks at once.

Request Body:
```json
{
    "task_ids": [5, 6, 7]
}
```

Example:
```bash
curl -X DELETE http://localhost:8000/api/tasks/bulk \
  -H "Content-Type: application/json" \
  -d '{"task_ids": [5, 6, 7]}'
```

## Response Format

### Success Response
```json
{
    "status": "success",
    "message": "Operation successful",
    "data": { ... }
}
```

### Error Response
```json
{
    "status": "error",
    "message": "Error description"
}
```

### Paginated Response
```json
{
    "status": "success",
    "data": [ ... ],
    "meta": {
        "current_page": 1,
        "last_page": 4,
        "per_page": 15,
        "total": 60,
        "from": 1,
        "to": 15
    },
    "links": {
        "first": "http://localhost:8000/api/tasks?page=1",
        "last": "http://localhost:8000/api/tasks?page=4",
        "prev": null,
        "next": "http://localhost:8000/api/tasks?page=2"
    }
}
```

## Spice Rating Guide

- **1 🌶️ (Mild)**: Family-friendly, innocent questions/dares
- **2 🌶️🌶️ (Medium)**: Slightly embarrassing but still tame
- **3 🌶️🌶️🌶️ (Hot)**: Moderately embarrassing or challenging
- **4 🌶️🌶️🌶️🌶️ (Extra Hot)**: Very embarrassing or difficult
- **5 🌶️🌶️🌶️🌶️🌶️ (Extreme)**: Maximum embarrassment or challenge

## PHP Usage Examples

### Using Laravel HTTP Client

```php
use Illuminate\Support\Facades\Http;

// Get all tasks
$response = Http::get('http://localhost:8000/api/tasks');
$tasks = $response->json('data');

// Create a new task
$response = Http::post('http://localhost:8000/api/tasks', [
    'type' => 'truth',
    'spice_rating' => 3,
    'description' => 'What is your biggest fear?',
    'draft' => false
]);

// Get random dare
$response = Http::get('http://localhost:8000/api/tasks/random', [
    'type' => 'dare',
    'max_spice' => 3
]);
$randomDare = $response->json('data');
```

### Using the Task Model Directly

```php
use App\Models\Task;

// Create a task
$task = Task::create([
    'type' => 'truth',
    'spice_rating' => 2,
    'description' => 'When was the last time you lied?',
    'draft' => false
]);

// Get random published dare with max spice of 3
$randomDare = Task::query()
    ->where('type', 'dare')
    ->where('draft', false)
    ->where('spice_rating', '<=', 3)
    ->inRandomOrder()
    ->first();

// Get all mild truths
$mildTruths = Task::truths()
    ->published()
    ->bySpiceLevel(1, 2)
    ->get();

// Toggle draft status
$task = Task::find(1);
$task->draft = !$task->draft;
$task->save();
```

## JavaScript/Axios Examples

```javascript
// Get all tasks
axios.get('/api/tasks')
    .then(response => {
        const tasks = response.data.data;
        console.log(tasks);
    });

// Create a new task
axios.post('/api/tasks', {
    type: 'dare',
    spice_rating: 4,
    description: 'Sing a song in public',
    draft: false
})
.then(response => {
    console.log('Task created:', response.data.data);
});

// Get random truth
axios.get('/api/tasks/random', {
    params: {
        type: 'truth',
        max_spice: 3
    }
})
.then(response => {
    const randomTruth = response.data.data;
    console.log('Random truth:', randomTruth.description);
});

// Get statistics
axios.get('/api/tasks/statistics')
    .then(response => {
        const stats = response.data.data;
        console.log('Total tasks:', stats.total_tasks);
    });
```

## Testing the API

You can test the API using the included test script:

```bash
# Run the test script
DB_CONNECTION=sqlite php test_tasks.php

# Start the development server
DB_CONNECTION=sqlite php artisan serve

# Then test with curl
curl http://localhost:8000/api/tasks/statistics
```

## Database Setup

1. Run migrations:
```bash
DB_CONNECTION=sqlite php artisan migrate
```

2. Seed the database with sample data:
```bash
DB_CONNECTION=sqlite php artisan db:seed
```

3. Or refresh and reseed:
```bash
DB_CONNECTION=sqlite php artisan migrate:fresh --seed
```
