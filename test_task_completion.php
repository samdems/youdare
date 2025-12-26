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

echo "🎮 Task Completion with Tag Removal Test\n";
echo str_repeat("=", 60) . "\n\n";

// Clean up old test data
echo "🧹 Cleaning up old test data...\n";
Game::where("code", "like", "TEST%")->delete();
Task::where("description", "like", "%TEST_COMPLETION%")->delete();
Tag::whereIn("slug", ["test-beginner", "test-intermediate", "test-advanced"])->delete();

// Create test tags
echo "\n📋 Creating test tags...\n";
$beginnerTag = Tag::create([
    "name" => "Test Beginner",
    "slug" => "test-beginner",
    "description" => "For beginners - will be removed on first task completion",
    "color" => "#22c55e",
    "min_spice_level" => 1,
]);

$intermediateTag = Tag::create([
    "name" => "Test Intermediate",
    "slug" => "test-intermediate",
    "description" => "For intermediate players - will be removed on second task completion",
    "color" => "#3b82f6",
    "min_spice_level" => 2,
]);

$advancedTag = Tag::create([
    "name" => "Test Advanced",
    "slug" => "test-advanced",
    "description" => "For advanced players - stays throughout",
    "color" => "#8b5cf6",
    "min_spice_level" => 3,
]);

echo "   ✓ Created: {$beginnerTag->name}\n";
echo "   ✓ Created: {$intermediateTag->name}\n";
echo "   ✓ Created: {$advancedTag->name}\n";

// Create test tasks
echo "\n📝 Creating test tasks...\n";

$task1 = Task::create([
    "type" => "truth",
    "spice_rating" => 1,
    "description" => "TEST_COMPLETION: What's your favorite color? (Removes beginner tag)",
    "draft" => false,
    "tags_to_remove" => [$beginnerTag->id],
]);
$task1->tags()->attach([$beginnerTag->id, $intermediateTag->id]);

$task2 = Task::create([
    "type" => "dare",
    "spice_rating" => 2,
    "description" => "TEST_COMPLETION: Do 5 jumping jacks (Removes intermediate tag)",
    "draft" => false,
    "tags_to_remove" => [$intermediateTag->id],
]);
$task2->tags()->attach([$intermediateTag->id, $advancedTag->id]);

$task3 = Task::create([
    "type" => "truth",
    "spice_rating" => 3,
    "description" => "TEST_COMPLETION: Tell an embarrassing story (No tags removed)",
    "draft" => false,
    "tags_to_remove" => [],
]);
$task3->tags()->attach([$advancedTag->id]);

echo "   ✓ Task 1: Beginner task (removes beginner tag)\n";
echo "   ✓ Task 2: Intermediate task (removes intermediate tag)\n";
echo "   ✓ Task 3: Advanced task (no tag removal)\n";

// Create game
echo "\n🎲 Creating test game...\n";
$game = Game::create([
    "code" => "TEST_" . strtoupper(substr(md5(time()), 0, 4)),
    "max_spice_rating" => 5,
    "status" => "active",
]);
echo "   ✓ Game Code: {$game->code}\n";

// Create player with all three tags
echo "\n👤 Creating test player...\n";
$player = $game->players()->create([
    "name" => "Alice",
    "gender" => "female",
    "score" => 0,
    "is_active" => true,
    "order" => 1,
]);
$player->tags()->attach([$beginnerTag->id, $intermediateTag->id, $advancedTag->id]);
$player->load("tags");

echo "   ✓ Player: {$player->name}\n";
echo "   ✓ Initial Tags: " . $player->tags->pluck("name")->join(", ") . "\n";
echo "   ✓ Initial Score: {$player->score}\n";

// Test task completion flow
echo "\n" . str_repeat("=", 60) . "\n";
echo "🎯 TASK COMPLETION FLOW\n";
echo str_repeat("=", 60) . "\n\n";

// Complete Task 1 (removes beginner tag)
echo "▶️  Round 1: Completing Task 1\n";
echo "   Task: {$task1->description}\n";
echo "   Tags to remove: " . $task1->getRemovableTags()->pluck("name")->join(", ") . "\n";

$removedTags = $task1->removeTagsFromPlayer($player);
$player->incrementScore();
$player->refresh();
$player->load("tags");

echo "   ✓ Removed {" . count($removedTags) . "} tag(s): ";
if (count($removedTags) > 0) {
    $removedTagNames = Tag::whereIn("id", $removedTags)->pluck("name")->join(", ");
    echo $removedTagNames . "\n";
} else {
    echo "none\n";
}
echo "   ✓ Current Tags: " . $player->tags->pluck("name")->join(", ") . "\n";
echo "   ✓ Current Score: {$player->score}\n\n";

// Complete Task 2 (removes intermediate tag)
echo "▶️  Round 2: Completing Task 2\n";
echo "   Task: {$task2->description}\n";
echo "   Tags to remove: " . $task2->getRemovableTags()->pluck("name")->join(", ") . "\n";

