<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Task;

// Set SQLite connection
config(['database.default' => 'sqlite']);

echo "Testing Task Model and Database\n";
echo "================================\n\n";

// Test 1: Count total tasks
$totalTasks = Task::count();
echo "✓ Total tasks in database: {$totalTasks}\n";

// Test 2: Count by type
$truthCount = Task::where('type', 'truth')->count();
$dareCount = Task::where('type', 'dare')->count();
echo "✓ Truth tasks: {$truthCount}\n";
echo "✓ Dare tasks: {$dareCount}\n\n";

// Test 3: Count by draft status
$publishedCount = Task::where('draft', false)->count();
$draftCount = Task::where('draft', true)->count();
echo "✓ Published tasks: {$publishedCount}\n";
echo "✓ Draft tasks: {$draftCount}\n\n";

// Test 4: Get spice rating distribution
echo "Spice Rating Distribution:\n";
for ($i = 1; $i <= 5; $i++) {
    $count = Task::where('spice_rating', $i)->count();
    $stars = str_repeat('🌶️', $i);
    echo "  Level {$i} {$stars}: {$count} tasks\n";
}
echo "\n";

// Test 5: Get a random truth
$randomTruth = Task::where('type', 'truth')
    ->where('draft', false)
    ->inRandomOrder()
    ->first();

if ($randomTruth) {
    echo "Random Truth (Spice: {$randomTruth->spice_rating}):\n";
    echo "  \"{$randomTruth->description}\"\n\n";
}

// Test 6: Get a random dare
$randomDare = Task::where('type', 'dare')
    ->where('draft', false)
    ->inRandomOrder()
    ->first();

if ($randomDare) {
    echo "Random Dare (Spice: {$randomDare->spice_rating}):\n";
    echo "  \"{$randomDare->description}\"\n\n";
}

// Test 7: Create a new task
echo "Testing task creation:\n";
$newTask = Task::create([
    'type' => 'truth',
    'spice_rating' => 3,
    'description' => 'What is the most embarrassing thing you have done this week?',
    'draft' => false
]);
echo "✓ Created task with ID: {$newTask->id}\n";

// Test 8: Update the task
$newTask->spice_rating = 4;
$newTask->save();
echo "✓ Updated task spice rating to 4\n";

// Test 9: Test model methods
echo "\nTesting model methods:\n";
echo "  Is Truth? " . ($newTask->isTruth() ? 'Yes' : 'No') . "\n";
echo "  Is Dare? " . ($newTask->isDare() ? 'Yes' : 'No') . "\n";
echo "  Is Draft? " . ($newTask->isDraft() ? 'Yes' : 'No') . "\n";
echo "  Spice Level: {$newTask->spice_level}\n";

// Test 10: Delete the test task
$newTask->delete();
echo "✓ Deleted test task\n";

// Test 11: Test scopes
echo "\nTesting query scopes:\n";
$mildTasks = Task::published()->bySpiceLevel(1, 2)->count();
echo "✓ Mild tasks (spice 1-2, published): {$mildTasks}\n";

$spicyTruths = Task::truths()->published()->bySpiceLevel(4, 5)->count();
echo "✓ Spicy truths (spice 4-5, published): {$spicyTruths}\n";

$draftDares = Task::dares()->drafts()->count();
echo "✓ Draft dares: {$draftDares}\n";

echo "\n================================\n";
echo "All tests completed successfully! ✅\n";
