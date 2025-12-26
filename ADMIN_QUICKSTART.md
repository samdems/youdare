# Admin System Quick Start Guide

## Overview

The YouDare application now has an admin system. Only admin users can create, edit, and delete tasks and tags. Regular users can view all content but cannot modify it.

## Making a User an Admin

### Command Line

Use the artisan command to grant admin privileges to a user:

```bash
php artisan user:make-admin user@example.com
```

**Example:**
```bash
php artisan user:make-admin sam@samcross.uk
```

**Output:**
```
✓ User 'Sam' (sam@samcross.uk) is now an admin!
```

## Removing Admin Privileges

To revoke admin access from a user:

```bash
php artisan user:remove-admin user@example.com
```

## Listing All Admins

To see all users with admin privileges:

```bash
php artisan user:list-admins
```

**Example Output:**
```
Admin Users:

+----+------+-----------------+---------------------+
| ID | Name | Email           | Created At          |
+----+------+-----------------+---------------------+
| 1  | Sam  | sam@samcross.uk | 2025-12-26 18:38:10 |
| 2  | Jane | jane@example.com| 2025-12-26 19:15:22 |
+----+------+-----------------+---------------------+

Total admin users: 2
```

## What Admins Can Do

### Web Interface

**Admins can:**
- ✅ Create new tasks
- ✅ Edit any task
- ✅ Delete any task
- ✅ Toggle draft status
- ✅ Create new tags
- ✅ Edit any tag
- ✅ Delete any tag
- ✅ Access admin-only pages
- ✅ See "Create Task" and "Create Tag" buttons in navigation

**Regular users can:**
- ✅ View all tasks and tags
- ✅ Play the game
- ✅ Get random tasks
- ✅ Use filters and search
- ❌ Cannot see create/edit/delete buttons

**Guests (not logged in) can:**
- ✅ View all tasks and tags
- ✅ Play the game
- ❌ Cannot see any admin features

### API

**Admin-only endpoints:**
```
POST   /api/tasks              Create task
PUT    /api/tasks/{id}         Update task
PATCH  /api/tasks/{id}         Update task
DELETE /api/tasks/{id}         Delete task
PATCH  /api/tasks/bulk         Bulk update
DELETE /api/tasks/bulk         Bulk delete
PATCH  /api/tasks/{id}/toggle-draft

POST   /api/tags               Create tag
PUT    /api/tags/{id}          Update tag
PATCH  /api/tags/{id}          Update tag
DELETE /api/tags/{id}          Delete tag
```

**Public endpoints (no admin required):**
```
GET    /api/tasks              View all tasks
GET    /api/tasks/{id}         View task
GET    /api/tasks/random       Get random task
GET    /api/tasks/statistics   Get statistics
GET    /api/tags               View all tags
GET    /api/tags/{id}          View tag
```

## Initial Setup

When first setting up the application:

1. **Register the first user:**
   ```bash
   # Via web: Visit http://localhost:8000/register
   # Or via API: POST /api/register
   ```

2. **Make them an admin:**
   ```bash
   php artisan user:make-admin their@email.com
   ```

3. **Verify admin status:**
   ```bash
   php artisan user:list-admins
   ```

4. **Login and start creating content:**
   ```
   Visit http://localhost:8000/login
   ```

## Database Structure

The `users` table has an `is_admin` column:
- Type: `boolean`
- Default: `false`
- All users are regular users by default
- Must be manually promoted to admin via command

## Security

### Route Protection

**Web routes:**
- Protected by `admin` middleware
- Checks if user is authenticated AND is admin
- Redirects to login if not authenticated
- Shows 403 error if authenticated but not admin

**API routes:**
- Protected by `admin` middleware
- Returns 401 if not authenticated
- Returns 403 if authenticated but not admin

### Middleware

The `EnsureUserIsAdmin` middleware:
1. Checks if user is authenticated
2. Checks if user has `is_admin = true`
3. Rejects non-admin users with 403 Forbidden

## User Model Helper

Check admin status in code:

```php
if ($user->isAdmin()) {
    // User is an admin
}

// Or
if (Auth::user()->isAdmin()) {
    // Current user is an admin
}
```

## Blade Directives

In views, check admin status:

```blade
@auth
    @if(Auth::user()->isAdmin())
        <a href="{{ route('tasks.create') }}">Create Task</a>
    @endif
@endauth
```

## Common Scenarios

### Scenario 1: First User Setup
```bash
# User registers via web interface
# Then promote them:
php artisan user:make-admin user@example.com
```

### Scenario 2: Adding Another Admin
```bash
# Existing admin creates account for someone
# Then promote them:
php artisan user:make-admin newadmin@example.com
```

### Scenario 3: Revoking Access
```bash
# Remove admin privileges:
php artisan user:remove-admin oldadmin@example.com
```

### Scenario 4: Checking Who Has Access
```bash
# List all admins:
php artisan user:list-admins
```

## API Usage

### For Admin Users

```bash
# 1. Login and get token
TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}' \
  | jq -r '.data.token')

# 2. Create a task (admin only)
curl -X POST http://localhost:8000/api/tasks \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "dare",
    "spice_rating": 3,
    "description": "Do 20 pushups",
    "draft": false
  }'
```

### For Non-Admin Users

```bash
# Will receive 403 Forbidden when trying to create:
curl -X POST http://localhost:8000/api/tasks \
  -H "Authorization: Bearer $NON_ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"type":"dare","spice_rating":3,"description":"Test","draft":false}'

# Response:
{
  "status": "error",
  "message": "Unauthorized. Admin access required."
}
```

## Troubleshooting

### "403 Forbidden" Error

**Problem:** User gets 403 when trying to create/edit content

**Solution:** Check if user is admin:
```bash
php artisan user:list-admins
```

If not in list:
```bash
php artisan user:make-admin their@email.com
```

### "404 Not Found" on /tasks/create

**Problem:** Route not found

**Solution:** Clear route cache:
```bash
php artisan route:clear
php artisan cache:clear
```

### Can't See Admin Buttons

**Problem:** Logged in but can't see create/edit buttons

**Checklist:**
1. Are you logged in? Check for user avatar in nav
2. Are you an admin? Run `php artisan user:list-admins`
3. If not admin, run `php artisan user:make-admin your@email.com`
4. Logout and login again
5. Clear browser cache

### Migration Error: Column Already Exists

**Problem:** `SQLSTATE[42S21]: Column already exists: 1060 Duplicate column name 'is_admin'`

**Solution:** The column already exists. The migration is now idempotent, just run:
```bash
php artisan migrate
```

It will skip adding the column if it already exists.

## Commands Reference

| Command | Description |
|---------|-------------|
| `php artisan user:make-admin EMAIL` | Grant admin privileges |
| `php artisan user:remove-admin EMAIL` | Revoke admin privileges |
| `php artisan user:list-admins` | List all admin users |

## Summary

- **Admin = Full Control** - Can create, edit, delete everything
- **User = View Only** - Can browse and play, cannot modify
- **Guest = View Only** - Can browse and play, must register to get account
- **Commands = Easy Management** - Simple CLI commands to manage admins
- **Secure = Middleware Protected** - All admin routes properly secured

Start by making your first user an admin, then use the web interface to manage content!