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
        Schema::table('event_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('event_documents', 'link')) {
                $table->string('link')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('event_documents', 'file_path')) {
                $table->string('file_path')->nullable()->after('link');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_documents', function (Blueprint $table) {
            if (Schema::hasColumn('event_documents', 'file_path')) {
                $table->dropColumn('file_path');
            }
            if (Schema::hasColumn('event_documents', 'link')) {
                $table->dropColumn('link');
            }
        });
    }
};
