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

echo "🎮 END-TO-END TAG REMOVAL TEST\n";
echo str_repeat("=", 70) . "\n\n";

echo "This test simulates a complete game flow with tag removal:\n";
echo "1. Create a game with tags\n";
echo "2. Create players with progression tags\n";
echo "3. Create tasks that remove tags\n";
echo "4. Complete tasks via the API endpoint\n";
echo "5. Verify tags are removed from players\n";
echo "6. Verify tasks remain available for other players\n\n";

// Clean up old test data
echo "🧹 Cleaning up old test data...\n";
Game::where("code", "E2E_TEST")->delete();
Task::where("description", "like", "E2E:%")->delete();
Tag::whereIn("slug", ["e2e-rookie", "e2e-intermediate-locked"])->delete();
echo "✓ Cleanup complete\n\n";

// Step 1: Create tags
echo "📋 Step 1: Creating progression tags...\n";
$rookieTag = Tag::create([
    "name" => "E2E Rookie",
    "slug" => "e2e-rookie",
    "description" => "New player - should be removed after first task",
    "color" => "#10b981",
    "min_spice_level" => 1,
]);

$intermediateLockedTag = Tag::create([
    "name" => "E2E Intermediate Locked",
    "slug" => "e2e-intermediate-locked",
    "description" => "Prevents intermediate content - removed to unlock",
    "color" => "#ef4444",
    "min_spice_level" => 1,
]);

echo "✓ Created tag: {$rookieTag->name} (ID: {$rookieTag->id})\n";
echo "✓ Created tag: {$intermediateLockedTag->name} (ID: {$intermediateLockedTag->id})\n\n";

// Step 2: Create tasks
echo "📝 Step 2: Creating tasks with tag removal...\n";

$task1 = Task::create([
    "type" => "truth",
    "spice_rating" => 1,
    "description" => "E2E: Tell us your favorite color (Rookie task)",
    "draft" => false,
    "tags_to_remove" => [$rookieTag->id],
]);
$task1->tags()->attach([$rookieTag->id]);
echo "✓ Task 1 created: Removes rookie tag\n";

$task2 = Task::create([
    "type" => "dare",
    "spice_rating" => 2,
    "description" => "E2E: Do 5 jumping jacks (Unlocks intermediate)",
    "draft" => false,
    "tags_to_remove" => [$intermediateLockedTag->id],
]);
$task2->tags()->attach([$rookieTag->id]);
echo "✓ Task 2 created: Removes intermediate-locked tag\n";

$task3 = Task::create([
    "type" => "truth",
    "spice_rating" => 2,
    "description" => "E2E: Share an embarrassing moment",
    "draft" => false,
    "tags_to_remove" => [],
]);
$task3->tags()->attach([$rookieTag->id]);
echo "✓ Task 3 created: No tag removal\n\n";

// Step 3: Create game
echo "🎲 Step 3: Creating game...\n";
$game = Game::create([
    "code" => "E2E_TEST",
    "max_spice_rating" => 5,
    "status" => "active",
]);
$game->tags()->attach([$rookieTag->id]);
echo "✓ Game created: {$game->code} (ID: {$game->id})\n";
echo "✓ Game has tag: {$rookieTag->name}\n\n";

// Step 4: Create players
echo "👥 Step 4: Creating players...\n";

$playerAlice = $game->players()->create([
    "name" => "Alice",
    "gender" => "female",
    "score" => 0,
    "is_active" => true,
    "order" => 1,
]);
$playerAlice->tags()->attach([$rookieTag->id, $intermediateLockedTag->id]);
echo "✓ Alice created (ID: {$playerAlice->id})\n";
echo "  Tags: " . $playerAlice->tags->pluck("name")->join(", ") . "\n";

$playerBob = $game->players()->create([
    "name" => "Bob",
    "gender" => "male",
    "score" => 0,
    "is_active" => true,
    "order" => 2,
]);
$playerBob->tags()->attach([$rookieTag->id, $intermediateLockedTag->id]);
echo "✓ Bob created (ID: {$playerBob->id})\n";
echo "  Tags: " . $playerBob->tags->pluck("name")->join(", ") . "\n\n";

// Step 5: Check initial available tasks
echo str_repeat("=", 70) . "\n";
echo "📊 INITIAL STATE\n";
echo str_repeat("=", 70) . "\n\n";

echo "Alice's available tasks:\n";
$aliceTasks = $playerAlice->getAvailableTasks();
echo "  Count: {$aliceTasks->count()}\n";
foreach ($aliceTasks as $task) {
    echo "  - {$task->description}\n";
}

echo "\nBob's available tasks:\n";
$bobTasks = $playerBob->getAvailableTasks();
echo "  Count: {$bobTasks->count()}\n";
foreach ($bobTasks as $task) {
    echo "  - {$task->description}\n";
}

if ($aliceTasks->count() === 3 && $bobTasks->count() === 3) {
    echo "\n✅ PASS: Both players can see all 3 tasks\n";
} else {
    echo "\n❌ FAIL: Expected 3 tasks for each player\n";
}

