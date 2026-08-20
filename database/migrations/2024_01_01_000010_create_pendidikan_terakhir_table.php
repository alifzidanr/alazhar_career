<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pendidikan_terakhir', function (Blueprint $table) {
            $table->tinyIncrements('id_pendidikan_terakhir');
            $table->string('pendidikan_terakhir', 10)
                ->comment('SD, SMP, SMA, D1, D2, D3, S1, S2, S3');
        });

        // Seeded here (rather than a seeder) since every other part of the
        // app — the public application form, StorePelamarRequest, admin
        // views — depends on these rows existing; a later migration removes
        // SD/D1/D2 as selectable levels, leaving SMP/SMA/D3/S1/S2/S3.
        DB::table('pendidikan_terakhir')->insert([
            ['id_pendidikan_terakhir' => 1, 'pendidikan_terakhir' => 'SD'],
            ['id_pendidikan_terakhir' => 2, 'pendidikan_terakhir' => 'SMP'],
            ['id_pendidikan_terakhir' => 3, 'pendidikan_terakhir' => 'SMA'],
            ['id_pendidikan_terakhir' => 4, 'pendidikan_terakhir' => 'D1'],
            ['id_pendidikan_terakhir' => 5, 'pendidikan_terakhir' => 'D2'],
            ['id_pendidikan_terakhir' => 6, 'pendidikan_terakhir' => 'D3'],
            ['id_pendidikan_terakhir' => 7, 'pendidikan_terakhir' => 'S1'],
            ['id_pendidikan_terakhir' => 8, 'pendidikan_terakhir' => 'S2'],
            ['id_pendidikan_terakhir' => 9, 'pendidikan_terakhir' => 'S3'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendidikan_terakhir');
    }
};
