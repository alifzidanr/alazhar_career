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
        Schema::table('pelamar', function (Blueprint $table) {
            $table->dropColumn('lokasi_inval_sebelumnya');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelamar', function (Blueprint $table) {
            $table->string('lokasi_inval_sebelumnya', 255)->nullable()->after('jenis_kepegawaian_al_azhar_lainnya');
        });
    }
};
