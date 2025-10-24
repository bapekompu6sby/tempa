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
        Schema::create('instructions', function (Blueprint $table) {
              $table->id();
              $table->string('name')->nullable();
              $table->longText('detail');
              $table->boolean('linkable')->default(false);
              $table->boolean('full_elearning')->default(false);
              $table->boolean('distance_learning')->default(false);
              $table->boolean('blended_learning')->default(false);
              $table->boolean('classical')->default(false);
              $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructions');
    }
};
