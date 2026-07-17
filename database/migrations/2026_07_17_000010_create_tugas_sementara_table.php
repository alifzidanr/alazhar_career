<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tugas_sementara', function (Blueprint $table) {
            $table->increments('id_tugas_sementara');
            $table->unsignedInteger('id_pelamar')->unique();
            $table->string('sk_tugas_sementara_upload', 255)->nullable();
            $table->string('hasil_tes_kesehatan_upload', 255)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->foreign('id_pelamar', 'fk_ts_pelamar')
                ->references('id_pelamar')->on('pelamar')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas_sementara');
    }
};
