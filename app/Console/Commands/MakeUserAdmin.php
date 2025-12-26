<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeUserAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "user:make-admin {email : The email address of the user}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Make a user an admin by their email address";

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument("email");

        // Find the user
        $user = User::where("email", $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return Command::FAILURE;
        }

        // Check if already admin
        if ($user->is_admin) {
            $this->warn("User '{$user->name}' ({$email}) is already an admin.");
            return Command::SUCCESS;
        }

        // Make admin
        $user->is_admin = true;
        $user->save();

        $this->info("✓ User '{$user->name}' ({$email}) is now an admin!");

        return Command::SUCCESS;
    }
}
