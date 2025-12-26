# Authentication System

This application now includes a complete authentication system that allows users to register, login, and logout. Only authenticated users can create, update, or delete tasks and tags.

## Features

- **User Registration**: New users can create an account with name, email, and password
- **User Login**: Existing users can log in with email and password
- **User Logout**: Authenticated users can log out
- **Protected Routes**: Creating, updating, and deleting tasks/tags requires authentication
- **Public Access**: Viewing tasks, tags, and playing the game remains public
- **API Token Authentication**: API endpoints support Laravel Sanctum token authentication

## Web Interface

### Registration
- Visit `/register` to create a new account
- Required fields:
  - Name
  - Email (must be unique)
  - Password (minimum 8 characters)
  - Password Confirmation

### Login
- Visit `/login` to access your account
- Required fields:
  - Email
  - Password
- Optional: "Remember me" checkbox

### Logout
- Click on your avatar in the top-right corner
- Select "Logout" from the dropdown menu

## Protected Features

The following features require authentication:

### Tasks
- Create new tasks
- Edit existing tasks
- Delete tasks
- Toggle draft status
- Bulk operations

### Tags
- Create new tags
- Edit existing tags
- Delete tags

### Public Features (No Authentication Required)
- View all tasks
- View all tags
- Get random task
- View task statistics
- Play the game
- View individual tasks/tags

## API Authentication

### Register a New User

**Endpoint:** `POST /api/register`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
  }
}
```

### Login

**Endpoint:** `POST /api/login`

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "User logged in successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "token": "2|xxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
  }
}
```

### Using API Tokens

Once you have a token, include it in the Authorization header for protected endpoints:

```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "type": "dare",
    "spice_rating": 3,
    "description": "Do 20 pushups",
    "draft": false
  }'
```

### Get Current User

**Endpoint:** `GET /api/me`

**Headers:**
```
Authorization: Bearer YOUR_TOKEN_HERE
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    }
  }
}
```

### Logout (Revoke Current Token)

**Endpoint:** `POST /api/logout`

**Headers:**
```
Authorization: Bearer YOUR_TOKEN_HERE
```

**Response:**
```json
{
  "status": "success",
  "message": "User logged out successfully"
}
```

## Protected API Endpoints

The following API endpoints require authentication (Bearer token):

### Tasks
- `POST /api/tasks` - Create a new task
- `PUT /api/tasks/{task}` - Update a task
- `PATCH /api/tasks/{task}` - Partially update a task
- `DELETE /api/tasks/{task}` - Delete a task
- `PATCH /api/tasks/{task}/toggle-draft` - Toggle draft status
- `PATCH /api/tasks/bulk` - Bulk update tasks
- `DELETE /api/tasks/bulk` - Bulk delete tasks

### Tags
- `POST /api/tags` - Create a new tag
- `PUT /api/tags/{tag}` - Update a tag
- `PATCH /api/tags/{tag}` - Partially update a tag
- `DELETE /api/tags/{tag}` - Delete a tag

## Database Changes

The authentication system added the following database changes:

### New Tables
- `personal_access_tokens` - Stores API tokens for Sanctum

### Modified Tables
- `tasks` - Added `user_id` foreign key (nullable)
- `tags` - Added `user_id` foreign key (nullable)

### User Tracking
When a logged-in user creates a task or tag, their user ID is automatically stored:
- Tasks and tags created via web interface are linked to the logged-in user
- Tasks and tags created via API are linked to the token owner
- The `user_id` field is nullable to allow existing records and anonymous creation (if configured)

## Model Relationships

### User Model
```php
$user->tasks;  // Get all tasks created by the user
$user->tags;   // Get all tags created by the user
```

### Task Model
```php
$task->user;   // Get the user who created this task
```

### Tag Model
```php
$tag->user;    // Get the user who created this tag
```

## Security Notes

1. **Password Hashing**: All passwords are hashed using bcrypt
2. **Token Security**: API tokens are securely generated and stored
3. **Session Security**: Web sessions use Laravel's secure session management
4. **CSRF Protection**: All web forms include CSRF token protection
5. **Validation**: All inputs are validated before processing

## Testing Authentication

### Web Interface
1. Start the development server: `php artisan serve`
2. Visit `http://localhost:8000/register`
3. Create an account
4. Try creating a task or tag
5. Logout and verify you can still view but not create

### API
1. Register a user via API
2. Save the returned token
3. Use the token to create a task
4. Try creating without a token (should fail with 401)

## Example: Creating a Task with Authentication

### Web Interface
1. Login at `/login`
2. Click "Create Task" in the navigation
3. Fill in the form
4. Submit - the task will be automatically linked to your account

### API
```bash
# 1. Register or login to get a token
TOKEN=$(curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password123"}' \
  | jq -r '.data.token')

# 2. Create a task using the token
curl -X POST http://localhost:8000/api/tasks \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "type": "truth",
    "spice_rating": 2,
    "description": "What is your biggest fear?",
    "draft": false
  }'
```

## Troubleshooting

### "Unauthenticated" Error
- Make sure you're logged in (web) or including a valid Bearer token (API)
- Check that the token hasn't been revoked
- Verify the token is in the correct format: `Authorization: Bearer TOKEN`

### Can't Create Tasks/Tags
- Verify you're logged in
- Check the browser console for errors
- Ensure CSRF token is present in forms

### Token Not Working
- Tokens are revoked when you logout
- Make sure you're using the most recent token
- Check that Sanctum is properly configured

## Migration Guide

If you have existing tasks and tags without user attribution:
- The `user_id` field is nullable, so existing records will work fine
- Existing records will have `user_id` set to `null`
- Only new tasks/tags will be linked to users
- You can manually update existing records if needed:

```php
// In tinker or a migration
$user = User::first();
Task::whereNull('user_id')->update(['user_id' => $user->id]);
```
