<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds an institusi column per tertiary level (S1/S2/S3), mirroring the
     * existing program_studi/kategori_perguruan_tinggi/ipk per-level split,
     * so an S2/S3 applicant can record a different institution for each
     * degree instead of only their final one. The original institusi
     * column is kept and now only applies to non-S1/S2/S3 levels (SD
     * through D3, which have no earlier tertiary level).
     */
    public function up(): void
    {
        Schema::table('pelamar', function (Blueprint $table) {
            $table->string('institusi', 150)->nullable()->change();
            $table->string('institusi_s1', 150)->nullable()->after('institusi');
            $table->string('institusi_s2', 150)->nullable()->after('institusi_s1');
            $table->string('institusi_s3', 150)->nullable()->after('institusi_s2');
        });

        // Backfill from the old single column into whichever level column
        // matches each pelamar's recorded education level, then clear the
        // old column for S1/S2/S3 rows since it's now non-tertiary-only.
        foreach (['S1' => 'institusi_s1', 'S2' => 'institusi_s2', 'S3' => 'institusi_s3'] as $label => $column) {
            DB::table('pelamar')
                ->whereIn('id_pendidikan_terakhir', function ($query) use ($label) {
                    $query->select('id_pendidikan_terakhir')->from('pendidikan_terakhir')->where('pendidikan_terakhir', $label);
                })
                ->whereNotNull('institusi')
                ->update([$column => DB::raw('institusi')]);
        }

        DB::table('pelamar')
            ->whereIn('id_pendidikan_terakhir', function ($query) {
                $query->select('id_pendidikan_terakhir')->from('pendidikan_terakhir')->whereIn('pendidikan_terakhir', ['S1', 'S2', 'S3']);
            })
            ->update(['institusi' => null]);
    }

    public function down(): void
    {
        foreach (['institusi_s1', 'institusi_s2', 'institusi_s3'] as $column) {
            DB::table('pelamar')
                ->whereNotNull($column)
                ->update(['institusi' => DB::raw($column)]);
        }

        Schema::table('pelamar', function (Blueprint $table) {
            $table->dropColumn(['institusi_s1', 'institusi_s2', 'institusi_s3']);
        });
    }
};
