# Authentication System Changelog

## [1.1.0] - 2025-12-26

### Added - Authentication System

#### Core Features
- **User Registration**: Users can create accounts with name, email, and password
- **User Login**: Session-based authentication for web, token-based for API
- **User Logout**: Secure logout with session/token revocation
- **Protected Routes**: Create, edit, and delete operations now require authentication
- **Public Access**: Viewing tasks, tags, and playing the game remains public

#### Backend Changes

**New Dependencies:**
- `laravel/sanctum` (^4.2) - API token authentication

**New Migrations:**
- `create_personal_access_tokens_table` - Sanctum token storage
- `add_user_id_to_tasks_and_tags_tables` - User attribution

**New Controllers:**
- `app/Http/Controllers/Api/AuthController.php` - API authentication endpoints
- `app/Http/Controllers/AuthController.php` - Web authentication endpoints

**Updated Models:**
- `User.php` - Added HasApiTokens trait and task/tag relationships
- `Task.php` - Added user_id field, auto-set on creation, added user relationship
- `Tag.php` - Added user_id field, auto-set on creation, added user relationship

**Updated Routes:**
- API routes (`routes/api.php`):
  - Added `/api/register` (POST) - User registration
  - Added `/api/login` (POST) - User login
  - Added `/api/logout` (POST) - User logout (protected)
  - Added `/api/me` (GET) - Get current user (protected)
  - Protected all create/update/delete endpoints with `auth:sanctum` middleware
  - Public routes remain accessible without authentication

- Web routes (`routes/web.php`):
  - Added `/register` (GET, POST) - Registration form and processing
  - Added `/login` (GET, POST) - Login form and processing
  - Added `/logout` (POST) - Logout
  - Protected all create/update/delete routes with `auth` middleware
  - Public viewing routes remain accessible

#### Frontend Changes

**New Views:**
- `resources/views/auth/login.blade.php` - Login form
- `resources/views/auth/register.blade.php` - Registration form

**Updated Views:**
- `resources/views/layouts/app.blade.php`:
  - Added authentication-aware navigation
  - Show login/register buttons for guests
  - Show user avatar dropdown for authenticated users
  - Conditional "Create Task" link based on auth status

- `resources/views/welcome.blade.php`:
  - Updated navigation with auth status
  - Different nav items for guests vs authenticated users

#### API Endpoints

**New Public Endpoints:**
```
POST   /api/register          Register new user
POST   /api/login             Login user
```

**New Protected Endpoints:**
```
POST   /api/logout            Logout user (revoke token)
GET    /api/me                Get current user info
```

**Updated Endpoints (now protected):**
```
POST   /api/tasks             Create task (requires auth)
PUT    /api/tasks/{id}        Update task (requires auth)
PATCH  /api/tasks/{id}        Update task (requires auth)
DELETE /api/tasks/{id}        Delete task (requires auth)
PATCH  /api/tasks/bulk        Bulk update tasks (requires auth)
DELETE /api/tasks/bulk        Bulk delete tasks (requires auth)
PATCH  /api/tasks/{id}/toggle-draft  Toggle draft (requires auth)

POST   /api/tags              Create tag (requires auth)
PUT    /api/tags/{id}         Update tag (requires auth)
PATCH  /api/tags/{id}         Update tag (requires auth)
DELETE /api/tags/{id}         Delete tag (requires auth)
```

**Still Public (no auth required):**
```
GET    /api/tasks             View all tasks
GET    /api/tasks/{id}        View single task
GET    /api/tasks/random      Get random task
GET    /api/tasks/statistics  Get statistics

GET    /api/tags              View all tags
GET    /api/tags/{id}         View single tag
```

#### Database Changes

**New Tables:**
- `personal_access_tokens` - Stores Sanctum API tokens

**Modified Tables:**
- `tasks` - Added nullable `user_id` foreign key
- `tags` - Added nullable `user_id` foreign key

#### Security Features

- **Password Hashing**: All passwords hashed with bcrypt
- **Token Security**: Sanctum tokens securely generated and stored
- **Session Security**: Laravel's built-in session management
- **CSRF Protection**: All web forms protected
- **Input Validation**: All endpoints validate inputs
- **Authorization**: Middleware protection on sensitive routes

#### Documentation

**New Files:**
- `AUTHENTICATION.md` - Complete authentication documentation
- `AUTHENTICATION_IMPLEMENTATION.md` - Technical implementation details
- `AUTH_QUICKSTART.md` - Quick start guide for developers
- `CHANGELOG_AUTH.md` - This changelog

**Updated Files:**
- `README.md` - Added authentication features to overview

#### Testing

**New Test Scripts:**
- `test_auth.sh` - Comprehensive authentication test suite
  - Tests registration
  - Tests login
  - Tests protected endpoints
  - Tests token authentication
  - Tests logout and token revocation

#### Usage Examples

**Web Interface:**
1. Visit `/register` to create account
2. Visit `/login` to access account
3. Create tasks/tags from navigation menu
4. Logout from user dropdown

**API Usage:**
```bash
# Register
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John","email":"john@example.com","password":"password123","password_confirmation":"password123"}'

# Login and get token
TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password123"}' \
  | jq -r '.data.token')

# Create task with token
curl -X POST http://localhost:8000/api/tasks \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"type":"dare","spice_rating":3,"description":"Do 20 pushups","draft":false}'
```

### Changed

- Task creation now automatically sets `user_id` from authenticated user
- Tag creation now automatically sets `user_id` from authenticated user
- Navigation UI adapts based on authentication status
- Welcome page shows different content for guests vs authenticated users

### Migration Notes

- **Backward Compatible**: Existing tasks and tags work fine (user_id is nullable)
- **No Breaking Changes**: Public APIs remain unchanged
- **Optional Migration**: Existing records can be assigned to a user if needed:
  ```php
  Task::whereNull('user_id')->update(['user_id' => $adminUserId]);
  ```

### Tested Scenarios

- ✅ User registration via web
- ✅ User registration via API
- ✅ User login via web
- ✅ User login via API
- ✅ Authenticated task creation
- ✅ Authenticated tag creation
- ✅ Rejection of unauthenticated create/update/delete
- ✅ Public viewing of tasks/tags
- ✅ User logout via web
- ✅ Token revocation via API
- ✅ Navigation updates based on auth status
- ✅ Automatic user_id attribution
- ✅ Token authentication with Bearer header
- ✅ Remember me functionality

### Known Issues

None at this time.

### Future Enhancements

Potential features for future versions:
- Email verification
- Password reset functionality
- User profile pages
- Edit profile information
- Avatar uploads
- Role-based permissions (admin, moderator)
- Users can only edit their own content
- OAuth integration (Google, Facebook, GitHub)
- Two-factor authentication
- Account deletion
- Activity logs

### Breaking Changes

None. All changes are backward compatible.

### Dependencies

- Laravel Sanctum 4.2+
- PHP 8.2+
- Laravel 12.0+

### Credits

Authentication system implemented using:
- Laravel Sanctum for API authentication
- Laravel's built-in Auth system for web authentication
- DaisyUI for form styling