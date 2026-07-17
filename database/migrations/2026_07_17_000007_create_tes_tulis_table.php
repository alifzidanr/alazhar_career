<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tes_tulis', function (Blueprint $table) {
            $table->increments('id_tes_tulis');
            $table->unsignedInteger('id_pelamar')->unique();
            $table->decimal('nilai_tes_agama_umum', 5, 2)->nullable();
            $table->decimal('nilai_tes_bidang_studi', 5, 2)->nullable();
            $table->decimal('nilai_tes_inggris_umum', 5, 2)->nullable();
            $table->dateTime('tanggal_pelaksanaan')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->foreign('id_pelamar', 'fk_tt_pelamar')
                ->references('id_pelamar')->on('pelamar')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tes_tulis');
    }
};
