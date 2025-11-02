<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_document_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_document_id')->constrained('event_documents')->onDelete('cascade');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('mime')->nullable();
            $table->integer('size')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_document_files');
    }
};
