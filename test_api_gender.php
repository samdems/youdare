<?php

require __DIR__ . "/vendor/autoload.php";

use App\Models\Game;
use App\Models\Player;
use App\Models\Tag;

// Bootstrap Laravel
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

echo "=== API Gender Tag Test ===\n\n";

// Create a test game
$game = Game::create([
    "name" => "API Test Game",
    "max_spice_rating" => 5,
    "current_player_id" => null,
]);

echo "Created game ID: {$game->id}\n\n";

// Test 1: Create male player via API (simulating the request)
echo "Test 1: Creating male player with empty tag_ids array...\n";
$malePlayerData = [
    "name" => "TestMale",
    "gender" => "male",
    "tag_ids" => [], // This is what the UI sends
];

$malePlayer = $game->players()->create([
    "name" => $malePlayerData["name"],
    "gender" => $malePlayerData["gender"],
]);

// Auto-assign gender-specific tags if gender is set (like the controller does)
if ($malePlayer->gender) {
    $malePlayer->assignGenderTags();
}

// Simulate the controller logic for additional tags
if (
    isset($malePlayerData["tag_ids"]) &&
    is_array($malePlayerData["tag_ids"]) &&
    count($malePlayerData["tag_ids"]) > 0
) {
    $malePlayer->tags()->syncWithoutDetaching($malePlayerData["tag_ids"]);
}

$malePlayer->load("tags");

echo "Player: {$malePlayer->name}\n";
echo "Gender: {$malePlayer->gender}\n";
echo "Tags assigned:\n";
foreach ($malePlayer->tags as $tag) {
    echo "  - {$tag->name} ({$tag->slug})\n";
}
echo "\n";

// Test 2: Create female player via API
echo "Test 2: Creating female player with empty tag_ids array...\n";
$femalePlayerData = [
    "name" => "TestFemale",
    "gender" => "female",
    "tag_ids" => [],
];

$femalePlayer = $game->players()->create([
    "name" => $femalePlayerData["name"],
    "gender" => $femalePlayerData["gender"],
]);

// Auto-assign gender-specific tags if gender is set (like the controller does)
if ($femalePlayer->gender) {
    $femalePlayer->assignGenderTags();
}

// Simulate the controller logic for additional tags
if (
    isset($femalePlayerData["tag_ids"]) &&
    is_array($femalePlayerData["tag_ids"]) &&
    count($femalePlayerData["tag_ids"]) > 0
) {
    $femalePlayer->tags()->syncWithoutDetaching($femalePlayerData["tag_ids"]);
}

$femalePlayer->load("tags");

echo "Player: {$femalePlayer->name}\n";
echo "Gender: {$femalePlayer->gender}\n";
echo "Tags assigned:\n";
foreach ($femalePlayer->tags as $tag) {
    echo "  - {$tag->name} ({$tag->slug})\n";
}
echo "\n";

// Test 3: Create male player with additional tags
echo "Test 3: Creating male player with additional custom tags...\n";
$customTag = Tag::where("slug", "adults-only")->first();
$malePlayer2Data = [
    "name" => "TestMaleWithTags",
    "gender" => "male",
    "tag_ids" => [$customTag->id],
];

$malePlayer2 = $game->players()->create([
    "name" => $malePlayer2Data["name"],
    "gender" => $malePlayer2Data["gender"],
]);

// Auto-assign gender-specific tags if gender is set (like the controller does)
if ($malePlayer2->gender) {
    $malePlayer2->assignGenderTags();
}

// Simulate the controller logic for additional tags
if (
    isset($malePlayer2Data["tag_ids"]) &&
    is_array($malePlayer2Data["tag_ids"]) &&
    count($malePlayer2Data["tag_ids"]) > 0
) {
    $malePlayer2->tags()->syncWithoutDetaching($malePlayer2Data["tag_ids"]);
}

$malePlayer2->load("tags");

echo "Player: {$malePlayer2->name}\n";
echo "Gender: {$malePlayer2->gender}\n";
echo "Tags assigned:\n";
foreach ($malePlayer2->tags as $tag) {
    echo "  - {$tag->name} ({$tag->slug})\n";
}
echo "\n";

// Test 4: Create player without gender
echo "Test 4: Creating player without gender...\n";
$noGenderData = [
    "name" => "TestNoGender",
    "gender" => null,
    "tag_ids" => [],
];

$noGenderPlayer = $game->players()->create([
    "name" => $noGenderData["name"],
    "gender" => $noGenderData["gender"],
]);

// Auto-assign gender-specific tags if gender is set (like the controller does)
if ($noGenderPlayer->gender) {
    $noGenderPlayer->assignGenderTags();
}

// Simulate the controller logic for additional tags
if (
    isset($noGenderData["tag_ids"]) &&
    is_array($noGenderData["tag_ids"]) &&
    count($noGenderData["tag_ids"]) > 0
) {
    $noGenderPlayer->tags()->syncWithoutDetaching($noGenderData["tag_ids"]);
}

$noGenderPlayer->load("tags");

echo "Player: {$noGenderPlayer->name}\n";
echo "Gender: " . ($noGenderPlayer->gender ?? "not set") . "\n";
echo "Tags assigned: " .
    ($noGenderPlayer->tags->count() > 0
        ? $noGenderPlayer->tags->pluck("name")->implode(", ")
        : "none") .
    "\n";
echo "\n";

// Cleanup
echo "Cleaning up test data...\n";
$game->delete();

echo "\n=== All Tests Complete ===\n";
echo "\nSummary:\n";
echo "✓ Male players with empty tag_ids should get: Male + Boxers\n";
echo "✓ Female players with empty tag_ids should get: Female + Bra + Skirt + Dress + Panties\n";
echo "✓ Players with gender + additional tags should get: Gender tags + Additional tags\n";
echo "✓ Players without gender should get: No auto-assigned tags\n";
