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
        Schema::table("tags", function (Blueprint $table) {
            $table
                ->foreignId("tag_group_id")
                ->nullable()
                ->after("description")
                ->constrained("tag_groups")
                ->onDelete("set null");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("tags", function (Blueprint $table) {
            $table->dropForeign(["tag_group_id"]);
            $table->dropColumn("tag_group_id");
        });
    }
};
