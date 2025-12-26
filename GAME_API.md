# Game and Player API Documentation

This document describes the API endpoints for managing games and players in the YouDare Truth or Dare game.

## Overview

The game system allows you to:
- Create game sessions with unique join codes
- Add players to games
- Set tags "in play" at the game level (applies to all players)
- Set player-specific tags (individual preferences)
- Fetch tasks filtered by game + player tags
- Track scores and manage game state

## Game Flow

1. **Create a game** with optional tags and settings
2. **Add players** to the game with optional player-specific tags
3. **Start the game** when all players have joined
4. **Get random tasks** for each player based on combined game + player tags
5. **Track scores** and progress
6. **Complete the game** when finished

## API Endpoints

### Games

#### Create a Game
```http
POST /api/games
```

**Request Body:**
```json
{
  "name": "Friday Night Fun",
  "max_spice_rating": 3,
  "tag_ids": [1, 6, 7],
  "settings": {
    "rounds": 10,
    "custom_setting": "value"
  }
}
```

**Response:**
```json
{
  "success": true,
  "message": "Game created successfully",
  "data": {
    "id": 1,
    "name": "Friday Night Fun",
    "code": "ABC123",
    "status": "waiting",
    "max_spice_rating": 3,
    "settings": {
      "rounds": 10,
      "custom_setting": "value"
    },
    "tags": [
      {"id": 1, "name": "Adults Only", "slug": "adults-only"},
      {"id": 6, "name": "Funny", "slug": "funny"},
      {"id": 7, "name": "Physical", "slug": "physical"}
    ],
    "players": [],
    "created_at": "2025-12-26T10:00:00.000000Z",
    "updated_at": "2025-12-26T10:00:00.000000Z"
  }
}
```

#### List All Games
```http
GET /api/games
```

