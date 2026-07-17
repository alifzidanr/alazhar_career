<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wawancara', function (Blueprint $table) {
            $table->increments('id_wawancara');
            $table->unsignedInteger('id_pelamar')->unique();
            $table->decimal('nilai_wawancara_agama', 5, 2)->nullable();
            $table->decimal('nilai_praktik_micro_teaching', 5, 2)->nullable();
            $table->decimal('nilai_wawancara_umum', 5, 2)->nullable();
            $table->dateTime('tanggal_pelaksanaan')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->foreign('id_pelamar', 'fk_w_pelamar')
                ->references('id_pelamar')->on('pelamar')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wawancara');
    }
};