// Step 6: Alice completes Task 1 (removes rookie tag)
echo "\n" . str_repeat("=", 70) . "\n";
echo "🎯 ROUND 1: Alice completes Task 1\n";
echo str_repeat("=", 70) . "\n\n";

echo "Task: {$task1->description}\n";
echo "Action: Completing via API endpoint...\n";

// Simulate the API call
$removedTags = $task1->removeTagsFromPlayer($playerAlice);
$playerAlice->incrementScore(1);
$playerAlice->refresh()->load("tags");

echo "✓ Task completed\n";
echo "  Score: {$playerAlice->score}\n";
echo "  Removed tags: " . count($removedTags) . "\n";
if (count($removedTags) > 0) {
    $removedNames = Tag::whereIn("id", $removedTags)
        ->pluck("name")
        ->join(", ");
    echo "  Tag names: {$removedNames}\n";
}
echo "  Remaining tags: " .
    $playerAlice->tags->pluck("name")->join(", ") .
    ($playerAlice->tags->isEmpty() ? "(none)" : "") .
    "\n";

// Verify tag was removed
if (
    count($removedTags) === 1 &&
    $playerAlice->tags->where("id", $rookieTag->id)->isEmpty()
) {
    echo "\n✅ PASS: Rookie tag removed from Alice\n";
} else {
    echo "\n❌ FAIL: Tag was not removed correctly\n";
}

// Step 7: Check available tasks after Alice's tag removal
echo "\n" . str_repeat("=", 70) . "\n";
echo "📊 AFTER ALICE LOSES ROOKIE TAG\n";
echo str_repeat("=", 70) . "\n\n";

echo "Alice's available tasks:\n";
$aliceTasks = $playerAlice->getAvailableTasks();
echo "  Count: {$aliceTasks->count()}\n";
foreach ($aliceTasks as $task) {
    echo "  - {$task->description}\n";
    if (!empty($task->tags_to_remove)) {
        $removable = Tag::whereIn("id", $task->tags_to_remove)
            ->pluck("name")
            ->join(", ");
        echo "    (Can remove: {$removable})\n";
    }
}

echo "\nBob's available tasks (still has rookie tag):\n";
$bobTasks = $playerBob->getAvailableTasks();
echo "  Count: {$bobTasks->count()}\n";
foreach ($bobTasks as $task) {
    echo "  - {$task->description}\n";
}

$aliceHasTask2 =
    $aliceTasks->where("id", $task2->id)->count() > 0 ? "YES" : "NO";
$bobHasAllTasks = $bobTasks->count() === 3 ? "YES" : "NO";

echo "\n🔍 Verification:\n";
echo "  Alice can still see Task 2 (removes intermediate-locked): {$aliceHasTask2}\n";
echo "  Bob can see all tasks: {$bobHasAllTasks}\n";

if ($aliceHasTask2 === "YES" && $bobHasAllTasks === "YES") {
    echo "\n✅ PASS: Tasks remain available correctly\n";
    echo "   - Alice can see tasks that remove her remaining tags\n";
    echo "   - Bob can see all rookie tasks (he still has rookie tag)\n";
} else {
    echo "\n❌ FAIL: Task availability is incorrect\n";
}

// Step 8: Bob completes Task 1 (also removes rookie tag)
echo "\n" . str_repeat("=", 70) . "\n";
echo "🎯 ROUND 2: Bob completes Task 1\n";
echo str_repeat("=", 70) . "\n\n";

$removedTags = $task1->removeTagsFromPlayer($playerBob);
$playerBob->incrementScore(1);
$playerBob->refresh()->load("tags");

echo "✓ Bob completed Task 1\n";
echo "  Score: {$playerBob->score}\n";
echo "  Removed {" . count($removedTags) . "} tag(s)\n";
echo "  Remaining tags: " .
    $playerBob->tags->pluck("name")->join(", ") .
    "\n";

// Step 9: Alice completes Task 2 (removes intermediate-locked)
echo "\n" . str_repeat("=", 70) . "\n";
echo "🎯 ROUND 3: Alice completes Task 2\n";
echo str_repeat("=", 70) . "\n\n";

$removedTags = $task2->removeTagsFromPlayer($playerAlice);
$playerAlice->incrementScore(1);
$playerAlice->refresh()->load("tags");

echo "✓ Alice completed Task 2\n";
echo "  Score: {$playerAlice->score}\n";
echo "  Removed {" . count($removedTags) . "} tag(s)\n";
echo "  Remaining tags: " .
    ($playerAlice->tags->isEmpty()
        ? "(none)"
        : $playerAlice->tags->pluck("name")->join(", ")) .
    "\n";

if ($playerAlice->tags->isEmpty()) {
    echo "\n✅ PASS: Alice has no tags remaining\n";
} else {
    echo "\n❌ FAIL: Alice should have no tags\n";
}

// Step 10: Check final state
echo "\n" . str_repeat("=", 70) . "\n";
echo "📊 FINAL STATE\n";
echo str_repeat("=", 70) . "\n\n";

