<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LokerSampleSeeder2 extends Seeder
{
    /**
     * Seed 10 additional sample lowongan (loker) with matching kriteria.
     */
    public function run(): void
    {
        $lokers = [
            [
                'judul_loker' => 'Guru Bahasa Inggris Bagian Kepegawaian (Trainer Internal)',
                'deskripsi_loker' => 'Memberikan pelatihan Bahasa Inggris untuk staf dan guru di lingkungan Al-Azhar.',
                'lokasi' => 'Bagian Kepegawaian, Jakarta Selatan',
                'status_loker' => 'dibuka',
                'start_time' => Carbon::parse('2026-07-15'),
                'end_time' => Carbon::parse('2026-08-31'),
                'id_posisi' => 1,
                'id_unit_kerja' => 3,
            ],
            [
                'judul_loker' => 'Guru Matematika Bagian Kepegawaian (Penyusun Modul)',
                'deskripsi_loker' => 'Menyusun modul dan bank soal Matematika untuk seluruh unit sekolah.',
                'lokasi' => 'Bagian Kepegawaian, Jakarta Selatan',
                'status_loker' => 'dibuka',
                'start_time' => Carbon::parse('2026-07-12'),
                'end_time' => Carbon::parse('2026-08-28'),
                'id_posisi' => 2,
                'id_unit_kerja' => 3,
            ],
            [
                'judul_loker' => 'Guru Bahasa Indonesia Bagian Kepegawaian (Editor Konten)',
                'deskripsi_loker' => 'Menyunting materi ajar dan publikasi Bahasa Indonesia untuk seluruh unit.',
                'lokasi' => 'Bagian Kepegawaian, Jakarta Selatan',
                'status_loker' => 'ditutup',
                'start_time' => Carbon::parse('2026-05-10'),
                'end_time' => Carbon::parse('2026-06-20'),
                'id_posisi' => 3,
                'id_unit_kerja' => 3,
            ],
            [
                'judul_loker' => 'Staff TU Sekretariat SMAIA 1 (Tambahan Semester Genap)',
                'deskripsi_loker' => 'Membantu administrasi akademik dan kesiswaan selama semester genap.',
                'lokasi' => 'SMAIA 1, Jakarta Selatan',
                'status_loker' => 'dibuka',
                'start_time' => Carbon::parse('2026-07-14'),
                'end_time' => Carbon::parse('2026-08-18'),
                'id_posisi' => 4,
                'id_unit_kerja' => 1,
            ],
            [
                'judul_loker' => 'Staff Helpdesk IT SMAIA 2 (Shift Sore)',
                'deskripsi_loker' => 'Menangani dukungan teknis IT untuk laboratorium komputer shift sore.',
                'lokasi' => 'SMAIA 2, Jakarta Selatan',
                'status_loker' => 'dibuka',
                'start_time' => Carbon::parse('2026-07-11'),
                'end_time' => Carbon::parse('2026-08-22'),
                'id_posisi' => 5,
                'id_unit_kerja' => 2,
            ],
            [
                'judul_loker' => 'Guru Bahasa Inggris SMAIA 1 (Kontrak Semester Ganjil)',
                'deskripsi_loker' => 'Mengajar Bahasa Inggris kelas X dengan kontrak satu semester.',
                'lokasi' => 'SMAIA 1, Jakarta Selatan',
                'status_loker' => 'ditutup',
                'start_time' => Carbon::parse('2026-02-01'),
                'end_time' => Carbon::parse('2026-03-15'),
                'id_posisi' => 1,
                'id_unit_kerja' => 1,
            ],
            [
                'judul_loker' => 'Guru Matematika SMAIA 2 (Kelas Persiapan Olimpiade)',
                'deskripsi_loker' => 'Membina kelas persiapan olimpiade Matematika tingkat SMA.',
                'lokasi' => 'SMAIA 2, Jakarta Selatan',
                'status_loker' => 'dibuka',
                'start_time' => Carbon::parse('2026-07-16'),
                'end_time' => Carbon::parse('2026-09-01'),
                'id_posisi' => 2,
                'id_unit_kerja' => 2,
            ],
            [
                'judul_loker' => 'Guru Bahasa Indonesia SMAIA 1 (Pembina Ekstrakurikuler)',
                'deskripsi_loker' => 'Mengajar Bahasa Indonesia sekaligus membina ekstrakurikuler teater.',
                'lokasi' => 'SMAIA 1, Jakarta Selatan',
                'status_loker' => 'ditutup',
                'start_time' => Carbon::parse('2026-04-01'),
                'end_time' => Carbon::parse('2026-05-15'),
                'id_posisi' => 3,
                'id_unit_kerja' => 1,
            ],
            [
                'judul_loker' => 'Staff TU Sekretariat Bagian Kepegawaian (Arsip Digital)',
                'deskripsi_loker' => 'Melakukan digitalisasi dan pengarsipan dokumen kepegawaian.',
                'lokasi' => 'Bagian Kepegawaian, Jakarta Selatan',
                'status_loker' => 'dibuka',
                'start_time' => Carbon::parse('2026-07-13'),
                'end_time' => Carbon::parse('2026-08-27'),
                'id_posisi' => 4,
                'id_unit_kerja' => 3,
            ],
            [
                'judul_loker' => 'Staff Helpdesk IT SMAIA 1 (Proyek Jaringan Baru)',
                'deskripsi_loker' => 'Mendukung instalasi dan pemeliharaan jaringan komputer sekolah.',
                'lokasi' => 'SMAIA 1, Jakarta Selatan',
                'status_loker' => 'ditutup',
                'start_time' => Carbon::parse('2026-03-10'),
                'end_time' => Carbon::parse('2026-04-25'),
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
