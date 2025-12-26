# YouDare - Truth or Dare Game with Advanced Tagging System

A modern Truth or Dare game built with Laravel and Vue.js, featuring an advanced tagging system for personalized content filtering.

## Features

- 🎮 **Truth or Dare Game**: Interactive game with customizable tasks
- 🏷️ **Advanced Tagging System**: Filter content based on user preferences
- 🔥 **Spice Ratings**: Tasks rated from 1-5 for intensity levels
- 📱 **RESTful API**: Full-featured API for mobile and web clients
- 🔒 **Content Filtering**: Users only see tasks matching their tags
- 🚫 **Can't Have Tags**: Hide tasks from players with specific tags
- ✨ **Tags to Add**: Automatically add tags to players when they complete tasks
- 🎯 **Random Task Selection**: Get random tasks based on preferences
- 📊 **Draft System**: Create and manage draft tasks before publishing
- 🔐 **Authentication System**: User registration, login, and protected routes
- 👤 **User Management**: Track task and tag creators

## Authentication & Security

The application now includes a complete authentication system:

- **User Registration & Login**: Secure account creation and authentication
- **Protected Routes**: Only logged-in users can create, edit, or delete tasks and tags
- **API Token Authentication**: Laravel Sanctum tokens for API access
- **Public Access**: Viewing tasks, tags, and playing the game remains public
- **User Attribution**: Automatically track who created each task and tag

**Quick Start:**
- Visit `/register` to create an account
- Visit `/login` to access your account
- Look for the Login/Register buttons in the top-right corner

See [AUTHENTICATION.md](AUTHENTICATION.md) for complete documentation.

## What's New: Tagging System

The tagging system allows you to create personalized experiences for different user groups:

- **Tag-Based Filtering**: Tasks are only visible to users who have matching tags
- **Can't Have Tags**: Hide tasks from players with specific tags (opposite of regular tags)
- **Tags to Add**: NEW! Automatically add tags to players when they complete tasks - perfect for achievements and progression!
- **User Preferences**: Users select tags that match their interests
- **Content Categorization**: Organize tasks by categories like "Family Friendly", "Adults Only", "Party Mode"
- **Flexible Matching**: Users see tasks if they have at least one matching tag
- **Universal Content**: Tasks without tags are visible only to users without tags

### How It Works

1. **Users select tags** during registration or in their profile (e.g., "Family Friendly", "Funny", "Social")
2. **Tasks are tagged** with appropriate categories when created
3. **Automatic filtering** ensures users only see relevant content
4. **Smart matching** shows tasks that have at least one tag in common with the user

Example:
- User has tags: `[Family Friendly, Funny]`
- Task has tags: `[Funny, Party Mode]`
- Result: ✅ User sees this task (both have "Funny")

### Can't Have Tags (Negative Filtering)

The "can't have tags" feature provides even more control by hiding tasks from players with specific tags:

- **Exclusive Content**: Show tasks only to players who DON'T have certain tags
- **Progressive Gameplay**: Hide advanced tasks until players complete prerequisites
- **Content Restrictions**: Prevent tasks from appearing based on player attributes

Example:
- Task has cant_have_tags: `[Beginner]`
- Player A has tags: `[Beginner, Funny]`
- Player B has tags: `[Expert, Funny]`
- Result: ❌ Player A can't see this task | ✅ Player B can see this task

**Quick Start:** See [CANT_HAVE_TAGS_QUICKSTART.md](CANT_HAVE_TAGS_QUICKSTART.md) for examples

### Tags to Add (Rewards & Progression)

The "tags to add" feature automatically adds tags to players when they complete tasks:

- **Achievement System**: Award badges when players complete challenges
- **Progression System**: Promote players through ranks (Newbie → Veteran → Legend)
- **Unlockable Content**: Grant access to new content tiers
- **Milestone Tracking**: Mark player accomplishments

Example:
- Task: "Complete your first challenge"
- tags_to_add: `[Veteran]`
- Result: ✨ Player receives the "Veteran" tag upon completion

Combine with tags_to_remove for smooth progression:
- tags_to_remove: `[Beginner]` (remove old status)
- tags_to_add: `[Veteran]` (add new status)

**Quick Start:** See [TAGS_TO_ADD_QUICKSTART.md](TAGS_TO_ADD_QUICKSTART.md) for examples

## Quick Start

### Installation

```bash
# Clone the repository
git clone <repository-url>
cd youdare

# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations and seed database
php artisan migrate
php artisan db:seed --class=TagSeeder

# Build frontend assets
npm run build

# Start development server
php artisan serve
```

### Running Tests

```bash
# Test tag filtering logic
php test_tag_filtering.php

# Run example usage
php example_tag_usage.php
```

## Tagging System Documentation

