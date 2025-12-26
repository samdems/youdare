<?php

/**
 * Test to reproduce the tag filtering bug
 * 
 * Bug: When a player has NO tags, they see ALL tasks instead of only tasks with no tags
 */

require __DIR__ . '/vendor/autoload.php';

use App\Models\Game;
use App\Models\Player;
use App\Models\Tag;
use App\Models\Task;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Tag Filter Bug Test ===\n\n";

// Create a test game
$game = Game::create([
    'name' => 'Bug Test Game',
    'max_spice_rating' => 5,
    'status' => 'active'
]);

// Create tags
$maleTag = Tag::firstOrCreate(['slug' => 'male'], ['name' => 'Male', 'min_spice_level' => 0]);
$femaleTag = Tag::firstOrCreate(['slug' => 'female'], ['name' => 'Female', 'min_spice_level' => 0]);

// Create a player with NO tags
$playerNoTags = $game->players()->create([
    'name' => 'Player with No Tags',
    'order' => 1
]);

// Create a player WITH tags
$playerWithTags = $game->players()->create([
    'name' => 'Player with Tags',
    'order' => 2
]);
$playerWithTags->tags()->attach($maleTag->id);

// Create tasks
$taskWithMaleTag = Task::create([
    'type' => 'truth',
    'spice_rating' => 1,
    'description' => 'Task with male tag',
    'draft' => false
]);
$taskWithMaleTag->tags()->attach($maleTag->id);

$taskWithFemaleTag = Task::create([
    'type' => 'truth',
    'spice_rating' => 1,
    'description' => 'Task with female tag',
    'draft' => false
]);
$taskWithFemaleTag->tags()->attach($femaleTag->id);

$taskWithNoTags = Task::create([
    'type' => 'truth',
    'spice_rating' => 1,
    'description' => 'Task with no tags (universal)',
    'draft' => false
]);

echo "Setup complete:\n";
echo "- Player with NO tags: {$playerNoTags->name}\n";
echo "- Player WITH tags: {$playerWithTags->name} (male)\n";
echo "- Task 1: {$taskWithMaleTag->description}\n";
echo "- Task 2: {$taskWithFemaleTag->description}\n";
echo "- Task 3: {$taskWithNoTags->description}\n";
echo "\n";

// Test: Player with NO tags
echo "Testing player with NO tags:\n";
echo str_repeat("-", 50) . "\n";
$tasks = $playerNoTags->getAvailableTasks();
echo "Available tasks count: " . $tasks->count() . "\n";
foreach ($tasks as $task) {
    echo "- {$task->description}\n";
}
echo "\n";

echo "EXPECTED: Should only see 'Task with no tags (universal)' (1 task)\n";
echo "ACTUAL: Sees {$tasks->count()} task(s)\n";

if ($tasks->count() == 1 && $tasks->contains($taskWithNoTags)) {
    echo "✅ PASS - Player with no tags only sees universal tasks\n";
} else {
    echo "❌ FAIL - Player with no tags sees ALL tasks (BUG!)\n";
}

echo "\n" . str_repeat("=", 50) . "\n\n";

// Test: Player WITH tags
echo "Testing player WITH tags:\n";
echo str_repeat("-", 50) . "\n";
$tasks = $playerWithTags->getAvailableTasks();
echo "Available tasks count: " . $tasks->count() . "\n";
foreach ($tasks as $task) {
    echo "- {$task->description}\n";
}
echo "\n";

echo "EXPECTED: Should see 'Task with male tag' + 'Task with no tags (universal)' (2 tasks)\n";
echo "ACTUAL: Sees {$tasks->count()} task(s)\n";

if ($tasks->count() == 2 && $tasks->contains($taskWithMaleTag) && $tasks->contains($taskWithNoTags)) {
    echo "✅ PASS - Player with tags sees matching tasks + universal tasks\n";
} else {
    echo "❌ FAIL - Player with tags doesn't see correct tasks\n";
}

// Cleanup
$game->delete();
$taskWithMaleTag->delete();
$taskWithFemaleTag->delete();
$taskWithNoTags->delete();
