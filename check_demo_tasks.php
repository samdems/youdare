<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$task1 = App\Models\Task::where('description', 'LIKE', '%most embarrassing%')->first();
$task2 = App\Models\Task::where('description', 'LIKE', '%sing them%')->first();

if ($task1) {
    echo "Task 1: {$task1->description}\n";
    echo "  - Tags: " . $task1->tags->pluck('name')->join(', ') . "\n";
    echo "  - tags_to_remove: " . json_encode($task1->tags_to_remove) . "\n";
    echo "  - tags_to_add: " . json_encode($task1->tags_to_add) . "\n";
}

echo "\n";

if ($task2) {
    echo "Task 2: {$task2->description}\n";
    echo "  - Tags: " . $task2->tags->pluck('name')->join(', ') . "\n";
    echo "  - tags_to_remove: " . json_encode($task2->tags_to_remove) . "\n";
    echo "  - tags_to_add: " . json_encode($task2->tags_to_add) . "\n";
}
