<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Removes "Tes Kesehatan" as a distinct pipeline stage (id 6); its only
     * remaining artifact, the health-test-result upload, moves into the new
     * tugas_sementara table. Terima SK (7) and Migrasi Data (8) are renumbered
     * down to 6/7 so the pipeline stays a contiguous 1-7 sequence for the
     * existing advance/regress (+-1) logic.
     *
     * Any *live* pelamar currently sitting at tahap 6/7/8 blocks this migration
     * (needs manual review). Historical riwayat_tahap_pelamar audit rows are
     * safe to renumber automatically: old tahap 6 (Tes Kesehatan) entries are
     * remapped to 5 (Tugas Sementara, which absorbed it), 7->6, 8->7.
     */
    public function up(): void
    {
        $liveReferencing = DB::table('pelamar')->whereIn('id_tahap_rekrutmen', [6, 7, 8])->count();

        if ($liveReferencing > 0) {
            throw new \RuntimeException(
                "Cannot renumber tahap_rekrutmen: {$liveReferencing} pelamar row(s) currently sit at tahap id 6/7/8. Resolve manually before running this migration."
            );
        }

        DB::table('riwayat_tahap_pelamar')->where('id_tahap_rekrutmen', 8)->update(['id_tahap_rekrutmen' => 7]);
        DB::table('riwayat_tahap_pelamar')->where('id_tahap_rekrutmen', 7)->update(['id_tahap_rekrutmen' => 6]);
        DB::table('riwayat_tahap_pelamar')->where('id_tahap_rekrutmen', 6)->update(['id_tahap_rekrutmen' => 5]);

        DB::table('tahap_rekrutmen')->where('id_tahap_rekrutmen', 6)->delete();
        DB::table('tahap_rekrutmen')->where('id_tahap_rekrutmen', 7)->update(['id_tahap_rekrutmen' => 6]);
        DB::table('tahap_rekrutmen')->where('id_tahap_rekrutmen', 8)->update(['id_tahap_rekrutmen' => 7]);
        DB::statement('ALTER TABLE tahap_rekrutmen AUTO_INCREMENT = 8');
    }

    public function down(): void
    {
        DB::table('tahap_rekrutmen')->where('id_tahap_rekrutmen', 7)->update(['id_tahap_rekrutmen' => 8]);
        DB::table('tahap_rekrutmen')->where('id_tahap_rekrutmen', 6)->update(['id_tahap_rekrutmen' => 7]);
        DB::table('tahap_rekrutmen')->insert(['id_tahap_rekrutmen' => 6, 'tahap_rekrutmen' => 'Tes Kesehatan']);
        DB::statement('ALTER TABLE tahap_rekrutmen AUTO_INCREMENT = 9');

        // Note: riwayat rows originally at old tahap 6 were folded into 5 and
        // cannot be distinguished from genuine tahap-5 history on rollback.
        DB::table('riwayat_tahap_pelamar')->where('id_tahap_rekrutmen', 7)->update(['id_tahap_rekrutmen' => 8]);
        DB::table('riwayat_tahap_pelamar')->where('id_tahap_rekrutmen', 6)->update(['id_tahap_rekrutmen' => 7]);
    }
};
