<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * program_studi and akreditasi no longer apply to SMP/SMA applicants, so they
     * must allow null (raw SQL used since doctrine/dbal isn't installed for ->change()).
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE pelamar MODIFY program_studi VARCHAR(150) NULL');
        DB::statement('ALTER TABLE pelamar MODIFY akreditasi VARCHAR(20) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE pelamar MODIFY program_studi VARCHAR(150) NOT NULL DEFAULT ''");
        DB::statement("ALTER TABLE pelamar MODIFY akreditasi VARCHAR(20) NOT NULL DEFAULT ''");
    }
};
