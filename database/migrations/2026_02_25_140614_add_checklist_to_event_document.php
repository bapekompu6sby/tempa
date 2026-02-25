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
        // add a boolean flag to mark documents as 'checked' or approved
        Schema::table('event_documents', function (Blueprint $table) {
            $table->boolean('checked')->default(false)->after('file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_documents', function (Blueprint $table) {
            $table->dropColumn('checked');
        });
    }
};
