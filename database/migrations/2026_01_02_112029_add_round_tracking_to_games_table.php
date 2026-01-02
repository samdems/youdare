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
        Schema::table("games", function (Blueprint $table) {
            $table->integer("current_round")->default(1)->after("status");
            $table
                ->integer("current_player_index")
                ->default(0)
                ->after("current_round");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("games", function (Blueprint $table) {
            $table->dropColumn(["current_round", "current_player_index"]);
        });
    }
};
