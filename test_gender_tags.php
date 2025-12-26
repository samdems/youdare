<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\Game;
use App\Models\Player;
use App\Models\Tag;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Gender-Based Auto-Tagging Test ===\n\n";

// Create a test game
$game = Game::create([
    'name' => 'Gender Tag Test Game',
    'max_spice_rating' => 5,
    'current_player_id' => null,
]);

echo "Created game: {$game->name}\n\n";

// Test 1: Create a male player
echo "Test 1: Creating male player...\n";
$malePlayer = $game->players()->create([
    'name' => 'John',
    'gender' => 'male',
]);

$malePlayer->load('tags');
echo "Player: {$malePlayer->name}\n";
echo "Gender: {$malePlayer->gender}\n";
echo "Auto-assigned tags:\n";
foreach ($malePlayer->tags as $tag) {
    echo "  - {$tag->name} ({$tag->slug})\n";
}
echo "\n";

// Test 2: Create a female player
echo "Test 2: Creating female player...\n";
$femalePlayer = $game->players()->create([
    'name' => 'Jane',
    'gender' => 'female',
]);

$femalePlayer->load('tags');
echo "Player: {$femalePlayer->name}\n";
echo "Gender: {$femalePlayer->gender}\n";
echo "Auto-assigned tags:\n";
foreach ($femalePlayer->tags as $tag) {
    echo "  - {$tag->name} ({$tag->slug})\n";
}
echo "\n";

// Test 3: Update player gender
echo "Test 3: Changing John's gender to female...\n";
$malePlayer->update(['gender' => 'female']);
$malePlayer->load('tags');

echo "Player: {$malePlayer->name}\n";
echo "New Gender: {$malePlayer->gender}\n";
echo "Updated tags:\n";
foreach ($malePlayer->tags as $tag) {
    echo "  - {$tag->name} ({$tag->slug})\n";
}
echo "\n";

// Test 4: Create player without gender
echo "Test 4: Creating player without gender...\n";
$noGenderPlayer = $game->players()->create([
    'name' => 'Alex',
    'gender' => null,
]);

$noGenderPlayer->load('tags');
echo "Player: {$noGenderPlayer->name}\n";
echo "Gender: " . ($noGenderPlayer->gender ?? 'not set') . "\n";
echo "Tags: " . ($noGenderPlayer->tags->count() > 0 ? $noGenderPlayer->tags->pluck('name')->implode(', ') : 'none') . "\n";
echo "\n";

// Cleanup
echo "Cleaning up test data...\n";
$game->delete(); // This will cascade delete players

echo "\n=== Test Complete ===\n";
