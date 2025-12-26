<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            // Content Type Tags
            [
                "name" => "Adults Only",
                "slug" => "adults-only",
                "description" => "Content suitable for adults only (18+)",
                "is_default" => true,
                "default_for_gender" => "both",
                "min_spice_level" => 1,
            ],
            [
                "name" => "Family Friendly",
                "slug" => "family-friendly",
                "description" => "Content suitable for all ages",
                "is_default" => false,
                "default_for_gender" => "none",
                "min_spice_level" => 1,
            ],
            [
                "name" => "Party Mode",
                "slug" => "party-mode",
                "description" =>
                    "Tasks suitable for parties and social gatherings",
                "is_default" => true,
                "default_for_gender" => "both",
                "min_spice_level" => 1,
            ],
            [
                "name" => "Romantic",
                "slug" => "romantic",
                "description" => "Tasks for couples and romantic situations",
                "is_default" => false,
                "default_for_gender" => "none",
                "min_spice_level" => 3,
            ],
            [
                "name" => "Extreme",
                "slug" => "extreme",
                "description" => "Tasks for those who dare to go extreme",
                "is_default" => false,
                "default_for_gender" => "none",
                "min_spice_level" => 5,
            ],
            [
                "name" => "Funny",
                "slug" => "funny",
                "description" => "Humorous and entertaining tasks",
                "is_default" => true,
                "default_for_gender" => "both",
                "min_spice_level" => 1,
            ],
            [
                "name" => "Physical",
                "slug" => "physical",
                "description" => "Tasks that require physical activity",
                "is_default" => true,
                "default_for_gender" => "both",
                "min_spice_level" => 1,
            ],
            [
                "name" => "Mental",
                "slug" => "mental",
                "description" => "Tasks that challenge your mind",
                "is_default" => true,
                "default_for_gender" => "both",
                "min_spice_level" => 1,
            ],
            [
                "name" => "Creative",
                "slug" => "creative",
                "description" =>
                    "Tasks that require creativity and imagination",
                "is_default" => true,
                "default_for_gender" => "both",
                "min_spice_level" => 1,
            ],
            [
                "name" => "Social",
                "slug" => "social",
                "description" => "Tasks involving social interaction",
                "is_default" => true,
                "default_for_gender" => "both",
                "min_spice_level" => 1,
            ],

            // Gender Tags
            [
                "name" => "Male",
                "slug" => "male",
                "description" => "Tasks specifically for male participants",
                "is_default" => false,
                "default_for_gender" => "male",
                "min_spice_level" => 1,
            ],
            [
                "name" => "Female",
                "slug" => "female",
                "description" => "Tasks specifically for female participants",
                "is_default" => false,
                "default_for_gender" => "female",
                "min_spice_level" => 1,
            ],
            [
                "name" => "Any Gender",
                "slug" => "any-gender",
                "description" =>
                    "Tasks suitable for participants of any gender",
                "is_default" => true,
                "default_for_gender" => "both",
                "min_spice_level" => 1,
            ],

            // Male-Specific Clothing Items
            [
                "name" => "Boxers",
                "slug" => "boxers",
                "description" => "Tasks involving men's boxers",
                "is_default" => false,
                "default_for_gender" => "male",
                "min_spice_level" => 2,
            ],

            // Female-Specific Clothing Items
            [
                "name" => "Bra",
                "slug" => "bra",
                "description" => "Tasks involving bras",
                "is_default" => false,
                "default_for_gender" => "female",
                "min_spice_level" => 2,
            ],
            [
                "name" => "Skirt",
                "slug" => "skirt",
                "description" => "Tasks involving skirts",
                "is_default" => false,
                "default_for_gender" => "female",
                "min_spice_level" => 2,
            ],
            [
                "name" => "Dress",
                "slug" => "dress",
                "description" => "Tasks involving dresses",
                "is_default" => false,
                "default_for_gender" => "female",
                "min_spice_level" => 2,
            ],
            [
                "name" => "Panties",
                "slug" => "panties",
                "description" => "Tasks involving panties",
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
