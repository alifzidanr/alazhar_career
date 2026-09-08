<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Staging table for the "Migrasi Data" (HRIS handoff) form: mirrors the
 * fields simpeg-proto's Tambah Pegawai form collects for t_pegawai. Saved
 * here in career's own database for now (no cross-database write to HRIS
 * yet) so an admin can complete the fields career never collected before
 * a real migration is built.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('migrasi_data_pegawai', function (Blueprint $table) {
            $table->increments('id_migrasi');
            $table->unsignedInteger('id_pelamar')->unique();

            // Required in HRIS's own Tambah Pegawai validation.
            $table->string('nip', 50)->nullable();
            $table->string('nomor_faceid', 100)->nullable();
            $table->string('nama_lengkap', 255)->nullable();
            $table->date('tgl_masuk')->nullable();
            $table->unsignedSmallInteger('id_status_kepegawaian')->nullable();
            $table->unsignedSmallInteger('id_status_keaktifan')->nullable();
            $table->unsignedSmallInteger('id_bidang_diampu')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
            $table->unsignedSmallInteger('id_status_nikah')->nullable();
            $table->unsignedInteger('id_regional')->nullable();

            // Nullable in HRIS's own validation.
            $table->string('gelar_depan', 50)->nullable();
            $table->string('gelar_belakang', 50)->nullable();
            $table->string('no_ktp', 50)->nullable();
            $table->string('npwp', 50)->nullable();
            $table->string('no_rekening', 20)->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->enum('golongan_darah', ['A', 'AB', 'B', 'O'])->nullable();
            $table->string('alamat', 255)->nullable();
            $table->unsignedInteger('usia_purnabakti')->nullable();
            $table->string('kota', 100)->nullable();
            $table->string('propinsi', 100)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('ayah_kandung', 100)->nullable();
            $table->string('ibu_kandung', 100)->nullable();

            $table->timestamps();

            $table->foreign('id_pelamar', 'fk_migrasi_pelamar')
                ->references('id_pelamar')->on('pelamar')->onDelete('cascade');
            $table->foreign('id_status_kepegawaian', 'fk_migrasi_status_kepegawaian')
                ->references('id_status_kepegawaian')->on('hris_status_kepegawaian')->nullOnDelete();
            $table->foreign('id_status_keaktifan', 'fk_migrasi_status_keaktifan')
                ->references('id_status_keaktifan')->on('hris_status_keaktifan')->nullOnDelete();
            $table->foreign('id_bidang_diampu', 'fk_migrasi_bidang_diampu')
                ->references('id_bidang_diampu')->on('hris_bidang_diampu')->nullOnDelete();
            $table->foreign('id_status_nikah', 'fk_migrasi_status_nikah')
                ->references('id_status_nikah')->on('hris_status_nikah')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('migrasi_data_pegawai');
    }
};
