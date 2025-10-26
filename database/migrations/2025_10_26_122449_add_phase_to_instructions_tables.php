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
        Schema::table('instructions', function (Blueprint $table) {
            if (!Schema::hasColumn('instructions', 'phase')) {
                $table->string('phase')->nullable()->default('pelaksanaan')->after('link_label');
            }
        });

        // also add to event_instructions so copies created for events carry the phase
        Schema::table('event_instructions', function (Blueprint $table) {
            if (!Schema::hasColumn('event_instructions', 'phase')) {
                $table->string('phase')->nullable()->default('pelaksanaan')->after('link_label');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructions', function (Blueprint $table) {
            if (Schema::hasColumn('instructions', 'phase')) {
                $table->dropColumn('phase');
            }
        });

        Schema::table('event_instructions', function (Blueprint $table) {
            if (Schema::hasColumn('event_instructions', 'phase')) {
                $table->dropColumn('phase');
            }
        });
    }
};
