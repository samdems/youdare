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
        Schema::table("players", function (Blueprint $table) {
            $table
                ->foreignId("player_group_id")
                ->nullable()
                ->after("gender")
                ->constrained("player_groups")
                ->onDelete("set null");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("players", function (Blueprint $table) {
            $table->dropForeign(["player_group_id"]);
            $table->dropColumn("player_group_id");
        });
    }
};
