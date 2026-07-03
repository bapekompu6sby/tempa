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
        Schema::create('asn_event_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asn_event_id')->constrained('asn_event')->cascadeOnDelete();
            $table->foreignId('event_subject_id')->constrained('event_subjects')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asn_event_subject');
    }
};
