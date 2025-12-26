# UI Quick Start Guide - Tag-Based Game System

## Overview

The YouDare game now includes a complete tag-based system that allows you to:
- Create game sessions with specific tags "in play"
- Add players with individual tag preferences
- Filter tasks based on combined game + player tags
- Track scores and manage game state

## Getting Started

### 1. Access the Game

Navigate to: `http://localhost:8000/game`

### 2. Game Setup Flow

#### Step 1: Add Players
- Add at least 2 players to start the game
- For each player, you must:
  - Enter their name
  - Select gender (optional, but recommended for auto-tags)
  - Assign tags to customize their experience
- Available tags include:
  - 🔞 Adults Only
  - 👨‍👩‍👧‍👦 Family Friendly
  - 🎉 Party Mode
  - 💕 Romantic
  - ⚡ Extreme
  - 😂 Funny
  - 🏃 Physical
  - 🧠 Mental
  - 🎨 Creative
  - 👥 Social
  - Gender-specific tags (Male, Female, Boxers, Bra, etc.)

**Example:**
- **Alice** (Female) - Gets auto-tags: Female, Bra, Skirt, Dress, Panties
  - Manually add: "Romantic", "Party Mode"
- **Bob** (Male) - Gets auto-tags: Male, Boxers
  - Manually add: "Physical", "Extreme", "Party Mode"
- **Carol** (Female) - Gets auto-tags: Female, Bra, Skirt, Dress, Panties
  - Manually add: "Funny", "Party Mode"

**Note:** All players in a game should share at least one common tag (e.g., "Party Mode") to ensure they all see relevant tasks.

#### Step 2: Configure Settings
- **Game Name:** Optional name for your game (e.g., "Friday Night Fun")
- **Max Spice Level:** Choose from 1-5 (limits task intensity)
  - 🌶️ Mild (1)
  - 🌶️🌶️ Medium (2)
  - 🌶️🌶️🌶️ Hot (3)
  - 🌶️🌶️🌶️🌶️ Extra Hot (4)
  - 🌶️🌶️🌶️🌶️🌶️ Extreme (5)

#### Step 3: Start Game
- Click "Create & Start Game" button
- Game must have at least 2 players

### 3. Playing the Game

#### Game Screen Features
- **Player Scoreboard:** Shows all players with current scores
- **Current Turn:** Highlighted player (scaled up, primary color)
- **Task Card:** Displays the current truth or dare
- **Task Tags:** Shows which tags the task matches
- **Spice Level:** Visual indicator of task intensity

#### During Your Turn
1. Read the task (Truth question or Dare challenge)
2. Choose action:
   - **✓ Completed!** - Mark as done, gain 1 point, next player's turn
   - **Skip** - Skip task, no points, next player's turn

#### Task Type Filter
Switch between:
- **💬🎯 Both** - Get truths and dares
- **💬 Truth** - Only truth questions
- **🎯 Dare** - Only dare challenges

#### Game Stats
Track your progress:
- **Round:** Current round number
- **Completed:** Total completed tasks
- **Skipped:** Total skipped tasks

### 4. Ending the Game

1. Click "End Game" button
2. View final scores and standings
3. See winner with 🥇 trophy
4. Options:
   - **Play Again** - Start a new game
   - **Back to Home** - Return to main page

## Tag Filtering Logic

### How Tags Work

**Player Tags** (the only tags that matter)
- Each player has their own set of tags
- Tags are assigned during player creation
- Gender selection auto-assigns relevant tags
- Additional tags can be manually selected
- Example: Alice (Female) gets auto-tags plus manually adds "Romantic", "Party Mode"

**Task Filtering** (what tasks a player can see)
- Tasks must have at least one tag matching the player's tags
- The game combines ALL player tags from ALL players
- If any player has a tag, tasks with that tag can appear
- Example: If Alice has "Romantic" and Bob has "Physical", tasks with either tag can appear

### Example Scenarios

#### Scenario 1: Party Game
```
Alice (Female) Tags: [Female, Bra, Skirt, Dress, Panties, Party Mode, Romantic]
Bob (Male) Tags: [Male, Boxers, Party Mode, Physical, Extreme]
Carol (Female) Tags: [Female, Bra, Skirt, Dress, Panties, Party Mode, Funny]

Combined Game Tags: All unique tags from all players
- Party Mode, Romantic, Physical, Extreme, Funny, Female, Male, Bra, Skirt, Dress, Panties, Boxers

Available Tasks: Any task that has at least one of these tags
```

