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
                ->unsignedTinyInteger("min_spice_level")
                ->default(1)
                ->after("default_for_gender")
                ->comment("Minimum spice level required to see this tag (1-5)");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("tags", function (Blueprint $table) {
            $table->dropColumn("min_spice_level");
        });
    }
};
