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
        Schema::table('asn_event_subject', function (Blueprint $table) {
            $table->float('behavioral_score')->default(0);
        });

        Schema::table('asn_event', function (Blueprint $table) {
            $table->float('total_behavioral_score')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asn_event_subject', function (Blueprint $table) {
            $table->dropColumn('behavioral_score');
        });

        Schema::table('asn_event', function (Blueprint $table) {
            $table->dropColumn('total_behavioral_score');
        });
    }
};
