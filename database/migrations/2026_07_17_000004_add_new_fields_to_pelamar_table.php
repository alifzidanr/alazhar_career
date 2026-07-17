<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelamar', function (Blueprint $table) {
            $table->string('nik', 16)->nullable()->after('nama');
            $table->string('kategori_perguruan_tinggi', 30)->nullable()->after('program_studi');
            $table->string('pas_foto_upload', 255)->nullable()->after('cv_upload');
            $table->string('surat_lamaran_upload', 255)->nullable()->after('pas_foto_upload');
            $table->string('sim_upload', 255)->nullable()->after('ktp_upload');
            $table->string('sertifikat_gada_pratama_upload', 255)->nullable()->after('transkrip_nilai_upload');
            $table->string('sertifikat_tambahan_upload', 255)->nullable()->after('sertifikat_gada_pratama_upload');
        });
    }

    public function down(): void
    {
        Schema::table('pelamar', function (Blueprint $table) {
            $table->dropColumn([
                'nik',
                'kategori_perguruan_tinggi',
                'pas_foto_upload',
                'surat_lamaran_upload',
                'sim_upload',
                'sertifikat_gada_pratama_upload',
                'sertifikat_tambahan_upload',
            ]);
        });
    }
};
