<?php

namespace Database\Seeders;

use App\Models\TagGroup;
use Illuminate\Database\Seeder;

class TagGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tagGroups = [
            [
                "name" => "Content Type",
                "slug" => "content-type",
                "description" => "Categories that define the overall nature and audience of the content",
                "sort_order" => 1,
            ],
            [
                "name" => "Activity Style",
                "slug" => "activity-style",
                "description" => "The type of challenge or activity involved",
                "sort_order" => 2,
            ],
            [
                "name" => "Gender",
                "slug" => "gender",
                "description" => "Gender-specific tags for participants",
                "sort_order" => 3,
            ],
            [
                "name" => "Clothing & Accessories",
                "slug" => "clothing-accessories",
                "description" => "Tags related to specific clothing items or accessories",
                "sort_order" => 4,
            ],
        ];

        foreach ($tagGroups as $tagGroup) {
            TagGroup::firstOrCreate(
                ["slug" => $tagGroup["slug"]],
                $tagGroup
            );
        }
    }
}
