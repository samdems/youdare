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

echo "🔍 DEBUG: Tag Removal Investigation\n";
echo str_repeat("=", 70) . "\n\n";

// Clean up old test data
echo "🧹 Cleaning up old test data...\n";
Game::where("code", "DEBUG_TEST")->delete();
Task::where("description", "like", "DEBUG:%")->delete();
Tag::where("slug", "debug-test-tag")->delete();

// Create a test tag
echo "\n📋 Creating test tag...\n";
$testTag = Tag::create([
    "name" => "Debug Test Tag",
    "slug" => "debug-test-tag",
    "description" => "Tag for debugging removal",
    "color" => "#ff0000",
    "min_spice_level" => 1,
]);
echo "✓ Created tag ID: {$testTag->id}\n";
echo "  Name: {$testTag->name}\n";
echo "  Slug: {$testTag->slug}\n";

// Create a test task that removes the tag
echo "\n📝 Creating test task...\n";
$task = Task::create([
    "type" => "truth",
    "spice_rating" => 1,
    "description" => "DEBUG: Task that removes test tag",
    "draft" => false,
    "tags_to_remove" => [$testTag->id],
]);
echo "✓ Created task ID: {$task->id}\n";
echo "  Description: {$task->description}\n";
echo "  tags_to_remove field: " . json_encode($task->tags_to_remove) . "\n";
echo "  tags_to_remove is array? " . (is_array($task->tags_to_remove) ? "YES" : "NO") . "\n";
echo "  tags_to_remove is empty? " . (empty($task->tags_to_remove) ? "YES" : "NO") . "\n";

// Attach the tag to the task as well
$task->tags()->attach([$testTag->id]);
echo "  Task has tags: " . $task->tags()->pluck("name")->join(", ") . "\n";

// Create a game
echo "\n🎲 Creating test game...\n";
$game = Game::create([
    "code" => "DEBUG_TEST",
    "max_spice_rating" => 5,
    "status" => "active",
]);
echo "✓ Created game ID: {$game->id}\n";

// Create a player with the tag
echo "\n👤 Creating test player...\n";
$player = $game->players()->create([
    "name" => "Debug Player",
    "gender" => "female",
    "score" => 0,
    "is_active" => true,
    "order" => 1,
]);
echo "✓ Created player ID: {$player->id}\n";

// Attach the tag to the player
echo "\n🏷️  Attaching tag to player...\n";
$player->tags()->attach([$testTag->id]);
$player->refresh()->load("tags");
echo "✓ Player tags: " . $player->tags->pluck("name")->join(", ") . "\n";
echo "  Player has {$player->tags->count()} tag(s)\n";

// Check the pivot table directly
echo "\n🔍 Checking player_tag pivot table...\n";
$pivotRecords = \DB::table("player_tag")
    ->where("player_id", $player->id)
    ->get();
echo "  Found {$pivotRecords->count()} record(s) in player_tag table:\n";
foreach ($pivotRecords as $record) {
    echo "    - player_id: {$record->player_id}, tag_id: {$record->tag_id}\n";
}

// Now attempt to remove the tag
echo "\n" . str_repeat("=", 70) . "\n";
echo "🎯 ATTEMPTING TAG REMOVAL\n";
echo str_repeat("=", 70) . "\n\n";

echo "Step 1: Check if tag exists on player\n";
$hasTag = $player->tags()->where("tag_id", $testTag->id)->exists();
echo "  Result: " . ($hasTag ? "YES - Tag exists" : "NO - Tag does NOT exist") . "\n\n";

echo "Step 2: Call removeTagsFromPlayer method\n";
echo "  Calling: \$task->removeTagsFromPlayer(\$player)\n";
$removedTagIds = $task->removeTagsFromPlayer($player);
echo "  Returned removed tag IDs: " . json_encode($removedTagIds) . "\n";
echo "  Count of removed tags: " . count($removedTagIds) . "\n\n";

echo "Step 3: Refresh player and check tags\n";
$player->refresh()->load("tags");
echo "  Player tags after removal: " . $player->tags->pluck("name")->join(", ") . "\n";
echo "  Player has {$player->tags->count()} tag(s)\n\n";

echo "Step 4: Check pivot table again\n";
$pivotRecordsAfter = \DB::table("player_tag")
    ->where("player_id", $player->id)
    ->get();
echo "  Found {$pivotRecordsAfter->count()} record(s) in player_tag table:\n";
foreach ($pivotRecordsAfter as $record) {
    echo "    - player_id: {$record->player_id}, tag_id: {$record->tag_id}\n";
}

// Summary
echo "\n" . str_repeat("=", 70) . "\n";
echo "📊 SUMMARY\n";
echo str_repeat("=", 70) . "\n\n";

if (count($removedTagIds) > 0 && $player->tags->count() === 0) {
    echo "✅ SUCCESS: Tag was removed successfully!\n";
    echo "   - removeTagsFromPlayer returned: " . json_encode($removedTagIds) . "\n";
    echo "   - Player now has 0 tags\n";
    echo "   - Pivot table is clean\n";
} else {
    echo "❌ FAILURE: Tag was NOT removed!\n";
    echo "\n🔍 DIAGNOSTIC INFO:\n";
    echo "   Task ID: {$task->id}\n";
    echo "   Task tags_to_remove: " . json_encode($task->tags_to_remove) . "\n";
    echo "   Task tags_to_remove type: " . gettype($task->tags_to_remove) . "\n";
    echo "   Player ID: {$player->id}\n";
    echo "   Player tags before: 1\n";
    echo "   Player tags after: {$player->tags->count()}\n";
    echo "   Removed tag IDs: " . json_encode($removedTagIds) . "\n";

    // Try manual removal
    echo "\n🔧 Attempting MANUAL removal...\n";
    $player->tags()->detach($testTag->id);
    $player->refresh()->load("tags");
    echo "   Player tags after manual detach: {$player->tags->count()}\n";

    if ($player->tags->count() === 0) {
        echo "   ✓ Manual removal worked! Issue is with removeTagsFromPlayer method\n";
    } else {
        echo "   ✗ Manual removal also failed! Issue is with database or relationship\n";
    }
}

// Test via API simulation
echo "\n" . str_repeat("=", 70) . "\n";
echo "🌐 SIMULATING API CALL\n";
echo str_repeat("=", 70) . "\n\n";

// Reset by adding tag back
$player->tags()->sync([$testTag->id]);
$player->refresh()->load("tags");
echo "Reset player tags: " . $player->tags->pluck("name")->join(", ") . "\n\n";

echo "Simulating POST /api/players/{$player->id}/complete-task\n";
echo "Body: " . json_encode(["task_id" => $task->id, "points" => 1]) . "\n\n";

// Simulate what the controller does
$taskToComplete = Task::findOrFail($task->id);
$removedViaApi = $taskToComplete->removeTagsFromPlayer($player);
$player->incrementScore(1);
$player->refresh()->load("tags");

echo "Response would include:\n";
echo "  - removed_tags_count: " . count($removedViaApi) . "\n";
echo "  - player tags: " . $player->tags->pluck("name")->join(", ") . "\n";
echo "  - player score: {$player->score}\n\n";

if (count($removedViaApi) > 0) {
    echo "✅ API simulation: Tag removed successfully\n";
} else {
    echo "❌ API simulation: Tag NOT removed\n";
}

// Cleanup
echo "\n🧹 Cleaning up...\n";
$game->delete();
$task->delete();
$testTag->delete();

echo "\n✅ Debug script completed!\n";
