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
                ->json("someone_tags")
                ->nullable()
                ->after("tags_to_add")
                ->comment(
                    "Tag IDs that the {{someone}} player must have (at least one)",
                );
            $table
                ->json("someone_cant_have_tags")
                ->nullable()
                ->after("someone_tags")
                ->comment("Tag IDs that the {{someone}} player must NOT have");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("tasks", function (Blueprint $table) {
            $table->dropColumn(["someone_tags", "someone_cant_have_tags"]);
        });
    }
};
