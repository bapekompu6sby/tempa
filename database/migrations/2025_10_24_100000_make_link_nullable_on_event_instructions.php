<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // NOTE: changing column types requires the doctrine/dbal package.
        // If you haven't installed it, run: composer require doctrine/dbal
        Schema::table('event_instructions', function (Blueprint $table) {
            // Change `link` from boolean to string and make it nullable
            $table->string('link')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_instructions', function (Blueprint $table) {
            // Revert `link` back to boolean with default false
            $table->boolean('link')->default(false)->change();
        });
    }
};
