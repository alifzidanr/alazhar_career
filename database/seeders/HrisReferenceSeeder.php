<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Snapshot of simpeg-proto's small HRIS reference tables (status kepegawaian,
 * status keaktifan, bidang diampu, status nikah), taken 2026-09-08, so the
 * career app's Migrasi Data form can offer matching dropdowns without a live
 * cross-database connection. IDs match HRIS's own primary keys 1:1.
 */
class HrisReferenceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('hris_status_kepegawaian')->upsert([
            ['id_status_kepegawaian' => 1, 'kode' => 'GTYK', 'status' => 'Guru Tetap Yayasan Khusus'],
            ['id_status_kepegawaian' => 2, 'kode' => 'GTY', 'status' => 'Guru Tetap Yayasan'],
            ['id_status_kepegawaian' => 3, 'kode' => 'TUY', 'status' => 'Tata Usaha Yayasan'],
            ['id_status_kepegawaian' => 4, 'kode' => 'TUYK', 'status' => 'Tata Usaha Yayasan Khusus'],
            ['id_status_kepegawaian' => 5, 'kode' => 'KTYK', 'status' => 'Karyawan Tetap Yayasan Khusus'],
            ['id_status_kepegawaian' => 6, 'kode' => 'TUY', 'status' => 'Tata Usaha Yayasan'],
            ['id_status_kepegawaian' => 7, 'kode' => 'HON', 'status' => 'Honorer'],
            ['id_status_kepegawaian' => 8, 'kode' => 'CPG', 'status' => 'Calon Pegawai'],
            ['id_status_kepegawaian' => 9, 'kode' => 'GTS', 'status' => 'Guru Tetap Subsidi'],
            ['id_status_kepegawaian' => 10, 'kode' => 'KTY', 'status' => 'Karyawan Tetap Yayasan'],
            ['id_status_kepegawaian' => 11, 'kode' => 'CPGP', 'status' => 'Calon Pegawai Diperbantukan'],
            ['id_status_kepegawaian' => 12, 'kode' => 'GTYP', 'status' => 'Guru Tetap Diperbantukan'],
            ['id_status_kepegawaian' => 13, 'kode' => 'PP', 'status' => 'Pensiunan Diperbantukan'],
            ['id_status_kepegawaian' => 14, 'kode' => 'GTT', 'status' => 'Guru Tidak Tetap'],
        ], ['id_status_kepegawaian']);

        DB::table('hris_status_keaktifan')->upsert([
            ['id_status_keaktifan' => 1, 'status_keaktifan' => 'Aktif'],
            ['id_status_keaktifan' => 2, 'status_keaktifan' => 'Keluar'],
            ['id_status_keaktifan' => 3, 'status_keaktifan' => 'Pensiun'],
            ['id_status_keaktifan' => 4, 'status_keaktifan' => 'Meninggal'],
            ['id_status_keaktifan' => 5, 'status_keaktifan' => 'CDT (Cuti Diluar Tanggungan)'],
            ['id_status_keaktifan' => 6, 'status_keaktifan' => 'Detasering'],
            ['id_status_keaktifan' => 7, 'status_keaktifan' => 'Pensiun Diperbantukan'],
        ], ['id_status_keaktifan']);

        DB::table('hris_bidang_diampu')->upsert([
            ['id_bidang_diampu' => 5, 'nama_bidang' => 'Non-Kependidikan'],
            ['id_bidang_diampu' => 6, 'nama_bidang' => 'Guru Pendamping'],
            ['id_bidang_diampu' => 7, 'nama_bidang' => 'Guru Tahfidz'],
            ['id_bidang_diampu' => 8, 'nama_bidang' => 'Satpam'],
            ['id_bidang_diampu' => 9, 'nama_bidang' => 'Guru Tilawati'],
            ['id_bidang_diampu' => 10, 'nama_bidang' => 'Tenaga Kepedidikan'],
        ], ['id_bidang_diampu']);

        DB::table('hris_status_nikah')->upsert([
            ['id_status_nikah' => 1, 'status_nikah' => 'Lajang'],
            ['id_status_nikah' => 2, 'status_nikah' => 'Menikah'],
            ['id_status_nikah' => 3, 'status_nikah' => 'Janda'],
            ['id_status_nikah' => 4, 'status_nikah' => 'Duda'],
            ['id_status_nikah' => 5, 'status_nikah' => 'Cerai'],
        ], ['id_status_nikah']);
    }
}
