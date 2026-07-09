<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('status_pelamar')->insert([
            ['id_status_pelamar' => 7, 'status_pelamar' => 'ongoing'],
            ['id_status_pelamar' => 8, 'status_pelamar' => 'diterima'],
            ['id_status_pelamar' => 9, 'status_pelamar' => 'migrated'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('status_pelamar')->whereIn('id_status_pelamar', [7, 8, 9])->delete();
    }
};
