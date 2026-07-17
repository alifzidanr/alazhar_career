<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ipk_s1 was NOT NULL from before the S1/S2/D3 split (every applicant used
     * to fill a single required "ipk" field). Now only S1/S2 applicants submit
     * it, so D3/other applicants would otherwise violate the NOT NULL constraint.
     */
    public function up(): void
    {
        Schema::table('pelamar', function (Blueprint $table) {
            $table->decimal('ipk_s1', 3, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pelamar', function (Blueprint $table) {
            $table->decimal('ipk_s1', 3, 2)->nullable(false)->change();
        });
    }
};
