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
        Schema::create('riwayat_tahap_pelamar', function (Blueprint $table) {
            $table->increments('id_riwayat');
            $table->unsignedInteger('id_pelamar');
            $table->unsignedTinyInteger('id_tahap_rekrutmen');
            $table->unsignedTinyInteger('id_status_pelamar');
            $table->text('catatan')->nullable();
            $table->string('created_by', 100)->nullable()->comment('user HR yang mengubah');
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->foreign('id_pelamar', 'fk_rtp_pelamar')
                ->references('id_pelamar')->on('pelamar')
                ->onDelete('cascade');
            $table->foreign('id_tahap_rekrutmen', 'fk_rtp_tahap')
                ->references('id_tahap_rekrutmen')->on('tahap_rekrutmen');
            $table->foreign('id_status_pelamar', 'fk_rtp_status')
                ->references('id_status_pelamar')->on('status_pelamar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_tahap_pelamar');
    }
};
