<?php

/**
 * Test Tag Filtering Logic
 *
 * This script tests the tag filtering functionality to ensure tasks
 * are properly filtered based on user tags.
 *
 * Run with: php test_tag_filtering.php
 */

require __DIR__ . '/vendor/autoload.php';

use App\Models\Tag;
use App\Models\Task;
use App\Models\User;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Tag Filtering Test Suite ===\n\n";

$testsPassed = 0;
$testsFailed = 0;

function test($description, $condition) {
    global $testsPassed, $testsFailed;
    echo "Testing: {$description}... ";
    if ($condition) {
        echo "✅ PASS\n";
        $testsPassed++;
    } else {
        echo "❌ FAIL\n";
        $testsFailed++;
    }
}

// Setup: Create test data
echo "Setting up test data...\n";
echo str_repeat("-", 50) . "\n";

// Get or create tags
$familyTag = Tag::firstOrCreate(['slug' => 'family-friendly'], ['name' => 'Family Friendly']);
$adultsTag = Tag::firstOrCreate(['slug' => 'adults-only'], ['name' => 'Adults Only']);
$partyTag = Tag::firstOrCreate(['slug' => 'party-mode'], ['name' => 'Party Mode']);

echo "✓ Tags ready: Family Friendly, Adults Only, Party Mode\n";

// Create test users
$userWithFamily = User::firstOrCreate(
    ['email' => 'family_user@test.com'],
    ['name' => 'Family User', 'password' => bcrypt('test')]
);
$userWithFamily->tags()->sync([$familyTag->id]);

$userWithAdults = User::firstOrCreate(
    ['email' => 'adult_user@test.com'],
    ['name' => 'Adult User', 'password' => bcrypt('test')]
);
$userWithAdults->tags()->sync([$adultsTag->id]);

$userWithBoth = User::firstOrCreate(
    ['email' => 'both_user@test.com'],
    ['name' => 'Both User', 'password' => bcrypt('test')]
);
$userWithBoth->tags()->sync([$familyTag->id, $adultsTag->id]);

$userWithNoTags = User::firstOrCreate(
    ['email' => 'notag_user@test.com'],
    ['name' => 'No Tag User', 'password' => bcrypt('test')]
);
$userWithNoTags->tags()->sync([]);

echo "✓ Test users created\n";

// Create test tasks
$familyTask = Task::firstOrCreate(
    ['description' => 'TEST: Family friendly task'],
    ['type' => 'truth', 'spice_rating' => 1, 'draft' => false]
);
$familyTask->tags()->sync([$familyTag->id]);

$adultTask = Task::firstOrCreate(
    ['description' => 'TEST: Adults only task'],
    ['type' => 'dare', 'spice_rating' => 4, 'draft' => false]
);
$adultTask->tags()->sync([$adultsTag->id]);

$partyTask = Task::firstOrCreate(
    ['description' => 'TEST: Party mode task'],
    ['type' => 'dare', 'spice_rating' => 3, 'draft' => false]
);
$partyTask->tags()->sync([$partyTag->id]);

$multiTagTask = Task::firstOrCreate(
    ['description' => 'TEST: Family and party task'],
    ['type' => 'truth', 'spice_rating' => 2, 'draft' => false]
);
$multiTagTask->tags()->sync([$familyTag->id, $partyTag->id]);

$untaggedTask = Task::firstOrCreate(
    ['description' => 'TEST: Untagged universal task'],
    ['type' => 'truth', 'spice_rating' => 1, 'draft' => false]
);
$untaggedTask->tags()->sync([]);

echo "✓ Test tasks created\n\n";

// Run tests
echo "Running tests...\n";
echo str_repeat("=", 50) . "\n\n";

// Test 1: User with family tag sees family tasks
$userTagIds = $userWithFamily->tags()->pluck('tags.id')->toArray();
$visibleTasks = Task::whereHas('tags', function($q) use ($userTagIds) {
    $q->whereIn('tags.id', $userTagIds);
})->pluck('id')->toArray();

test(
    "User with 'Family' tag sees family tasks",
    in_array($familyTask->id, $visibleTasks)
);

// Test 2: User with family tag does NOT see adult tasks
test(
    "User with 'Family' tag does NOT see adult tasks",
    !in_array($adultTask->id, $visibleTasks)
);

// Test 3: User with family tag does NOT see party tasks
test(
    "User with 'Family' tag does NOT see party tasks",
    !in_array($partyTask->id, $visibleTasks)
);

// Test 4: User with family tag sees multi-tag task (has family tag)
test(
    "User with 'Family' tag sees multi-tag task",
    in_array($multiTagTask->id, $visibleTasks)
);

// Test 5: User with family tag does NOT see untagged tasks
test(
    "User with 'Family' tag does NOT see untagged tasks",
    !in_array($untaggedTask->id, $visibleTasks)
);

