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
        Schema::create('pendidikan_terakhir', function (Blueprint $table) {
            $table->tinyIncrements('id_pendidikan_terakhir');
            $table->string('pendidikan_terakhir', 10)
                ->comment('SD, SMP, SMA, D1, D2, D3, S1, S2, S3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendidikan_terakhir');
    }
};
