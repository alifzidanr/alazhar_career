<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenjang', function (Blueprint $table) {
            $table->smallIncrements('id_jenjang');
            $table->string('nama_jenjang', 100)->unique();
        });

        DB::table('jenjang')->insert([
            ['nama_jenjang' => 'Guru TK'],
            ['nama_jenjang' => 'Guru SD'],
            ['nama_jenjang' => 'Guru SMP'],
            ['nama_jenjang' => 'Guru SMA'],
            ['nama_jenjang' => 'Satpam'],
            ['nama_jenjang' => 'Driver'],
            ['nama_jenjang' => 'Tata Usaha'],
            ['nama_jenjang' => 'Teknisi'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('jenjang');
    }
};
