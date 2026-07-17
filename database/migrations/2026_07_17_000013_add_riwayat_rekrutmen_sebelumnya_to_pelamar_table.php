<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelamar', function (Blueprint $table) {
            $table->string('pernah_rekrutmen_sebelumnya', 10)->nullable()->after('alamat');
            $table->unsignedTinyInteger('bulan_rekrutmen_sebelumnya')->nullable()->after('pernah_rekrutmen_sebelumnya');
            $table->unsignedSmallInteger('tahun_rekrutmen_sebelumnya')->nullable()->after('bulan_rekrutmen_sebelumnya');
            $table->unsignedTinyInteger('id_tahap_rekrutmen_sebelumnya')->nullable()->after('tahun_rekrutmen_sebelumnya');

            $table->foreign('id_tahap_rekrutmen_sebelumnya', 'fk_p_tahap_sebelumnya')
                ->references('id_tahap_rekrutmen')->on('tahap_rekrutmen')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pelamar', function (Blueprint $table) {
            $table->dropForeign('fk_p_tahap_sebelumnya');
            $table->dropColumn([
                'pernah_rekrutmen_sebelumnya',
                'bulan_rekrutmen_sebelumnya',
                'tahun_rekrutmen_sebelumnya',
                'id_tahap_rekrutmen_sebelumnya',
            ]);
        });
    }
};
