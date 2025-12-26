<?php

/**
 * Test if Game model has similar filtering bug
 */

require __DIR__ . '/vendor/autoload.php';

use App\Models\Game;
use App\Models\Tag;
use App\Models\Task;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Game Tag Filter Test ===\n\n";

// Create a test game
$game = Game::create([
    'name' => 'Game Filter Test',
    'max_spice_rating' => 5,
    'status' => 'active'
]);

// Create tags
$testTag = Tag::firstOrCreate(['slug' => 'test-tag'], ['name' => 'Test Tag', 'min_spice_level' => 0]);

// Add tag to game
$game->tags()->attach($testTag->id);

// Create tasks
$taskWithTag = Task::create([
    'type' => 'truth',
    'spice_rating' => 1,
    'description' => 'Task with test tag',
    'draft' => false
]);
$taskWithTag->tags()->attach($testTag->id);

$taskWithNoTags = Task::create([
    'type' => 'truth',
    'spice_rating' => 1,
    'description' => 'Universal task (no tags)',
    'draft' => false
]);

echo "Setup:\n";
echo "- Game with 'Test Tag'\n";
echo "- Task 1: with test tag\n";
echo "- Task 2: with no tags (universal)\n\n";

// Test game.getAvailableTasks()
$tasks = $game->getAvailableTasks();

echo "Game's available tasks: {$tasks->count()}\n";
foreach ($tasks as $task) {
    echo "- {$task->description}\n";
}
echo "\n";

echo "Question: Should games see universal tasks?\n";
echo "Current behavior: Universal tasks are " . ($tasks->contains($taskWithNoTags) ? "included" : "excluded") . "\n";

// Cleanup
$game->delete();
$taskWithTag->delete();
$taskWithNoTags->delete();
