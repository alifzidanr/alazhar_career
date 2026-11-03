<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LokerSampleSeeder4 extends Seeder
{
    /**
     * Seed 20 dummy lowongan (loker) that are all open from 2026-11-03
     * until 2026-12-25 (Berlaku Sampai), each with matching kriteria.
     */
    public function run(): void
    {
        // [judul, id_jenjang, id_pendidikan_terakhir, wilayah, deskripsi, [[id_kriteria, bobot], ...]]
        $lokers = [
            ['Guru TK Al Azhar Kebayoran Baru', 1, 7, 'Kebayoran Baru', 'Mendampingi tumbuh kembang anak usia dini melalui pembelajaran bermain yang islami dan menyenangkan.', [[26, 'wajib'], [8, 'diutamakan'], [7, 'wajib']]],
            ['Guru TK Al Azhar Depok', 1, 7, 'Depok', 'Merancang kegiatan belajar sentra dan membina akhlak siswa TK.', [[26, 'wajib'], [22, 'diutamakan'], [7, 'wajib']]],
            ['Guru Kelas SD Al Azhar Pejaten', 2, 7, 'Pejaten', 'Mengajar sebagai guru kelas SD, menyusun perangkat ajar dan asesmen formatif.', [[26, 'wajib'], [4, 'diutamakan'], [8, 'diutamakan']]],
            ['Guru Bahasa Inggris SD Al Azhar Cikarang', 2, 7, 'Cikarang', 'Mengajar Bahasa Inggris tingkat SD dengan pendekatan komunikatif.', [[26, 'wajib'], [31, 'wajib'], [8, 'nilai_tambah']]],
            ['Guru Matematika SMP Al Azhar Jakarta', 3, 7, 'DKI Jakarta', 'Mengajar Matematika SMP dan membina tim olimpiade sekolah.', [[26, 'wajib'], [4, 'wajib'], [8, 'diutamakan']]],
            ['Guru IPA SMP Al Azhar Malang', 3, 7, 'Malang', 'Mengajar IPA terpadu dan mengelola kegiatan praktikum laboratorium.', [[26, 'wajib'], [19, 'diutamakan'], [8, 'diutamakan']]],
            ['Guru Seni Budaya SMP Al Azhar Sidoarjo', 3, 7, 'Sidoarjo', 'Mengajar Seni Budaya dan membina ekstrakurikuler seni rupa.', [[28, 'wajib'], [8, 'nilai_tambah']]],
            ['Guru Bahasa Inggris SMA Al Azhar Palembang', 4, 7, 'Palembang', 'Mengajar Bahasa Inggris SMA dan mempersiapkan siswa menghadapi tes internasional.', [[26, 'wajib'], [29, 'wajib'], [8, 'diutamakan']]],
            ['Guru Bahasa Indonesia SMA Al Azhar Pangkalpinang', 4, 7, 'Pangkalpinang', 'Mengajar Bahasa Indonesia dan membina literasi serta jurnalistik siswa.', [[26, 'wajib'], [4, 'diutamakan'], [22, 'diutamakan']]],
            ['Guru Matematika SMA Al Azhar Bengkulu', 4, 7, 'Bengkulu', 'Mengajar Matematika SMA peminatan dan membimbing persiapan seleksi PTN.', [[26, 'wajib'], [4, 'wajib'], [15, 'diutamakan']]],
            ['Satpam Kampus Al Azhar Kebayoran Baru', 5, 3, 'Kebayoran Baru', 'Menjaga keamanan dan ketertiban lingkungan kampus serta mengatur lalu lintas antar-jemput siswa.', [[13, 'wajib'], [36, 'wajib'], [23, 'wajib']]],
            ['Satpam Al Azhar Jadetabek', 5, 3, 'Jadetabek', 'Melakukan patroli rutin dan pengawasan akses keluar-masuk area sekolah.', [[13, 'wajib'], [36, 'diutamakan'], [3, 'wajib']]],
            ['Driver Operasional Al Azhar Jakarta', 6, 3, 'Jakarta', 'Mengemudikan kendaraan operasional yayasan dan merawat kondisi kendaraan.', [[13, 'wajib'], [14, 'wajib'], [15, 'diutamakan']]],
            ['Driver Bus Sekolah Al Azhar Depok', 6, 3, 'Depok', 'Mengantar-jemput siswa dengan bus sekolah secara aman dan tepat waktu.', [[13, 'wajib'], [14, 'wajib'], [3, 'wajib']]],
            ['Staf Tata Usaha SMA Al Azhar Pejaten', 7, 7, 'Pejaten', 'Mengelola administrasi akademik, data siswa, dan arsip persuratan sekolah.', [[26, 'wajib'], [22, 'diutamakan'], [15, 'nilai_tambah']]],
            ['Staf Tata Usaha SD Al Azhar Cikarang', 7, 7, 'Cikarang', 'Menangani administrasi keuangan sekolah dan layanan orang tua siswa.', [[26, 'wajib'], [4, 'diutamakan']]],
            ['Teknisi IT Al Azhar DKI Jakarta', 8, 3, 'DKI Jakarta', 'Memelihara perangkat komputer, jaringan, dan perangkat multimedia kelas.', [[13, 'wajib'], [15, 'diutamakan'], [22, 'nilai_tambah']]],
            ['Staf Humas Yayasan Al Azhar', 10, 7, 'DKI Jakarta', 'Mengelola publikasi, media sosial, dan hubungan masyarakat yayasan.', [[18, 'wajib'], [20, 'wajib'], [22, 'diutamakan']]],
            ['Janitor Kampus Al Azhar Malang', 11, 2, 'Malang', 'Menjaga kebersihan ruang kelas, kantor, dan fasilitas umum kampus.', [[27, 'wajib'], [7, 'diutamakan']]],
            ['Laboran IPA Al Azhar Sidoarjo', 12, 6, 'Sidoarjo', 'Menyiapkan alat dan bahan praktikum serta menjaga keselamatan laboratorium IPA.', [[30, 'wajib'], [15, 'diutamakan']]],
        ];

        $start = Carbon::parse('2026-11-03 00:00:00');
        $end = Carbon::parse('2026-12-25 00:00:00');

        DB::transaction(function () use ($lokers, $start, $end) {
            foreach ($lokers as [$judul, $idJenjang, $idPendidikan, $wilayah, $deskripsi, $kriteria]) {
                $idLoker = DB::table('loker')->insertGetId([
                    'judul_loker' => $judul,
                    'deskripsi_loker' => "<p>{$deskripsi}</p>",
                    'wilayah' => $wilayah,
                    'id_pendidikan_terakhir' => $idPendidikan,
                    'id_jenjang' => $idJenjang,
                    'status_loker' => 'dibuka',
                    'start_time' => $start,
                    'end_time' => $end,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], 'id_loker');

                foreach ($kriteria as [$idKriteria, $bobot]) {
                    DB::table('kriteria_loker')->insert([
                        'id_loker' => $idLoker,
                        'id_kriteria' => $idKriteria,
                        'bobot' => $bobot,
                    ]);
                }
            }
        });
    }
}
