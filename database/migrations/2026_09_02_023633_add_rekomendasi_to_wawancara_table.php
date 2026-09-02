<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wawancara', function (Blueprint $table) {
            $table->text('rekomendasi_wawancara_agama')->nullable()->after('nilai_wawancara_agama');
            $table->text('rekomendasi_praktik_micro_teaching')->nullable()->after('nilai_praktik_micro_teaching');
            $table->text('rekomendasi_wawancara_umum')->nullable()->after('nilai_wawancara_umum');
        });
    }

    public function down(): void
    {
        Schema::table('wawancara', function (Blueprint $table) {
            $table->dropColumn(['rekomendasi_wawancara_agama', 'rekomendasi_praktik_micro_teaching', 'rekomendasi_wawancara_umum']);
        });
    }
};