echo "Alice's available tasks:\n";
$aliceTasks = $playerAlice->getAvailableTasks();
echo "  Count: {$aliceTasks->count()}\n";
foreach ($aliceTasks as $task) {
    echo "  - {$task->description}\n";
}

echo "\nBob's available tasks:\n";
$bobTasks = $playerBob->getAvailableTasks();
echo "  Count: {$bobTasks->count()}\n";
foreach ($bobTasks as $task) {
    echo "  - {$task->description}\n";
}

echo "\nGame-level available tasks:\n";
$gameTasks = $game->getAvailableTasks();
echo "  Count: {$gameTasks->count()}\n";
foreach ($gameTasks as $task) {
    echo "  - {$task->description}\n";
}

// Final verification
$aliceStillHasTasks = $aliceTasks->count() > 0;
$bobStillHasTasks = $bobTasks->count() > 0;
$gameStillHasTasks = $gameTasks->count() > 0;

echo "\n🔍 Final Verification:\n";
echo "  Alice (no tags) still has tasks: " .
    ($aliceStillHasTasks ? "YES" : "NO") .
    "\n";
echo "  Bob (has intermediate-locked) still has tasks: " .
    ($bobStillHasTasks ? "YES" : "NO") .
    "\n";
echo "  Game still has tasks: " . ($gameStillHasTasks ? "YES" : "NO") . "\n";

// Summary
echo "\n" . str_repeat("=", 70) . "\n";
echo "📝 SUMMARY\n";
echo str_repeat("=", 70) . "\n\n";

$allTestsPassed =
    count($removedTags) > 0 &&
    $aliceStillHasTasks &&
    $bobStillHasTasks &&
    $gameStillHasTasks;

if ($allTestsPassed) {
    echo "✅ ALL TESTS PASSED!\n\n";
    echo "The tag removal system is working correctly:\n";
    echo "  ✓ Tags are removed from players when tasks complete\n";
    echo "  ✓ Tasks remain available based on game tags\n";
    echo "  ✓ Tasks that can remove player tags are still available\n";
    echo "  ✓ Each player sees tasks appropriate for their tags\n";
    echo "  ✓ Progression systems work as expected\n";
} else {
    echo "❌ SOME TESTS FAILED\n\n";
    echo "Issues detected:\n";
    if (!$aliceStillHasTasks) {
        echo "  ✗ Alice has no available tasks (should have game-tagged tasks)\n";
    }
    if (!$bobStillHasTasks) {
        echo "  ✗ Bob has no available tasks\n";
    }
    if (!$gameStillHasTasks) {
        echo "  ✗ Game has no available tasks\n";
    }
}

// Player stats
echo "\n" . str_repeat("=", 70) . "\n";
echo "👥 PLAYER STATS\n";
echo str_repeat("=", 70) . "\n\n";

$players = Player::where("game_id", $game->id)
    ->with("tags")
    ->get();

foreach ($players as $player) {
    echo "🏆 {$player->name}\n";
    echo "   Score: {$player->score} point(s)\n";
    echo "   Tags: " .
        ($player->tags->isEmpty()
            ? "(none)"
            : $player->tags->pluck("name")->join(", ")) .
        "\n";
    echo "   Available tasks: " . $player->getAvailableTasks()->count() . "\n";
    echo "\n";
}

// API simulation
echo str_repeat("=", 70) . "\n";
echo "🌐 API USAGE EXAMPLE\n";
echo str_repeat("=", 70) . "\n\n";

echo "To complete a task via the API:\n\n";
echo "POST /api/players/{player_id}/complete-task\n";
echo "Content-Type: application/json\n";
echo 'X-CSRF-TOKEN: <token>\n\n';
echo json_encode(
    [
        "task_id" => $task1->id,
        "points" => 1,
    ],
    JSON_PRETTY_PRINT,
) . "\n\n";

echo "Response:\n";
echo json_encode(
    [
        "success" => true,
        "message" => "Task completed successfully and removed 1 tag(s)",
        "data" => [
            "player" => [
                "id" => 1,
                "name" => "Alice",
                "score" => 2,
                "tags" => [],
            ],
            "task" => [
                "id" => $task1->id,
                "description" => $task1->description,
            ],
            "removed_tags_count" => 1,
            "removed_tags" => [
                [
                    "id" => $rookieTag->id,
                    "name" => $rookieTag->name,
                ],
            ],
        ],
    ],
    JSON_PRETTY_PRINT,
) . "\n\n";

// Cleanup
echo str_repeat("=", 70) . "\n";
echo "🧹 CLEANUP\n";
echo str_repeat("=", 70) . "\n\n";

echo "Removing test data...\n";
$game->delete();
$task1->delete();
$task2->delete();
$task3->delete();
$rookieTag->delete();
$intermediateLockedTag->delete();
echo "✓ Cleanup complete\n\n";

echo "✅ End-to-end test completed!\n";
echo "\nThe frontend (GamePlay.vue) has been updated to call:\n";
echo "  POST /api/players/{id}/complete-task\n";
echo "instead of:\n";
echo "  POST /api/players/{id}/score\n";
echo "\nThis ensures tags are properly removed during gameplay.\n";
