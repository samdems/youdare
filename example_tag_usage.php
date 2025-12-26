<?php

/**
 * Example Tag Usage Script
 *
 * This script demonstrates how to use the tagging system in the YouDare application.
 * Run with: php example_tag_usage.php
 */

require __DIR__ . '/vendor/autoload.php';

use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== YouDare Tagging System Examples ===\n\n";

// Example 1: List all available tags
echo "1. Listing all available tags:\n";
echo str_repeat("-", 50) . "\n";
$tags = Tag::all();
foreach ($tags as $tag) {
    echo "   [{$tag->id}] {$tag->name} ({$tag->slug})\n";
    echo "       {$tag->description}\n\n";
}

// Example 2: Create a new tag
echo "\n2. Creating a new tag:\n";
echo str_repeat("-", 50) . "\n";
$newTag = Tag::firstOrCreate(
    ['slug' => 'indoor-activities'],
    [
        'name' => 'Indoor Activities',
        'description' => 'Tasks that can be performed indoors'
    ]
);
echo "   Created/Found tag: {$newTag->name} (ID: {$newTag->id})\n";

// Example 3: Create a task with tags
echo "\n3. Creating a task with tags:\n";
echo str_repeat("-", 50) . "\n";
$task = Task::create([
    'type' => 'dare',
    'spice_rating' => 2,
    'description' => 'Perform a funny dance for 30 seconds',
    'draft' => false
]);
// Attach tags to the task
$funnyTag = Tag::where('slug', 'funny')->first();
$physicalTag = Tag::where('slug', 'physical')->first();
if ($funnyTag && $physicalTag) {
    $task->tags()->attach([$funnyTag->id, $physicalTag->id]);
    echo "   Task created with ID: {$task->id}\n";
    echo "   Tags attached: Funny, Physical\n";
    echo "   Description: {$task->description}\n";
}

// Example 4: Create a user and assign tags
echo "\n4. Creating a user and assigning tags:\n";
echo str_repeat("-", 50) . "\n";
$user = User::firstOrCreate(
    ['email' => 'demo@example.com'],
    [
        'name' => 'Demo User',
        'password' => bcrypt('password123')
    ]
);
echo "   User: {$user->name} (ID: {$user->id})\n";

// Assign tags to user
$familyFriendlyTag = Tag::where('slug', 'family-friendly')->first();
$funnyTag = Tag::where('slug', 'funny')->first();
$socialTag = Tag::where('slug', 'social')->first();

if ($familyFriendlyTag && $funnyTag && $socialTag) {
    $user->tags()->sync([
        $familyFriendlyTag->id,
        $funnyTag->id,
        $socialTag->id
    ]);
    echo "   Tags assigned: Family Friendly, Funny, Social\n";
}

// Example 5: Check if user has specific tags
echo "\n5. Checking user tags:\n";
echo str_repeat("-", 50) . "\n";
if ($user->hasTag('funny')) {
    echo "   ✓ User has 'Funny' tag\n";
}
if ($user->hasTag('adults-only')) {
    echo "   ✓ User has 'Adults Only' tag\n";
} else {
    echo "   ✗ User does NOT have 'Adults Only' tag\n";
}

// Example 6: Get tasks filtered by user's tags
echo "\n6. Getting tasks filtered by user's tags:\n";
echo str_repeat("-", 50) . "\n";
$userTagIds = $user->tags()->pluck('tags.id')->toArray();
echo "   User's tag IDs: [" . implode(', ', $userTagIds) . "]\n\n";

if (!empty($userTagIds)) {
    $filteredTasks = Task::whereHas('tags', function ($q) use ($userTagIds) {
        $q->whereIn('tags.id', $userTagIds);
    })->with('tags')->get();

    echo "   Found {$filteredTasks->count()} matching tasks:\n";
    foreach ($filteredTasks->take(5) as $task) {
        $tagNames = $task->tags->pluck('name')->implode(', ');
        echo "   - [{$task->id}] {$task->type} (Spice: {$task->spice_rating})\n";
        echo "     Tags: {$tagNames}\n";
        echo "     Description: " . substr($task->description, 0, 50) . "...\n\n";
    }
} else {
    echo "   User has no tags, showing tasks without tags...\n";
    $untaggedTasks = Task::whereDoesntHave('tags')->get();
    echo "   Found {$untaggedTasks->count()} untagged tasks\n";
}

// Example 7: Create tasks with different tag combinations
echo "\n7. Creating sample tasks with various tags:\n";
echo str_repeat("-", 50) . "\n";

$sampleTasks = [
    [
        'type' => 'truth',
        'spice_rating' => 1,
        'description' => 'What is your favorite childhood memory?',
        'tags' => ['family-friendly', 'social']
    ],
    [
        'type' => 'dare',
        'spice_rating' => 3,
        'description' => 'Tell a romantic story about your partner',
        'tags' => ['romantic', 'adults-only']
    ],
    [
        'type' => 'dare',
        'spice_rating' => 2,
        'description' => 'Do 20 jumping jacks right now',
        'tags' => ['physical', 'party-mode']
    ],
    [
        'type' => 'truth',
        'spice_rating' => 2,
        'description' => 'What is the most creative thing you have ever done?',
        'tags' => ['creative', 'mental']
    ]
];