$removedTags = $task2->removeTagsFromPlayer($player);
$player->incrementScore();
$player->refresh();
$player->load("tags");

echo "   ✓ Removed {" . count($removedTags) . "} tag(s): ";
if (count($removedTags) > 0) {
    $removedTagNames = Tag::whereIn("id", $removedTags)->pluck("name")->join(", ");
    echo $removedTagNames . "\n";
} else {
    echo "none\n";
}
echo "   ✓ Current Tags: " . $player->tags->pluck("name")->join(", ") . "\n";
echo "   ✓ Current Score: {$player->score}\n\n";

// Complete Task 3 (removes no tags)
echo "▶️  Round 3: Completing Task 3\n";
echo "   Task: {$task3->description}\n";
if ($task3->tags_to_remove && count($task3->tags_to_remove) > 0) {
    echo "   Tags to remove: " . $task3->getRemovableTags()->pluck("name")->join(", ") . "\n";
} else {
    echo "   Tags to remove: none\n";
}

$removedTags = $task3->removeTagsFromPlayer($player);
$player->incrementScore();
$player->refresh();
$player->load("tags");

echo "   ✓ Removed {" . count($removedTags) . "} tag(s): ";
if (count($removedTags) > 0) {
    $removedTagNames = Tag::whereIn("id", $removedTags)->pluck("name")->join(", ");
    echo $removedTagNames . "\n";
} else {
    echo "none\n";
}
echo "   ✓ Current Tags: " . $player->tags->pluck("name")->join(", ") . "\n";
echo "   ✓ Current Score: {$player->score}\n\n";

// Final summary
echo str_repeat("=", 60) . "\n";
echo "📊 FINAL SUMMARY\n";
echo str_repeat("=", 60) . "\n\n";

echo "Player: {$player->name}\n";
echo "Final Score: {$player->score}\n";
echo "Final Tags: " . $player->tags->pluck("name")->join(", ") . "\n\n";

echo "Tag Progression:\n";
echo "  Started with: Test Beginner, Test Intermediate, Test Advanced\n";
echo "  After Task 1: Test Intermediate, Test Advanced (removed Beginner)\n";
echo "  After Task 2: Test Advanced (removed Intermediate)\n";
echo "  After Task 3: Test Advanced (no change)\n\n";

// API endpoint test
echo str_repeat("=", 60) . "\n";
echo "🔌 API ENDPOINT TEST\n";
echo str_repeat("=", 60) . "\n\n";

echo "To test via API, use:\n\n";
echo "POST /api/players/{$player->id}/complete-task\n";
echo "Content-Type: application/json\n\n";
echo json_encode([
    "task_id" => $task1->id,
    "points" => 1
], JSON_PRETTY_PRINT) . "\n\n";

echo "This endpoint will:\n";
echo "  1. Remove tags specified in task's tags_to_remove field\n";
echo "  2. Increment player's score by specified points (default: 1)\n";
echo "  3. Return player data with updated tags\n\n";

// Available tasks check
echo str_repeat("=", 60) . "\n";
echo "📋 AVAILABLE TASKS BY TAG\n";
echo str_repeat("=", 60) . "\n\n";

$availableTags = $player->getAllAvailableTags();
$availableTasks = Task::published()
    ->whereHas("tags", function ($q) use ($availableTags) {
        $q->whereIn("tags.id", $availableTags);
    })
    ->get();

echo "Player currently has " . count($availableTags) . " tag(s)\n";
echo "Available tasks: {$availableTasks->count()}\n\n";

foreach ($availableTasks as $task) {
    echo "  • [{$task->type}] {$task->description}\n";
    echo "    Tags: " . $task->tags->pluck("name")->join(", ") . "\n";
    if ($task->tags_to_remove && count($task->tags_to_remove) > 0) {
        echo "    Removes: " . $task->getRemovableTags()->pluck("name")->join(", ") . "\n";
    }
    echo "\n";
}

echo str_repeat("=", 60) . "\n";
echo "✅ Test completed successfully!\n";
echo str_repeat("=", 60) . "\n\n";

echo "💡 Use Cases for Tag Removal:\n";
echo "  • Progressive difficulty (remove 'beginner' after first task)\n";
echo "  • One-time challenges (remove 'first-time' tags)\n";
echo "  • Unlock content (remove restrictions after achievements)\n";
echo "  • Tutorial progression (remove 'tutorial' tags as player advances)\n";
echo "  • Category completion (remove category tags when done)\n\n";

echo "To clean up test data, run:\n";
echo "  php artisan tinker\n";
echo "  Game::where('code', '{$game->code}')->delete();\n";
echo "  Task::where('description', 'like', '%TEST_COMPLETION%')->delete();\n";
echo "  Tag::whereIn('slug', ['test-beginner', 'test-intermediate', 'test-advanced'])->delete();\n";
