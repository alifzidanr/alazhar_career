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
            $table->unsignedTinyInteger('bulan_kerja_al_azhar_sebelumnya')->nullable()->after('lokasi_kerja_al_azhar_sebelumnya');
            $table->unsignedSmallInteger('tahun_kerja_al_azhar_sebelumnya')->nullable()->after('bulan_kerja_al_azhar_sebelumnya');
            $table->string('pernah_inval', 10)->nullable()->after('tahun_kerja_al_azhar_sebelumnya');
            $table->string('lokasi_inval_sebelumnya', 255)->nullable()->after('pernah_inval');
            $table->decimal('ipk_s3', 3, 2)->nullable()->after('ipk_s2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelamar', function (Blueprint $table) {
            $table->dropColumn([
                'bulan_kerja_al_azhar_sebelumnya',
                'tahun_kerja_al_azhar_sebelumnya',
                'pernah_inval',
                'lokasi_inval_sebelumnya',
                'ipk_s3',
            ]);
        });
    }
};
