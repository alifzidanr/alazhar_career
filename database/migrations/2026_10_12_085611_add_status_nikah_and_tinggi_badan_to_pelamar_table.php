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
            $table->enum('status_nikah', ['Ya', 'Tidak'])->nullable()->after('jenis_kelamin');
            // cm — only collected/required for the Satpam jenjang (see
            // StorePelamarRequest), nullable here since every other jenjang
            // leaves it blank.
            $table->unsignedSmallInteger('tinggi_badan')->nullable()->after('status_nikah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelamar', function (Blueprint $table) {
            $table->dropColumn(['status_nikah', 'tinggi_badan']);
        });
    }
};
