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
        Schema::create("game_tag", function (Blueprint $table) {
            $table->id();
            $table->foreignId("game_id")->constrained()->onDelete("cascade");
            $table->foreignId("tag_id")->constrained()->onDelete("cascade");
            $table->timestamps();

            // Ensure a tag can only be added once per game
            $table->unique(["game_id", "tag_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("game_tag");
    }
};
