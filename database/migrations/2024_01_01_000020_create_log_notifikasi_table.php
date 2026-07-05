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
        Schema::create('log_notifikasi', function (Blueprint $table) {
            $table->increments('id_log');
            $table->unsignedInteger('id_pelamar');
            $table->enum('channel', ['whatsapp', 'email']);
            $table->string('template', 100)->nullable()
                ->comment('nama template, null = pesan kosong/manual');
            $table->text('pesan')->nullable();
            $table->enum('status_kirim', ['terkirim', 'gagal', 'pending'])->default('pending');
            $table->string('created_by', 100)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->foreign('id_pelamar', 'fk_ln_pelamar')
                ->references('id_pelamar')->on('pelamar')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_notifikasi');
    }
};
