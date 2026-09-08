<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Read-only mirrors of small HRIS (simpeg-proto) reference tables, snapshotted
 * so the Migrasi Data form can offer the same dropdown options HRIS expects,
 * without a live cross-database connection. IDs match HRIS's own primary keys
 * 1:1 so a future real migration can reuse them directly.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hris_status_kepegawaian', function (Blueprint $table) {
            $table->unsignedSmallInteger('id_status_kepegawaian')->primary();
            $table->string('kode', 20);
            $table->string('status', 100);
        });

        Schema::create('hris_status_keaktifan', function (Blueprint $table) {
            $table->unsignedSmallInteger('id_status_keaktifan')->primary();
            $table->string('status_keaktifan', 100);
        });

        Schema::create('hris_bidang_diampu', function (Blueprint $table) {
            $table->unsignedSmallInteger('id_bidang_diampu')->primary();
            $table->string('nama_bidang', 100);
        });

        Schema::create('hris_status_nikah', function (Blueprint $table) {
            $table->unsignedSmallInteger('id_status_nikah')->primary();
            $table->string('status_nikah', 50);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hris_status_nikah');
        Schema::dropIfExists('hris_bidang_diampu');
        Schema::dropIfExists('hris_status_keaktifan');
        Schema::dropIfExists('hris_status_kepegawaian');
    }
};
