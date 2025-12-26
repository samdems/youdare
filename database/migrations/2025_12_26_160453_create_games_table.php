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
        Schema::create("games", function (Blueprint $table) {
            $table->id();
            $table->string("name")->nullable();
            $table->string("code", 6)->unique(); // Short code for joining
            $table
                ->enum("status", ["waiting", "active", "completed"])
                ->default("waiting");
            $table->integer("max_spice_rating")->default(5);
            $table->json("settings")->nullable(); // Additional game settings
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("games");
    }
};