foreach ($sampleTasks as $taskData) {
    $tagSlugs = $taskData['tags'];
    unset($taskData['tags']);

    $task = Task::create(array_merge($taskData, ['draft' => false]));

    $tagIds = Tag::whereIn('slug', $tagSlugs)->pluck('id')->toArray();
    if (!empty($tagIds)) {
        $task->tags()->attach($tagIds);
    }

    echo "   ✓ Created {$task->type} task with tags: " . implode(', ', $tagSlugs) . "\n";
}

// Example 8: Using query scopes
echo "\n8. Using query scopes to filter tasks:\n";
echo str_repeat("-", 50) . "\n";

// Get all tasks with specific tags
$partyTag = Tag::where('slug', 'party-mode')->first();
if ($partyTag) {
    $partyTasks = Task::withTags([$partyTag->id])->count();
    echo "   Tasks with 'Party Mode' tag: {$partyTasks}\n";
}

$funnyTag = Tag::where('slug', 'funny')->first();
if ($funnyTag) {
    $funnyTasks = Task::withTags([$funnyTag->id])->count();
    echo "   Tasks with 'Funny' tag: {$funnyTasks}\n";
}

// Example 9: Tag statistics
echo "\n9. Tag statistics:\n";
echo str_repeat("-", 50) . "\n";
$tagsWithCounts = Tag::withCount(['tasks', 'users'])->orderBy('tasks_count', 'desc')->get();
echo "   Tag Usage:\n";
echo "   " . str_pad("Tag", 25) . str_pad("Tasks", 10) . str_pad("Users", 10) . "\n";
echo "   " . str_repeat("-", 45) . "\n";
foreach ($tagsWithCounts->take(10) as $tag) {
    echo "   " . str_pad($tag->name, 25) . str_pad($tag->tasks_count, 10) . str_pad($tag->users_count, 10) . "\n";
}

// Example 10: Simulating user experience
echo "\n10. Simulating user experience:\n";
echo str_repeat("-", 50) . "\n";

// Create another user with different preferences
$user2 = User::firstOrCreate(
    ['email' => 'party@example.com'],
    [
        'name' => 'Party Person',
        'password' => bcrypt('password123')
    ]
);

$partyTag = Tag::where('slug', 'party-mode')->first();
$extremeTag = Tag::where('slug', 'extreme')->first();
$adultsTag = Tag::where('slug', 'adults-only')->first();

if ($partyTag && $extremeTag && $adultsTag) {
    $user2->tags()->sync([
        $partyTag->id,
        $extremeTag->id,
        $adultsTag->id
    ]);

    echo "   User: {$user2->name}\n";
    echo "   Tags: Party Mode, Extreme, Adults Only\n\n";

    // Get tasks this user can see
    $user2TagIds = $user2->tags()->pluck('tags.id')->toArray();
    $visibleTasks = Task::whereHas('tags', function ($q) use ($user2TagIds) {
        $q->whereIn('tags.id', $user2TagIds);
    })->count();

    echo "   This user can see {$visibleTasks} tasks\n";

    // Compare with first user
    $user1TagIds = $user->tags()->pluck('tags.id')->toArray();
    $user1VisibleTasks = Task::whereHas('tags', function ($q) use ($user1TagIds) {
        $q->whereIn('tags.id', $user1TagIds);
    })->count();

    echo "   {$user->name} can see {$user1VisibleTasks} tasks\n";
    echo "   Different content for different users! ✓\n";
}

// Example 11: Creating universal content (no tags)
echo "\n11. Creating universal content (no tags):\n";
echo str_repeat("-", 50) . "\n";
$universalTask = Task::create([
    'type' => 'truth',
    'spice_rating' => 1,
    'description' => 'What is your name?',
    'draft' => false
]);
echo "   Created universal task (no tags): {$universalTask->description}\n";
echo "   This task is only visible to users with NO tags\n";

$usersWithNoTags = User::whereDoesntHave('tags')->count();
echo "   Users with no tags: {$usersWithNoTags}\n";

// Example 12: Batch operations
echo "\n12. Batch tag operations:\n";
echo str_repeat("-", 50) . "\n";

// Find all tasks of a certain type and add a tag
$allDares = Task::where('type', 'dare')->get();
$physicalTag = Tag::where('slug', 'physical')->first();

if ($physicalTag) {
    $count = 0;
    foreach ($allDares as $dare) {
        if (!$dare->tags->contains($physicalTag->id)) {
            $dare->tags()->attach($physicalTag->id);
            $count++;
        }
    }
    echo "   Added 'Physical' tag to {$count} dare tasks\n";
}

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "Summary:\n";
echo str_repeat("=", 50) . "\n";
echo "Total Tags: " . Tag::count() . "\n";
echo "Total Tasks: " . Task::count() . "\n";
echo "Tasks with tags: " . Task::has('tags')->count() . "\n";
echo "Tasks without tags: " . Task::doesntHave('tags')->count() . "\n";
echo "Total Users: " . User::count() . "\n";
echo "Users with tags: " . User::has('tags')->count() . "\n";
echo "Users without tags: " . User::doesntHave('tags')->count() . "\n";

echo "\n✅ All examples completed successfully!\n";
echo "\nFor API usage examples, see TAGGING_SYSTEM.md\n";
