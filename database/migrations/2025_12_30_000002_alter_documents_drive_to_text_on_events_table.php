<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'documents_drive')) {
                $table->dropColumn('documents_drive');
            }
            $table->text('document_drive_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'document_drive_url')) {
                $table->dropColumn('document_drive_url');
            }
            $table->string('documents_drive', 255)->nullable();
        });
    }
};
