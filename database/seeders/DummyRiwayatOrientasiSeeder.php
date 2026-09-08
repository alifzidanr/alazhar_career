<?php

namespace Database\Seeders;

use App\Models\Loker;
use App\Models\Orientasi;
use App\Models\Pelamar;
use App\Models\StatusPelamar;
use App\Models\TahapRekrutmen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Purely additive demo data for the "riwayat Orientasi" red-flag + tooltip
 * feature in Manajemen Pelamar. Each case below is one applicant (one NIK)
 * with two or more applications at different lokers, covering the rule: only
 * the NEWEST application can be flagged red, and only when an OLDER
 * application (any status) reached the Orientasi stage.
 *
 * Safe to re-run - it deletes only the rows it created (by NIK) before
 * recreating them, and never touches any other data.
 */
class DummyRiwayatOrientasiSeeder extends Seeder
{
    private const PLACEHOLDER_IMG = 'placeholders/blank.png';

    private const PLACEHOLDER_PDF = 'placeholders/blank.pdf';

    public function run(): void
    {
        $cases = [
            // Case 1: older application reached Orientasi (status Tidak Lolos,
            // proving status doesn't matter) -> newest application IS flagged red.
            [
                'nik' => '3171056001990001',
                'nama' => 'Rizky Pratama (Dummy Riwayat Orientasi)',
                'aplikasi' => [
                    ['loker' => 3, 'tanggal' => '2026-08-12', 'tahap' => TahapRekrutmen::ORIENTASI, 'status' => StatusPelamar::TIDAK_LOLOS, 'orientasi' => true],
                    ['loker' => 14, 'tanggal' => '2026-09-05', 'tahap' => TahapRekrutmen::SELEKSI_BERKAS, 'status' => StatusPelamar::SCREENING],
                ],
                'expect' => 'Newest (Loker 14) is red. Tooltip shows Loker 3, tahap Orientasi.',
            ],

            // Case 2: older application never reached Orientasi -> newest is NOT
            // red, but still gets a tooltip (riwayat exists regardless of the flag).
            [
                'nik' => '3271056001990002',
                'nama' => 'Dewi Anggraini (Dummy Tanpa Riwayat Orientasi)',
                'aplikasi' => [
                    ['loker' => 96, 'tanggal' => '2026-08-01', 'tahap' => TahapRekrutmen::WAWANCARA, 'status' => StatusPelamar::TIDAK_LOLOS],
                    ['loker' => 56, 'tanggal' => '2026-09-03', 'tahap' => TahapRekrutmen::SELEKSI_BERKAS, 'status' => StatusPelamar::SCREENING],
                ],
                'expect' => 'Newest (Loker 56) is NOT red. Tooltip shows Loker 96, tahap Wawancara.',
            ],

            // Case 3: three applications, the two OLDER ones both reached Orientasi
            // or beyond -> newest is red, and its tooltip shows only the LATEST of
            // the older ones (the middle application), not the oldest.
            [
                'nik' => '3271056001990003',
                'nama' => 'Fajar Nugroho (Dummy Riwayat Ganda)',
                'aplikasi' => [
                    ['loker' => 77, 'tanggal' => '2026-08-05', 'tahap' => TahapRekrutmen::ORIENTASI, 'status' => StatusPelamar::DICADANGKAN, 'orientasi' => true],
                    ['loker' => 49, 'tanggal' => '2026-08-25', 'tahap' => TahapRekrutmen::TUGAS_SEMENTARA, 'status' => StatusPelamar::ONGOING],
                    ['loker' => 90, 'tanggal' => '2026-09-06', 'tahap' => TahapRekrutmen::SELEKSI_BERKAS, 'status' => StatusPelamar::SCREENING],
                ],
                'expect' => 'Newest (Loker 90) is red. Tooltip shows Loker 49 (latest of the two older ones), not Loker 77.',
            ],

            // Case 4: the OLDER application never reached Orientasi, but the
            // NEWEST application itself is currently at Orientasi -> still NOT
            // red, since reaching Orientasi on the newest application itself
            // doesn't count as history.
            [
                'nik' => '3271056001990004',
                'nama' => 'Maya Kusuma (Dummy Orientasi Bukan Riwayat)',
                'aplikasi' => [
                    ['loker' => 6, 'tanggal' => '2026-08-10', 'tahap' => TahapRekrutmen::SELEKSI_BERKAS, 'status' => StatusPelamar::TIDAK_LOLOS],
                    ['loker' => 10, 'tanggal' => '2026-09-07', 'tahap' => TahapRekrutmen::ORIENTASI, 'status' => StatusPelamar::ONGOING, 'orientasi' => true],
                ],
                'expect' => 'Newest (Loker 10) is NOT red, even though it is itself at Orientasi. Tooltip shows Loker 6.',
            ],

            // Case 5: older application went all the way to Terima SK with a
            // positive "Diterima" status -> still counts as Orientasi-stage
            // history (any status, any tahap >= Orientasi), so newest is red.
            [
                'nik' => '3271056001990005',
                'nama' => 'Siti Rahmawati (Dummy Riwayat Diterima)',
                'aplikasi' => [
                    ['loker' => 61, 'tanggal' => '2026-08-03', 'tahap' => TahapRekrutmen::TERIMA_SK, 'status' => StatusPelamar::DITERIMA],
                    ['loker' => 17, 'tanggal' => '2026-09-04', 'tahap' => TahapRekrutmen::SELEKSI_BERKAS, 'status' => StatusPelamar::SCREENING],
                ],
                'expect' => 'Newest (Loker 17) is red. Older reached Terima SK with status Diterima.',
            ],

            // Case 6: older application reached Orientasi then withdrew (Mundur).
            [
                'nik' => '3271056001990006',
                'nama' => 'Bagus Setiawan (Dummy Riwayat Mundur)',
                'aplikasi' => [
                    ['loker' => 46, 'tanggal' => '2026-08-06', 'tahap' => TahapRekrutmen::ORIENTASI, 'status' => StatusPelamar::MUNDUR, 'orientasi' => true],
                    ['loker' => 20, 'tanggal' => '2026-09-02', 'tahap' => TahapRekrutmen::SELEKSI_BERKAS, 'status' => StatusPelamar::SCREENING],
                ],
                'expect' => 'Newest (Loker 20) is red. Older reached Orientasi with status Mundur.',
            ],

            // Case 7: only one application ever - no riwayat, no red flag, no tooltip.
            [
                'nik' => '3271056001990007',
                'nama' => 'Nadia Kartika (Dummy Tanpa Riwayat)',
                'aplikasi' => [
                    ['loker' => 39, 'tanggal' => '2026-09-01', 'tahap' => TahapRekrutmen::SELEKSI_BERKAS, 'status' => StatusPelamar::SCREENING],
                ],
                'expect' => 'Only application - not red, no tooltip at all.',
            ],

            // Case 8: older application is a fully closed-out Migrasi Data record
            // (the furthest tahap past Orientasi) -> still counts as history.
            [
                'nik' => '3271056001990008',
                'nama' => 'Yoga Pranata (Dummy Migrasi Data Riwayat)',
                'aplikasi' => [
                    ['loker' => 64, 'tanggal' => '2026-08-02', 'tahap' => TahapRekrutmen::MIGRASI_DATA, 'status' => StatusPelamar::MIGRATED],
                    ['loker' => 35, 'tanggal' => '2026-09-03', 'tahap' => TahapRekrutmen::SELEKSI_BERKAS, 'status' => StatusPelamar::SCREENING],
                ],
                'expect' => 'Newest (Loker 35) is red. Older reached Migrasi Data (furthest possible tahap).',
            ],

            // Case 9: four applications - only the OLDEST reached Orientasi, the
            // two in between didn't. Newest is still red (any older one counts),
            // and its tooltip shows the latest OTHER application (the Wawancara
            // one), not the oldest Orientasi one.
            [
                'nik' => '3271056001990009',
                'nama' => 'Intan Permatasari (Dummy Riwayat Berlapis)',
                'aplikasi' => [
                    ['loker' => 24, 'tanggal' => '2026-07-28', 'tahap' => TahapRekrutmen::ORIENTASI, 'status' => StatusPelamar::TIDAK_LOLOS, 'orientasi' => true],
                    ['loker' => 26, 'tanggal' => '2026-08-10', 'tahap' => TahapRekrutmen::TES_TULIS, 'status' => StatusPelamar::ONGOING],
                    ['loker' => 28, 'tanggal' => '2026-08-28', 'tahap' => TahapRekrutmen::WAWANCARA, 'status' => StatusPelamar::ONGOING],
                    ['loker' => 47, 'tanggal' => '2026-09-06', 'tahap' => TahapRekrutmen::SELEKSI_BERKAS, 'status' => StatusPelamar::SCREENING],
                ],
                'expect' => 'Newest (Loker 47) is red (oldest reached Orientasi). Tooltip shows Loker 28 (latest of the three older ones), not Loker 24.',
            ],

            // Case 10: two applications filed on the SAME date - tests the
            // id_pelamar tie-break for "which one is the newest". The one
            // created second (higher id_pelamar) is treated as newest.
            [
                'nik' => '3271056001990010',
                'nama' => 'Wahyu Kurniawan (Dummy Tahap Sama Tanggal)',
                'aplikasi' => [
                    ['loker' => 37, 'tanggal' => '2026-08-20', 'tahap' => TahapRekrutmen::ORIENTASI, 'status' => StatusPelamar::ONGOING, 'orientasi' => true],
                    ['loker' => 95, 'tanggal' => '2026-08-20', 'tahap' => TahapRekrutmen::SELEKSI_BERKAS, 'status' => StatusPelamar::SCREENING],
                ],
                'expect' => 'Same tanggal_apply on both - Loker 95 (created second) wins the tie-break as newest and is red; Loker 37 is not red.',
            ],

            // Case 11: BOTH the older and the newest application are themselves
            // at Orientasi. Newest is still red - triggered by the older one,
            // independent of the newest's own tahap.
            [
                'nik' => '3271056001990011',
                'nama' => 'Doni Setiabudi (Dummy Orientasi Ganda)',
                'aplikasi' => [
                    ['loker' => 69, 'tanggal' => '2026-08-04', 'tahap' => TahapRekrutmen::ORIENTASI, 'status' => StatusPelamar::DICADANGKAN, 'orientasi' => true],
                    ['loker' => 71, 'tanggal' => '2026-09-05', 'tahap' => TahapRekrutmen::ORIENTASI, 'status' => StatusPelamar::ONGOING, 'orientasi' => true],
                ],
                'expect' => 'Newest (Loker 71) is red because of the OLDER Orientasi record, even though the newest is also currently at Orientasi.',
            ],

            // Case 12: the Orientasi-stage history sits on a loker that is now
            // CLOSED - proves the flag doesn't depend on the old loker still
            // being open.
            [
                'nik' => '3271056001990012',
                'nama' => 'Reza Firmansyah (Dummy Riwayat Loker Tertutup)',
                'aplikasi' => [
                    ['loker' => 18, 'tanggal' => '2026-08-05', 'tahap' => TahapRekrutmen::ORIENTASI, 'status' => StatusPelamar::LOLOS, 'orientasi' => true],
                    ['loker' => 82, 'tanggal' => '2026-09-03', 'tahap' => TahapRekrutmen::SELEKSI_BERKAS, 'status' => StatusPelamar::SCREENING],
                ],
                'expect' => 'Newest (Loker 82) is red. Older Orientasi record (status Lolos) is on Loker 18, which is now closed.',
            ],

            // Case 13: older application reached Tugas Sementara (one stage past
            // Orientasi) with a positive Lolos status.
            [
                'nik' => '3271056001990013',
                'nama' => 'Yusuf Alamsyah (Dummy Riwayat Tugas Sementara)',
                'aplikasi' => [
                    ['loker' => 70, 'tanggal' => '2026-08-07', 'tahap' => TahapRekrutmen::TUGAS_SEMENTARA, 'status' => StatusPelamar::LOLOS],
                    ['loker' => 74, 'tanggal' => '2026-09-02', 'tahap' => TahapRekrutmen::SELEKSI_BERKAS, 'status' => StatusPelamar::SCREENING],
                ],
                'expect' => 'Newest (Loker 74) is red. Older reached Tugas Sementara with status Lolos.',
            ],

            // Case 14: older application reached Terima SK but was later marked
            // Dicadangkan (an unusual combination) - still counts, regardless
            // of how mismatched the tahap/status pairing looks.
            [
                'nik' => '3271056001990014',
                'nama' => 'Citra Ananda (Dummy Riwayat Kombinasi Tidak Umum)',
                'aplikasi' => [
                    ['loker' => 99, 'tanggal' => '2026-08-09', 'tahap' => TahapRekrutmen::TERIMA_SK, 'status' => StatusPelamar::DICADANGKAN],
                    ['loker' => 78, 'tanggal' => '2026-09-04', 'tahap' => TahapRekrutmen::SELEKSI_BERKAS, 'status' => StatusPelamar::SCREENING],
                ],
                'expect' => 'Newest (Loker 78) is red regardless of the unusual tahap/status pairing on the older application.',
            ],
        ];

        $base = [
            'tanggal_lahir' => Carbon::parse('1999-01-15'),
            'jenis_kelamin' => 'P',
            'no_hp' => '081234500001',
            'alamat' => 'Jl. Contoh Dummy No. 1, Jakarta',
            'pernah_rekrutmen_sebelumnya' => 'Tidak',
            'pernah_bekerja_di_al_azhar' => 'Tidak',
            'id_pendidikan_terakhir' => 7, // S1
            'institusi_s1' => 'Universitas Negeri Jakarta',
            'program_studi_s1' => 'Pendidikan Matematika',
            'kategori_perguruan_tinggi_s1' => 'Perguruan Tinggi Negeri',
            'akreditasi' => 'A',
            'tahun_lulus' => 2021,
            'ipk_s1' => '3.55',
            'cv_upload' => self::PLACEHOLDER_PDF,
            'ijazah_upload' => self::PLACEHOLDER_PDF,
            'ktp_upload' => self::PLACEHOLDER_IMG,
            'pas_foto_upload' => self::PLACEHOLDER_IMG,
            'transkrip_nilai_s1_upload' => self::PLACEHOLDER_PDF,
            'surat_lamaran_upload' => self::PLACEHOLDER_PDF,
        ];

        foreach ($cases as $case) {
            Pelamar::where('nik', $case['nik'])->get()->each(function (Pelamar $p) {
                $p->orientasi?->delete();
                $p->delete();
            });

            foreach ($case['aplikasi'] as $aplikasi) {
                $pelamar = Pelamar::create($base + [
                    'nik' => $case['nik'],
                    'nama' => $case['nama'],
                    'email' => Str::slug($case['nama']).'@example.test',
                    'id_loker' => $aplikasi['loker'],
                    'id_tahap_rekrutmen' => $aplikasi['tahap'],
                    'id_status_pelamar' => $aplikasi['status'],
                    'tanggal_apply' => Carbon::parse($aplikasi['tanggal']),
                ]);

                if ($aplikasi['orientasi'] ?? false) {
                    Orientasi::create([
                        'id_pelamar' => $pelamar->id_pelamar,
                        'tanggal_mulai' => Carbon::parse($aplikasi['tanggal'])->addDays(8),
                        'tanggal_selesai' => Carbon::parse($aplikasi['tanggal'])->addDays(15),
                        'catatan' => 'Data dummy untuk uji fitur riwayat Orientasi.',
                    ]);
                }
            }

            $this->command?->line("Seeded: {$case['nama']} - {$case['expect']}");
        }
    }
}
