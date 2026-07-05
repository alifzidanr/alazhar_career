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
        Schema::create('status_pelamar', function (Blueprint $table) {
            $table->tinyIncrements('id_status_pelamar');
            $table->string('status_pelamar', 20)
                ->comment('lolos, tidak lolos, dicadangkan, ditolak, mundur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_pelamar');
    }
};
