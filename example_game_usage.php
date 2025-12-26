<?php

/**
 * Example Game Usage Script
 *
 * This script demonstrates how to use the game and player system
 * with tags to create a personalized Truth or Dare game.
 */

require __DIR__ . "/vendor/autoload.php";

use App\Models\Game;
use App\Models\Player;
use App\Models\Tag;
use App\Models\Task;

// Initialize Laravel
$app = require_once __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== YouDare Game System Example ===\n\n";

// ============================================
// SCENARIO 1: Create a Party Game
// ============================================
echo "SCENARIO 1: Party Game with Mixed Preferences\n";
echo str_repeat("-", 50) . "\n\n";

// Step 1: Create a new game
echo "1. Creating a new game...\n";
$game = Game::create([
    "name" => "Friday Night Party",
    "max_spice_rating" => 3, // Keep it moderate
]);

echo "   ✓ Game created: {$game->name}\n";
echo "   ✓ Join Code: {$game->code}\n";
echo "   ✓ Max Spice: {$game->max_spice_rating}\n\n";

// Step 2: Set game-wide tags (tags that apply to everyone)
echo "2. Setting game-wide tags...\n";
$gameTags = Tag::whereIn("slug", ["party-mode", "funny", "social"])->get();
$game->tags()->sync($gameTags->pluck("id"));

echo "   ✓ Game tags: " . $gameTags->pluck("name")->join(", ") . "\n\n";

// Step 3: Add players with individual preferences
echo "3. Adding players...\n\n";

// Player 1: Alice - likes romantic content
$alice = $game->players()->create([
    "name" => "Alice",
    "order" => 0,
]);
$aliceTags = Tag::whereIn("slug", ["romantic"])->pluck("id");
$alice->tags()->sync($aliceTags);
echo "   Player: Alice\n";
echo "   - Personal tags: Romantic\n";
echo "   - Combined tags: Party Mode, Funny, Social, Romantic\n\n";

// Player 2: Bob - likes physical challenges
$bob = $game->players()->create([
    "name" => "Bob",
    "order" => 1,
]);
$bobTags = Tag::whereIn("slug", ["physical", "extreme"])->pluck("id");
$bob->tags()->sync($bobTags);
echo "   Player: Bob\n";
echo "   - Personal tags: Physical, Extreme\n";
echo "   - Combined tags: Party Mode, Funny, Social, Physical, Extreme\n\n";

// Player 3: Carol - no additional tags
$carol = $game->players()->create([
    "name" => "Carol",
    "order" => 2,
]);
echo "   Player: Carol\n";
echo "   - Personal tags: None\n";
echo "   - Combined tags: Party Mode, Funny, Social (inherits from game)\n\n";

// Step 4: Check available tasks for each player
echo "4. Checking available tasks...\n\n";

$aliceTaskCount = $alice->getAvailableTasks()->count();
$bobTaskCount = $bob->getAvailableTasks()->count();
$carolTaskCount = $carol->getAvailableTasks()->count();

echo "   Alice: {$aliceTaskCount} tasks available\n";
echo "   Bob: {$bobTaskCount} tasks available\n";
echo "   Carol: {$carolTaskCount} tasks available\n\n";

// Step 5: Start the game
echo "5. Starting the game...\n";
$game->start();
echo "   ✓ Game status: {$game->status}\n\n";

// Step 6: Simulate a round for each player
echo "6. Playing a round...\n\n";

// Alice's turn
$aliceTask = $alice->getRandomTask("truth");
if ($aliceTask) {
    echo "   ALICE'S TURN (Truth):\n";
    echo "   Q: {$aliceTask->description}\n";
    echo "   Spice: {$aliceTask->spice_rating}/5\n";
    echo "   Tags: " . $aliceTask->tags->pluck("name")->join(", ") . "\n";
    $alice->incrementScore();
    echo "   ✓ Alice completed the task! Score: {$alice->score}\n\n";
}

// Bob's turn
$bobTask = $bob->getRandomTask("dare");
if ($bobTask) {
    echo "   BOB'S TURN (Dare):\n";
    echo "   Challenge: {$bobTask->description}\n";
    echo "   Spice: {$bobTask->spice_rating}/5\n";
    echo "   Tags: " . $bobTask->tags->pluck("name")->join(", ") . "\n";
    $bob->incrementScore();
    echo "   ✓ Bob completed the dare! Score: {$bob->score}\n\n";
}

// Carol's turn
$carolTask = $carol->getRandomTask();
if ($carolTask) {
    echo "   CAROL'S TURN ({$carolTask->type}):\n";
    echo "   " . ucfirst($carolTask->type) . ": {$carolTask->description}\n";
    echo "   Spice: {$carolTask->spice_rating}/5\n";
    echo "   Tags: " . $carolTask->tags->pluck("name")->join(", ") . "\n";
    $carol->incrementScore();
    echo "   ✓ Carol completed the task! Score: {$carol->score}\n\n";
}

// Step 7: Show final scores
echo "7. Current Scores:\n";
$game->load("players");
foreach ($game->players as $player) {
    echo "   {$player->name}: {$player->score} point(s)\n";
}
echo "\n";

// ============================================
// SCENARIO 2: Couples Game (Adults Only)
// ============================================
echo "\n" . str_repeat("=", 50) . "\n";
echo "SCENARIO 2: Romantic Couples Game\n";
echo str_repeat("-", 50) . "\n\n";

