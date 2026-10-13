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
        Schema::table('jenjang', function (Blueprint $table) {
            $table->unsignedInteger('urutan')->default(0)->after('id_jenjang');
        });

        // Backfill using the existing id_jenjang order so dropdowns keep
        // showing today's order until an admin reorders them.
        DB::table('jenjang')->orderBy('id_jenjang')->pluck('id_jenjang')
            ->each(fn ($id, $i) => DB::table('jenjang')->where('id_jenjang', $id)->update(['urutan' => $i + 1]));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenjang', function (Blueprint $table) {
            $table->dropColumn('urutan');
        });
    }
};
