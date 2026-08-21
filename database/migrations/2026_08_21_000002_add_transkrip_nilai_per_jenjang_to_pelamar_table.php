<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds a transkrip_nilai_upload column per tertiary level (S1/S2/S3),
     * mirroring the institusi/program_studi/kategori_perguruan_tinggi/ipk
     * per-level split, so an S2/S3 applicant uploads a separate transcript
     * per degree instead of one combined file. The original
     * transkrip_nilai_upload column is kept and now only applies to
     * non-S1/S2/S3 levels (SD through D3).
     */
    public function up(): void
    {
        Schema::table('pelamar', function (Blueprint $table) {
            $table->string('transkrip_nilai_s1_upload', 255)->nullable()->after('transkrip_nilai_upload');
            $table->string('transkrip_nilai_s2_upload', 255)->nullable()->after('transkrip_nilai_s1_upload');
            $table->string('transkrip_nilai_s3_upload', 255)->nullable()->after('transkrip_nilai_s2_upload');
        });

        // Backfill from the old single column into whichever level column
        // matches each pelamar's recorded education level, then clear the
        // old column for S1/S2/S3 rows since it's now non-tertiary-only.
        foreach (['S1' => 'transkrip_nilai_s1_upload', 'S2' => 'transkrip_nilai_s2_upload', 'S3' => 'transkrip_nilai_s3_upload'] as $label => $column) {
            DB::table('pelamar')
                ->whereIn('id_pendidikan_terakhir', function ($query) use ($label) {
                    $query->select('id_pendidikan_terakhir')->from('pendidikan_terakhir')->where('pendidikan_terakhir', $label);
                })
                ->whereNotNull('transkrip_nilai_upload')
                ->update([$column => DB::raw('transkrip_nilai_upload')]);
        }

        DB::table('pelamar')
            ->whereIn('id_pendidikan_terakhir', function ($query) {
                $query->select('id_pendidikan_terakhir')->from('pendidikan_terakhir')->whereIn('pendidikan_terakhir', ['S1', 'S2', 'S3']);
            })
            ->update(['transkrip_nilai_upload' => null]);
    }

    public function down(): void
    {
        foreach (['transkrip_nilai_s1_upload', 'transkrip_nilai_s2_upload', 'transkrip_nilai_s3_upload'] as $column) {
            DB::table('pelamar')
                ->whereNotNull($column)
                ->update(['transkrip_nilai_upload' => DB::raw($column)]);
        }

        Schema::table('pelamar', function (Blueprint $table) {
            $table->dropColumn(['transkrip_nilai_s1_upload', 'transkrip_nilai_s2_upload', 'transkrip_nilai_s3_upload']);
        });
    }
};
