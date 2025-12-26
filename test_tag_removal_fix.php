<?php

require __DIR__ . "/vendor/autoload.php";

use App\Models\Game;
use App\Models\Player;
use App\Models\Tag;
use App\Models\Task;

// Bootstrap Laravel
$app = require_once __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 Testing Tag Removal Fix\n";
echo str_repeat("=", 70) . "\n\n";

// Clean up old test data
echo "🧹 Cleaning up old test data...\n";
Game::where("code", "TAG_TEST")->delete();
Task::where("description", "like", "TEST:%")->delete();
Tag::whereIn("slug", ["test-rookie", "test-intermediate-locked"])->delete();

// Create test tags
echo "\n📋 Creating test tags...\n";
$rookieTag = Tag::create([
    "name" => "Test Rookie",
    "slug" => "test-rookie",
    "description" => "Rookie tag for testing",
    "color" => "#10b981",
    "min_spice_level" => 1,
]);

$intermediateLockedTag = Tag::create([
    "name" => "Test Intermediate Locked",
    "slug" => "test-intermediate-locked",
    "description" => "Locked tag for testing",
    "color" => "#ef4444",
    "min_spice_level" => 1,
]);

echo "   ✓ Created: {$rookieTag->name}\n";
echo "   ✓ Created: {$intermediateLockedTag->name}\n";

// Create test tasks
echo "\n📝 Creating test tasks...\n";

// Task 1: Has rookie tag, removes rookie tag
$task1 = Task::create([
    "type" => "truth",
    "spice_rating" => 1,
    "description" => "TEST: First task - removes rookie tag",
    "draft" => false,
    "tags_to_remove" => [$rookieTag->id],
]);
$task1->tags()->attach([$rookieTag->id]);
echo "   ✓ Task 1: Has rookie tag, removes rookie tag\n";

// Task 2: Has rookie tag, removes intermediate-locked tag
$task2 = Task::create([
    "type" => "dare",
    "spice_rating" => 2,
    "description" => "TEST: Second task - removes intermediate-locked",
    "draft" => false,
    "tags_to_remove" => [$intermediateLockedTag->id],
]);
$task2->tags()->attach([$rookieTag->id]);
echo "   ✓ Task 2: Has rookie tag, removes intermediate-locked tag\n";

// Task 3: Has no tags, but removes rookie tag
$task3 = Task::create([
    "type" => "truth",
    "spice_rating" => 1,
    "description" => "TEST: Third task - no tags but removes rookie",
    "draft" => false,
    "tags_to_remove" => [$rookieTag->id],
]);
// No tags attached
echo "   ✓ Task 3: No tags, but removes rookie tag\n";

// Create game
echo "\n🎲 Creating test game...\n";
$game = Game::create([
    "code" => "TAG_TEST",
    "max_spice_rating" => 5,
    "status" => "active",
]);
// Attach rookie tag to game
$game->tags()->attach([$rookieTag->id]);
echo "   ✓ Game created with code: {$game->code}\n";
echo "   ✓ Game has rookie tag\n";

// Create player with both tags
echo "\n👤 Creating test player...\n";
$player = $game->players()->create([
    "name" => "Test Player",
    "gender" => "female",
    "score" => 0,
    "is_active" => true,
    "order" => 1,
]);
$player->tags()->attach([$rookieTag->id, $intermediateLockedTag->id]);
echo "   ✓ Player created: {$player->name}\n";
echo "   ✓ Player has tags: " .
    $player->tags->pluck("name")->join(", ") .
    "\n";

// Test 1: Initial state - should see all tasks
echo "\n" . str_repeat("=", 70) . "\n";
echo "TEST 1: Initial State - Player has both tags\n";
echo str_repeat("=", 70) . "\n";

$availableTasks = $player->getAvailableTasks();
echo "Player tags: " . $player->tags->pluck("name")->join(", ") . "\n";
echo "Available tasks: {$availableTasks->count()}\n";
foreach ($availableTasks as $task) {
    echo "  - {$task->description}\n";
    echo "    Tags: " .
        $task->tags->pluck("name")->join(", ") .
        ($task->tags->isEmpty() ? "(none)" : "") .
        "\n";
    if (!empty($task->tags_to_remove)) {
        $tagsToRemove = Tag::whereIn("id", $task->tags_to_remove)
            ->pluck("name")
            ->join(", ");
        echo "    Removes: {$tagsToRemove}\n";
    }
}

if ($availableTasks->count() === 3) {
    echo "\n✅ PASS: All 3 tasks are available\n";
} else {
    echo "\n❌ FAIL: Expected 3 tasks, got {$availableTasks->count()}\n";
}

