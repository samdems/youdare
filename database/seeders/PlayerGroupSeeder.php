<?php

namespace Database\Seeders;

use App\Models\PlayerGroup;
use Illuminate\Database\Seeder;

class PlayerGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $playerGroups = [
            [
                "name" => "Team A",
                "slug" => "team-a",
                "description" => "Players on Team A",
                "color" => "#3b82f6", // Blue
                "icon" => "🔵",
                "sort_order" => 1,
            ],
            [
                "name" => "Team B",
                "slug" => "team-b",
                "description" => "Players on Team B",
                "color" => "#ef4444", // Red
                "icon" => "🔴",
                "sort_order" => 2,
            ],
            [
                "name" => "Couples",
                "slug" => "couples",
                "description" => "Players who are in a relationship",
                "color" => "#ec4899", // Pink
                "icon" => "💕",
                "sort_order" => 3,
            ],
            [
                "name" => "Friends",
                "slug" => "friends",
                "description" => "Friends playing together",
                "color" => "#10b981", // Green
                "icon" => "👥",
                "sort_order" => 4,
            ],
            [
                "name" => "VIP",
                "slug" => "vip",
                "description" => "Special players or guests",
                "color" => "#f59e0b", // Amber/Gold
                "icon" => "⭐",
                "sort_order" => 5,
            ],
        ];

        foreach ($playerGroups as $playerGroup) {
            PlayerGroup::firstOrCreate(
                ["slug" => $playerGroup["slug"]],
                $playerGroup
            );
        }
    }
}
