<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListAdmins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "user:list-admins";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "List all admin users";

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $admins = User::where("is_admin", true)->get();

        if ($admins->isEmpty()) {
            $this->warn("No admin users found.");
            return Command::SUCCESS;
        }

        $this->info("Admin Users:");
        $this->newLine();

        $tableData = $admins->map(function ($admin) {
            return [
                $admin->id,
                $admin->name,
                $admin->email,
                $admin->created_at->format("Y-m-d H:i:s"),
            ];
        });

        $this->table(
            ["ID", "Name", "Email", "Created At"],
            $tableData->toArray(),
        );

        $this->newLine();
        $this->info("Total admin users: " . $admins->count());

        return Command::SUCCESS;
    }
}
