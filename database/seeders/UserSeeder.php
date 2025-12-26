<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ["email" => "admin@example.com"],
            [
                "name" => "Admin User",
                "password" => Hash::make("password"),
                "is_admin" => true,
            ],
        );

        // Create regular test user
        User::firstOrCreate(
            ["email" => "test@example.com"],
            [
                "name" => "Test User",
                "password" => Hash::make("password"),
                "is_admin" => false,
            ],
        );

        // Create additional test users (optional)
        User::firstOrCreate(
            ["email" => "user1@example.com"],
            [
                "name" => "Alice Johnson",
                "password" => Hash::make("password"),
                "is_admin" => false,
            ],
        );

        User::firstOrCreate(
            ["email" => "user2@example.com"],
            [
                "name" => "Bob Smith",
                "password" => Hash::make("password"),
                "is_admin" => false,
            ],
        );

        $this->command->info("Users seeded successfully!");
        $this->command->info("Admin: admin@example.com / password");
        $this->command->info("Test User: test@example.com / password");
    }
}
