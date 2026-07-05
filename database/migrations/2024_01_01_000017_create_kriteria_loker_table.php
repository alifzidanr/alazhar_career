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
        Schema::create('kriteria_loker', function (Blueprint $table) {
            $table->increments('id_kriteria_loker');
            $table->unsignedInteger('id_loker');
            $table->unsignedSmallInteger('id_posisi')->nullable();
            $table->unsignedSmallInteger('id_unit_kerja')->nullable();
            $table->enum('bobot', ['wajib', 'diutamakan', 'nilai_tambah'])->default('wajib');
            $table->string('keterangan', 255)->nullable();

            $table->foreign('id_loker', 'fk_kl_loker')
                ->references('id_loker')->on('loker')
                ->onDelete('cascade');
            $table->foreign('id_posisi', 'fk_kl_posisi')
                ->references('id_posisi')->on('posisi')
                ->onDelete('set null');
            $table->foreign('id_unit_kerja', 'fk_kl_unitkerja')
                ->references('id_unit_kerja')->on('unit_kerja')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kriteria_loker');
    }
};
