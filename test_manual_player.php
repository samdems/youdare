<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\Game;
use App\Models\Player;
use App\Models\Tag;
use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Manual Player Creation Test ===\n\n";

// Check if tags exist first
echo "Checking if required tags exist...\n";
$maleTag = Tag::where('slug', 'male')->first();
$femaleTag = Tag::where('slug', 'female')->first();
$boxersTag = Tag::where('slug', 'boxers')->first();
$braTag = Tag::where('slug', 'bra')->first();
$skirtTag = Tag::where('slug', 'skirt')->first();
$dressTag = Tag::where('slug', 'dress')->first();
$pantiesTag = Tag::where('slug', 'panties')->first();

echo "Male tag: " . ($maleTag ? "EXISTS (ID: {$maleTag->id})" : "NOT FOUND") . "\n";
echo "Female tag: " . ($femaleTag ? "EXISTS (ID: {$femaleTag->id})" : "NOT FOUND") . "\n";
echo "Boxers tag: " . ($boxersTag ? "EXISTS (ID: {$boxersTag->id})" : "NOT FOUND") . "\n";
echo "Bra tag: " . ($braTag ? "EXISTS (ID: {$braTag->id})" : "NOT FOUND") . "\n";
echo "Skirt tag: " . ($skirtTag ? "EXISTS (ID: {$skirtTag->id})" : "NOT FOUND") . "\n";
echo "Dress tag: " . ($dressTag ? "EXISTS (ID: {$dressTag->id})" : "NOT FOUND") . "\n";
echo "Panties tag: " . ($pantiesTag ? "EXISTS (ID: {$pantiesTag->id})" : "NOT FOUND") . "\n\n";

// Create test game
$game = Game::create([
    'name' => 'Manual Test Game',
    'max_spice_rating' => 5,
]);

echo "Created game ID: {$game->id}\n\n";

// Test 1: Create male player and manually call assignGenderTags
echo "=== TEST 1: Male Player ===\n";
$malePlayer = $game->players()->create([
    'name' => 'TestMale',
    'gender' => 'male',
]);

echo "Player created: ID={$malePlayer->id}, Name={$malePlayer->name}, Gender={$malePlayer->gender}\n";
echo "Tags before assignGenderTags(): " . $malePlayer->tags()->count() . "\n";

// Manually call assignGenderTags like the controller does
echo "Calling assignGenderTags()...\n";
$malePlayer->assignGenderTags();

// Refresh to get latest data
$malePlayer->refresh();
$malePlayer->load('tags');

echo "Tags after assignGenderTags(): " . $malePlayer->tags()->count() . "\n";
echo "Tags assigned:\n";
foreach ($malePlayer->tags as $tag) {
    echo "  - {$tag->name} (slug: {$tag->slug}, id: {$tag->id})\n";
}
echo "\n";

// Test 2: Create female player and manually call assignGenderTags
echo "=== TEST 2: Female Player ===\n";
$femalePlayer = $game->players()->create([
    'name' => 'TestFemale',
    'gender' => 'female',
]);

echo "Player created: ID={$femalePlayer->id}, Name={$femalePlayer->name}, Gender={$femalePlayer->gender}\n";
echo "Tags before assignGenderTags(): " . $femalePlayer->tags()->count() . "\n";

// Manually call assignGenderTags like the controller does
echo "Calling assignGenderTags()...\n";
$femalePlayer->assignGenderTags();

// Refresh to get latest data
$femalePlayer->refresh();
$femalePlayer->load('tags');

echo "Tags after assignGenderTags(): " . $femalePlayer->tags()->count() . "\n";
echo "Tags assigned:\n";
foreach ($femalePlayer->tags as $tag) {
    echo "  - {$tag->name} (slug: {$tag->slug}, id: {$tag->id})\n";
}
echo "\n";

// Test 3: Player without gender
echo "=== TEST 3: No Gender Player ===\n";
$noGenderPlayer = $game->players()->create([
    'name' => 'TestNoGender',
    'gender' => null,
]);

echo "Player created: ID={$noGenderPlayer->id}, Name={$noGenderPlayer->name}, Gender=" . ($noGenderPlayer->gender ?? 'null') . "\n";
echo "Tags: " . $noGenderPlayer->tags()->count() . "\n";
if ($noGenderPlayer->gender) {
    echo "Would call assignGenderTags() but gender is null\n";
}
echo "\n";

// Cleanup
echo "Cleaning up...\n";
$game->delete();

echo "\n=== Test Complete ===\n";
