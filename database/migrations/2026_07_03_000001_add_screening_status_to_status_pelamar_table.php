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
            'id_status_pelamar' => 6,
            'status_pelamar' => 'screening',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('status_pelamar')->where('id_status_pelamar', 6)->delete();
    }
};
