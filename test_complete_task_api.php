<?php

/**
 * Test the complete task API endpoint to ensure tags are added/removed
 */

require __DIR__ . '/vendor/autoload.php';

use App\Models\Game;
use App\Models\Player;
use App\Models\Tag;
use App\Models\Task;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Complete Task API Test ===\n\n";

// Setup
$game = Game::create([
    'name' => 'API Test Game',
    'max_spice_rating' => 5,
    'status' => 'active'
]);

$beginnerTag = Tag::firstOrCreate(['slug' => 'beginner'], ['name' => 'Beginner', 'min_spice_level' => 0]);
$veteranTag = Tag::firstOrCreate(['slug' => 'veteran'], ['name' => 'Veteran', 'min_spice_level' => 0]);

$player = $game->players()->create(['name' => 'API Test Player', 'order' => 1]);
$player->tags()->attach($beginnerTag->id);

$promotionTask = Task::create([
    'type' => 'dare',
    'spice_rating' => 2,
    'description' => 'API Promotion challenge',
    'draft' => false,
    'tags_to_remove' => [$beginnerTag->id],
    'tags_to_add' => [$veteranTag->id]
]);
$promotionTask->tags()->attach($beginnerTag->id);

echo "Setup:\n";
echo "- Player: {$player->name} (ID: {$player->id})\n";
echo "- Initial tags: " . $player->tags->pluck('name')->join(', ') . "\n";
echo "- Task: {$promotionTask->description} (ID: {$promotionTask->id})\n";
echo "- Task removes: Beginner\n";
echo "- Task adds: Veteran\n";
echo "\n";

// Simulate the API call using the controller method
$controller = new App\Http\Controllers\Api\PlayerController();
$request = new Illuminate\Http\Request([
    'task_id' => $promotionTask->id,
    'points' => 1
]);

$response = $controller->completeTask($request, $player);
$responseData = $response->getData(true);

echo "API Response:\n";
echo "- Success: " . ($responseData['success'] ? 'Yes' : 'No') . "\n";
echo "- Message: " . $responseData['message'] . "\n";
echo "- Removed tags count: " . $responseData['data']['removed_tags_count'] . "\n";
echo "- Added tags count: " . $responseData['data']['added_tags_count'] . "\n";
echo "\n";

$player->refresh();
echo "After completion:\n";
echo "- Player tags: " . $player->tags->pluck('name')->join(', ') . "\n";
echo "\n";

// Verify
if ($responseData['success'] && 
    $responseData['data']['removed_tags_count'] === 1 &&
    $responseData['data']['added_tags_count'] === 1 &&
    !$player->tags->contains($beginnerTag) &&
    $player->tags->contains($veteranTag)) {
    echo "✅ Test PASSED! Tags were correctly added and removed.\n";
    $exitCode = 0;
} else {
    echo "❌ Test FAILED!\n";
    $exitCode = 1;
}

// Cleanup
$game->delete();
$promotionTask->delete();

exit($exitCode);
