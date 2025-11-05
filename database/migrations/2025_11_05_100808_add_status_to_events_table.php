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
            // Add a nullable status column for event lifecycle (e.g., draft, published, canceled)
            // We keep it nullable to avoid forcing a default application-side.
            if (!Schema::hasColumn('events', 'status')) {
                // default to 'tentative' for newly created/updated events
                $table->string('status')->nullable()->default('tentative');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