// Test 6: User with adult tag sees adult tasks
$userTagIds = $userWithAdults->tags()->pluck('tags.id')->toArray();
$visibleTasks = Task::whereHas('tags', function($q) use ($userTagIds) {
    $q->whereIn('tags.id', $userTagIds);
})->pluck('id')->toArray();

test(
    "User with 'Adults' tag sees adult tasks",
    in_array($adultTask->id, $visibleTasks)
);

// Test 7: User with both tags sees both types
$userTagIds = $userWithBoth->tags()->pluck('tags.id')->toArray();
$visibleTasks = Task::whereHas('tags', function($q) use ($userTagIds) {
    $q->whereIn('tags.id', $userTagIds);
})->pluck('id')->toArray();

test(
    "User with both tags sees family tasks",
    in_array($familyTask->id, $visibleTasks)
);

test(
    "User with both tags sees adult tasks",
    in_array($adultTask->id, $visibleTasks)
);

test(
    "User with both tags sees multi-tag task",
    in_array($multiTagTask->id, $visibleTasks)
);

test(
    "User with both tags does NOT see party tasks",
    !in_array($partyTask->id, $visibleTasks)
);

// Test 8: User with NO tags only sees untagged tasks
$untaggedTasksOnly = Task::whereDoesntHave('tags')->pluck('id')->toArray();

test(
    "User with NO tags sees untagged tasks",
    in_array($untaggedTask->id, $untaggedTasksOnly)
);

test(
    "User with NO tags does NOT see family tasks",
    !in_array($familyTask->id, $untaggedTasksOnly)
);

test(
    "User with NO tags does NOT see adult tasks",
    !in_array($adultTask->id, $untaggedTasksOnly)
);

test(
    "User with NO tags does NOT see party tasks",
    !in_array($partyTask->id, $untaggedTasksOnly)
);

// Test 9: Task relationship counts
test(
    "Family task has correct tag count",
    $familyTask->tags()->count() === 1
);

test(
    "Multi-tag task has correct tag count",
    $multiTagTask->tags()->count() === 2
);

test(
    "Untagged task has no tags",
    $untaggedTask->tags()->count() === 0
);

// Test 10: User helper methods
test(
    "hasTag() works with tag ID",
    $userWithFamily->hasTag($familyTag->id)
);

test(
    "hasTag() works with tag slug",
    $userWithFamily->hasTag('family-friendly')
);

test(
    "hasTag() returns false for missing tag",
    !$userWithFamily->hasTag('adults-only')
);

test(
    "hasAnyTag() works correctly",
    $userWithBoth->hasAnyTag([$familyTag->id, $partyTag->id])
);

test(
    "hasAllTags() works correctly for true case",
    $userWithBoth->hasAllTags([$familyTag->id, $adultsTag->id])
);

test(
    "hasAllTags() works correctly for false case",
    !$userWithBoth->hasAllTags([$familyTag->id, $partyTag->id])
);

// Test 11: Scope methods
$tasksWithFamily = Task::withTags([$familyTag->id])->pluck('id')->toArray();

test(
    "withTags() scope includes family task",
    in_array($familyTask->id, $tasksWithFamily)
);

test(
    "withTags() scope includes multi-tag task",
    in_array($multiTagTask->id, $tasksWithFamily)
);

test(
    "withTags() scope excludes adult-only task",
    !in_array($adultTask->id, $tasksWithFamily)
);

// Test 12: Draft filtering still works with tags
$draftTask = Task::create([
    'type' => 'truth',
    'spice_rating' => 1,
    'description' => 'TEST: Draft task with family tag',
    'draft' => true
]);
$draftTask->tags()->attach($familyTag->id);

$userTagIds = $userWithFamily->tags()->pluck('tags.id')->toArray();
$publishedTasksOnly = Task::where('draft', false)
    ->whereHas('tags', function($q) use ($userTagIds) {
        $q->whereIn('tags.id', $userTagIds);
    })
    ->pluck('id')->toArray();

test(
    "Draft tasks are not included in published task list",
    !in_array($draftTask->id, $publishedTasksOnly)
);

// Test 13: Random task filtering
$userTagIds = $userWithFamily->tags()->pluck('tags.id')->toArray();
$randomTask = Task::where('draft', false)
    ->whereHas('tags', function($q) use ($userTagIds) {
        $q->whereIn('tags.id', $userTagIds);
    })
    ->inRandomOrder()
    ->first();

test(
    "Random task has a tag matching user's tags",
    $randomTask && $randomTask->tags()->whereIn('tags.id', $userTagIds)->exists()
);

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
echo "Test Results:\n";
echo str_repeat("=", 50) . "\n";
echo "✅ Tests Passed: {$testsPassed}\n";
echo "❌ Tests Failed: {$testsFailed}\n";
echo "Total Tests: " . ($testsPassed + $testsFailed) . "\n";

if ($testsFailed === 0) {
    echo "\n🎉 All tests passed! Tag filtering is working correctly.\n";
    exit(0);
} else {
    echo "\n⚠️  Some tests failed. Please review the failures above.\n";
    exit(1);
}
