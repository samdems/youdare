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
                ->json("cant_have_tags")
                ->nullable()
                ->after("tags_to_remove")
                ->comment(
                    "Array of tag IDs that players must NOT have for this task to appear",
                );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("tasks", function (Blueprint $table) {
            $table->dropColumn("cant_have_tags");
        });
    }
};
