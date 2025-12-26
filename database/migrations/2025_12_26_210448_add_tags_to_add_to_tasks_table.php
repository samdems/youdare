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
        Schema::table("tasks", function (Blueprint $table) {
            $table
                ->json("tags_to_add")
                ->nullable()
                ->after("cant_have_tags")
                ->comment(
                    "Array of tag IDs that will be added to players when they complete this task",
                );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("tasks", function (Blueprint $table) {
            $table->dropColumn("tags_to_add");
        });
    }
};
