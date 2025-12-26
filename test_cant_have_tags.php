<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\Game;
use App\Models\Player;
use App\Models\Tag;
use App\Models\Task;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Can't Have Tags Functionality Test ===\n\n";

// Clean up any existing test data
echo "Cleaning up test data...\n";
Task::where('description', 'LIKE', 'TEST:%')->delete();
Tag::where('slug', 'LIKE', 'test-%')->delete();
Game::where('name', 'Test Cant Have Tags Game')->delete();

// Create test tags
echo "Creating test tags...\n";
$beginnerTag = Tag::create([
    'name' => 'Test Beginner',
    'slug' => 'test-beginner',
    'description' => 'Beginner player tag'
]);

$experiencedTag = Tag::create([
    'name' => 'Test Experienced',
    'slug' => 'test-experienced',
    'description' => 'Experienced player tag'
]);

$vipTag = Tag::create([
    'name' => 'Test VIP',
    'slug' => 'test-vip',
    'description' => 'VIP player tag'
]);

echo "Created tags: {$beginnerTag->name} (ID: {$beginnerTag->id}), ";
echo "{$experiencedTag->name} (ID: {$experiencedTag->id}), ";
echo "{$vipTag->name} (ID: {$vipTag->id})\n\n";

// Create test game
echo "Creating test game...\n";
$game = Game::create([
    'name' => 'Test Cant Have Tags Game',
    'code' => 'TEST99',
    'status' => 'active',
    'max_spice_rating' => 5
]);

// Add game tags
$game->tags()->attach([$beginnerTag->id, $experiencedTag->id, $vipTag->id]);

// Create test players
echo "Creating test players...\n";
$beginnerPlayer = Player::create([
    'game_id' => $game->id,
    'name' => 'Beginner Player',
    'order' => 1,
    'is_active' => true
]);
$beginnerPlayer->tags()->attach($beginnerTag->id);

$experiencedPlayer = Player::create([
    'game_id' => $game->id,
    'name' => 'Experienced Player',
    'order' => 2,
    'is_active' => true
]);
$experiencedPlayer->tags()->attach($experiencedTag->id);

$vipPlayer = Player::create([
    'game_id' => $game->id,
    'name' => 'VIP Player',
    'order' => 3,
    'is_active' => true
]);
$vipPlayer->tags()->attach($vipTag->id);

echo "Created players: {$beginnerPlayer->name}, {$experiencedPlayer->name}, {$vipPlayer->name}\n\n";

// Create test tasks
echo "Creating test tasks...\n";

// Task 1: Available to everyone (no restrictions)
$task1 = Task::create([
    'type' => 'dare',
    'description' => 'TEST: Task for everyone',
    'spice_rating' => 1,
    'draft' => false,
    'cant_have_tags' => null
]);
$task1->tags()->attach([$beginnerTag->id, $experiencedTag->id, $vipTag->id]);
echo "Task 1: {$task1->description} (no cant_have_tags)\n";

// Task 2: NOT available to beginners
$task2 = Task::create([
    'type' => 'dare',
    'description' => 'TEST: Advanced task (not for beginners)',
    'spice_rating' => 3,
    'draft' => false,
    'cant_have_tags' => [$beginnerTag->id]
]);
$task2->tags()->attach([$experiencedTag->id, $vipTag->id]);
echo "Task 2: {$task2->description} (cant_have_tags: beginner)\n";

// Task 3: NOT available to experienced players
$task3 = Task::create([
    'type' => 'dare',
    'description' => 'TEST: Beginner-only task',
    'spice_rating' => 1,
    'draft' => false,
    'cant_have_tags' => [$experiencedTag->id]
]);
$task3->tags()->attach($beginnerTag->id);
echo "Task 3: {$task3->description} (cant_have_tags: experienced)\n";

// Task 4: NOT available to VIP players
$task4 = Task::create([
    'type' => 'truth',
    'description' => 'TEST: Non-VIP task',
    'spice_rating' => 2,
    'draft' => false,
    'cant_have_tags' => [$vipTag->id]
]);
$task4->tags()->attach([$beginnerTag->id, $experiencedTag->id]);
echo "Task 4: {$task4->description} (cant_have_tags: vip)\n";

// Task 5: NOT available to beginners OR VIP
$task5 = Task::create([
    'type' => 'dare',
    'description' => 'TEST: Experienced-only task (no beginners or VIPs)',
    'spice_rating' => 4,
    'draft' => false,
    'cant_have_tags' => [$beginnerTag->id, $vipTag->id]
]);
$task5->tags()->attach($experiencedTag->id);
echo "Task 5: {$task5->description} (cant_have_tags: beginner, vip)\n\n";

// Test filtering
echo "=== Testing Task Filtering ===\n\n";

// Test beginner player
echo "Beginner Player should see:\n";
$beginnerTasks = $beginnerPlayer->getAvailableTasks();
foreach ($beginnerTasks as $task) {
    echo "  ✓ {$task->description}\n";
}
echo "Beginner Player task count: " . $beginnerTasks->count() . "\n";
echo "Expected: Task 1, Task 3, Task 4 (3 tasks)\n\n";

// Test experienced player
echo "Experienced Player should see:\n";
$experiencedTasks = $experiencedPlayer->getAvailableTasks();
foreach ($experiencedTasks as $task) {
    echo "  ✓ {$task->description}\n";
}
echo "Experienced Player task count: " . $experiencedTasks->count() . "\n";
echo "Expected: Task 1, Task 2, Task 4, Task 5 (4 tasks)\n\n";

