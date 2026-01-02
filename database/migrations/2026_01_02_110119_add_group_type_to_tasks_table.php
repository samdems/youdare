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
        // Modify the type enum to include 'group'
        Schema::table("tasks", function (Blueprint $table) {
            $table->enum("type", ["truth", "dare", "group"])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to only 'truth' and 'dare'
        Schema::table("tasks", function (Blueprint $table) {
            $table->enum("type", ["truth", "dare"])->change();
        });
    }
};
