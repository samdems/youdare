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
        Schema::create("tasks", function (Blueprint $table) {
            $table->id();
            $table->enum("type", ["truth", "dare"]);
            $table
                ->integer("spice_rating")
                ->unsigned()
                ->default(1)
                ->comment("Rating from 1-5 for spice level");
            $table->text("description");
            $table->boolean("draft")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("tasks");
    }
};
