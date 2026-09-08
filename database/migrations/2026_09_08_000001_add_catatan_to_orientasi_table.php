<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orientasi', function (Blueprint $table) {
            $table->text('catatan')->nullable()->after('sk_orientasi_upload');
        });
    }

    public function down(): void
    {
        Schema::table('orientasi', function (Blueprint $table) {
            $table->dropColumn('catatan');
        });
    }
};
