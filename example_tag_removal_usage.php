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

echo "🎉 Party Game with Progressive Challenges - Tag Removal Example\n";
echo str_repeat("=", 70) . "\n\n";

echo "Scenario: A party game where players progress through difficulty levels\n";
echo "Players start as rookies and advance by completing challenges.\n\n";

// Clean up old demo data
echo "🧹 Setting up demo...\n";
Game::where("code", "PARTY_DEMO")->delete();
Task::where("description", "like", "DEMO:%")->delete();
Tag::whereIn("slug", [
    "rookie",
    "intermediate-locked",
    "expert-locked",
    "warm-up",
    "party-master",
])->delete();

// Create progression tags
echo "\n📋 Creating progression system...\n";

$rookieTag = Tag::create([
    "name" => "Rookie",
    "slug" => "rookie",
    "description" => "New to the game - removed after first challenge",
    "color" => "#10b981",
    "min_spice_level" => 1,
]);

$warmUpTag = Tag::create([
    "name" => "Warm-Up",
    "slug" => "warm-up",
    "description" => "Easy starter challenges - removed after 3 tasks",
    "color" => "#f59e0b",
    "min_spice_level" => 1,
]);

$intermediateLockedTag = Tag::create([
    "name" => "Intermediate Locked",
    "slug" => "intermediate-locked",
    "description" => "Prevents intermediate tasks - removed to unlock",
    "color" => "#ef4444",
    "min_spice_level" => 1,
]);

$expertLockedTag = Tag::create([
    "name" => "Expert Locked",
    "slug" => "expert-locked",
    "description" => "Prevents expert tasks - removed to unlock",
    "color" => "#dc2626",
    "min_spice_level" => 1,
]);

$partyMasterTag = Tag::create([
    "name" => "Party Master",
    "slug" => "party-master",
    "description" => "Unlocked after becoming expert",
    "color" => "#8b5cf6",
    "min_spice_level" => 3,
]);

echo "   ✓ Rookie (removed after first task)\n";
echo "   ✓ Warm-Up (removed after starter challenges)\n";
echo "   ✓ Intermediate Locked (removed to unlock intermediate tasks)\n";
echo "   ✓ Expert Locked (removed to unlock expert tasks)\n";
echo "   ✓ Party Master (unlocked at expert level)\n";

// Create progressive tasks
echo "\n📝 Creating progressive tasks...\n\n";

echo "Level 1: Rookie Tasks (remove rookie status)\n";
$rookieTask1 = Task::create([
    "type" => "truth",
    "spice_rating" => 1,
    "description" =>
        "DEMO: What's your favorite ice cream flavor? (Welcome task)",
    "draft" => false,
    "tags_to_remove" => [$rookieTag->id],
]);
$rookieTask1->tags()->attach([$rookieTag->id]);
echo "   ✓ Task 1: Easy truth (removes Rookie tag)\n";

echo "\nLevel 2: Warm-Up Tasks (unlock intermediate)\n";
$warmUpTask1 = Task::create([
    "type" => "dare",
    "spice_rating" => 1,
    "description" => "DEMO: Give everyone a high-five (Warm-up challenge)",
    "draft" => false,
    "tags_to_remove" => [],
]);
$warmUpTask1->tags()->attach([$warmUpTag->id]);

$warmUpTask2 = Task::create([
    "type" => "truth",
    "spice_rating" => 2,
    "description" => "DEMO: Share a fun fact about yourself",
    "draft" => false,
    "tags_to_remove" => [],
]);
$warmUpTask2->tags()->attach([$warmUpTag->id]);

$warmUpTask3 = Task::create([
    "type" => "dare",
    "spice_rating" => 2,
    "description" =>
        "DEMO: Do your best celebrity impression (Unlocks intermediate)",
    "draft" => false,
    "tags_to_remove" => [$warmUpTag->id, $intermediateLockedTag->id],
]);
$warmUpTask3->tags()->attach([$warmUpTag->id]);
echo "   ✓ Tasks 2-4: Warm-up challenges\n";
echo "   ✓ Last warm-up task unlocks intermediate level\n";

echo "\nLevel 3: Intermediate Tasks (unlock expert)\n";
$intermediateTask1 = Task::create([
    "type" => "truth",
    "spice_rating" => 3,
    "description" =>
        "DEMO: What's the most embarrassing thing in your search history?",
    "draft" => false,
    "tags_to_remove" => [],
]);
// Only visible when intermediate is unlocked (no intermediate-locked tag)
$intermediateTask1->tags()->attach([]);

