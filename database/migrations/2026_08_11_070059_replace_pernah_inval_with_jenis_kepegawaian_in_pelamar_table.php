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
        Schema::table('pelamar', function (Blueprint $table) {
            $table->dropColumn(['pernah_inval', 'lokasi_inval_sebelumnya']);
            $table->string('jenis_kepegawaian_al_azhar_sebelumnya', 30)->nullable()->after('tahun_kerja_al_azhar_sebelumnya');
            $table->string('jenis_kepegawaian_al_azhar_lainnya', 255)->nullable()->after('jenis_kepegawaian_al_azhar_sebelumnya');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelamar', function (Blueprint $table) {
            $table->dropColumn(['jenis_kepegawaian_al_azhar_sebelumnya', 'jenis_kepegawaian_al_azhar_lainnya']);
            $table->string('pernah_inval', 10)->nullable()->after('tahun_kerja_al_azhar_sebelumnya');
            $table->string('lokasi_inval_sebelumnya', 255)->nullable()->after('pernah_inval');
        });
    }
};
