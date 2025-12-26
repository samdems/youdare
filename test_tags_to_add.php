<?php

require __DIR__ . "/vendor/autoload.php";

use App\Models\Game;
use App\Models\Player;
use App\Models\Tag;
use App\Models\Task;

$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Tags to Add Functionality Test ===\n\n";

// Clean up any existing test data
echo "Cleaning up test data...\n";
Task::where("description", "LIKE", "TEST-ADD:%")->delete();
Tag::where("slug", "LIKE", "test-add-%")->delete();
Game::where("name", "Test Tags to Add Game")->delete();

// Create test tags
echo "Creating test tags...\n";
$newbieTag = Tag::create([
    "name" => "Test Add Newbie",
    "slug" => "test-add-newbie",
    "description" => "New player status",
]);

$veteranTag = Tag::create([
    "name" => "Test Add Veteran",
    "slug" => "test-add-veteran",
    "description" => "Experienced player status",
]);

$achieverTag = Tag::create([
    "name" => "Test Add Achiever",
    "slug" => "test-add-achiever",
    "description" => "Achievement unlocked",
]);

$braveTag = Tag::create([
    "name" => "Test Add Brave",
    "slug" => "test-add-brave",
    "description" => "Completed brave challenges",
]);

echo "Created tags: {$newbieTag->name} (ID: {$newbieTag->id}), ";
echo "{$veteranTag->name} (ID: {$veteranTag->id}), ";
echo "{$achieverTag->name} (ID: {$achieverTag->id}), ";
echo "{$braveTag->name} (ID: {$braveTag->id})\n\n";

// Create test game
echo "Creating test game...\n";
$game = Game::create([
    "name" => "Test Tags to Add Game",
    "code" => "TESTADD",
    "status" => "active",
    "max_spice_rating" => 5,
]);

// Add game tags
$game
    ->tags()
    ->attach([
        $newbieTag->id,
        $veteranTag->id,
        $achieverTag->id,
        $braveTag->id,
    ]);

// Create test player
echo "Creating test player...\n";
$player = Player::create([
    "game_id" => $game->id,
    "name" => "Test Player",
    "order" => 1,
    "is_active" => true,
]);
$player->tags()->attach($newbieTag->id);

echo "Created player: {$player->name} with tag: {$newbieTag->name}\n\n";

// Create test tasks
echo "Creating test tasks...\n";

// Task 1: Adds veteran tag, removes newbie tag
$task1 = Task::create([
    "type" => "dare",
    "description" => "TEST-ADD: Complete your first challenge",
    "spice_rating" => 1,
    "draft" => false,
    "tags_to_remove" => [$newbieTag->id],
    "tags_to_add" => [$veteranTag->id],
]);
$task1->tags()->attach($newbieTag->id);
echo "Task 1: Removes newbie, adds veteran\n";

// Task 2: Adds achiever tag
$task2 = Task::create([
    "type" => "dare",
    "description" => "TEST-ADD: Unlock achievement",
    "spice_rating" => 2,
    "draft" => false,
    "tags_to_add" => [$achieverTag->id],
]);
$task2->tags()->attach($newbieTag->id);
echo "Task 2: Adds achiever tag\n";

// Task 3: Adds brave tag
$task3 = Task::create([
    "type" => "dare",
    "description" => "TEST-ADD: Show your bravery",
    "spice_rating" => 3,
    "draft" => false,
    "tags_to_add" => [$braveTag->id],
]);
$task3->tags()->attach($newbieTag->id);
echo "Task 3: Adds brave tag\n";

// Task 4: Adds multiple tags
$task4 = Task::create([
    "type" => "dare",
    "description" => "TEST-ADD: Epic challenge with multiple rewards",
    "spice_rating" => 4,
    "draft" => false,
    "tags_to_add" => [$achieverTag->id, $braveTag->id],
]);
$task4->tags()->attach($veteranTag->id);
echo "Task 4: Adds multiple tags (achiever + brave)\n\n";

// Test getAddableTags method
echo "=== Testing getAddableTags Method ===\n\n";

echo "Task 1 addable tags:\n";
$addableTags = $task1->getAddableTags();
foreach ($addableTags as $tag) {
    echo "  - {$tag->name} (ID: {$tag->id})\n";
}

echo "\nTask 4 addable tags:\n";
$addableTags = $task4->getAddableTags();
foreach ($addableTags as $tag) {
    echo "  - {$tag->name} (ID: {$tag->id})\n";
}

// Test addTagsToPlayer method
echo "\n\n=== Testing addTagsToPlayer Method ===\n\n";

echo "Player tags before completion:\n";
$playerTags = $player->tags()->get();
foreach ($playerTags as $tag) {
    echo "  - {$tag->name} (ID: {$tag->id})\n";
}
echo "Total tags: " . $playerTags->count() . "\n\n";

// Complete Task 1 (removes newbie, adds veteran)
echo "Completing Task 1 (removes newbie, adds veteran)...\n";
$removedTags = $task1->removeTagsFromPlayer($player);
$addedTags = $task1->addTagsToPlayer($player);
echo "  Removed tags: " . implode(", ", $removedTags) . "\n";
echo "  Added tags: " . implode(", ", $addedTags) . "\n\n";

echo "Player tags after Task 1:\n";
$playerTags = $player->tags()->get();
foreach ($playerTags as $tag) {
    echo "  - {$tag->name} (ID: {$tag->id})\n";
}
echo "Total tags: " . $playerTags->count() . "\n\n";

// Complete Task 2 (adds achiever)
echo "Completing Task 2 (adds achiever)...\n";
$addedTags = $task2->addTagsToPlayer($player);
echo "  Added tags: " . implode(", ", $addedTags) . "\n\n";

