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
                ->enum("someone_gender", ["any", "same", "other"])
                ->default("any")
                ->after("someone_cant_have_tags")
                ->comment(
                    "Gender filter for {{someone}}: any, same as current player, or other than current player",
                );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("tasks", function (Blueprint $table) {
            $table->dropColumn("someone_gender");
        });
    }
};
