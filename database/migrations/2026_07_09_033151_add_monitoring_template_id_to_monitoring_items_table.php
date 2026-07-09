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
        Schema::table('monitoring_items', function (Blueprint $table) {
            $table->foreignId('monitoring_template_id')->nullable()->constrained('monitoring_templates')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitoring_items', function (Blueprint $table) {
            $table->dropForeign(['monitoring_template_id']);
            $table->dropColumn('monitoring_template_id');
        });
    }
};
