<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drops the old free-text `wilayah` column (unused — 0 rows populated)
     * ahead of renaming `loker.lokasi` into that name instead, since the
     * "Lokasi" lookup concept is being renamed to "Wilayah" app-wide.
     */
    public function up(): void
    {
        Schema::table('loker', function (Blueprint $table) {
            $table->dropColumn('wilayah');
        });
    }

    public function down(): void
    {
        Schema::table('loker', function (Blueprint $table) {
            $table->string('wilayah', 100)->nullable()->after('lokasi');
        });
    }
};
