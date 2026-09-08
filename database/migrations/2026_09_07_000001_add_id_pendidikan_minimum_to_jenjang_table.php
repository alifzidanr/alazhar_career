<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Backfills existing jenjang rows per current HR policy: Guru* and Tata
     * Usaha require S1, Teknisi/Satpam/Driver require SMA. Any jenjang not
     * matching those patterns (e.g. a future "Karyawan" row) is left with no
     * minimum until an admin sets one on the Manajemen Jenjang page.
     */
    public function up(): void
    {
        Schema::table('jenjang', function (Blueprint $table) {
            $table->unsignedTinyInteger('id_pendidikan_minimum')->nullable()->after('nama_jenjang');

            $table->foreign('id_pendidikan_minimum', 'fk_jenjang_pendidikan_minimum')
                ->references('id_pendidikan_terakhir')->on('pendidikan_terakhir')->nullOnDelete();
        });

        $sma = DB::table('pendidikan_terakhir')->where('pendidikan_terakhir', 'SMA')->value('id_pendidikan_terakhir');
        $s1 = DB::table('pendidikan_terakhir')->where('pendidikan_terakhir', 'S1')->value('id_pendidikan_terakhir');

        DB::table('jenjang')->where('nama_jenjang', 'like', 'Guru%')
            ->orWhere('nama_jenjang', 'Tata Usaha')
            ->update(['id_pendidikan_minimum' => $s1]);

        DB::table('jenjang')->whereIn('nama_jenjang', ['Teknisi', 'Satpam', 'Driver'])
            ->update(['id_pendidikan_minimum' => $sma]);
    }

    public function down(): void
    {
        Schema::table('jenjang', function (Blueprint $table) {
            $table->dropForeign('fk_jenjang_pendidikan_minimum');
            $table->dropColumn('id_pendidikan_minimum');
        });
    }
};
