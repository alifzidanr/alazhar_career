<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('status_pelamar', function (Blueprint $table) {
            $table->tinyIncrements('id_status_pelamar');
            $table->string('status_pelamar', 20)
                ->comment('lolos, tidak lolos, dicadangkan, ditolak, mundur');
        });

        // Seeded here (rather than a seeder) since StatusPelamar's id constants
        // are relied on throughout the app; later migrations append ids 6-9.
        DB::table('status_pelamar')->insert([
            ['id_status_pelamar' => 1, 'status_pelamar' => 'lolos'],
            ['id_status_pelamar' => 2, 'status_pelamar' => 'tidak lolos'],
            ['id_status_pelamar' => 3, 'status_pelamar' => 'dicadangkan'],
            ['id_status_pelamar' => 4, 'status_pelamar' => 'ditolak'],
            ['id_status_pelamar' => 5, 'status_pelamar' => 'mundur'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_pelamar');
    }
};
