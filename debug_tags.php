<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Game;
use App\Models\Player;

// Find the test game we created
$game = Game::where('name', 'Bug Test Game')->first();

if (!$game) {
    echo "Game not found, creating new one...\n";
    exit;
}

$playerNoTags = $game->players()->where('name', 'Player with No Tags')->first();
$playerWithTags = $game->players()->where('name', 'Player with Tags')->first();

if ($playerNoTags) {
    echo "Player with NO tags:\n";
    echo "  - getAllAvailableTags(): " . json_encode($playerNoTags->getAllAvailableTags()) . "\n";
    echo "  - tags()->pluck('tags.id'): " . json_encode($playerNoTags->tags()->pluck('tags.id')->toArray()) . "\n";
    echo "  - Game tags: " . json_encode($game->tags()->pluck('tags.id')->toArray()) . "\n";
}

echo "\n";

if ($playerWithTags) {
    echo "Player WITH tags:\n";
    echo "  - getAllAvailableTags(): " . json_encode($playerWithTags->getAllAvailableTags()) . "\n";
    echo "  - tags()->pluck('tags.id'): " . json_encode($playerWithTags->tags()->pluck('tags.id')->toArray()) . "\n";
    echo "  - Game tags: " . json_encode($game->tags()->pluck('tags.id')->toArray()) . "\n";
}
