<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orientasi', function (Blueprint $table) {
            $table->increments('id_orientasi');
            $table->unsignedInteger('id_pelamar')->unique();
            $table->unsignedSmallInteger('id_unit_kerja')->nullable();
            $table->unsignedInteger('uang_makan')->nullable();
            $table->unsignedInteger('uang_transport')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('sk_orientasi_upload', 255)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->foreign('id_pelamar', 'fk_orientasi_pelamar')
                ->references('id_pelamar')->on('pelamar')->onDelete('cascade');
            $table->foreign('id_unit_kerja', 'fk_orientasi_unitkerja')
                ->references('id_unit_kerja')->on('unit_kerja')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orientasi');
    }
};
