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
        Schema::create("players", function (Blueprint $table) {
            $table->id();
            $table->foreignId("game_id")->constrained()->onDelete("cascade");
            $table->string("name");
            $table->integer("score")->default(0);
            $table->boolean("is_active")->default(true);
            $table->integer("order")->default(0); // Turn order
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("players");
    }
};
