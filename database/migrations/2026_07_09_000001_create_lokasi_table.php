<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lokasi', function (Blueprint $table) {
            $table->smallIncrements('id_lokasi');
            $table->string('nama_lokasi', 150)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lokasi');
    }
};
