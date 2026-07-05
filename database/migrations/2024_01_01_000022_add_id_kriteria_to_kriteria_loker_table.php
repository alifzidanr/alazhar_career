<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kriteria_loker', function (Blueprint $table) {
            $table->unsignedSmallInteger('id_kriteria')->nullable()->after('id_unit_kerja');
            $table->foreign('id_kriteria', 'fk_kl_kriteria')->references('id_kriteria')->on('kriteria')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('kriteria_loker', function (Blueprint $table) {
            $table->dropForeign('fk_kl_kriteria');
            $table->dropColumn('id_kriteria');
        });
    }
};
