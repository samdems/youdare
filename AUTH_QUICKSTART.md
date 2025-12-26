# Authentication Quick Start Guide

## For End Users

### Creating an Account
1. Visit `http://localhost:8000/register`
2. Enter your name, email, and password
3. Click "Register"
4. You're now logged in and can create tasks and tags!

### Logging In
1. Visit `http://localhost:8000/login`
2. Enter your email and password
3. Click "Login"
4. You're redirected to the tasks page

### Creating Content
Once logged in:
- Click **"Create Task"** in the navigation menu
- Click your avatar → **"Create Tag"** for tags
- Edit or delete your content from the tasks/tags pages

## For API Developers

### 1. Register a User

```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
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
    "token": "1|abcdefghijklmnopqrstuvwxyz"
  }
}
```

**Save the token!** You'll need it for authenticated requests.

### 2. Login (Get a Token)

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### 3. Create a Task (Protected Route)

```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "type": "dare",
    "spice_rating": 3,
    "description": "Do 20 pushups right now!",
    "draft": false
  }'
```

### 4. Create a Tag (Protected Route)

```bash
curl -X POST http://localhost:8000/api/tags \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Funny",
    "description": "Funny and humorous tasks",
    "min_spice_level": 1,
    "is_default": false,
    "default_for_gender": "none"
  }'
```

### 5. Get Current User Info

```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### 6. Logout (Revoke Token)

```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

## Public Routes (No Authentication Required)

### View All Tasks
```bash
curl http://localhost:8000/api/tasks
```

### View All Tags
```bash
curl http://localhost:8000/api/tags
```

### Get Random Task
```bash
curl http://localhost:8000/api/tasks/random?type=dare&max_spice=3
```

### Get Task Statistics
```bash
curl http://localhost:8000/api/tasks/statistics
```

## Testing Script

Run the automated test script:

```bash
./test_auth.sh
```

This will test:
- User registration
- User login
- Creating tasks with authentication
- Creating tags with authentication
- Attempting to create without authentication (should fail)
- Logout functionality
- Token revocation

## Common Patterns

### Using Environment Variables

```bash
# Save token to environment variable
export TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password123"}' \
  | jq -r '.data.token')

# Use token in subsequent requests
curl -X POST http://localhost:8000/api/tasks \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"type":"dare","spice_rating":3,"description":"Jump 10 times","draft":false}'
```

### JavaScript/Fetch Example

```javascript
// Register
const registerResponse = await fetch('http://localhost:8000/api/register', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    name: 'John Doe',
    email: 'john@example.com',
    password: 'password123',
    password_confirmation: 'password123'
  })
});
const { data } = await registerResponse.json();
const token = data.token;

// Create Task
const taskResponse = await fetch('http://localhost:8000/api/tasks', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    type: 'dare',
    spice_rating: 3,
    description: 'Do 20 pushups',
    draft: false
  })
});
```

### Python Example

```python
import requests

# Register
response = requests.post('http://localhost:8000/api/register', json={
    'name': 'John Doe',
    'email': 'john@example.com',
    'password': 'password123',
    'password_confirmation': 'password123'
})
token = response.json()['data']['token']

# Create Task
headers = {
    'Authorization': f'Bearer {token}',
    'Content-Type': 'application/json',
    'Accept': 'application/json'
}
task_data = {
    'type': 'dare',
    'spice_rating': 3,
    'description': 'Do 20 pushups',
    'draft': False
}
task_response = requests.post(
    'http://localhost:8000/api/tasks',
    json=task_data,
    headers=headers
)
```

## Quick Reference

| Action | Requires Auth | Method | Endpoint |
|--------|--------------|---------|----------|
| Register | No | POST | `/api/register` |
| Login | No | POST | `/api/login` |
| Logout | Yes | POST | `/api/logout` |
| Get User | Yes | GET | `/api/me` |
| View Tasks | No | GET | `/api/tasks` |
| Create Task | Yes | POST | `/api/tasks` |
| Edit Task | Yes | PUT/PATCH | `/api/tasks/{id}` |
| Delete Task | Yes | DELETE | `/api/tasks/{id}` |
| View Tags | No | GET | `/api/tags` |
| Create Tag | Yes | POST | `/api/tags` |
| Edit Tag | Yes | PUT/PATCH | `/api/tags/{id}` |
| Delete Tag | Yes | DELETE | `/api/tags/{id}` |

## Tips

1. **Store tokens securely** - Don't expose tokens in client-side code
2. **Check token expiration** - Tokens don't expire by default in Sanctum, but you can configure this
3. **Handle 401 errors** - Redirect to login when you get "Unauthenticated" responses
4. **Use HTTPS in production** - Always use HTTPS for authentication in production
5. **Validate inputs** - The API validates all inputs and returns clear error messages

## Need Help?

- Check `AUTHENTICATION.md` for detailed documentation
- Check `AUTHENTICATION_IMPLEMENTATION.md` for technical details
- Run `./test_auth.sh` to verify your setup
- Check Laravel logs: `tail -f storage/logs/laravel.log`
