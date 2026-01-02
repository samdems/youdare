<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            "name" => "Test User",
            "email" => "test@example.com",
        ]);

        // Seed tag groups
        $this->call(TagGroupSeeder::class);

        // Seed tags
        $this->call(TagSeeder::class);

        // Seed player groups
        $this->call(PlayerGroupSeeder::class);

        // Seed tasks
        $this->call(TaskSeeder::class);

        // Seed promo codes
        $this->call(PromoCodeSeeder::class);
    }
}
