<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loker', function (Blueprint $table) {
            $table->renameColumn('lokasi', 'wilayah');
        });
    }

    public function down(): void
    {
        Schema::table('loker', function (Blueprint $table) {
            $table->renameColumn('wilayah', 'lokasi');
        });
    }
};
