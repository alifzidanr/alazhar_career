<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Splits the single kategori_perguruan_tinggi column into one per
     * tertiary level (D3/S1/S2/S3), mirroring the existing ipk_* columns,
     * so the application form can ask for it at every level a candidate
     * completed rather than only their final one.
     */
    public function up(): void
    {
        Schema::table('pelamar', function (Blueprint $table) {
            $table->string('kategori_perguruan_tinggi_d3', 30)->nullable()->after('program_studi');
            $table->string('kategori_perguruan_tinggi_s1', 30)->nullable()->after('kategori_perguruan_tinggi_d3');
            $table->string('kategori_perguruan_tinggi_s2', 30)->nullable()->after('kategori_perguruan_tinggi_s1');
            $table->string('kategori_perguruan_tinggi_s3', 30)->nullable()->after('kategori_perguruan_tinggi_s2');
        });

        // Backfill from the old single column into whichever level column
        // matches each pelamar's recorded education level.
        foreach (['D3' => 'kategori_perguruan_tinggi_d3', 'S1' => 'kategori_perguruan_tinggi_s1', 'S2' => 'kategori_perguruan_tinggi_s2', 'S3' => 'kategori_perguruan_tinggi_s3'] as $label => $column) {
            DB::table('pelamar')
                ->whereIn('id_pendidikan_terakhir', function ($query) use ($label) {
                    $query->select('id_pendidikan_terakhir')->from('pendidikan_terakhir')->where('pendidikan_terakhir', $label);
                })
                ->whereNotNull('kategori_perguruan_tinggi')
                ->update([$column => DB::raw('kategori_perguruan_tinggi')]);
        }

        Schema::table('pelamar', function (Blueprint $table) {
            $table->dropColumn('kategori_perguruan_tinggi');
        });
    }

    public function down(): void
    {
        Schema::table('pelamar', function (Blueprint $table) {
            $table->string('kategori_perguruan_tinggi', 30)->nullable()->after('program_studi');
        });

        foreach (['kategori_perguruan_tinggi_d3', 'kategori_perguruan_tinggi_s1', 'kategori_perguruan_tinggi_s2', 'kategori_perguruan_tinggi_s3'] as $column) {
            DB::table('pelamar')
                ->whereNotNull($column)
                ->update(['kategori_perguruan_tinggi' => DB::raw($column)]);
        }

        Schema::table('pelamar', function (Blueprint $table) {
            $table->dropColumn([
                'kategori_perguruan_tinggi_d3',
                'kategori_perguruan_tinggi_s1',
                'kategori_perguruan_tinggi_s2',
                'kategori_perguruan_tinggi_s3',
            ]);
        });
    }
};
