<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Removes SD/D1/D2 as selectable education levels. Any pelamar still
     * referencing one of these (only id_pelamar=9 on D2 as of writing, a test
     * record) is bumped to D3 as a one-off data fix before the rows are deleted,
     * since these are dev/test records, not a general reassignment policy.
     */
    public function up(): void
    {
        DB::table('pelamar')
            ->whereIn('id_pendidikan_terakhir', [1, 4, 5])
            ->update(['id_pendidikan_terakhir' => 6]);

        DB::table('pendidikan_terakhir')->whereIn('id_pendidikan_terakhir', [1, 4, 5])->delete();
    }

    public function down(): void
    {
        DB::table('pendidikan_terakhir')->insert([
            ['id_pendidikan_terakhir' => 1, 'pendidikan_terakhir' => 'SD'],
            ['id_pendidikan_terakhir' => 4, 'pendidikan_terakhir' => 'D1'],
            ['id_pendidikan_terakhir' => 5, 'pendidikan_terakhir' => 'D2'],
        ]);
    }
};
