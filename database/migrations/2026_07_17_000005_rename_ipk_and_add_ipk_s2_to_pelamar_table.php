<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelamar', function (Blueprint $table) {
            $table->renameColumn('ipk', 'ipk_s1');
        });

        Schema::table('pelamar', function (Blueprint $table) {
            $table->decimal('ipk_s2', 3, 2)->nullable()->after('ipk_s1');
        });
    }

    public function down(): void
    {
        Schema::table('pelamar', function (Blueprint $table) {
            $table->dropColumn('ipk_s2');
        });

        Schema::table('pelamar', function (Blueprint $table) {
            $table->renameColumn('ipk_s1', 'ipk');
        });
    }
};