**Query Parameters:**
- `status` (optional): Filter by status (`waiting`, `active`, `completed`)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Friday Night Fun",
      "code": "ABC123",
      "status": "waiting",
      "max_spice_rating": 3,
      "tags": [...],
      "players": [...]
    }
  ]
}
```

#### Get Game by ID
```http
GET /api/games/{game}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Friday Night Fun",
    "code": "ABC123",
    "status": "waiting",
    "max_spice_rating": 3,
    "tags": [...],
    "players": [
      {
        "id": 1,
        "name": "Alice",
        "score": 0,
        "is_active": true,
        "order": 0,
        "tags": [...]
      }
    ]
  }
}
```

#### Find Game by Code
```http
POST /api/games/find
```

**Request Body:**
```json
{
  "code": "ABC123"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Friday Night Fun",
    "code": "ABC123",
    "status": "waiting",
    "players": [...]
  }
}
```

#### Update Game
```http
PUT /api/games/{game}
```

**Request Body:**
```json
{
  "name": "Updated Name",
  "status": "active",
  "max_spice_rating": 4,
  "settings": {}
}
```

#### Delete Game
```http
DELETE /api/games/{game}
```

#### Start Game
```http
POST /api/games/{game}/start
```

**Requirements:**
- Game must be in `waiting` status
- At least 2 players must be added

**Response:**
```json
{
  "success": true,
  "message": "Game started successfully",
  "data": {
    "id": 1,
    "status": "active",
    ...
  }
}
```

#### Complete Game
```http
POST /api/games/{game}/complete
```

**Requirements:**
- Game must be in `active` status

#### Sync Game Tags
Replace all game tags with new ones.

```http
POST /api/games/{game}/tags/sync
```

**Request Body:**
```json
{
  "tag_ids": [1, 2, 6, 7]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Game tags updated successfully",
  "data": {
    "id": 1,
    "tags": [
      {"id": 1, "name": "Adults Only"},
      {"id": 2, "name": "Family Friendly"},
      {"id": 6, "name": "Funny"},
      {"id": 7, "name": "Physical"}
    ]
  }
}
```

#### Attach Tag to Game
```http
POST /api/games/{game}/tags/{tag}/attach
```

**Response:**
```json
{
  "success": true,
  "message": "Tag attached to game successfully",
  "data": {...}
}
```

#### Detach Tag from Game
```http
DELETE /api/games/{game}/tags/{tag}/detach
```

#### Get Available Tasks for Game
Get all tasks that match the game's tags and max spice rating.

```http
GET /api/games/{game}/tasks
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "type": "dare",
      "spice_rating": 2,
      "description": "Do 20 jumping jacks",
      "tags": [...]
    }
  ],
  "count": 45
}
```

### Players

#### Add Player to Game
```http
POST /api/games/{game}/players
```

**Request Body:**
```json
{
  "name": "Alice",
  "tag_ids": [4, 9]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Player added successfully",
  "data": {
    "id": 1,
    "game_id": 1,
    "name": "Alice",
    "score": 0,
    "is_active": true,
    "order": 0,
    "tags": [
      {"id": 4, "name": "Romantic", "slug": "romantic"},
      {"id": 9, "name": "Creative", "slug": "creative"}
    ]
  }
}
```

#### List Players in Game
```http
GET /api/games/{game}/players
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Alice",
      "score": 5,
      "tags": [...]
    },
    {
      "id": 2,
      "name": "Bob",
      "score": 3,
      "tags": [...]
    }
  ]
}
```

#### Get Player Details
```http
GET /api/players/{player}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "game_id": 1,
    "name": "Alice",
    "score": 5,
    "is_active": true,
    "order": 0,
    "game": {
      "id": 1,
      "name": "Friday Night Fun",
      "status": "active"
    },
    "tags": [...]
  }
}
```

#### Update Player
```http
PUT /api/players/{player}
```

**Request Body:**
```json
{
  "name": "Alice Smith",
  "score": 10,
  "is_active": true,
  "order": 1
}
```

#### Delete Player
```http
DELETE /api/players/{player}
```

#### Sync Player Tags
Replace all player tags with new ones.

```http
POST /api/players/{player}/tags/sync
```

**Request Body:**
```json
{
  "tag_ids": [4, 6, 9]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Player tags updated successfully",
  "data": {
    "id": 1,
    "name": "Alice",
    "tags": [
      {"id": 4, "name": "Romantic"},
      {"id": 6, "name": "Funny"},
      {"id": 9, "name": "Creative"}
    ]
  }
}
```

#### Attach Tag to Player
```http
POST /api/players/{player}/tags/{tag}/attach
```

#### Detach Tag from Player
```http
DELETE /api/players/{player}/tags/{tag}/detach
```

#### Get Available Tasks for Player
Get all tasks that match the player's combined tags (game tags + player tags).

```http
GET /api/players/{player}/tasks
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "type": "truth",
      "spice_rating": 2,
      "description": "What is your favorite movie?",
      "tags": [...]
    }
  ],
  "count": 32
}
```

#### Get Random Task for Player
Get a random task for the player based on their combined tags.

```http
GET /api/players/{player}/tasks/random?type=truth
```

**Query Parameters:**
- `type` (optional): Filter by task type (`truth` or `dare`)

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 15,
    "type": "truth",
    "spice_rating": 2,
    "description": "Have you ever had a crush on a teacher?",
    "tags": [
      {"id": 2, "name": "Family Friendly"},
      {"id": 6, "name": "Funny"}
    ]
  }
}
```

#### Increment Player Score
```http
POST /api/players/{player}/score
```

**Request Body:**
```json
{
  "points": 1
}
```

**Response:**
```json
{
  "success": true,
  "message": "Score updated successfully",
  "data": {
    "id": 1,
    "name": "Alice",
    "score": 6
  }
}
```

## Tag Filtering Logic

### How Tags Work

Tags can be set at two levels:

1. **Game Level** (applies to all players)
2. **Player Level** (specific to individual players)

When fetching tasks for a player, the system combines both:
- Game tags + Player tags = Available tags for that player

### Example Scenario

**Game Setup:**
```json
{
  "name": "Party Game",
  "max_spice_rating": 3,
  "tags": [
    {"id": 3, "name": "Party Mode"},
    {"id": 6, "name": "Funny"}
  ]
}
```

