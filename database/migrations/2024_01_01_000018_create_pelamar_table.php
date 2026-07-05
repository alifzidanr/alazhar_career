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
        Schema::create('pelamar', function (Blueprint $table) {
            $table->increments('id_pelamar');
            $table->unsignedInteger('id_loker');
            $table->string('nama', 150);
            $table->string('gelar', 50)->nullable();
            $table->string('no_hp', 20)->nullable()->comment('Untuk notifikasi WhatsApp');
            $table->string('email', 150)->nullable()->comment('Untuk notifikasi email');
            $table->unsignedTinyInteger('id_pendidikan_terakhir');
            $table->unsignedTinyInteger('id_status_pelamar')->default(1);
            $table->unsignedTinyInteger('id_tahap_rekrutmen')->default(1);
            $table->date('tanggal_apply');
            $table->string('ijazah_upload', 255)->nullable();
            $table->string('ktp_upload', 255)->nullable();
            $table->string('transkrip_nilai_upload', 255)->nullable();
            $table->unsignedInteger('id_pelamar_cadangan_dari')->nullable()
                ->comment('FK ke id_pelamar yang menjadi sumber cadangan');
            $table->text('catatan')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->foreign('id_loker', 'fk_p_loker')
                ->references('id_loker')->on('loker');
            $table->foreign('id_pendidikan_terakhir', 'fk_p_pendidikan')
                ->references('id_pendidikan_terakhir')->on('pendidikan_terakhir');
            $table->foreign('id_status_pelamar', 'fk_p_status')
                ->references('id_status_pelamar')->on('status_pelamar');
            $table->foreign('id_tahap_rekrutmen', 'fk_p_tahap')
                ->references('id_tahap_rekrutmen')->on('tahap_rekrutmen');
            $table->foreign('id_pelamar_cadangan_dari', 'fk_p_cadangan')
                ->references('id_pelamar')->on('pelamar')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelamar');
    }
};
