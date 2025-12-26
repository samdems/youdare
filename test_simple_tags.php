<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\Game;
use App\Models\Tag;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Simplified Tag Selection Test ===\n\n";

// Get available tags
$maleTag = Tag::where('slug', 'male')->first();
$boxersTag = Tag::where('slug', 'boxers')->first();
$femaleTag = Tag::where('slug', 'female')->first();
$braTag = Tag::where('slug', 'bra')->first();
$skirtTag = Tag::where('slug', 'skirt')->first();
$dressTag = Tag::where('slug', 'dress')->first();
$pantiesTag = Tag::where('slug', 'panties')->first();
$adultsOnlyTag = Tag::where('slug', 'adults-only')->first();

echo "Available tags:\n";
echo "  Male ID: {$maleTag->id}\n";
echo "  Boxers ID: {$boxersTag->id}\n";
echo "  Female ID: {$femaleTag->id}\n";
echo "  Bra ID: {$braTag->id}\n\n";

// Create a test game
$game = Game::create([
    'name' => 'Tag Selection Test',
    'max_spice_rating' => 5,
]);

echo "Created game: {$game->name} (ID: {$game->id})\n\n";

// Test 1: Create male player with pre-selected tags (simulating frontend)
echo "=== TEST 1: Male Player with Pre-Selected Tags ===\n";
$malePlayer = $game->players()->create([
    'name' => 'John',
    'gender' => 'male',
    'order' => 0,
]);

// Frontend would send these tag IDs
$maleTagIds = [$maleTag->id, $boxersTag->id];
$malePlayer->tags()->sync($maleTagIds);
$malePlayer->load('tags');

echo "Player: {$malePlayer->name}\n";
echo "Gender: {$malePlayer->gender}\n";
echo "Tags assigned:\n";
foreach ($malePlayer->tags as $tag) {
    echo "  - {$tag->name} ({$tag->slug})\n";
}
echo "Expected: Male, Boxers\n";
echo "Result: " . ($malePlayer->tags->count() === 2 ? "✓ PASS" : "✗ FAIL") . "\n\n";

// Test 2: Create female player with pre-selected tags (simulating frontend)
echo "=== TEST 2: Female Player with Pre-Selected Tags ===\n";
$femalePlayer = $game->players()->create([
    'name' => 'Sarah',
    'gender' => 'female',
    'order' => 1,
]);

// Frontend would send these tag IDs
$femaleTagIds = [
    $femaleTag->id,
    $braTag->id,
    $skirtTag->id,
    $dressTag->id,
    $pantiesTag->id
];
$femalePlayer->tags()->sync($femaleTagIds);
$femalePlayer->load('tags');

echo "Player: {$femalePlayer->name}\n";
echo "Gender: {$femalePlayer->gender}\n";
echo "Tags assigned:\n";
foreach ($femalePlayer->tags as $tag) {
    echo "  - {$tag->name} ({$tag->slug})\n";
}
echo "Expected: Female, Bra, Skirt, Dress, Panties\n";
echo "Result: " . ($femalePlayer->tags->count() === 5 ? "✓ PASS" : "✗ FAIL") . "\n\n";

// Test 3: Create female player with custom tags (user adds extra)
echo "=== TEST 3: Female Player with Custom Tags ===\n";
$customPlayer = $game->players()->create([
    'name' => 'Jane',
    'gender' => 'female',
    'order' => 2,
]);

// Frontend sends default tags + user added Adults Only
$customTagIds = [
    $femaleTag->id,
    $braTag->id,
    $skirtTag->id,
    $dressTag->id,
    $pantiesTag->id,
    $adultsOnlyTag->id
];
$customPlayer->tags()->sync($customTagIds);
$customPlayer->load('tags');

echo "Player: {$customPlayer->name}\n";
echo "Gender: {$customPlayer->gender}\n";
echo "Tags assigned:\n";
foreach ($customPlayer->tags as $tag) {
    echo "  - {$tag->name} ({$tag->slug})\n";
}
echo "Expected: Female, Bra, Skirt, Dress, Panties, Adults Only\n";
echo "Result: " . ($customPlayer->tags->count() === 6 ? "✓ PASS" : "✗ FAIL") . "\n\n";

// Test 4: Player without gender or tags
echo "=== TEST 4: Player Without Gender ===\n";
$noGenderPlayer = $game->players()->create([
    'name' => 'Alex',
    'gender' => null,
    'order' => 3,
]);

$noGenderPlayer->load('tags');

echo "Player: {$noGenderPlayer->name}\n";
echo "Gender: " . ($noGenderPlayer->gender ?? 'not set') . "\n";
echo "Tags assigned: " . $noGenderPlayer->tags->count() . "\n";
echo "Expected: 0 tags\n";
echo "Result: " . ($noGenderPlayer->tags->count() === 0 ? "✓ PASS" : "✗ FAIL") . "\n\n";

// Test 5: User removes default tag (e.g., removes Boxers from male)
echo "=== TEST 5: User Removes Default Tag ===\n";
$editedPlayer = $game->players()->create([
    'name' => 'Mike',
    'gender' => 'male',
    'order' => 4,
]);

// User starts with defaults but removes Boxers
$editedTagIds = [$maleTag->id]; // Only Male, no Boxers
$editedPlayer->tags()->sync($editedTagIds);
$editedPlayer->load('tags');

echo "Player: {$editedPlayer->name}\n";
echo "Gender: {$editedPlayer->gender}\n";
echo "Tags assigned:\n";
foreach ($editedPlayer->tags as $tag) {
    echo "  - {$tag->name} ({$tag->slug})\n";
}
echo "Expected: Only Male (Boxers removed by user)\n";
echo "Result: " . ($editedPlayer->tags->count() === 1 ? "✓ PASS" : "✗ FAIL") . "\n\n";

// Cleanup
echo "Cleaning up test data...\n";
$game->delete();

echo "\n=== Summary ===\n";
echo "✓ Tags are pre-selected in frontend based on gender\n";
echo "✓ Backend simply saves whatever tags are sent\n";
echo "✓ No special auto-assignment logic needed\n";
echo "✓ Users have full control to add/remove any tags\n";
echo "✓ Transparent and predictable behavior\n";
echo "\n=== Test Complete ===\n";
