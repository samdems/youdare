<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\TagGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get tag group IDs
        $contentTypeGroup = TagGroup::where("slug", "content-type")->first();
        $activityStyleGroup = TagGroup::where(
            "slug",
            "activity-style",
        )->first();
        $genderGroup = TagGroup::where("slug", "gender")->first();
        $clothingGroup = TagGroup::where(
            "slug",
            "clothing-accessories",
        )->first();

        $tags = [
            // Content Type Tags
            [
                "name" => "Adults Only",
                "slug" => "adults-only",
                "description" => "Content suitable for adults only (18+)",
                "tag_group_id" => $contentTypeGroup?->id,
                "is_default" => true,
                "default_for_gender" => "both",
                "min_spice_level" => 1,
            ],
            [
                "name" => "Family Friendly",
                "slug" => "family-friendly",
                "description" => "Content suitable for all ages",
                "tag_group_id" => $contentTypeGroup?->id,
                "is_default" => false,
                "default_for_gender" => "none",
                "min_spice_level" => 1,
            ],
            [
                "name" => "Party Mode",
                "slug" => "party-mode",
                "description" =>
                    "Tasks suitable for parties and social gatherings",
                "tag_group_id" => $contentTypeGroup?->id,
                "is_default" => true,
                "default_for_gender" => "both",
                "min_spice_level" => 1,
            ],
            [
                "name" => "Romantic",
                "slug" => "romantic",
                "description" => "Tasks for couples and romantic situations",
                "tag_group_id" => $contentTypeGroup?->id,
                "is_default" => false,
                "default_for_gender" => "none",
                "min_spice_level" => 3,
            ],
            [
                "name" => "Extreme",
                "slug" => "extreme",
                "description" => "Tasks for those who dare to go extreme",
                "tag_group_id" => $contentTypeGroup?->id,
                "is_default" => false,
                "default_for_gender" => "none",
                "min_spice_level" => 5,
            ],

            // Activity Style Tags
            [
                "name" => "Funny",
                "slug" => "funny",
                "description" => "Humorous and entertaining tasks",
                "tag_group_id" => $activityStyleGroup?->id,
                "is_default" => true,
                "default_for_gender" => "both",
                "min_spice_level" => 1,
            ],
            [
                "name" => "Physical",
                "slug" => "physical",
                "description" => "Tasks that require physical activity",
                "tag_group_id" => $activityStyleGroup?->id,
                "is_default" => true,
                "default_for_gender" => "both",
                "min_spice_level" => 1,
            ],
            [
                "name" => "Mental",
                "slug" => "mental",
                "description" => "Tasks that challenge your mind",
                "tag_group_id" => $activityStyleGroup?->id,
                "is_default" => true,
                "default_for_gender" => "both",
                "min_spice_level" => 1,
            ],
            [
                "name" => "Creative",
                "slug" => "creative",
                "description" =>
                    "Tasks that require creativity and imagination",
                "tag_group_id" => $activityStyleGroup?->id,
                "is_default" => true,
                "default_for_gender" => "both",
                "min_spice_level" => 1,
            ],
            [
                "name" => "Social",
                "slug" => "social",
                "description" => "Tasks involving social interaction",
                "tag_group_id" => $activityStyleGroup?->id,
                "is_default" => true,
                "default_for_gender" => "both",
                "min_spice_level" => 1,
            ],

            // Gender Tags
            [
                "name" => "Male",
                "slug" => "male",
                "description" => "Tasks specifically for male participants",
                "tag_group_id" => $genderGroup?->id,
                "is_default" => false,
                "default_for_gender" => "male",
                "min_spice_level" => 1,
            ],
            [
                "name" => "Female",
                "slug" => "female",
                "description" => "Tasks specifically for female participants",
                "tag_group_id" => $genderGroup?->id,
                "is_default" => false,
                "default_for_gender" => "female",
                "min_spice_level" => 1,
            ],
            [
                "name" => "Any Gender",
                "slug" => "any-gender",
                "description" =>
                    "Tasks suitable for participants of any gender",
                "tag_group_id" => $genderGroup?->id,
                "is_default" => true,
                "default_for_gender" => "both",
                "min_spice_level" => 1,
            ],

            // Clothing & Accessories Tags
            [
                "name" => "Boxers",
                "slug" => "boxers",
                "description" => "Tasks involving men's boxers",
                "tag_group_id" => $clothingGroup?->id,
                "is_default" => false,
                "default_for_gender" => "male",
                "min_spice_level" => 2,
            ],
            [
                "name" => "Bra",
                "slug" => "bra",
                "description" => "Tasks involving bras",
                "tag_group_id" => $clothingGroup?->id,
                "is_default" => false,
                "default_for_gender" => "female",
                "min_spice_level" => 2,
            ],
            [
                "name" => "Skirt",
                "slug" => "skirt",
                "description" => "Tasks involving skirts",
                "tag_group_id" => $clothingGroup?->id,
                "is_default" => false,
                "default_for_gender" => "female",
                "min_spice_level" => 2,
            ],
            [
                "name" => "Dress",
                "slug" => "dress",
                "description" => "Tasks involving dresses",
                "tag_group_id" => $clothingGroup?->id,
                "is_default" => false,
                "default_for_gender" => "female",
                "min_spice_level" => 2,
            ],
            [
                "name" => "Panties",
                "slug" => "panties",
                "description" => "Tasks involving panties",
                "tag_group_id" => $clothingGroup?->id,
                "is_default" => false,
                "default_for_gender" => "female",
                "min_spice_level" => 2,
            ],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(["slug" => $tag["slug"]], $tag);
        }
    }
}
