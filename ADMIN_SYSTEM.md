# Admin System Documentation

## Overview

The YouDare application now includes a complete admin system. Only users with admin privileges can create, edit, or delete tasks and tags. Regular users and guests can view all content but cannot modify it.

## Features

- **Admin Flag**: Users have an `is_admin` boolean field
- **Admin Middleware**: Protects all create/update/delete routes
- **Admin Commands**: CLI commands to manage admin users
- **UI Adaptation**: Interface shows/hides admin actions based on user role
- **API Protection**: API endpoints require admin privileges for modifications

## User Roles

### Guest (Not Logged In)
- ✅ View all tasks and tags
- ✅ Browse and search content
- ✅ Play the game
- ✅ Get random tasks
- ❌ Cannot create/edit/delete anything
- 👉 See login/register prompts

### Regular User (Logged In, Not Admin)
- ✅ View all tasks and tags
- ✅ Browse and search content
- ✅ Play the game
- ✅ Get random tasks
- ❌ Cannot create/edit/delete tasks or tags
- 👤 See their name in navigation
- 🔒 No admin buttons visible

### Admin User (Logged In, Admin Flag = true)
- ✅ Everything regular users can do
- ✅ Create new tasks
- ✅ Edit any task
- ✅ Delete any task
- ✅ Toggle task draft status
- ✅ Create new tags
- ✅ Edit any tag
- ✅ Delete any tag
- 👑 Full content management access

## Making Users Admin

### Command Line Interface

#### Make User Admin
```bash
php artisan user:make-admin user@example.com
```

**Output:**
```
✓ User 'John Doe' (user@example.com) is now an admin!
```

**If already admin:**
```
⚠ User 'John Doe' (user@example.com) is already an admin.
```

**If user not found:**
```
✗ User with email 'user@example.com' not found.
```

#### Remove Admin Privileges
```bash
php artisan user:remove-admin user@example.com
```

**Output:**
```
✓ Admin privileges removed from 'John Doe' (user@example.com).
```

#### List All Admins
```bash
php artisan user:list-admins
```

**Output:**
```
Admin Users:

+----+----------+-------------------+---------------------+
| ID | Name     | Email             | Created At          |
+----+----------+-------------------+---------------------+
| 1  | John Doe | admin@example.com | 2025-12-26 10:30:00 |
| 2  | Jane Doe | jane@example.com  | 2025-12-26 11:45:00 |
+----+----------+-------------------+---------------------+

Total admin users: 2
```

## Database Schema

### Users Table
```sql
users
  - id (bigint)
  - name (varchar)
  - email (varchar, unique)
  - password (varchar)
  - is_admin (boolean, default: false)  ← NEW
  - email_verified_at (timestamp, nullable)
  - remember_token (varchar, nullable)
  - created_at (timestamp)
  - updated_at (timestamp)
```

## Backend Implementation

### User Model

**New Method:**
```php
public function isAdmin(): bool
{
    return $this->is_admin;
}
```

**Usage:**
```php
if ($user->isAdmin()) {
    // User is an admin
}

// Or via Auth facade
if (Auth::user()->isAdmin()) {
    // Current user is an admin
}
```

### Admin Middleware

**File:** `app/Http/Middleware/EnsureUserIsAdmin.php`

**Purpose:** Checks if the authenticated user has admin privileges.

**Behavior:**
- If not authenticated → 401 Unauthorized (API) or redirect to login (Web)
- If authenticated but not admin → 403 Forbidden

**Registration:** Registered as `admin` middleware alias in `bootstrap/app.php`

### Protected Routes

#### Web Routes (`routes/web.php`)

**Protected with `admin` middleware:**
```php
// Tasks
GET  /tasks/create
POST /tasks
GET  /tasks/{task}/edit
PUT  /tasks/{task}
DELETE /tasks/{task}
PATCH /tasks/{task}/toggle-draft

// Tags
GET  /tags/create
POST /tags
GET  /tags/{task}/edit
PUT  /tags/{tag}
DELETE /tags/{tag}
```

**Public (no authentication required):**
```php
GET /tasks           - View all tasks
GET /tasks/{task}    - View single task
GET /tags            - View all tags
GET /tags/{tag}      - View single tag
GET /game            - Play game
```

#### API Routes (`routes/api.php`)

**Protected with `admin` middleware:**
```php
// Tasks
POST   /api/tasks
PUT    /api/tasks/{task}
PATCH  /api/tasks/{task}
DELETE /api/tasks/{task}
PATCH  /api/tasks/bulk
DELETE /api/tasks/bulk
PATCH  /api/tasks/{task}/toggle-draft

// Tags
POST   /api/tags
PUT    /api/tags/{tag}
PATCH  /api/tags/{tag}
DELETE /api/tags/{tag}
```

**Public:**
```php
GET /api/tasks              - View all tasks
GET /api/tasks/{task}       - View single task
GET /api/tasks/random       - Get random task