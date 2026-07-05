<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kriteria', function (Blueprint $table) {
            $table->smallIncrements('id_kriteria');
            $table->string('teks_kriteria', 255)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kriteria');
    }
};
