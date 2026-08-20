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
        Schema::create('tahap_rekrutmen', function (Blueprint $table) {
            $table->tinyIncrements('id_tahap_rekrutmen');
            $table->string('tahap_rekrutmen', 50);
        });

        // Seeded here (rather than a seeder) since TahapRekrutmen's id
        // constants are relied on throughout the app. Includes the original
        // "Tes Kesehatan" stage (id 6) that 2026_07_17_000003 later removes,
        // renumbering Terima SK/Migrasi Data down to 6/7 to match the model's
        // current constants.
        DB::table('tahap_rekrutmen')->insert([
            ['id_tahap_rekrutmen' => 1, 'tahap_rekrutmen' => 'Seleksi Berkas'],
            ['id_tahap_rekrutmen' => 2, 'tahap_rekrutmen' => 'Tes Tulis'],
            ['id_tahap_rekrutmen' => 3, 'tahap_rekrutmen' => 'Wawancara'],
            ['id_tahap_rekrutmen' => 4, 'tahap_rekrutmen' => 'Orientasi'],
            ['id_tahap_rekrutmen' => 5, 'tahap_rekrutmen' => 'Tugas Sementara'],
            ['id_tahap_rekrutmen' => 6, 'tahap_rekrutmen' => 'Tes Kesehatan'],
            ['id_tahap_rekrutmen' => 7, 'tahap_rekrutmen' => 'Terima SK'],
            ['id_tahap_rekrutmen' => 8, 'tahap_rekrutmen' => 'Migrasi Data'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahap_rekrutmen');
    }
};