### Quick Links
- 📖 **[Quick Start Guide](TAGS_QUICKSTART.md)** - Get started in 5 minutes
- 📚 **[Full Documentation](TAGGING_SYSTEM.md)** - Complete API reference and examples
- 🚫 **[Can't Have Tags Guide](CANT_HAVE_TAGS_QUICKSTART.md)** - Negative filtering feature
- 📄 **[Can't Have Tags Documentation](CANT_HAVE_TAGS.md)** - Complete can't have tags reference
- ✨ **[Tags to Add Guide](TAGS_TO_ADD_QUICKSTART.md)** - Reward and progression system
- 🔬 **[Test Suite](test_tag_filtering.php)** - 28 automated tests
- 🔬 **[Can't Have Tags Tests](test_cant_have_tags.php)** - Can't have tags validation
- 🔬 **[Tags to Add Tests](test_tags_to_add.php)** - Tags to add validation
- 💡 **[Usage Examples](example_tag_usage.php)** - Code examples

### Default Tags

The system includes 10 pre-configured tags:
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

### Tags

```
GET    /api/tags              - List all tags
POST   /api/tags              - Create new tag
GET    /api/tags/{id}         - Get single tag
PUT    /api/tags/{id}         - Update tag
DELETE /api/tags/{id}         - Delete tag

GET    /api/tags/user         - Get user's tags (auth required)
POST   /api/tags/user/sync    - Replace all user tags (auth required)
POST   /api/tags/{id}/attach  - Add tag to user (auth required)
DELETE /api/tags/{id}/detach  - Remove tag from user (auth required)
```

### Tasks (Filtered by User Tags)

```
GET    /api/tasks             - List tasks (filtered)
POST   /api/tasks             - Create task with tags
GET    /api/tasks/{id}        - Get task with tags
PUT    /api/tasks/{id}        - Update task and tags
DELETE /api/tasks/{id}        - Delete task

GET    /api/tasks/random      - Get random task (filtered)
GET    /api/tasks/statistics  - Get task statistics
PATCH  /api/tasks/bulk        - Bulk update tasks
DELETE /api/tasks/bulk        - Bulk delete tasks
```

## Database Schema

### Tables
- `users` - User accounts
- `tasks` - Truth and dare tasks
- `tags` - Content categories
- `task_tag` - Task-tag relationships (pivot)
- `tag_user` - User-tag relationships (pivot)

### Key Relationships
- Users have many tags (many-to-many)
- Tasks have many tags (many-to-many)
- Tags belong to many users and tasks

## Usage Examples

### Creating a Task with Tags

```php
use App\Models\Task;
use App\Models\Tag;

$task = Task::create([
    'type' => 'dare',
    'spice_rating' => 2,
    'description' => 'Do a funny dance',
    'draft' => false
]);

$funnyTag = Tag::where('slug', 'funny')->first();
$task->tags()->attach([$funnyTag->id]);
```

### Assigning Tags to Users

```php
use App\Models\User;
use App\Models\Tag;

$user = User::find(1);
$tags = Tag::whereIn('slug', ['family-friendly', 'funny'])->pluck('id');
$user->tags()->sync($tags);
```

### Filtering Tasks by User Tags

```php
// Automatic filtering in controllers
$user = Auth::user();
$userTagIds = $user->tags()->pluck('tags.id')->toArray();

$tasks = Task::whereHas('tags', function($q) use ($userTagIds) {
    $q->whereIn('tags.id', $userTagIds);
})->get();
```

## API Examples

### Get User's Tags

```bash
curl -X GET http://localhost/api/tags/user \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Add Tag to User

```bash
curl -X POST http://localhost/api/tags/1/attach \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Create Task with Tags

```bash
curl -X POST http://localhost/api/tasks \
  -H "Content-Type: application/json" \
  -d '{
    "type": "dare",
    "spice_rating": 3,
    "description": "Tell a funny story",
    "tags": [1, 6]
  }'
```

### Get Filtered Tasks

```bash
curl -X GET http://localhost/api/tasks \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Project Structure

```
youdare/
├── app/
│   ├── Http/Controllers/
│   │   ├── TaskController.php       # Web task controller
│   │   ├── TagController.php        # Web tag controller
│   │   └── Api/
│   │       ├── TaskController.php   # API task controller
│   │       └── TagController.php    # API tag controller
│   └── Models/
│       ├── Task.php                 # Task model with tags
│       ├── Tag.php                  # Tag model
│       └── User.php                 # User model with tags
├── database/
│   ├── migrations/
│   │   ├── *_create_tasks_table.php
│   │   ├── *_create_tags_table.php
│   │   ├── *_create_task_tag_table.php
│   │   └── *_create_tag_user_table.php
│   └── seeders/
│       └── TagSeeder.php            # Default tags
├── routes/
│   ├── web.php                      # Web routes
│   └── api.php                      # API routes
├── TAGGING_SYSTEM.md                # Full documentation
├── TAGS_QUICKSTART.md               # Quick start guide
├── test_tag_filtering.php           # Test suite
└── example_tag_usage.php            # Usage examples
```

## Configuration

### Environment Variables

```env
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database.sqlite
```

### Task Types

- `truth` - Truth questions
- `dare` - Dare challenges

### Spice Ratings

- `1` - Mild
- `2` - Medium
- `3` - Hot
- `4` - Extra Hot
- `5` - Extreme

## Security Features

- Tag filtering enforced at database query level
- Authentication required for user tag management
- Input validation on all endpoints
- Prepared statements prevent SQL injection
- XSS protection on all outputs

## Testing

The project includes comprehensive tests:

```bash
# Run tag filtering tests (28 tests)
php test_tag_filtering.php
```

Tests verify:
- ✅ Users see tasks with matching tags
- ✅ Users don't see tasks without matching tags
- ✅ Users without tags only see untagged tasks
- ✅ Multi-tag scenarios work correctly
- ✅ Helper methods function properly
- ✅ Query scopes work as expected

## Performance

- Indexed foreign keys in pivot tables
- Efficient many-to-many queries
- Eager loading support for relationships
- Query caching recommendations included

## Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch
3. Write tests for new features
4. Ensure all tests pass
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Credits

Built with:
- [Laravel](https://laravel.com) - PHP Framework
- [Vue.js](https://vuejs.org) - Frontend Framework
- [Tailwind CSS](https://tailwindcss.com) - Styling

## Support

For questions and support:
- Read the [documentation](TAGGING_SYSTEM.md)
- Check [quick start guide](TAGS_QUICKSTART.md)
- Run example scripts
- Review test cases

---

Made with ❤️ for creating personalized game experiences