<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LokerSampleSeeder3 extends Seeder
{
    /**
     * Seed 20 additional sample lowongan (loker) with matching kriteria.
     */
    public function run(): void
    {
        $posisi = [
            1 => 'Guru Bahasa Inggris',
            2 => 'Guru Matematika',
            3 => 'Guru Bahasa Indonesia',
            4 => 'TU Sekretariat',
            5 => 'Helpdesk IT',
        ];

        $unitKerja = [
            1 => 'SMAIA 1',
            2 => 'SMAIA 2',
            3 => 'Bagian Kepegawaian',
        ];

        $deskripsi = [
            1 => 'Mengajar dan membina kemampuan berbahasa Inggris siswa, menyusun bahan ajar dan asesmen.',
            2 => 'Mengajar Matematika, menyusun bahan ajar, dan membina siswa berprestasi.',
            3 => 'Mengajar Bahasa Indonesia, mengembangkan literasi dan minat baca siswa.',
            4 => 'Mengelola administrasi akademik, kesiswaan, dan arsip persuratan unit.',
            5 => 'Mendukung operasional TIK, pemeliharaan perangkat dan jaringan unit.',
        ];

        $keterangan = [
            'Reguler', 'Kontrak', 'Paruh Waktu', 'Pengganti Sementara', 'Program Khusus',
        ];

        $lokasi = [
            1 => 'SMAIA 1, Jakarta Selatan',
            2 => 'SMAIA 2, Jakarta Selatan',
            3 => 'Bagian Kepegawaian, Jakarta Selatan',
        ];

        $startBase = Carbon::parse('2026-01-05');

        $rows = [];
        for ($i = 0; $i < 20; $i++) {
            $idPosisi = ($i % 5) + 1;
            $idUnitKerja = ($i % 3) + 1;
            $label = $keterangan[$i % count($keterangan)];

            $start = $startBase->copy()->addDays($i * 9);
            $end = $start->copy()->addDays(25 + ($i % 15));
            $isPastDeadline = $end->lt(Carbon::parse('2026-07-09'));

            $rows[] = [
                'judul_loker' => "{$posisi[$idPosisi]} {$unitKerja[$idUnitKerja]} ({$label})",
                'deskripsi_loker' => $deskripsi[$idPosisi],
                'lokasi' => $lokasi[$idUnitKerja],
                'status_loker' => $isPastDeadline ? 'ditutup' : 'dibuka',
                'start_time' => $start,
                'end_time' => $end,
                'id_posisi' => $idPosisi,
                'id_unit_kerja' => $idUnitKerja,
            ];
        }

        foreach ($rows as $loker) {
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
