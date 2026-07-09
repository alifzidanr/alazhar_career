<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LokerSampleSeeder extends Seeder
{
    /**
     * Seed 10 sample lowongan (loker) with matching kriteria.
     */
    public function run(): void
    {
        $lokers = [
            [
                'judul_loker' => 'Guru Bahasa Inggris SMAIA 1',
                'deskripsi_loker' => 'Mengajar Bahasa Inggris untuk siswa SMA, menyusun RPP, dan membina kegiatan English Club.',
                'lokasi' => 'SMAIA 1, Jakarta Selatan',
                'status_loker' => 'dibuka',
                'start_time' => Carbon::parse('2026-07-01'),
                'end_time' => Carbon::parse('2026-08-15'),
                'id_posisi' => 1,
                'id_unit_kerja' => 1,
            ],
            [
                'judul_loker' => 'Guru Matematika SMAIA 2',
                'deskripsi_loker' => 'Mengajar Matematika kelas X-XII, membimbing siswa dalam olimpiade sains.',
                'lokasi' => 'SMAIA 2, Jakarta Selatan',
                'status_loker' => 'dibuka',
                'start_time' => Carbon::parse('2026-07-05'),
                'end_time' => Carbon::parse('2026-08-20'),
                'id_posisi' => 2,
                'id_unit_kerja' => 2,
            ],
            [
                'judul_loker' => 'Guru Bahasa Indonesia SMAIA 1',
                'deskripsi_loker' => 'Mengajar Bahasa Indonesia, mengembangkan minat baca dan literasi siswa.',
                'lokasi' => 'SMAIA 1, Jakarta Selatan',
                'status_loker' => 'dibuka',
                'start_time' => Carbon::parse('2026-06-20'),
                'end_time' => Carbon::parse('2026-07-31'),
                'id_posisi' => 3,
                'id_unit_kerja' => 1,
            ],
            [
                'judul_loker' => 'Staff TU Sekretariat Bagian Kepegawaian',
                'deskripsi_loker' => 'Mengelola administrasi kepegawaian, arsip surat menyurat, dan data karyawan.',
                'lokasi' => 'Bagian Kepegawaian, Jakarta Selatan',
                'status_loker' => 'dibuka',
                'start_time' => Carbon::parse('2026-07-08'),
                'end_time' => Carbon::parse('2026-08-10'),
                'id_posisi' => 4,
                'id_unit_kerja' => 3,
            ],
            [
                'judul_loker' => 'Staff Helpdesk IT Bagian Kepegawaian',
                'deskripsi_loker' => 'Menangani permintaan dukungan teknis IT untuk seluruh unit kerja.',
                'lokasi' => 'Bagian Kepegawaian, Jakarta Selatan',
                'status_loker' => 'ditutup',
                'start_time' => Carbon::parse('2026-05-01'),
                'end_time' => Carbon::parse('2026-06-15'),
                'id_posisi' => 5,
                'id_unit_kerja' => 3,
            ],
            [
                'judul_loker' => 'Guru Bahasa Inggris SMAIA 2',
                'deskripsi_loker' => 'Mengajar Bahasa Inggris, menyiapkan siswa untuk tes TOEFL/IELTS.',
                'lokasi' => 'SMAIA 2, Jakarta Selatan',
                'status_loker' => 'ditutup',
                'start_time' => Carbon::parse('2026-04-15'),
                'end_time' => Carbon::parse('2026-05-30'),
                'id_posisi' => 1,
                'id_unit_kerja' => 2,
            ],
            [
                'judul_loker' => 'Guru Matematika SMAIA 1',
                'deskripsi_loker' => 'Mengajar Matematika kelas X-XII dan membina tim olimpiade matematika.',
                'lokasi' => 'SMAIA 1, Jakarta Selatan',
                'status_loker' => 'dibuka',
                'start_time' => Carbon::parse('2026-07-10'),
                'end_time' => Carbon::parse('2026-08-25'),
                'id_posisi' => 2,
                'id_unit_kerja' => 1,
            ],
            [
                'judul_loker' => 'Staff TU Sekretariat SMAIA 2',
                'deskripsi_loker' => 'Menangani administrasi akademik, surat menyurat, dan arsip sekolah.',
                'lokasi' => 'SMAIA 2, Jakarta Selatan',
                'status_loker' => 'dibuka',
                'start_time' => Carbon::parse('2026-07-03'),
                'end_time' => Carbon::parse('2026-08-05'),
                'id_posisi' => 4,
                'id_unit_kerja' => 2,
            ],
            [
                'judul_loker' => 'Guru Bahasa Indonesia SMAIA 2',
                'deskripsi_loker' => 'Mengajar Bahasa Indonesia dan membina ekstrakurikuler jurnalistik.',
                'lokasi' => 'SMAIA 2, Jakarta Selatan',
                'status_loker' => 'ditutup',
                'start_time' => Carbon::parse('2026-03-01'),
                'end_time' => Carbon::parse('2026-04-10'),
                'id_posisi' => 3,
                'id_unit_kerja' => 2,
            ],
            [
                'judul_loker' => 'Staff Helpdesk IT SMAIA 1',
                'deskripsi_loker' => 'Mendukung operasional TIK sekolah, perawatan komputer dan jaringan lab.',
                'lokasi' => 'SMAIA 1, Jakarta Selatan',
                'status_loker' => 'dibuka',
                'start_time' => Carbon::parse('2026-07-09'),
                'end_time' => Carbon::parse('2026-08-30'),
                'id_posisi' => 5,
                'id_unit_kerja' => 1,
            ],
        ];

        foreach ($lokers as $loker) {
            $idPosisi = $loker['id_posisi'];
            $idUnitKerja = $loker['id_unit_kerja'];
            unset($loker['id_posisi'], $loker['id_unit_kerja']);

            $idLoker = DB::table('loker')->insertGetId([
                ...$loker,
                'created_at' => now(),
                'updated_at' => now(),
            ], 'id_loker');

            DB::table('kriteria_loker')->insert([
                'id_loker' => $idLoker,
                'id_posisi' => $idPosisi,
                'id_unit_kerja' => $idUnitKerja,
                'bobot' => 'wajib',
                'keterangan' => 'Minimal S1 sesuai bidang, pengalaman diutamakan.',
            ]);
        }
    }
}
