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
        // NOTE: Changing column nullability requires doctrine/dbal.
        Schema::table('event_documents', function (Blueprint $table) {
            $table->longText('notes')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_documents', function (Blueprint $table) {
            $table->longText('notes')->nullable(false)->change();
        });
    }
};
