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
        if (!Schema::hasTable('instructions')) {
            return;
        }

        Schema::table('instructions', function (Blueprint $table) {
            if (!Schema::hasColumn('instructions', 'link_label')) {
                $table->string('link_label')->nullable()->after('linkable');
            }
        });

        if (Schema::hasTable('event_instructions')) {
            Schema::table('event_instructions', function (Blueprint $table) {
                if (!Schema::hasColumn('event_instructions', 'link_label')) {
                    $table->string('link_label')->nullable()->after('link');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('event_instructions') && Schema::hasColumn('event_instructions', 'link_label')) {
            Schema::table('event_instructions', function (Blueprint $table) {
                $table->dropColumn('link_label');
            });
        }

        if (Schema::hasTable('instructions') && Schema::hasColumn('instructions', 'link_label')) {
            Schema::table('instructions', function (Blueprint $table) {
                $table->dropColumn('link_label');
            });
        }
    }
};
