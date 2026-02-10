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
        Schema::table('events', function (Blueprint $table) {
            $table->integer('target')->nullable()->after('event_report_url');
            $table->integer('jp_module')->nullable()->after('target');
            $table->integer('jp_facilitator')->nullable()->after('jp_module');
            $table->string('field')->nullable()->after('jp_facilitator');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['target', 'jp_module', 'jp_facilitator', 'field']);
        });
    }
};
