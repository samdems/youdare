<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get tags by slug for easy reference
        $tags = [
            "adultsOnly" => Tag::where("slug", "adults-only")->first(),
            "familyFriendly" => Tag::where("slug", "family-friendly")->first(),
            "partyMode" => Tag::where("slug", "party-mode")->first(),
            "romantic" => Tag::where("slug", "romantic")->first(),
            "extreme" => Tag::where("slug", "extreme")->first(),
            "funny" => Tag::where("slug", "funny")->first(),
            "physical" => Tag::where("slug", "physical")->first(),
            "mental" => Tag::where("slug", "mental")->first(),
            "creative" => Tag::where("slug", "creative")->first(),
            "social" => Tag::where("slug", "social")->first(),
        ];

        // Create a mix of truth and dare tasks with varying spice ratings

        // Mild truths (spice rating 1-2) - Family Friendly, Funny, Mental, Social
        Task::factory()
            ->truth()
            ->withSpiceRating(1)
            ->published()
            ->count(5)
            ->create()
            ->each(function ($task) use ($tags) {
                $task
                    ->tags()
                    ->attach([
                        $tags["familyFriendly"]->id,
                        $tags["funny"]->id,
                        $tags["mental"]->id,
                    ]);
            });
        Task::factory()
            ->truth()
            ->withSpiceRating(2)
            ->published()
            ->count(5)
            ->create()
            ->each(function ($task) use ($tags) {
                $task
                    ->tags()
                    ->attach([
                        $tags["familyFriendly"]->id,
                        $tags["social"]->id,
                        $tags["mental"]->id,
                    ]);
            });

        // Medium truths (spice rating 3) - Party Mode, Social
        Task::factory()
            ->truth()
            ->withSpiceRating(3)
            ->published()
            ->count(5)
            ->create()
            ->each(function ($task) use ($tags) {
                $task
                    ->tags()
                    ->attach([
                        $tags["partyMode"]->id,
                        $tags["social"]->id,
                        $tags["mental"]->id,
                    ]);
            });

        // Spicy truths (spice rating 4-5) - Adults Only, Extreme, Romantic
        Task::factory()
            ->truth()
            ->withSpiceRating(4)
            ->published()
            ->count(3)
            ->create()
            ->each(function ($task) use ($tags) {
                $task
                    ->tags()
                    ->attach([
                        $tags["adultsOnly"]->id,
                        $tags["romantic"]->id,
                        $tags["mental"]->id,
                    ]);
            });
        Task::factory()
            ->truth()
            ->withSpiceRating(5)
            ->published()
            ->count(2)
            ->create()
            ->each(function ($task) use ($tags) {
                $task
                    ->tags()
                    ->attach([
                        $tags["adultsOnly"]->id,
                        $tags["extreme"]->id,
                        $tags["mental"]->id,
                    ]);
            });

        // Mild dares (spice rating 1-2) - Family Friendly, Funny, Physical
        Task::factory()
            ->dare()
            ->withSpiceRating(1)
            ->published()
            ->count(5)
            ->create()
            ->each(function ($task) use ($tags) {
                $task
                    ->tags()
                    ->attach([
                        $tags["familyFriendly"]->id,
                        $tags["funny"]->id,
                        $tags["physical"]->id,
                    ]);
            });
        Task::factory()
            ->dare()
            ->withSpiceRating(2)
            ->published()
            ->count(5)
            ->create()
            ->each(function ($task) use ($tags) {
                $task
                    ->tags()
                    ->attach([
                        $tags["familyFriendly"]->id,
                        $tags["creative"]->id,
                        $tags["social"]->id,
                    ]);
            });

        // Medium dares (spice rating 3) - Party Mode, Social, Physical
        Task::factory()
            ->dare()
            ->withSpiceRating(3)
            ->published()
            ->count(5)
            ->create()
            ->each(function ($task) use ($tags) {
                $task
                    ->tags()
                    ->attach([
                        $tags["partyMode"]->id,
                        $tags["social"]->id,
                        $tags["physical"]->id,
                    ]);
            });

        // Spicy dares (spice rating 4-5) - Adults Only, Extreme, Physical
        Task::factory()
            ->dare()
            ->withSpiceRating(4)
            ->published()
            ->count(3)
            ->create()
            ->each(function ($task) use ($tags) {
                $task
                    ->tags()
                    ->attach([
                        $tags["adultsOnly"]->id,
                        $tags["romantic"]->id,
                        $tags["social"]->id,
                    ]);
            });
        Task::factory()
            ->dare()
            ->withSpiceRating(5)
            ->published()
            ->count(2)
            ->create()
            ->each(function ($task) use ($tags) {
                $task
                    ->tags()
                    ->attach([
                        $tags["adultsOnly"]->id,
                        $tags["extreme"]->id,
                        $tags["physical"]->id,
                    ]);
            });

        // Create some draft tasks (no tags needed for drafts)
        Task::factory()->truth()->draft()->count(5)->create();
        Task::factory()->dare()->draft()->count(5)->create();

        // Create some specific predefined tasks with appropriate tags
        $predefinedTasks = [
            [
                "type" => "truth",
                "spice_rating" => 1,
                "description" => "What is your favorite movie of all time?",
                "draft" => false,
                "tags" => [
                    $tags["familyFriendly"]->id,
                    $tags["funny"]->id,
                    $tags["mental"]->id,
                ],
            ],
            [
                "type" => "truth",
                "spice_rating" => 2,
                "description" => "Have you ever had a crush on a teacher?",
                "draft" => false,
                "tags" => [
                    $tags["familyFriendly"]->id,
                    $tags["funny"]->id,
                    $tags["social"]->id,
                ],
            ],
            [
                "type" => "truth",
                "spice_rating" => 3,
                "description" =>
                    "What is the most embarrassing thing you have done in public?",
                "draft" => false,
                "tags" => [
                    $tags["partyMode"]->id,
                    $tags["funny"]->id,
                    $tags["social"]->id,
                ],
            ],
            [
                "type" => "truth",
                "spice_rating" => 4,
                "description" => "Have you ever ghosted someone? Why?",
                "draft" => false,
                "tags" => [
                    $tags["adultsOnly"]->id,
                    $tags["romantic"]->id,
                    $tags["mental"]->id,
                ],
            ],
            [
                "type" => "truth",
                "spice_rating" => 5,
                "description" => "What is your deepest, darkest secret?",
                "draft" => false,
                "tags" => [
                    $tags["adultsOnly"]->id,
                    $tags["extreme"]->id,
                    $tags["mental"]->id,
                ],
            ],
            [
                "type" => "dare",
                "spice_rating" => 1,
                "description" => "Show the last photo in your camera roll",
                "draft" => false,
                "tags" => [
                    $tags["familyFriendly"]->id,
                    $tags["funny"]->id,
                    $tags["social"]->id,
                ],
            ],
            [
                "type" => "dare",
                "spice_rating" => 2,
                "description" => "Do 20 jumping jacks",
                "draft" => false,
                "tags" => [
                    $tags["familyFriendly"]->id,
                    $tags["physical"]->id,
                    $tags["funny"]->id,
                ],
            ],
            [
                "type" => "dare",
                "spice_rating" => 3,
                "description" =>
                    "Let someone write a word on your forehead in marker",
                "draft" => false,
                "tags" => [
                    $tags["partyMode"]->id,
                    $tags["funny"]->id,
                    $tags["creative"]->id,
                ],
            ],
            [
                "type" => "dare",
                "spice_rating" => 4,
                "description" => "Text your crush and tell them you like them",
                "draft" => false,
                "tags" => [
                    $tags["adultsOnly"]->id,
                    $tags["romantic"]->id,
                    $tags["extreme"]->id,
                ],
            ],
            [
                "type" => "dare",
                "spice_rating" => 5,
                "description" =>
                    "Let the group go through your phone for 1 minute",
                "draft" => false,
                "tags" => [
                    $tags["adultsOnly"]->id,
                    $tags["extreme"]->id,
                    $tags["social"]->id,
                ],
            ],
        ];

        foreach ($predefinedTasks as $taskData) {
            $tagIds = $taskData["tags"];
            unset($taskData["tags"]);

            $task = Task::create($taskData);
            $task->tags()->attach($tagIds);
        }
    }
}