echo "Player tags after Task 2:\n";
$playerTags = $player->tags()->get();
foreach ($playerTags as $tag) {
    echo "  - {$tag->name} (ID: {$tag->id})\n";
}
echo "Total tags: " . $playerTags->count() . "\n\n";

// Complete Task 3 (adds brave)
echo "Completing Task 3 (adds brave)...\n";
$addedTags = $task3->addTagsToPlayer($player);
echo "  Added tags: " . implode(", ", $addedTags) . "\n\n";

echo "Player tags after Task 3:\n";
$playerTags = $player->tags()->get();
foreach ($playerTags as $tag) {
    echo "  - {$tag->name} (ID: {$tag->id})\n";
}
echo "Total tags: " . $playerTags->count() . "\n\n";

// Test duplicate prevention
echo "=== Testing Duplicate Prevention ===\n\n";
echo "Attempting to add achiever tag again (should be skipped)...\n";
$addedTags = $task2->addTagsToPlayer($player);
if (count($addedTags) === 0) {
    echo "✓ Correctly prevented duplicate tag addition\n";
} else {
    echo "✗ FAILED: Duplicate tag was added\n";
}

echo "\nPlayer still has " .
    $player->tags()->count() .
    " tags (should be 3)\n\n";

// Validation tests
echo "=== Validation Tests ===\n\n";

$allPassed = true;

// Test 1: Task 1 should have veteran in tags_to_add
if (in_array($veteranTag->id, $task1->tags_to_add)) {
    echo "✓ Test 1 PASSED: Task 1 has veteran tag in tags_to_add\n";
} else {
    echo "✗ Test 1 FAILED: Task 1 missing veteran tag in tags_to_add\n";
    $allPassed = false;
}

// Test 2: Player should have veteran tag after Task 1
if ($player->tags()->where("tag_id", $veteranTag->id)->exists()) {
    echo "✓ Test 2 PASSED: Player has veteran tag\n";
} else {
    echo "✗ Test 2 FAILED: Player doesn't have veteran tag\n";
    $allPassed = false;
}

// Test 3: Player should NOT have newbie tag after Task 1
if (!$player->tags()->where("tag_id", $newbieTag->id)->exists()) {
    echo "✓ Test 3 PASSED: Player doesn't have newbie tag\n";
} else {
    echo "✗ Test 3 FAILED: Player still has newbie tag\n";
    $allPassed = false;
}

// Test 4: Player should have achiever tag after Task 2
if ($player->tags()->where("tag_id", $achieverTag->id)->exists()) {
    echo "✓ Test 4 PASSED: Player has achiever tag\n";
} else {
    echo "✗ Test 4 FAILED: Player doesn't have achiever tag\n";
    $allPassed = false;
}

// Test 5: Player should have brave tag after Task 3
if ($player->tags()->where("tag_id", $braveTag->id)->exists()) {
    echo "✓ Test 5 PASSED: Player has brave tag\n";
} else {
    echo "✗ Test 5 FAILED: Player doesn't have brave tag\n";
    $allPassed = false;
}

// Test 6: Player should have exactly 3 tags
$tagCount = $player->tags()->count();
if ($tagCount === 3) {
    echo "✓ Test 6 PASSED: Player has exactly 3 tags\n";
} else {
    echo "✗ Test 6 FAILED: Player has {$tagCount} tags (expected 3)\n";
    $allPassed = false;
}

// Test 7: Task 4 should have 2 tags in tags_to_add
if (count($task4->tags_to_add) === 2) {
    echo "✓ Test 7 PASSED: Task 4 has 2 tags in tags_to_add\n";
} else {
    $count = count($task4->tags_to_add);
    echo "✗ Test 7 FAILED: Task 4 has {$count} tags in tags_to_add (expected 2)\n";
    $allPassed = false;
}

// Test progression scenario
echo "\n=== Testing Complete Progression Scenario ===\n\n";

// Create new player for progression test
$newPlayer = Player::create([
    "game_id" => $game->id,
    "name" => "Progression Test Player",
    "order" => 2,
    "is_active" => true,
]);
$newPlayer->tags()->attach($newbieTag->id);

echo "Created new player with newbie tag\n";
echo "Starting tags: " . $newPlayer->tags()->count() . "\n\n";

echo "Step 1: Complete beginner task\n";
$task1->removeTagsFromPlayer($newPlayer);
$task1->addTagsToPlayer($newPlayer);
echo "  Tags after: " .
    $newPlayer->tags()->pluck("name")->implode(", ") .
    "\n\n";

echo "Step 2: Unlock achievement\n";
$task2->addTagsToPlayer($newPlayer);
echo "  Tags after: " .
    $newPlayer->tags()->pluck("name")->implode(", ") .
    "\n\n";

echo "Step 3: Show bravery\n";
$task3->addTagsToPlayer($newPlayer);
echo "  Tags after: " .
    $newPlayer->tags()->pluck("name")->implode(", ") .
    "\n\n";

$finalTagCount = $newPlayer->tags()->count();
if ($finalTagCount === 3) {
    echo "✓ Test 8 PASSED: Progression complete, player has 3 tags\n";
} else {
    echo "✗ Test 8 FAILED: Player has {$finalTagCount} tags (expected 3)\n";
    $allPassed = false;
}

echo "\n=== Summary ===\n";
if ($allPassed) {
    echo "✓ All tests PASSED!\n";
} else {
    echo "✗ Some tests FAILED. Review the output above.\n";
}

echo "\nTest complete. Test data has been created (not cleaned up for inspection).\n";
echo "To clean up manually, delete the game 'Test Tags to Add Game'.\n";
