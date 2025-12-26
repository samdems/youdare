<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\Game;
use App\Models\Player;
use App\Models\Tag;
use App\Models\Task;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$game = Game::create(['name' => 'Debug Game', 'max_spice_rating' => 5, 'status' => 'active']);
$adultTag = Tag::firstOrCreate(['slug' => 'adult'], ['name' => 'Adult', 'min_spice_level' => 0]);

$playerNoTags = $game->players()->create(['name' => 'No Tags', 'order' => 1]);

$universalTask = Task::create([
    'type' => 'truth',
    'spice_rating' => 1,
    'description' => 'Universal task',
    'draft' => false
]);

$cantHaveAdultTask = Task::create([
    'type' => 'dare',
    'spice_rating' => 1,
    'description' => 'Cant have adult',
    'draft' => false,
    'cant_have_tags' => [$adultTag->id]
]);

$removeAdultTask = Task::create([
    'type' => 'truth',
    'spice_rating' => 1,
    'description' => 'Remove adult',
    'draft' => false,
    'tags_to_remove' => [$adultTag->id]
]);

echo "Player with no tags:\n";
$tasks = $playerNoTags->getAvailableTasks();
echo "Total tasks: {$tasks->count()}\n";
foreach ($tasks as $task) {
    echo "- {$task->description} (tags: " . $task->tags->count() . ", tags_to_remove: " . ($task->tags_to_remove ? 'yes' : 'no') . ")\n";
}

// Cleanup
$game->delete();
$universalTask->delete();
$cantHaveAdultTask->delete();
$removeAdultTask->delete();