$intermediateTask2 = Task::create([
    "type" => "dare",
    "spice_rating" => 3,
    "description" =>
        "DEMO: Call someone and sing them 'Happy Birthday' (Unlocks expert)",
    "draft" => false,
    "tags_to_remove" => [$expertLockedTag->id],
]);
$intermediateTask2->tags()->attach([]);
echo "   ✓ Tasks 5-6: Intermediate challenges\n";
echo "   ✓ Completing these unlocks expert level\n";

echo "\nLevel 4: Expert Tasks (earn Party Master status)\n";
$expertTask = Task::create([
    "type" => "dare",
    "spice_rating" => 4,
    "description" =>
        "DEMO: Perform a dramatic reading of your last text message (Become Party Master)",
    "draft" => false,
    "tags_to_remove" => [],
]);
$expertTask->tags()->attach([$partyMasterTag->id]);
echo "   ✓ Tasks 7+: Expert challenges\n";
echo "   ✓ Requires Party Master tag\n";

// Create game and players
echo "\n🎲 Starting party game...\n";
$game = Game::create([
    "code" => "PARTY_DEMO",
    "max_spice_rating" => 5,
    "status" => "active",
]);
echo "   ✓ Game Code: {$game->code}\n";

// Create three players at different stages
echo "\n👥 Adding players...\n";

$alice = $game->players()->create([
    "name" => "Alice",
    "gender" => "female",
    "score" => 0,
    "is_active" => true,
    "order" => 1,
]);
$alice
    ->tags()
    ->attach([
        $rookieTag->id,
        $warmUpTag->id,
        $intermediateLockedTag->id,
        $expertLockedTag->id,
    ]);
echo "   ✓ Alice (Complete Beginner)\n";
echo "      Tags: Rookie, Warm-Up, Intermediate Locked, Expert Locked\n";

$bob = $game->players()->create([
    "name" => "Bob",
    "gender" => "male",
    "score" => 3,
    "is_active" => true,
    "order" => 2,
]);
$bob->tags()->attach([$warmUpTag->id, $expertLockedTag->id]);
echo "   ✓ Bob (Intermediate Player)\n";
echo "      Tags: Warm-Up, Expert Locked\n";

$carol = $game->players()->create([
    "name" => "Carol",
    "gender" => "female",
    "score" => 8,
    "is_active" => true,
    "order" => 3,
]);
$carol->tags()->attach([$partyMasterTag->id]);
echo "   ✓ Carol (Expert Player)\n";
echo "      Tags: Party Master\n";

// Simulate game progression for Alice
echo "\n" . str_repeat("=", 70) . "\n";
echo "🎮 ALICE'S PROGRESSION STORY\n";
echo str_repeat("=", 70) . "\n\n";

echo "Alice joins the party as a complete beginner...\n\n";

// Round 1: First task
echo "═══ Round 1: Welcome Task ═══\n";
$task = $rookieTask1;
echo "📋 Task: {$task->description}\n";
echo "   Type: {$task->type} | Spice: {$task->spice_rating}/5\n";
echo "   Alice's tags before: " .
    $alice->tags->pluck("name")->join(", ") .
    "\n\n";

$removedTags = $task->removeTagsFromPlayer($alice);
$alice->incrementScore();
$alice->refresh()->load("tags");

if (count($removedTags) > 0) {
    $removedNames = Tag::whereIn("id", $removedTags)
        ->pluck("name")
        ->join(", ");
    echo "   ✨ Removed tags: {$removedNames}\n";
}
echo "   ⭐ New score: {$alice->score}\n";
echo "   🏷️  Tags now: " . $alice->tags->pluck("name")->join(", ") . "\n";
echo "   ✓ Alice is no longer a rookie!\n\n";

// Round 2: Warm-up tasks
echo "═══ Round 2-4: Warm-Up Challenges ═══\n";
$warmUpTasks = [$warmUpTask1, $warmUpTask2, $warmUpTask3];

foreach ($warmUpTasks as $index => $task) {
    echo "Task " . ($index + 2) . ": {$task->description}\n";
    $removedTags = $task->removeTagsFromPlayer($alice);
    $alice->incrementScore();
    $alice->refresh()->load("tags");

    if (count($removedTags) > 0) {
        $removedNames = Tag::whereIn("id", $removedTags)
            ->pluck("name")
            ->join(", ");
        echo "   ✨ Removed tags: {$removedNames}\n";
    }
    echo "   ⭐ Score: {$alice->score}\n\n";
}

echo "   🏷️  Current tags: " . $alice->tags->pluck("name")->join(", ") . "\n";
echo "   ✓ Intermediate level unlocked!\n\n";

