<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations — convert English status values to Indonesian ones.
     * This updates existing rows safely.
     *
     * @return void
     */
    public function up()
    {
        // completed -> selesai
        DB::table('events')->where('status', 'completed')->update(['status' => 'selesai']);
        // cancelled -> dibatalkan
        DB::table('events')->where('status', 'cancelled')->update(['status' => 'dibatalkan']);
    }

    /**
     * Reverse the migrations — convert Indonesian back to English.
     * Use with care if the app has already started relying on Indonesian values.
     *
     * @return void
     */
    public function down()
    {
        DB::table('events')->where('status', 'selesai')->update(['status' => 'completed']);
        DB::table('events')->where('status', 'dibatalkan')->update(['status' => 'cancelled']);
    }
};
