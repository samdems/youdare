<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table("game_task_history", function (Blueprint $table) {
            // Drop the old unique constraint (game_id + task_id)
            $table->dropUnique(["game_id", "task_id"]);

            // Add new unique constraint (game_id + player_id + task_id)
            // This allows the same task to appear for different players
            $table->unique(
                ["game_id", "player_id", "task_id"],
                "game_player_task_unique",
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("game_task_history", function (Blueprint $table) {
            // Drop the per-player unique constraint
            $table->dropUnique("game_player_task_unique");

            // Restore the old game-wide unique constraint
            $table->unique(["game_id", "task_id"]);
        });
    }
};
