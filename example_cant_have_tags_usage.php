<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\Game;
use App\Models\Player;
use App\Models\Tag;
use App\Models\Task;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Can't Have Tags Usage Examples ===\n\n";

// Example 1: Progressive Content System
echo "Example 1: Progressive Content System\n";
echo "--------------------------------------\n";

$beginnerTag = Tag::firstOrCreate(
    ['slug' => 'beginner-example'],
    ['name' => 'Beginner (Example)', 'description' => 'New player']
);

// Create beginner task
$beginnerTask = Task::create([
    'type' => 'dare',
    'description' => 'EXAMPLE: Introduce yourself to the group',
    'spice_rating' => 1,
    'tags_to_remove' => [$beginnerTag->id], // Removes beginner tag on completion
    'draft' => false
]);
$beginnerTask->tags()->attach($beginnerTag->id);

echo "Created beginner task that removes 'beginner' tag on completion\n";

// Create advanced task that beginners can't see
$advancedTask = Task::create([
    'type' => 'dare',
    'description' => 'EXAMPLE: Advanced challenge for experienced players',
    'spice_rating' => 4,
    'cant_have_tags' => [$beginnerTag->id], // Hidden from beginners
    'draft' => false
]);

echo "Created advanced task hidden from beginners\n\n";

// Example 2: Exclusive Group Content
echo "Example 2: Exclusive Group Content\n";
echo "-----------------------------------\n";

$teamATag = Tag::firstOrCreate(
    ['slug' => 'team-a-example'],
    ['name' => 'Team A (Example)', 'description' => 'Member of Team A']
);

$teamBTag = Tag::firstOrCreate(
    ['slug' => 'team-b-example'],
    ['name' => 'Team B (Example)', 'description' => 'Member of Team B']
);

// Task only for Team A (Team B can't see it)
$teamATask = Task::create([
    'type' => 'truth',
    'description' => 'EXAMPLE: Team A secret mission',
    'spice_rating' => 2,
    'cant_have_tags' => [$teamBTag->id],
    'draft' => false
]);
$teamATask->tags()->attach($teamATag->id);

echo "Created Team A exclusive task (hidden from Team B)\n";

// Task only for Team B (Team A can't see it)
$teamBTask = Task::create([
    'type' => 'dare',
    'description' => 'EXAMPLE: Team B special challenge',
    'spice_rating' => 2,
    'cant_have_tags' => [$teamATag->id],
    'draft' => false
]);
$teamBTask->tags()->attach($teamBTag->id);

echo "Created Team B exclusive task (hidden from Team A)\n\n";

// Example 3: Content Filtering
echo "Example 3: Content Filtering\n";
echo "-----------------------------\n";

$nonDrinkerTag = Tag::firstOrCreate(
    ['slug' => 'non-drinker-example'],
    ['name' => 'Non-Drinker (Example)', 'description' => 'Does not drink alcohol']
);

$alcoholTag = Tag::firstOrCreate(
    ['slug' => 'alcohol-example'],
    ['name' => 'Alcohol Friendly (Example)', 'description' => 'Comfortable with drinking']
);

// Drinking task hidden from non-drinkers
$drinkingTask = Task::create([
    'type' => 'dare',
    'description' => 'EXAMPLE: Take a sip of your drink',
    'spice_rating' => 2,
    'cant_have_tags' => [$nonDrinkerTag->id],
    'draft' => false
]);
$drinkingTask->tags()->attach($alcoholTag->id);

echo "Created drinking task (hidden from non-drinkers)\n\n";

// Example 4: Testing the Filtering
echo "Example 4: Testing the Filtering\n";
echo "---------------------------------\n";

// Create test game
$game = Game::create([
    'name' => 'Example Cant Have Tags Game',
    'code' => 'EXMPL1',
    'status' => 'active',
    'max_spice_rating' => 5
]);

// Add all tags to game
$game->tags()->attach([
    $beginnerTag->id,
    $teamATag->id,
    $teamBTag->id,
    $alcoholTag->id,
    $nonDrinkerTag->id
]);

echo "Created test game with all tags\n";

