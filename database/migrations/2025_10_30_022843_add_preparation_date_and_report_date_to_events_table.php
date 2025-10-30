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
        Schema::table('events', function (Blueprint $table) {
            // add preparation and report dates (nullable)
            if (!Schema::hasColumn('events', 'preparation_date')) {
                $table->date('preparation_date')->nullable()->after('end_date');
            }
            if (!Schema::hasColumn('events', 'report_date')) {
                $table->date('report_date')->nullable()->after('preparation_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'report_date')) {
                $table->dropColumn('report_date');
            }
            if (Schema::hasColumn('events', 'preparation_date')) {
                $table->dropColumn('preparation_date');
            }
        });
    }
};
