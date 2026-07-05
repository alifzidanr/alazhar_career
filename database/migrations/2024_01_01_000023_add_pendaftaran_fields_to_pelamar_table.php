<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelamar', function (Blueprint $table) {
            $table->date('tanggal_lahir')->after('nama');
            $table->enum('jenis_kelamin', ['L', 'P'])->after('tanggal_lahir');
            $table->text('alamat')->after('email');
            $table->string('institusi', 150)->after('id_pendidikan_terakhir');
            $table->string('program_studi', 150)->after('institusi');
            $table->string('akreditasi', 20)->after('program_studi');
            $table->unsignedSmallInteger('tahun_lulus')->after('akreditasi');
            $table->decimal('ipk', 3, 2)->after('tahun_lulus');
            $table->string('cv_upload', 255)->after('ipk');
        });
    }

    public function down(): void
    {
        Schema::table('pelamar', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_lahir',
                'jenis_kelamin',
                'alamat',
                'institusi',
                'program_studi',
                'akreditasi',
                'tahun_lulus',
                'ipk',
                'cv_upload',
            ]);
        });
    }
};