// Create beginner player
$beginnerPlayer = Player::create([
    'game_id' => $game->id,
    'name' => 'Beginner Player',
    'order' => 1,
    'is_active' => true
]);
$beginnerPlayer->tags()->attach($beginnerTag->id);

echo "Created beginner player\n";

// Create Team A player
$teamAPlayer = Player::create([
    'game_id' => $game->id,
    'name' => 'Team A Player',
    'order' => 2,
    'is_active' => true
]);
$teamAPlayer->tags()->attach($teamATag->id);

echo "Created Team A player\n";

// Create non-drinker player
$nonDrinkerPlayer = Player::create([
    'game_id' => $game->id,
    'name' => 'Non-Drinker Player',
    'order' => 3,
    'is_active' => true
]);
$nonDrinkerPlayer->tags()->attach($nonDrinkerTag->id);

echo "Created non-drinker player\n\n";

// Test filtering
echo "Testing Task Visibility:\n";
echo "========================\n\n";

echo "Beginner Player can see:\n";
$beginnerTasks = $beginnerPlayer->getAvailableTasks();
foreach ($beginnerTasks as $task) {
    if (str_contains($task->description, 'EXAMPLE:')) {
        echo "  - {$task->description}\n";
    }
}
echo "  Total: " . $beginnerTasks->filter(fn($t) => str_contains($t->description, 'EXAMPLE:'))->count() . " example tasks\n\n";

echo "Team A Player can see:\n";
$teamATasks = $teamAPlayer->getAvailableTasks();
foreach ($teamATasks as $task) {
    if (str_contains($task->description, 'EXAMPLE:')) {
        echo "  - {$task->description}\n";
    }
}
echo "  Total: " . $teamATasks->filter(fn($t) => str_contains($t->description, 'EXAMPLE:'))->count() . " example tasks\n\n";

echo "Non-Drinker Player can see:\n";
$nonDrinkerTasks = $nonDrinkerPlayer->getAvailableTasks();
foreach ($nonDrinkerTasks as $task) {
    if (str_contains($task->description, 'EXAMPLE:')) {
        echo "  - {$task->description}\n";
    }
}
echo "  Total: " . $nonDrinkerTasks->filter(fn($t) => str_contains($t->description, 'EXAMPLE:'))->count() . " example tasks\n\n";

// Example 5: Using isAvailableForPlayer() method
echo "Example 5: Checking Task Availability\n";
echo "--------------------------------------\n";

echo "Advanced task availability:\n";
echo "  Beginner Player: " . ($advancedTask->isAvailableForPlayer($beginnerPlayer) ? "✓ Yes" : "✗ No") . "\n";
echo "  Team A Player: " . ($advancedTask->isAvailableForPlayer($teamAPlayer) ? "✓ Yes" : "✗ No") . "\n\n";

echo "Team A exclusive task availability:\n";
echo "  Team A Player: " . ($teamATask->isAvailableForPlayer($teamAPlayer) ? "✓ Yes" : "✗ No") . "\n";
echo "  Beginner Player: " . ($teamATask->isAvailableForPlayer($beginnerPlayer) ? "✓ Yes" : "✗ No") . "\n\n";

echo "Drinking task availability:\n";
echo "  Non-Drinker Player: " . ($drinkingTask->isAvailableForPlayer($nonDrinkerPlayer) ? "✓ Yes" : "✗ No") . "\n";
echo "  Team A Player: " . ($drinkingTask->isAvailableForPlayer($teamAPlayer) ? "✓ Yes" : "✗ No") . "\n\n";

// Example 6: Getting Can't Have Tags
echo "Example 6: Getting Can't Have Tags\n";
echo "-----------------------------------\n";

echo "Advanced task can't have tags:\n";
foreach ($advancedTask->getCantHaveTags() as $tag) {
    echo "  - {$tag->name} (ID: {$tag->id})\n";
}

echo "\nDrinking task can't have tags:\n";
foreach ($drinkingTask->getCantHaveTags() as $tag) {
    echo "  - {$tag->name} (ID: {$tag->id})\n";
}

echo "\n\n=== Usage Examples Complete ===\n";
echo "\nNote: Example data created with 'EXAMPLE:' prefix in descriptions\n";
echo "Clean up by deleting tasks/tags with 'Example' in their names\n";
