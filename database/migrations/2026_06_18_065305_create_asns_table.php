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
        Schema::create('asns', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique()->nullable();
            $table->string('name');
            $table->string('job_title')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('birth_city')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->string('rank_grade')->nullable();
            $table->string('latest_education')->nullable();
            $table->text('office_address')->nullable();
            $table->enum('asn_type', ['pns', 'cpns', 'pppk', 'lainnya'])->nullable();
            $table->enum('asn_source', ['pusat', 'daerah', 'lainnya'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asns');
    }
};