// Round 3: Intermediate
echo "═══ Round 5-6: Intermediate Challenges ═══\n";
$intermediateTasks = [$intermediateTask1, $intermediateTask2];

foreach ($intermediateTasks as $index => $task) {
    echo "Task " . ($index + 5) . ": {$task->description}\n";
    $removedTags = $task->removeTagsFromPlayer($alice);
    $alice->incrementScore();
    $alice->refresh()->load("tags");

    if (count($removedTags) > 0) {
        $removedNames = Tag::whereIn("id", $removedTags)
            ->pluck("name")
            ->join(", ");
        echo "   ✨ Removed tags: {$removedNames}\n";
    }
    echo "   ⭐ Score: {$alice->score}\n\n";
}

echo "   🏷️  Current tags: " . $alice->tags->pluck("name")->join(", ") . "\n";
echo "   ✓ Expert level unlocked!\n\n";

// Final summary
echo str_repeat("=", 70) . "\n";
echo "📊 FINAL PARTY STANDINGS\n";
echo str_repeat("=", 70) . "\n\n";

$players = Player::where("game_id", $game->id)
    ->with("tags")
    ->orderBy("score", "desc")
    ->get();

foreach ($players as $player) {
    echo "🏆 {$player->name}\n";
    echo "   Score: {$player->score} points\n";
    echo "   Level: ";

    $tagNames = $player->tags->pluck("slug")->toArray();
    if (in_array("rookie", $tagNames)) {
        echo "Rookie (Just started)\n";
    } elseif (in_array("warm-up", $tagNames)) {
        echo "Beginner (Warming up)\n";
    } elseif (in_array("expert-locked", $tagNames)) {
        echo "Intermediate (Making progress)\n";
    } elseif (in_array("party-master", $tagNames)) {
        echo "Party Master (Expert!)\n";
    } else {
        echo "Expert (Advanced)\n";
    }

    echo "   Tags: " . $player->tags->pluck("name")->join(", ") . "\n\n";
}

// Show progression mechanics
echo str_repeat("=", 70) . "\n";
echo "🎯 PROGRESSION MECHANICS\n";
echo str_repeat("=", 70) . "\n\n";

echo "This demo shows how tag removal creates progression:\n\n";

echo "1. NEW PLAYERS start with:\n";
echo "   • Rookie tag (removed after first task)\n";
echo "   • Warm-Up tag (removed after starter challenges)\n";
echo "   • Locked tags (prevent access to advanced content)\n\n";

echo "2. PROGRESSION happens when:\n";
echo "   • Completing first task removes 'Rookie'\n";
echo "   • Completing warm-up removes 'Intermediate Locked'\n";
echo "   • Completing intermediate removes 'Expert Locked'\n\n";

echo "3. CONTENT UNLOCKING:\n";
echo "   • Rookies see only welcome tasks\n";
echo "   • Warm-up players see beginner challenges\n";
echo "   • Intermediate players see harder content\n";
echo "   • Experts see all challenging tasks\n\n";

echo "4. BENEFITS:\n";
echo "   ✓ Automatic difficulty progression\n";
echo "   ✓ Players can't skip ahead\n";
echo "   ✓ Ensures everyone has fun at their level\n";
echo "   ✓ Creates sense of achievement\n";
echo "   ✓ No manual intervention needed\n\n";

// API examples
echo str_repeat("=", 70) . "\n";
echo "🔌 API USAGE EXAMPLES\n";
echo str_repeat("=", 70) . "\n\n";

echo "Complete a task with tag removal:\n";
echo "POST /api/players/{$alice->id}/complete-task\n\n";
echo json_encode(
    [
        "task_id" => $rookieTask1->id,
        "points" => 1,
    ],
    JSON_PRETTY_PRINT,
) . "\n\n";

echo "Check player's current tags:\n";
echo "GET /api/players/{$alice->id}\n\n";

echo "Get available tasks for player:\n";
echo "GET /api/players/{$alice->id}/tasks\n\n";

// Cleanup instructions
echo str_repeat("=", 70) . "\n";
echo "🧹 CLEANUP\n";
echo str_repeat("=", 70) . "\n\n";

echo "To remove demo data:\n\n";
echo "Game::where('code', 'PARTY_DEMO')->delete();\n";
echo "Task::where('description', 'like', 'DEMO:%')->delete();\n";
echo "Tag::whereIn('slug', ['rookie', 'warm-up', 'intermediate-locked', 'expert-locked', 'party-master'])->delete();\n\n";

echo "✅ Demo completed!\n";
echo "This example shows a complete party game progression system using tag removal.\n";