// Create adults-only game
echo "1. Creating romantic game for couples...\n";
$couplesGame = Game::create([
    "name" => "Date Night Dares",
    "max_spice_rating" => 5, // Allow all spice levels
]);

echo "   ✓ Game: {$couplesGame->name}\n";
echo "   ✓ Code: {$couplesGame->code}\n\n";

// Set romantic tags
echo "2. Setting romantic game tags...\n";
$romanticTags = Tag::whereIn("slug", ["adults-only", "romantic"])->get();
$couplesGame->tags()->sync($romanticTags->pluck("id"));
echo "   ✓ Tags: " . $romanticTags->pluck("name")->join(", ") . "\n\n";

// Add couple
echo "3. Adding players...\n";
$player1 = $couplesGame->players()->create(["name" => "Sarah", "order" => 0]);
$player2 = $couplesGame->players()->create(["name" => "Mike", "order" => 1]);
echo "   ✓ Sarah and Mike joined\n\n";

$taskCount = $couplesGame->getAvailableTasks()->count();
echo "4. Available tasks: {$taskCount}\n\n";

// ============================================
// SCENARIO 3: Family Friendly Game
// ============================================
echo "\n" . str_repeat("=", 50) . "\n";
echo "SCENARIO 3: Family Friendly Game\n";
echo str_repeat("-", 50) . "\n\n";

echo "1. Creating family game...\n";
$familyGame = Game::create([
    "name" => "Family Game Night",
    "max_spice_rating" => 2, // Keep it mild
]);

echo "   ✓ Game: {$familyGame->name}\n";
echo "   ✓ Code: {$familyGame->code}\n\n";

echo "2. Setting family-friendly tags...\n";
$familyTags = Tag::whereIn("slug", ["family-friendly", "funny"])->get();
$familyGame->tags()->sync($familyTags->pluck("id"));
echo "   ✓ Tags: " . $familyTags->pluck("name")->join(", ") . "\n\n";

echo "3. Adding family members...\n";
$familyGame->players()->create(["name" => "Mom", "order" => 0]);
$familyGame->players()->create(["name" => "Dad", "order" => 1]);
$familyGame->players()->create(["name" => "Tommy (12)", "order" => 2]);
$familyGame->players()->create(["name" => "Sarah (10)", "order" => 3]);
echo "   ✓ 4 players added\n\n";

$taskCount = $familyGame->getAvailableTasks()->count();
echo "4. Available tasks: {$taskCount}\n";
echo "   (Only spice level 1-2, family-friendly content)\n\n";

// ============================================
// SCENARIO 4: Find Game by Code
// ============================================
echo "\n" . str_repeat("=", 50) . "\n";
echo "SCENARIO 4: Join Game Using Code\n";
echo str_repeat("-", 50) . "\n\n";

echo "1. Someone wants to join the party game...\n";
echo "   Join code: {$game->code}\n\n";

$foundGame = Game::findByCode($game->code);
if ($foundGame) {
    echo "   ✓ Game found: {$foundGame->name}\n";
    echo "   ✓ Status: {$foundGame->status}\n";
    echo "   ✓ Current players: {$foundGame->players()->count()}\n\n";

    echo "2. Adding new player...\n";
    $newPlayer = $foundGame->players()->create([
        "name" => "Dave",
        "order" => 3,
    ]);
    echo "   ✓ Dave joined the game!\n\n";
}

// ============================================
// SCENARIO 5: Dynamic Tag Management
// ============================================
echo "\n" . str_repeat("=", 50) . "\n";
echo "SCENARIO 5: Changing Tags Mid-Game\n";
echo str_repeat("-", 50) . "\n\n";

echo "1. Players want to add 'Extreme' tag to make it spicier...\n";
$extremeTag = Tag::where("slug", "extreme")->first();

$beforeCount = $game->getAvailableTasks()->count();
echo "   Tasks before: {$beforeCount}\n";

$game->tags()->attach($extremeTag->id);
$game->load("tags");

$afterCount = $game->getAvailableTasks()->count();
echo "   Tasks after: {$afterCount}\n";
$difference = $afterCount - $beforeCount;
echo "   ✓ {$difference} more tasks available!\n\n";

// ============================================
// SUMMARY
// ============================================
echo "\n" . str_repeat("=", 50) . "\n";
echo "SUMMARY\n";
echo str_repeat("=", 50) . "\n\n";

echo "✓ Created 3 different game types:\n";
echo "  1. Party game with individual player preferences\n";
echo "  2. Romantic couples game (adults only)\n";
echo "  3. Family-friendly game (low spice)\n\n";

echo "✓ Demonstrated:\n";
echo "  - Game-wide tags (apply to all players)\n";
echo "  - Player-specific tags (individual preferences)\n";
echo "  - Tag inheritance (players get game tags + their own)\n";
echo "  - Dynamic tag management (add/remove during game)\n";
echo "  - Task filtering based on combined tags\n";
echo "  - Score tracking\n";
echo "  - Game state management (waiting → active → completed)\n";
echo "  - Finding games by code\n\n";

echo "Key Takeaways:\n";
echo "  • Game tags = shared preferences for all players\n";
echo "  • Player tags = individual customization\n";
echo "  • Combined tags = game tags + player tags\n";
echo "  • Max spice rating filters tasks globally\n";
echo "  • Players can join using 6-character game codes\n\n";

// Clean up (optional - comment out to keep data)
echo "Cleaning up test data...\n";
Game::whereIn("id", [$game->id, $couplesGame->id, $familyGame->id])->delete();
echo "✓ Done!\n\n";

echo "=== Example Complete ===\n";
