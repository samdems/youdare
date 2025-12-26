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
                ->enum("default_for_gender", ["male", "female", "both", "none"])
                ->default("none")
                ->after("is_default");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("tags", function (Blueprint $table) {
            $table->dropColumn("default_for_gender");
        });
    }
};
