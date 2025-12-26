<?php

/**
 * Final Tag Filtering Test - with correct expectations
 * 
 * Tests the fixed tag filtering logic for:
 * 1. Players with no tags - should only see universal tasks + cant_have_tags they don't have
 * 2. Players with tags - should see matching tasks + universal tasks + tasks they can complete
 * 3. Tasks with tags_to_remove - only visible to players with those tags
 * 4. Tasks with cant_have_tags - not visible to players with those tags
 */

require __DIR__ . '/vendor/autoload.php';

use App\Models\Game;
use App\Models\Player;
use App\Models\Tag;
use App\Models\Task;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Final Tag Filtering Test ===\n\n";

$testsPassed = 0;
$testsFailed = 0;

function test($description, $condition) {
    global $testsPassed, $testsFailed;
    echo ($condition ? "✅" : "❌") . " {$description}\n";
    $condition ? $testsPassed++ : $testsFailed++;
    return $condition;
}

// Setup
$game = Game::create([
    'name' => 'Final Test Game',
    'max_spice_rating' => 5,
    'status' => 'active'
]);

$maleTag = Tag::firstOrCreate(['slug' => 'male'], ['name' => 'Male', 'min_spice_level' => 0]);
$femaleTag = Tag::firstOrCreate(['slug' => 'female'], ['name' => 'Female', 'min_spice_level' => 0]);
$adultTag = Tag::firstOrCreate(['slug' => 'adult'], ['name' => 'Adult', 'min_spice_level' => 0]);

// Create players
$playerNoTags = $game->players()->create(['name' => 'No Tags', 'order' => 1]);
$playerMale = $game->players()->create(['name' => 'Male Player', 'order' => 2]);
$playerMale->tags()->attach($maleTag->id);
$playerFemale = $game->players()->create(['name' => 'Female Player', 'order' => 3]);
$playerFemale->tags()->attach($femaleTag->id);
$playerAdult = $game->players()->create(['name' => 'Adult Player', 'order' => 4]);
$playerAdult->tags()->attach($adultTag->id);

// Create tasks
$universalTask = Task::create([
    'type' => 'truth',
    'spice_rating' => 1,
    'description' => 'Universal task (everyone)',
    'draft' => false
]);

$maleTask = Task::create([
    'type' => 'dare',
    'spice_rating' => 1,
    'description' => 'Male only task',
    'draft' => false
]);
$maleTask->tags()->attach($maleTag->id);

$femaleTask = Task::create([
    'type' => 'truth',
    'spice_rating' => 1,
    'description' => 'Female only task',
    'draft' => false
]);
$femaleTask->tags()->attach($femaleTag->id);

$cantHaveAdultTask = Task::create([
    'type' => 'dare',
    'spice_rating' => 1,
    'description' => 'Non-adult task (cant have adult tag)',
    'draft' => false,
    'cant_have_tags' => [$adultTag->id]
]);

$removeAdultTask = Task::create([
    'type' => 'truth',
    'spice_rating' => 1,
    'description' => 'Task that removes adult tag',
    'draft' => false,
    'tags_to_remove' => [$adultTag->id]
]);

echo "Setup complete:\n";
echo "- 4 players: No Tags, Male, Female, Adult\n";
echo "- 5 tasks: Universal, Male-only, Female-only, Can't-have-adult, Remove-adult\n\n";

// Test 1: Player with NO tags
echo "Test 1: Player with NO tags\n";
echo str_repeat("-", 50) . "\n";
$tasks = $playerNoTags->getAvailableTasks();
test("Sees universal task", $tasks->contains($universalTask));
test("Does NOT see male task", !$tasks->contains($maleTask));
test("Does NOT see female task", !$tasks->contains($femaleTask));
test("DOES see cant-have-adult task (doesn't have adult tag)", $tasks->contains($cantHaveAdultTask));
test("Does NOT see remove-adult task (doesn't have adult tag to remove)", !$tasks->contains($removeAdultTask));
test("Total: exactly 2 tasks", $tasks->count() === 2);
echo "\n";

// Test 2: Male player
echo "Test 2: Male Player\n";
echo str_repeat("-", 50) . "\n";
$tasks = $playerMale->getAvailableTasks();
test("Sees universal task", $tasks->contains($universalTask));
test("Sees male task", $tasks->contains($maleTask));
test("Does NOT see female task", !$tasks->contains($femaleTask));
test("Sees cant-have-adult task (doesn't have adult tag)", $tasks->contains($cantHaveAdultTask));
test("Does NOT see remove-adult task (doesn't have adult tag to remove)", !$tasks->contains($removeAdultTask));
test("Total: exactly 3 tasks", $tasks->count() === 3);
echo "\n";

// Test 3: Female player
echo "Test 3: Female Player\n";
echo str_repeat("-", 50) . "\n";
$tasks = $playerFemale->getAvailableTasks();
test("Sees universal task", $tasks->contains($universalTask));
test("Does NOT see male task", !$tasks->contains($maleTask));
test("Sees female task", $tasks->contains($femaleTask));
test("Sees cant-have-adult task (doesn't have adult tag)", $tasks->contains($cantHaveAdultTask));
test("Does NOT see remove-adult task (doesn't have adult tag to remove)", !$tasks->contains($removeAdultTask));
test("Total: exactly 3 tasks", $tasks->count() === 3);
echo "\n";

// Test 4: Adult player
echo "Test 4: Adult Player\n";
echo str_repeat("-", 50) . "\n";
$tasks = $playerAdult->getAvailableTasks();
test("Sees universal task", $tasks->contains($universalTask));
test("Does NOT see male task", !$tasks->contains($maleTask));
test("Does NOT see female task", !$tasks->contains($femaleTask));
test("Does NOT see cant-have-adult task (HAS adult tag)", !$tasks->contains($cantHaveAdultTask));
test("Sees remove-adult task (can remove their adult tag)", $tasks->contains($removeAdultTask));
test("Total: exactly 2 tasks", $tasks->count() === 2);
echo "\n";

// Summary
echo str_repeat("=", 50) . "\n";
echo "Test Results:\n";
echo "✅ Passed: {$testsPassed}\n";
echo "❌ Failed: {$testsFailed}\n";
echo "Total: " . ($testsPassed + $testsFailed) . "\n";

if ($testsFailed === 0) {
    echo "\n🎉 All tests passed! Tag filtering is working correctly.\n";
    $exitCode = 0;
} else {
    echo "\n⚠️  Some tests failed.\n";
    $exitCode = 1;
}

// Cleanup
$game->delete();
$universalTask->delete();
$maleTask->delete();
$femaleTask->delete();
$cantHaveAdultTask->delete();
$removeAdultTask->delete();

exit($exitCode);
