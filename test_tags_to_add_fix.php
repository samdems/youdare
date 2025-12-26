<?php

/**
 * Test that tags_to_add functionality works when completing a task
 */

require __DIR__ . '/vendor/autoload.php';

use App\Models\Game;
use App\Models\Player;
use App\Models\Tag;
use App\Models\Task;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Tags To Add Functionality Test ===\n\n";

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
    'name' => 'Tags Add Test Game',
    'max_spice_rating' => 5,
    'status' => 'active'
]);

$beginnerTag = Tag::firstOrCreate(['slug' => 'beginner'], ['name' => 'Beginner', 'min_spice_level' => 0]);
$veteranTag = Tag::firstOrCreate(['slug' => 'veteran'], ['name' => 'Veteran', 'min_spice_level' => 0]);
$achievementTag = Tag::firstOrCreate(['slug' => 'achievement'], ['name' => 'Achievement Badge', 'min_spice_level' => 0]);

// Create player with beginner tag
$player = $game->players()->create(['name' => 'Test Player', 'order' => 1]);
$player->tags()->attach($beginnerTag->id);

// Test 1: Task that adds a single tag
echo "Test 1: Task that adds a single achievement tag\n";
echo str_repeat("-", 50) . "\n";

$taskAddOne = Task::create([
    'type' => 'truth',
    'spice_rating' => 1,
    'description' => 'Earn achievement',
    'draft' => false,
    'tags_to_add' => [$achievementTag->id]
]);
$taskAddOne->tags()->attach($beginnerTag->id);

$playerTagsBefore = $player->tags()->pluck('tags.id')->toArray();
test("Player does NOT have achievement tag before", !in_array($achievementTag->id, $playerTagsBefore));

// Complete the task
$addedTags = $taskAddOne->addTagsToPlayer($player);
$player->refresh();

$playerTagsAfter = $player->tags()->pluck('tags.id')->toArray();
test("Player HAS achievement tag after", in_array($achievementTag->id, $playerTagsAfter));
test("addTagsToPlayer returned correct count", count($addedTags) === 1);
test("addTagsToPlayer returned correct ID", in_array($achievementTag->id, $addedTags));
echo "\n";

// Test 2: Task that removes one tag and adds another (promotion)
echo "Test 2: Task that removes beginner and adds veteran\n";
echo str_repeat("-", 50) . "\n";

// Reset player to beginner only
$player->tags()->sync([$beginnerTag->id]);
$player->refresh();

$promotionTask = Task::create([
    'type' => 'dare',
    'spice_rating' => 2,
    'description' => 'Promotion challenge',
    'draft' => false,
    'tags_to_remove' => [$beginnerTag->id],
    'tags_to_add' => [$veteranTag->id]
]);
$promotionTask->tags()->attach($beginnerTag->id);

$playerTagsBefore = $player->tags()->pluck('tags.id')->toArray();
test("Player has beginner tag before", in_array($beginnerTag->id, $playerTagsBefore));
test("Player does NOT have veteran tag before", !in_array($veteranTag->id, $playerTagsBefore));

// Complete the task
$removedTags = $promotionTask->removeTagsFromPlayer($player);
$addedTags = $promotionTask->addTagsToPlayer($player);
$player->refresh();

$playerTagsAfter = $player->tags()->pluck('tags.id')->toArray();
test("Player does NOT have beginner tag after", !in_array($beginnerTag->id, $playerTagsAfter));
test("Player HAS veteran tag after", in_array($veteranTag->id, $playerTagsAfter));
test("Removed correct count", count($removedTags) === 1);
test("Added correct count", count($addedTags) === 1);
echo "\n";

// Test 3: Task that adds a tag the player already has (should not duplicate)
echo "Test 3: Task that adds a tag player already has\n";
echo str_repeat("-", 50) . "\n";

// Player already has veteran tag from previous test
$taskAddDuplicate = Task::create([
    'type' => 'truth',
    'spice_rating' => 1,
    'description' => 'Task that tries to add veteran again',
    'draft' => false,
    'tags_to_add' => [$veteranTag->id]
]);

$playerTagsBefore = $player->tags()->pluck('tags.id')->toArray();
$countBefore = count($playerTagsBefore);
test("Player already has veteran tag", in_array($veteranTag->id, $playerTagsBefore));

// Try to add it again
$addedTags = $taskAddDuplicate->addTagsToPlayer($player);
$player->refresh();

$playerTagsAfter = $player->tags()->pluck('tags.id')->toArray();
$countAfter = count($playerTagsAfter);
test("No tags were added (already has it)", count($addedTags) === 0);
test("Tag count unchanged", $countBefore === $countAfter);
test("Still has veteran tag", in_array($veteranTag->id, $playerTagsAfter));
echo "\n";

// Test 4: Task with no tags_to_add field
echo "Test 4: Task with no tags_to_add field\n";
echo str_repeat("-", 50) . "\n";

$taskNoAdd = Task::create([
    'type' => 'truth',
    'spice_rating' => 1,
    'description' => 'Normal task with no tag changes',
    'draft' => false
]);

$addedTags = $taskNoAdd->addTagsToPlayer($player);
test("Returns empty array when no tags_to_add", count($addedTags) === 0);
echo "\n";

// Summary
echo str_repeat("=", 50) . "\n";
echo "Test Results:\n";
echo "✅ Passed: {$testsPassed}\n";
echo "❌ Failed: {$testsFailed}\n";
echo "Total: " . ($testsPassed + $testsFailed) . "\n";

if ($testsFailed === 0) {
    echo "\n🎉 All tests passed! Tags to add functionality is working correctly.\n";
    $exitCode = 0;
} else {
    echo "\n⚠️  Some tests failed.\n";
    $exitCode = 1;
}

// Cleanup
$game->delete();
$taskAddOne->delete();
$promotionTask->delete();
$taskAddDuplicate->delete();
$taskNoAdd->delete();

exit($exitCode);
