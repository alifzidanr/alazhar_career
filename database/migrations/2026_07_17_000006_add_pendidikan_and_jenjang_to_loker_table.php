<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nullable at the DB level since 44 existing loker rows have no value to
     * backfill from; StoreLokerRequest requires both fields for new/edited
     * postings going forward.
     */
    public function up(): void
    {
        Schema::table('loker', function (Blueprint $table) {
            $table->unsignedTinyInteger('id_pendidikan_terakhir')->nullable()->after('lokasi');
            $table->unsignedSmallInteger('id_jenjang')->nullable()->after('id_pendidikan_terakhir');

            $table->foreign('id_pendidikan_terakhir', 'fk_loker_pendidikan')
                ->references('id_pendidikan_terakhir')->on('pendidikan_terakhir')->nullOnDelete();
            $table->foreign('id_jenjang', 'fk_loker_jenjang')
                ->references('id_jenjang')->on('jenjang')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('loker', function (Blueprint $table) {
            $table->dropForeign('fk_loker_pendidikan');
            $table->dropForeign('fk_loker_jenjang');
            $table->dropColumn(['id_pendidikan_terakhir', 'id_jenjang']);
        });
    }
};