// Test VIP player
echo "VIP Player should see:\n";
$vipTasks = $vipPlayer->getAvailableTasks();
foreach ($vipTasks as $task) {
    echo "  ✓ {$task->description}\n";
}
echo "VIP Player task count: " . $vipTasks->count() . "\n";
echo "Expected: Task 1, Task 2, Task 3 (3 tasks)\n\n";

// Test isAvailableForPlayer method
echo "=== Testing isAvailableForPlayer Method ===\n\n";

echo "Task 2 (Advanced task) availability:\n";
echo "  Beginner: " . ($task2->isAvailableForPlayer($beginnerPlayer) ? "✓ Available" : "✗ Not Available") . " (Expected: Not Available)\n";
echo "  Experienced: " . ($task2->isAvailableForPlayer($experiencedPlayer) ? "✓ Available" : "✗ Not Available") . " (Expected: Available)\n";
echo "  VIP: " . ($task2->isAvailableForPlayer($vipPlayer) ? "✓ Available" : "✗ Not Available") . " (Expected: Available)\n\n";

echo "Task 3 (Beginner-only) availability:\n";
echo "  Beginner: " . ($task3->isAvailableForPlayer($beginnerPlayer) ? "✓ Available" : "✗ Not Available") . " (Expected: Available)\n";
echo "  Experienced: " . ($task3->isAvailableForPlayer($experiencedPlayer) ? "✓ Available" : "✗ Not Available") . " (Expected: Not Available)\n";
echo "  VIP: " . ($task3->isAvailableForPlayer($vipPlayer) ? "✓ Available" : "✗ Not Available") . " (Expected: Available)\n\n";

echo "Task 5 (Experienced-only) availability:\n";
echo "  Beginner: " . ($task5->isAvailableForPlayer($beginnerPlayer) ? "✓ Available" : "✗ Not Available") . " (Expected: Not Available)\n";
echo "  Experienced: " . ($task5->isAvailableForPlayer($experiencedPlayer) ? "✓ Available" : "✗ Not Available") . " (Expected: Available)\n";
echo "  VIP: " . ($task5->isAvailableForPlayer($vipPlayer) ? "✓ Available" : "✗ Not Available") . " (Expected: Not Available)\n\n";

// Test getCantHaveTags method
echo "=== Testing getCantHaveTags Method ===\n\n";

echo "Task 2 cant_have_tags:\n";
$cantHaveTags = $task2->getCantHaveTags();
foreach ($cantHaveTags as $tag) {
    echo "  - {$tag->name} (ID: {$tag->id})\n";
}

echo "\nTask 5 cant_have_tags:\n";
$cantHaveTags = $task5->getCantHaveTags();
foreach ($cantHaveTags as $tag) {
    echo "  - {$tag->name} (ID: {$tag->id})\n";
}

// Validation tests
echo "\n=== Validation Tests ===\n\n";

$allPassed = true;

// Test 1: Beginner should see exactly 3 tasks
if ($beginnerTasks->count() === 3) {
    echo "✓ Test 1 PASSED: Beginner player sees 3 tasks\n";
} else {
    echo "✗ Test 1 FAILED: Beginner player sees {$beginnerTasks->count()} tasks (expected 3)\n";
    $allPassed = false;
}

// Test 2: Experienced should see exactly 4 tasks
if ($experiencedTasks->count() === 4) {
    echo "✓ Test 2 PASSED: Experienced player sees 4 tasks\n";
} else {
    echo "✗ Test 2 FAILED: Experienced player sees {$experiencedTasks->count()} tasks (expected 4)\n";
    $allPassed = false;
}

// Test 3: VIP should see exactly 3 tasks
if ($vipTasks->count() === 3) {
    echo "✓ Test 3 PASSED: VIP player sees 3 tasks\n";
} else {
    echo "✗ Test 3 FAILED: VIP player sees {$vipTasks->count()} tasks (expected 3)\n";
    $allPassed = false;
}

// Test 4: Beginner should NOT see Task 2 (advanced)
if (!$beginnerTasks->contains($task2)) {
    echo "✓ Test 4 PASSED: Beginner cannot see advanced task\n";
} else {
    echo "✗ Test 4 FAILED: Beginner can see advanced task\n";
    $allPassed = false;
}

// Test 5: Experienced should NOT see Task 3 (beginner-only)
if (!$experiencedTasks->contains($task3)) {
    echo "✓ Test 5 PASSED: Experienced cannot see beginner-only task\n";
} else {
    echo "✗ Test 5 FAILED: Experienced can see beginner-only task\n";
    $allPassed = false;
}

// Test 6: VIP should NOT see Task 4 (non-VIP)
if (!$vipTasks->contains($task4)) {
    echo "✓ Test 6 PASSED: VIP cannot see non-VIP task\n";
} else {
    echo "✗ Test 6 FAILED: VIP can see non-VIP task\n";
    $allPassed = false;
}

// Test 7: Task 5 should only be available to experienced player
if ($experiencedTasks->contains($task5) && !$beginnerTasks->contains($task5) && !$vipTasks->contains($task5)) {
    echo "✓ Test 7 PASSED: Task 5 only available to experienced player\n";
} else {
    echo "✗ Test 7 FAILED: Task 5 availability incorrect\n";
    $allPassed = false;
}

echo "\n=== Summary ===\n";
if ($allPassed) {
    echo "✓ All tests PASSED!\n";
} else {
    echo "✗ Some tests FAILED. Review the output above.\n";
}

echo "\nTest complete. Test data has been created (not cleaned up for inspection).\n";
echo "To clean up manually, delete the game 'Test Cant Have Tags Game'.\n";
