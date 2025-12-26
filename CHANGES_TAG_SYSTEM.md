# Tag-Based Game System - Changes Summary

## What Was Added

This update adds a complete game session and player management system with tag-based task filtering to the YouDare Truth or Dare application.

## New Features

### 1. Game Sessions
- Create game sessions with unique join codes
- Set maximum spice rating per game
- Configure game-wide tags that apply to all players
- Track game status (waiting, active, completed)
- Start and complete games through the UI

### 2. Player Management
- Add multiple players to each game
- Assign player-specific tags for individual preferences
- Track player scores automatically
- Manage turn order
- Each player sees tasks filtered by their combined tags

### 3. Tag System Integration
- **Game-Level Tags**: Apply to all players in the game
- **Player-Level Tags**: Individual preferences per player
- **Combined Filtering**: Tasks match if they have any tag from (game tags + player tags)
- Real-time tag-based task filtering

### 4. Enhanced UI
- Modern, step-by-step game setup flow
- Visual tag selection with emojis
- Player management with expandable tag customization
- Live scoreboard during gameplay
- Task type filtering (Truth/Dare/Both)
- End game summary with winner celebration

## Database Changes

### New Tables
1. **games** - Game sessions
   - `id`, `name`, `code`, `status`, `max_spice_rating`, `settings`
   
2. **players** - Players in games
   - `id`, `game_id`, `name`, `score`, `is_active`, `order`
   
3. **game_tag** - Pivot table for game tags
   - `game_id`, `tag_id` (many-to-many)
   
4. **player_tag** - Pivot table for player tags
   - `player_id`, `tag_id` (many-to-many)

### Updated Tables
- Tasks already have tags via `task_tag` table
- Users already have tags via `tag_user` table (for future auth features)

## New Backend Files

### Models
- `app/Models/Game.php` - Game session model with relationships
- `app/Models/Player.php` - Player model with tag filtering

### Controllers
- `app/Http/Controllers/Api/GameController.php` - Game CRUD and management
- `app/Http/Controllers/Api/PlayerController.php` - Player CRUD and task retrieval

### Migrations
- `2025_12_26_160453_create_games_table.php`
- `2025_12_26_160453_create_players_table.php`
- `2025_12_26_160457_create_game_tag_table.php`
- `2025_12_26_160457_create_player_tag_table.php`

### Documentation
- `GAME_API.md` - Complete API documentation (708 lines)
- `UI_QUICKSTART.md` - UI user guide (291 lines)
- `example_game_usage.php` - Backend examples (284 lines)

## New Frontend Files

### Vue Components
- `resources/js/components/GameManager.vue` - Main game wrapper
- `resources/js/components/GameSetup.vue` - Setup screen with tag selection
- `resources/js/components/GamePlay.vue` - Active gameplay screen

### Updates
- `resources/js/app.js` - Added GameManager component registration
- `resources/views/game.blade.php` - Uses new GameManager component

## API Endpoints

### Game Management
```
POST   /api/games                      - Create game
GET    /api/games                      - List games
GET    /api/games/{game}               - Get game details
PUT    /api/games/{game}               - Update game
DELETE /api/games/{game}               - Delete game
POST   /api/games/find                 - Find by code
POST   /api/games/{game}/start         - Start game
POST   /api/games/{game}/complete      - Complete game
```

### Game Tags
```
POST   /api/games/{game}/tags/sync            - Replace all tags
POST   /api/games/{game}/tags/{tag}/attach    - Add tag
DELETE /api/games/{game}/tags/{tag}/detach    - Remove tag
GET    /api/games/{game}/tasks                - Get available tasks
```

### Player Management
```
POST   /api/games/{game}/players              - Add player to game
GET    /api/games/{game}/players              - List game players
GET    /api/players/{player}                  - Get player details
PUT    /api/players/{player}                  - Update player
DELETE /api/players/{player}                  - Remove player
POST   /api/players/{player}/score            - Update score
```

### Player Tags & Tasks
```
POST   /api/players/{player}/tags/sync           - Replace player tags
POST   /api/players/{player}/tags/{tag}/attach   - Add tag to player
DELETE /api/players/{player}/tags/{tag}/detach   - Remove tag from player
GET    /api/players/{player}/tasks               - Get available tasks
GET    /api/players/{player}/tasks/random        - Get random task
```