**Player 1 (Alice):**
```json
{
  "name": "Alice",
  "tags": [
    {"id": 4, "name": "Romantic"}
  ]
}
```

**Player 2 (Bob):**
```json
{
  "name": "Bob",
  "tags": [
    {"id": 7, "name": "Physical"},
    {"id": 5, "name": "Extreme"}
  ]
}
```

**Result:**
- **Alice** sees tasks tagged with: `Party Mode`, `Funny`, OR `Romantic`
- **Bob** sees tasks tagged with: `Party Mode`, `Funny`, `Physical`, OR `Extreme`
- Both are limited to `spice_rating <= 3`

### Use Cases

1. **All Players Share Same Tags**
   - Set tags at game level only
   - Don't set player-specific tags

2. **Players Have Different Preferences**
   - Set common tags at game level (e.g., "Party Mode")
   - Add individual preferences at player level (e.g., "Romantic" for couples)

3. **Mix of Both**
   - Game tags: General categories everyone wants
   - Player tags: Individual comfort levels or interests

## Example Game Session

### Step 1: Create Game
```bash
POST /api/games
{
  "name": "Friday Fun",
  "max_spice_rating": 3,
  "tag_ids": [3, 6]  # Party Mode, Funny
}
# Response: code = "ABC123"
```

### Step 2: Add Players
```bash
POST /api/games/1/players
{
  "name": "Alice",
  "tag_ids": [4]  # Romantic
}

POST /api/games/1/players
{
  "name": "Bob",
  "tag_ids": [7]  # Physical
}
```

### Step 3: Start Game
```bash
POST /api/games/1/start
```

### Step 4: Get Task for Player
```bash
GET /api/players/1/tasks/random?type=truth
# Returns a truth task tagged with Party Mode, Funny, OR Romantic
```

### Step 5: Complete Task & Update Score
```bash
POST /api/players/1/score
{
  "points": 1
}
```

### Step 6: Complete Game
```bash
POST /api/games/1/complete
```

## Error Responses

All endpoints return consistent error responses:

```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

**Common HTTP Status Codes:**
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `404` - Not Found
- `422` - Validation Error

## Database Schema

### Games Table
- `id` - Primary key
- `name` - Game name (nullable)
- `code` - 6-character join code (unique)
- `status` - Game status: `waiting`, `active`, `completed`
- `max_spice_rating` - Maximum spice rating (1-5)
- `settings` - JSON field for custom settings
- `created_at`, `updated_at`

### Players Table
- `id` - Primary key
- `game_id` - Foreign key to games
- `name` - Player name
- `score` - Player score (default: 0)
- `is_active` - Active status (default: true)
- `order` - Turn order (0-based)
- `created_at`, `updated_at`

### Pivot Tables
- `game_tag` - Links games to tags
- `player_tag` - Links players to tags

## Best Practices

1. **Set Game Tags First**
   - Define common tags before adding players
   - Keeps the game consistent

2. **Add Player-Specific Tags Sparingly**
   - Only for genuine individual preferences
   - Too many different tags can fragment the task pool

3. **Test Tag Combinations**
   - Check available task count before starting
   - Use `GET /api/games/{game}/tasks` or `GET /api/players/{player}/tasks`

4. **Balance Max Spice Rating**
   - Lower ratings limit available tasks
   - Higher ratings include all lower-rated tasks too

5. **Manage Game State**
   - Start game only when all players are ready
   - Complete game to archive it

## Tips

- **Short Game Codes**: 6 characters, easy to share
- **Tag Inheritance**: Players inherit game tags automatically
- **Spice Filtering**: Max spice rating filters at query time
- **Turn Order**: Use `order` field to track whose turn it is
- **Scoring**: Track points however you want (customizable)

## Next Steps

1. Integrate with your frontend
2. Add WebSocket support for real-time updates
3. Create game templates with pre-configured tags
4. Add game history and statistics

---

For more information, see:
- [Tag System Documentation](TAGGING_SYSTEM.md)
- [Task API Documentation](API_EXAMPLES.md)
- [Quick Start Guide](TAGS_QUICKSTART.md)