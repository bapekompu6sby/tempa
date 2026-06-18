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
        Schema::create('asn_event', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asn_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->enum('passing_status', ['lulus', 'tidak_lulus'])->nullable();
            $table->enum('participant_type', ['utama', 'cadangan'])->nullable();
            $table->enum('participant_status', ['terdaftar', 'mundur', 'dibatalkan', 'cadangan'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asn_event');
    }
};
