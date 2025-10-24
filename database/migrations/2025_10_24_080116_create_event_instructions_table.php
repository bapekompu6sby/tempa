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
        Schema::create('event_instructions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('instruction_id');
            $table->boolean('checked')->default(false);
            $table->boolean('linkable')->default(false);
            $table->boolean('link')->default(false);
            $table->boolean('full_elearning')->default(false);
            $table->boolean('distance_learning')->default(false);
            $table->boolean('blended_learning')->default(false);
            $table->boolean('classical')->default(false);
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('instruction_id')->references('id')->on('instructions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_instructions');
    }
};
