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
        Schema::create("game_task_history", function (Blueprint $table) {
            $table->id();
            $table->foreignId("game_id")->constrained()->onDelete("cascade");
            $table->foreignId("task_id")->constrained()->onDelete("cascade");
            $table
                ->foreignId("player_id")
                ->nullable()
                ->constrained()
                ->onDelete("set null")
                ->comment("Player who received this task");
            $table->timestamps();

            // Prevent duplicate entries for same game-task combination
            $table->unique(["game_id", "task_id"]);

            // Index for faster queries
            $table->index(["game_id", "created_at"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("game_task_history");
    }
};
