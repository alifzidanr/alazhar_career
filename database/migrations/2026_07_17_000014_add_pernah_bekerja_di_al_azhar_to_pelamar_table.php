<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelamar', function (Blueprint $table) {
            $table->string('pernah_bekerja_di_al_azhar', 10)->nullable()->after('id_tahap_rekrutmen_sebelumnya');
            $table->string('lokasi_kerja_al_azhar_sebelumnya', 255)->nullable()->after('pernah_bekerja_di_al_azhar');
        });
    }

    public function down(): void
    {
        Schema::table('pelamar', function (Blueprint $table) {
            $table->dropColumn(['pernah_bekerja_di_al_azhar', 'lokasi_kerja_al_azhar_sebelumnya']);
        });
    }
};
