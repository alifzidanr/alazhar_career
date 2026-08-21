<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds a program_studi column per tertiary level (S1/S2/S3), mirroring
     * the existing kategori_perguruan_tinggi and ipk per-level split, so an
     * S3 applicant can record a different major for each degree instead of
     * only their final one. The original program_studi column is kept and
     * now only applies to D3 (which has no earlier tertiary level).
     */
    public function up(): void
    {
        Schema::table('pelamar', function (Blueprint $table) {
            $table->string('program_studi_s1', 150)->nullable()->after('program_studi');
            $table->string('program_studi_s2', 150)->nullable()->after('program_studi_s1');
            $table->string('program_studi_s3', 150)->nullable()->after('program_studi_s2');
        });

        // Backfill from the old single column into whichever level column
        // matches each pelamar's recorded education level, then clear the
        // old column for S1/S2/S3 rows since it's now D3-only.
        foreach (['S1' => 'program_studi_s1', 'S2' => 'program_studi_s2', 'S3' => 'program_studi_s3'] as $label => $column) {
            DB::table('pelamar')
                ->whereIn('id_pendidikan_terakhir', function ($query) use ($label) {
                    $query->select('id_pendidikan_terakhir')->from('pendidikan_terakhir')->where('pendidikan_terakhir', $label);
                })
                ->whereNotNull('program_studi')
                ->update([$column => DB::raw('program_studi')]);
        }

        DB::table('pelamar')
            ->whereIn('id_pendidikan_terakhir', function ($query) {
                $query->select('id_pendidikan_terakhir')->from('pendidikan_terakhir')->whereIn('pendidikan_terakhir', ['S1', 'S2', 'S3']);
            })
            ->update(['program_studi' => null]);
    }

    public function down(): void
    {
        foreach (['program_studi_s1', 'program_studi_s2', 'program_studi_s3'] as $column) {
            DB::table('pelamar')
                ->whereNotNull($column)
                ->update(['program_studi' => DB::raw($column)]);
        }

        Schema::table('pelamar', function (Blueprint $table) {
            $table->dropColumn(['program_studi_s1', 'program_studi_s2', 'program_studi_s3']);
        });
    }
};
