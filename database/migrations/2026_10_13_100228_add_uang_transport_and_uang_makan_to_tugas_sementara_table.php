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
        Schema::table('tugas_sementara', function (Blueprint $table) {
            $table->unsignedInteger('uang_makan')->nullable()->after('id_pelamar');
            $table->unsignedInteger('uang_transport')->nullable()->after('uang_makan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tugas_sementara', function (Blueprint $table) {
            $table->dropColumn(['uang_makan', 'uang_transport']);
        });
    }
};
