<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('lokasi', 'wilayah');

        Schema::table('wilayah', function (Blueprint $table) {
            $table->renameColumn('id_lokasi', 'id_wilayah');
            $table->renameColumn('nama_lokasi', 'nama_wilayah');
        });
    }

    public function down(): void
    {
        Schema::table('wilayah', function (Blueprint $table) {
            $table->renameColumn('id_wilayah', 'id_lokasi');
            $table->renameColumn('nama_wilayah', 'nama_lokasi');
        });

        Schema::rename('wilayah', 'lokasi');
    }
};
