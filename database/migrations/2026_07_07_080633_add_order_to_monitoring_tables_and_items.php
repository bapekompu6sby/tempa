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
        Schema::table('monitoring_templates', function (Blueprint $table) {
            $table->integer('order')->default(0)->after('sub_category');
        });

        Schema::table('monitoring_items', function (Blueprint $table) {
            $table->integer('order')->default(0)->after('sub_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_items', function (Blueprint $table) {
            $table->dropColumn('order');
        });

        Schema::table('monitoring_templates', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