#### Scenario 2: Couples Game
```
Sarah (Female) Tags: [Female, Bra, Skirt, Dress, Panties, Adults Only, Romantic, Extreme]
Mike (Male) Tags: [Male, Boxers, Adults Only, Romantic, Extreme]

Combined Game Tags: Adults Only, Romantic, Extreme, Female, Male, Bra, Skirt, Dress, Panties, Boxers

Available Tasks: Any task matching these tags
- Max spice = 5 (all intensities allowed)
```

#### Scenario 3: Family Game
```
Mom (Female) Tags: [Female, Bra, Skirt, Dress, Panties, Family Friendly, Funny]
Dad (Male) Tags: [Male, Boxers, Family Friendly, Funny]
Kid1 Tags: [Family Friendly, Funny]
Kid2 Tags: [Family Friendly, Funny]

Combined Game Tags: Family Friendly, Funny, Female, Male, Bra, Skirt, Dress, Panties, Boxers

Available Tasks: Any task matching these tags
- Max spice = 2 (keeps it mild)
```

## Development Setup

### Build Frontend Assets
```bash
npm run build
```

### Watch for Changes (Development)
```bash
npm run dev
```

### Clear Cache
```bash
php artisan cache:clear
php artisan view:clear
```

## Component Architecture

### Vue Components

1. **GameManager.vue** - Main wrapper component
   - Manages game state (setup → play → results)
   - Routes between child components

2. **GameSetup.vue** - Game creation and configuration
   - Player management with gender selection
   - Player tag assignment (each player gets their own tags)
   - Game settings (name, max spice level)

3. **GamePlay.vue** - Active gameplay
   - Task display
   - Score tracking
   - Turn management
   - Task type filtering

### API Integration

The UI communicates with these API endpoints:

**Games:**
- `POST /api/games` - Create game
- `GET /api/games/{id}` - Get game details
- `POST /api/games/{id}/start` - Start game
- `POST /api/games/{id}/complete` - End game

**Players:**
- `POST /api/games/{id}/players` - Add player
- `GET /api/games/{id}/players` - List players
- `POST /api/players/{id}/score` - Update score

**Tasks:**
- `GET /api/players/{id}/tasks/random` - Get random task for player

**Tags:**
- `GET /api/tags` - List all available tags

## Troubleshooting

### Issue: Tags not loading
**Solution:** Check that tags are seeded
```bash
php artisan db:seed --class=TagSeeder
```

### Issue: No tasks available
**Solution:** 
1. Check that tasks are seeded and have tags
2. Verify spice level isn't too restrictive
3. Try adding more game tags

### Issue: Vue component not mounting
**Solution:**
1. Check browser console for errors
2. Rebuild assets: `npm run build`
3. Clear browser cache

### Issue: Players can't join
**Solution:** Ensure at least 2 players before starting

## Best Practices

### Tag Selection
1. **Choose Gender** - Select gender for auto-tags (recommended)
2. **Add Common Tags** - Ensure all players share at least one tag
3. **Customize Individually** - Add tags specific to each player's preferences
4. **Test Filters** - Check that players have compatible tags before starting

### Spice Levels
1. **Family Games** - Use level 1-2
2. **Party Games** - Use level 2-3
3. **Adult Games** - Use level 4-5

### Game Management
1. **Clear Names** - Give your game a descriptive name
2. **Player Order** - Alphabetical or seating order works well
3. **Score Tracking** - Use completed tasks as score

## Advanced Features

### Game Codes
- Every game gets a unique 6-character code
- Players can join using the code (future feature)
- Displayed during gameplay

### Tag Customization
- Each player has their own set of tags
- All player tags are combined to determine available tasks
- Gender selection auto-assigns appropriate clothing and gender tags

### Score System
- Each completed task = 1 point
- Skipped tasks = 0 points
- Winner is player with most points

## Next Steps

1. **Try Different Tag Combinations** - Experiment with various tag sets
2. **Create Custom Tasks** - Add tasks with specific tags at `/tasks/create`
3. **Check API Documentation** - See `GAME_API.md` for full API reference
4. **Run Examples** - Test backend with `php example_game_usage.php`

## Resources

- **Full API Docs:** `GAME_API.md`
- **Tag System Guide:** `TAGGING_SYSTEM.md`
- **Backend Examples:** `example_game_usage.php`
- **Quick Reference:** `TAGS_QUICK_REFERENCE.md`

---

**Need Help?**
Check the browser console for errors, and ensure all migrations and seeds are up to date:
```bash
php artisan migrate:fresh --seed
npm run build
```

Enjoy your tag-based Truth or Dare game! 🎮🔥