<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class RemoveUserAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "user:remove-admin {email : The email address of the user}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Remove admin privileges from a user by their email address";

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

        // Check if not admin
        if (!$user->is_admin) {
            $this->warn("User '{$user->name}' ({$email}) is not an admin.");
            return Command::SUCCESS;
        }

        // Remove admin
        $user->is_admin = false;
        $user->save();

        $this->info(
            "✓ Admin privileges removed from '{$user->name}' ({$email}).",
        );

        return Command::SUCCESS;
    }
}
