<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\Game;
use App\Models\Player;
use App\Models\Tag;
use App\Models\Task;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$game = Game::where('name', 'Comprehensive Test Game')->first();
$adultTag = Tag::where('slug', 'adult')->first();
$cantHaveAdultTask = Task::where('description', 'LIKE', '%Non-adult%')->first();
$playerNoTags = $game->players()->where('name', 'No Tags')->first();
$removeAdultTask = Task::where('description', 'LIKE', '%removes adult%')->first();
$playerMale = $game->players()->where('name', 'Male Player')->first();

if ($cantHaveAdultTask) {
    echo "cant_have_adult_task:\n";
    echo "  ID: {$cantHaveAdultTask->id}\n";
    echo "  Tags: " . $cantHaveAdultTask->tags->pluck('name')->join(', ') . "\n";
    echo "  cant_have_tags: " . json_encode($cantHaveAdultTask->cant_have_tags) . "\n";
    echo "  Is available for playerNoTags? " . ($cantHaveAdultTask->isAvailableForPlayer($playerNoTags) ? 'Yes' : 'No') . "\n";
}

echo "\n";

if ($removeAdultTask) {
    echo "remove_adult_task:\n";
    echo "  ID: {$removeAdultTask->id}\n";
    echo "  Tags: " . $removeAdultTask->tags->pluck('name')->join(', ') . "\n";
    echo "  tags_to_remove: " . json_encode($removeAdultTask->tags_to_remove) . "\n";
}

echo "\n";

if ($playerMale) {
    echo "playerMale tags: " . $playerMale->tags->pluck('name')->join(', ') . "\n";
    echo "playerMale getAllAvailableTags: " . json_encode($playerMale->getAllAvailableTags()) . "\n";
}