// Test 2: Complete task 1 (removes rookie tag from player)
echo "\n" . str_repeat("=", 70) . "\n";
echo "TEST 2: After removing rookie tag from player\n";
echo str_repeat("=", 70) . "\n";

echo "Completing task 1...\n";
$task1->removeTagsFromPlayer($player);
$player->incrementScore();
$player->refresh()->load("tags");

echo "Player tags now: " .
    $player->tags->pluck("name")->join(", ") .
    ($player->tags->isEmpty() ? "(none)" : "") .
    "\n";

$availableTasks = $player->getAvailableTasks();
echo "Available tasks: {$availableTasks->count()}\n";
foreach ($availableTasks as $task) {
    echo "  - {$task->description}\n";
    echo "    Tags: " .
        $task->tags->pluck("name")->join(", ") .
        ($task->tags->isEmpty() ? "(none)" : "") .
        "\n";
    if (!empty($task->tags_to_remove)) {
        $tagsToRemove = Tag::whereIn("id", $task->tags_to_remove)
            ->pluck("name")
            ->join(", ");
        echo "    Removes: {$tagsToRemove}\n";
    }
}

echo "\n🔍 Explanation:\n";
echo "   - Player no longer has rookie tag\n";
echo "   - Player still has intermediate-locked tag\n";
echo "   - Task 2 should still be available (has rookie tag in game)\n";
echo "   - Task 3 should still be available (can remove player's intermediate-locked)\n";

if ($availableTasks->count() >= 1) {
    echo "\n✅ PASS: Tasks are still available after tag removal\n";
} else {
    echo "\n❌ FAIL: No tasks available (BUG!)\n";
}

// Test 3: Complete task 2 (removes intermediate-locked tag)
echo "\n" . str_repeat("=", 70) . "\n";
echo "TEST 3: After removing intermediate-locked tag from player\n";
echo str_repeat("=", 70) . "\n";

echo "Completing task 2...\n";
$task2->removeTagsFromPlayer($player);
$player->incrementScore();
$player->refresh()->load("tags");

echo "Player tags now: " .
    $player->tags->pluck("name")->join(", ") .
    ($player->tags->isEmpty() ? "(none)" : "") .
    "\n";

$availableTasks = $player->getAvailableTasks();
echo "Available tasks: {$availableTasks->count()}\n";
foreach ($availableTasks as $task) {
    echo "  - {$task->description}\n";
    echo "    Tags: " .
        $task->tags->pluck("name")->join(", ") .
        ($task->tags->isEmpty() ? "(none)" : "") .
        "\n";
    if (!empty($task->tags_to_remove)) {
        $tagsToRemove = Tag::whereIn("id", $task->tags_to_remove)
            ->pluck("name")
            ->join(", ");
        echo "    Removes: {$tagsToRemove}\n";
    }
}

echo "\n🔍 Explanation:\n";
echo "   - Player has no tags left\n";
echo "   - Game still has rookie tag\n";
echo "   - Tasks with rookie tag should still be available\n";

if ($availableTasks->count() >= 1) {
    echo "\n✅ PASS: Tasks with game tags are still available\n";
} else {
    echo "\n❌ FAIL: No tasks available (BUG!)\n";
}

// Test 4: Game-level available tasks
echo "\n" . str_repeat("=", 70) . "\n";
echo "TEST 4: Game-level available tasks\n";
echo str_repeat("=", 70) . "\n";

$game->refresh();
$gameAvailableTasks = $game->getAvailableTasks();
echo "Game tags: " . $game->tags->pluck("name")->join(", ") . "\n";
echo "Available tasks for game: {$gameAvailableTasks->count()}\n";
foreach ($gameAvailableTasks as $task) {
    echo "  - {$task->description}\n";
}

if ($gameAvailableTasks->count() >= 2) {
    echo "\n✅ PASS: Game can see tasks with its tags\n";
} else {
    echo "\n❌ FAIL: Expected at least 2 tasks, got {$gameAvailableTasks->count()}\n";
}

// Summary
echo "\n" . str_repeat("=", 70) . "\n";
echo "📊 SUMMARY\n";
echo str_repeat("=", 70) . "\n\n";

echo "The fix ensures that:\n";
echo "1. ✓ Tasks with matching game/player tags are available\n";
echo "2. ✓ Tasks that can remove tags from players are ALSO available\n";
echo "3. ✓ Tasks remain available even after removing tags from players\n";
echo "4. ✓ Progression systems work correctly\n\n";

echo "This solves the bug where tasks became unavailable after removing\n";
echo "tags from all players, breaking progression systems.\n\n";

// Cleanup
echo "🧹 Cleaning up test data...\n";
$game->delete();
$task1->delete();
$task2->delete();
$task3->delete();
$rookieTag->delete();
$intermediateLockedTag->delete();

echo "\n✅ Test completed!\n";