## Updated Seeders

### TaskSeeder.php
- All seeded tasks now have appropriate tags assigned
- Tags are contextually matched to task content and spice level
- Examples:
  - Spice 1-2: Family Friendly, Funny, Mental/Physical
  - Spice 3: Party Mode, Social
  - Spice 4-5: Adults Only, Extreme, Romantic

## How It Works

### Game Flow
1. **Setup**: Create game → Select game tags → Add players → Assign player tags → Configure settings
2. **Play**: System shows tasks filtered by (game tags + current player's tags)
3. **Scoring**: Players earn points for completed tasks
4. **End**: View final scores and winner

### Tag Filtering Logic
```
Player's Available Tags = Game Tags ∪ Player's Personal Tags

Available Tasks = Tasks WHERE:
  - spice_rating ≤ game.max_spice_rating
  AND
  - task has at least ONE tag that matches player's available tags
```

### Example Scenario
```
Game: "Party Night"
  - Game Tags: [Party Mode, Funny, Social]
  - Max Spice: 3

Players:
  - Alice + [Romantic] = sees tasks with: Party Mode, Funny, Social, OR Romantic
  - Bob + [Physical, Extreme] = sees tasks with: Party Mode, Funny, Social, Physical, OR Extreme
  - Carol + [] = sees tasks with: Party Mode, Funny, OR Social

All limited to spice_rating ≤ 3
```

## Key Features

### Flexibility
- Game-wide tags for shared preferences
- Player-specific tags for individual customization
- Combine or use separately

### Inclusivity
- Players without personal tags still see game-tagged tasks
- Multiple tag combinations supported
- Spice level provides additional filtering

### User Experience
- Visual tag selection with emojis
- Real-time task availability
- Clear feedback on tag selections
- Score tracking and turn management

## Testing

### Manual Testing
1. Navigate to `/game`
2. Select some game tags
3. Add 2+ players with different personal tags
4. Start game and verify tasks match selected tags
5. Complete tasks and check scores update
6. End game and view results

### Backend Testing
```bash
php example_game_usage.php
```
This runs 5 scenarios demonstrating different tag combinations.

### API Testing
See `GAME_API.md` for cURL examples.

## Breaking Changes

### None
- Old game functionality (`GameScreen.vue`) still available
- New system runs alongside existing features
- Can be gradually adopted

## Migration Path

### To Use New System
1. Ensure database is migrated: `php artisan migrate`
2. Seed tags if not already: `php artisan db:seed --class=TagSeeder`
3. Seed tasks with tags: `php artisan db:seed --class=TaskSeeder`
4. Build frontend: `npm run build`
5. Navigate to `/game` - new UI automatically loads

### To Keep Old System
- Old `GameScreen.vue` component still exists
- Can be used by updating `game.blade.php` to use `<game-screen>` instead of `<game-manager>`

## Performance Considerations

- Database queries use eager loading for relationships
- Indexes on foreign keys in pivot tables
- Tag filtering at database level (not in-memory)
- Efficient query scopes on models

## Security

- CSRF token required for all POST/PUT/DELETE requests
- Input validation on all endpoints
- Foreign key constraints prevent orphaned records
- Cascading deletes for cleanup

## Future Enhancements

Potential additions:
- Join game by code (partially implemented)
- Real-time multiplayer via WebSockets
- Game history and statistics
- Custom tag creation by users
- Task recommendations based on playing history
- Mobile-optimized UI
- Game templates (pre-configured tag sets)

## Documentation

- **GAME_API.md** - Full API reference with examples
- **UI_QUICKSTART.md** - UI usage guide
- **example_game_usage.php** - Backend code examples
- **TAGGING_SYSTEM.md** - General tag system docs (existing)

## Support

For issues or questions:
1. Check browser console for errors
2. Verify migrations: `php artisan migrate:status`
3. Rebuild assets: `npm run build`
4. Review API responses for error messages

## Summary

This update transforms YouDare from a simple random task generator into a sophisticated multiplayer game platform with:
- ✅ Full game session management
- ✅ Player-specific tag preferences
- ✅ Advanced task filtering
- ✅ Modern, intuitive UI
- ✅ Complete API for integration
- ✅ Comprehensive documentation

All while maintaining backward compatibility with existing features.