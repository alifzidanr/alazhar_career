<?php

namespace Database\Seeders;

use App\Models\Jenjang;
use App\Models\Kriteria;
use App\Models\Loker;
use App\Models\Pelamar;
use App\Models\RiwayatTahapPelamar;
use App\Models\StatusPelamar;
use App\Models\TahapRekrutmen;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Bulk-generates 100 active (dibuka) loker, each with a handful of kriteria
 * and 3-5 pelamar, on top of whatever is already seeded (jenjang, wilayah,
 * pendidikan_terakhir). Unlike DemoRekrutmenSeeder this does not wipe
 * existing data — it only adds new rows — and is meant for load/volume
 * testing of the admin listing, filters, and pagination.
 */
class MockScaleSeeder extends Seeder
{
    private const PLACEHOLDER_IMG = 'placeholders/blank.png';

    private const PLACEHOLDER_PDF = 'placeholders/blank.pdf';

    private const LOKER_COUNT = 100;

    /** id_pendidikan_terakhir for each label, as seeded by the pendidikan_terakhir migration. */
    private const PENDIDIKAN_ID = ['SMP' => 2, 'SMA' => 3, 'D3' => 6, 'S1' => 7, 'S2' => 8, 'S3' => 9];

    public function run(): void
    {
        $today = Carbon::parse('2026-08-19');

        $jenjangId = Jenjang::pluck('id_jenjang', 'nama_jenjang')->toArray();
        $wilayahList = Wilayah::pluck('nama_wilayah')->toArray();

        $posisiPool = $this->posisiPool();

        for ($i = 1; $i <= self::LOKER_COUNT; $i++) {
            $jenjangNama = array_rand($posisiPool);
            $posisi = $posisiPool[$jenjangNama];
            $judul = $posisi['judul'][array_rand($posisi['judul'])];
            $wilayah = $wilayahList[array_rand($wilayahList)];
            $pendidikanLabel = $posisi['pendidikan'][array_rand($posisi['pendidikan'])];

            $loker = Loker::create([
                'judul_loker' => $judul,
                'deskripsi_loker' => $posisi['deskripsi'],
                'wilayah' => $wilayah,
                'id_pendidikan_terakhir' => self::PENDIDIKAN_ID[$pendidikanLabel],
                'id_jenjang' => $jenjangId[$jenjangNama],
                'status_loker' => 'dibuka',
                'start_time' => $today->copy()->subDays(random_int(1, 30)),
                'end_time' => $today->copy()->addDays(random_int(5, 60)),
            ]);

            foreach ($this->kriteriaUntuk($posisi, $pendidikanLabel) as [$text, $bobot]) {
                $loker->kriteria()->create([
                    'id_kriteria' => Kriteria::firstOrCreate(['teks_kriteria' => $text])->id_kriteria,
                    'bobot' => $bobot,
                ]);
            }

            $this->buatPelamar($loker, random_int(3, 5), $posisi, $pendidikanLabel);
        }
    }

    /** @return array<string,array{judul:string[],pendidikan:string[],deskripsi:string,prodi:string[],tema:string}> */
    private function posisiPool(): array
    {
        return [
            'Guru TK' => [
                'judul' => ['Guru Kelas TK', 'Guru Pendamping TK', 'Guru Seni & Musik TK'],
                'pendidikan' => ['S1'],
                'prodi' => ['Pendidikan Guru Pendidikan Anak Usia Dini', 'Psikologi Pendidikan'],
                'deskripsi' => 'Mengasuh dan mendidik anak usia dini melalui kegiatan bermain sambil belajar.',
                'tema' => 'guru',
            ],
            'Guru SD' => [
                'judul' => ['Guru PAI (SD)', 'Guru Matematika (SD)', 'Guru Bahasa Indonesia (SD)', 'Guru IPA (SD)', 'Guru PJOK (SD)', 'Guru Damping (SD)', 'Guru Seni Budaya (SD)'],
                'pendidikan' => ['S1'],
                'prodi' => ['Pendidikan Guru Sekolah Dasar', 'Pendidikan Agama Islam', 'Pendidikan Jasmani Kesehatan dan Rekreasi'],
                'deskripsi' => 'Mengajar dan membina siswa SD sesuai bidang studi serta nilai-nilai keislaman.',
                'tema' => 'guru',
            ],
            'Guru SMP' => [
                'judul' => ['Guru Bahasa Indonesia (SMP)', 'Guru IPS (SMP)', 'Guru Matematika (SMP)', 'Guru IPA (SMP)', 'Guru Bahasa Inggris (SMP)', 'Guru PAI (SMP)', 'Guru TIK (SMP)'],
                'pendidikan' => ['S1'],
                'prodi' => ['Pendidikan Bahasa Indonesia', 'Pendidikan IPS', 'Pendidikan Matematika', 'Pendidikan Bahasa Inggris'],
                'deskripsi' => 'Mengajar siswa SMP dan mengembangkan kompetensi akademik sesuai bidang studi.',
                'tema' => 'guru',
            ],
            'Guru SMA' => [
                'judul' => ['Guru Matematika (SMA)', 'Guru Bahasa Arab (SMA)', 'Guru Fisika (SMA)', 'Guru Kimia (SMA)', 'Guru Biologi (SMA)', 'Guru Ekonomi (SMA)', 'Guru Sejarah (SMA)', 'Guru Seni Rupa (SMA)', 'Guru BK (SMA)'],
                'pendidikan' => ['S1', 'S1', 'S1', 'S2'],
                'prodi' => ['Pendidikan Matematika', 'Pendidikan Bahasa Arab', 'Pendidikan Fisika', 'Pendidikan Kimia', 'Pendidikan Biologi', 'Bimbingan Konseling'],
                'deskripsi' => 'Mengajar siswa SMA, membina prestasi akademik, dan mempersiapkan siswa untuk jenjang lanjut.',
                'tema' => 'guru',
            ],
            'Staf Humas' => [
                'judul' => ['Staf Humas', 'Staf Media Sosial', 'Staf Dokumentasi & Publikasi'],
                'pendidikan' => ['S1'],
                'prodi' => ['Ilmu Komunikasi', 'Hubungan Masyarakat', 'Manajemen Pemasaran'],
                'deskripsi' => 'Mengelola komunikasi publik, media sosial, dan dokumentasi kegiatan yayasan.',
                'tema' => 'admin',
            ],
            'Tata Usaha' => [
                'judul' => ['TU Keuangan', 'TU PSB', 'TU Kepegawaian', 'TU Umum', 'TU IT', 'TU Sarana Prasarana'],
                'pendidikan' => ['S1', 'S1', 'D3'],
                'prodi' => ['Manajemen', 'Ilmu Administrasi Negara', 'Ilmu Ekonomi', 'Akuntansi'],
                'deskripsi' => 'Mengelola administrasi umum unit, korespondensi, dan kearsipan dokumen.',
                'tema' => 'admin',
            ],
            'Teknisi' => [
                'judul' => ['Teknisi IT', 'Teknisi Jaringan', 'Teknisi Elektronik', 'Teknisi AC & Kelistrikan'],
                'pendidikan' => ['D3', 'S1'],
                'prodi' => ['Teknik Informatika', 'Teknik Elektro', 'Sistem Informasi'],
                'deskripsi' => 'Memelihara dan memperbaiki infrastruktur IT serta peralatan elektronik unit.',
                'tema' => 'admin',
            ],
            'Laboran' => [
                'judul' => ['Laboran IPA', 'Laboran Kimia', 'Laboran Komputer'],
                'pendidikan' => ['D3'],
                'prodi' => ['Kimia', 'Biologi', 'Fisika'],
                'deskripsi' => 'Mengelola dan menyiapkan peralatan laboratorium untuk kegiatan praktikum siswa.',
                'tema' => 'admin',
            ],
            'Driver' => [
                'judul' => ['Driver'],
                'pendidikan' => ['SMA'],
                'prodi' => [],
                'deskripsi' => 'Mengemudikan kendaraan operasional yayasan serta menjaga kebersihan dan kelaikan kendaraan.',
                'tema' => 'driver',
            ],
            'Satpam' => [
                'judul' => ['Satpam'],
                'pendidikan' => ['SMA'],
                'prodi' => [],
                'deskripsi' => 'Menjaga keamanan dan ketertiban lingkungan unit selama jam operasional.',
                'tema' => 'satpam',
            ],
            'Janitor' => [
                'judul' => ['Janitor'],
                'pendidikan' => ['SMP', 'SMA'],
                'prodi' => [],
                'deskripsi' => 'Menjaga kebersihan dan kerapian lingkungan unit, termasuk ruang kelas dan area umum.',
                'tema' => 'janitor',
            ],
        ];
    }

    /** @return array<int,array{0:string,1:string}> [teks_kriteria, bobot] pairs for one loker. */
    private function kriteriaUntuk(array $posisi, string $pendidikanLabel): array
    {
        $wajib = fn (string $t) => [$t, 'wajib'];
        $diutamakan = fn (string $t) => [$t, 'diutamakan'];

        $list = [$wajib('Usia Maksimal 35 Tahun')];

        if (in_array($posisi['tema'], ['guru', 'admin'], true)) {
            $prodi = $posisi['prodi'] ? $posisi['prodi'][array_rand($posisi['prodi'])] : 'Linier';
            $list[] = $wajib("{$pendidikanLabel} {$prodi}/Linier");
            $list[] = $wajib('IPK Minimal 3.00 dari 4.00');
            $list[] = $wajib('Akreditasi Minimal B (Baik Sekali)');
            $list[] = $wajib('Terampil Bahasa Inggris (Lisan & Tulisan)');
            $list[] = $diutamakan($posisi['tema'] === 'guru' ? 'Memiliki Pengalaman Mengajar' : 'Memiliki Pengalaman Kerja');
        }

        if ($posisi['tema'] === 'driver') {
            $list[] = $wajib('Pendidikan Minimal SMA/Sederajat');
            $list[] = $wajib('Memiliki SIM A');
            $list[] = $wajib('Tidak Merokok & Tidak Bertato');
            $list[] = $diutamakan('Memiliki Pengalaman Kerja');
        }

        if ($posisi['tema'] === 'satpam') {
            $list[] = $wajib('Pendidikan Minimal SMA');
            $list[] = $wajib('Memiliki Ijazah Gada Pratama');
            $list[] = $wajib('Tinggi Badan Minimal 167cm');
            $list[] = $diutamakan('Memiliki Pengalaman Kerja');
        }

        if ($posisi['tema'] === 'janitor') {
            $list[] = $wajib('Pendidikan Minimal SMP/SLTP/Sederajat');
            $list[] = $diutamakan('Memiliki Pengalaman Kerja');
        }

        $list[] = $wajib('Berpenampilan Islami');
        $list[] = $wajib('Sehat Jasmani & Rohani');
        $list[] = $wajib('Lulus Tes');

        return $list;
    }

    private function buatPelamar(Loker $loker, int $jumlah, array $posisi, string $pendidikanLabel): void
    {
        $namaDepanL = ['Ahmad', 'Muhammad', 'Budi', 'Andi', 'Dedi', 'Eko', 'Fajar', 'Hendra', 'Irfan', 'Joko', 'Kurniawan', 'Lukman', 'Rizky', 'Sigit', 'Taufik', 'Wahyu', 'Yusuf', 'Bayu', 'Doni', 'Rian'];
        $namaDepanP = ['Siti', 'Dewi', 'Rina', 'Fitri', 'Nurul', 'Indah', 'Ratna', 'Wulan', 'Yuni', 'Astuti', 'Puspita', 'Maya', 'Lestari', 'Anita', 'Diah', 'Sari', 'Novita', 'Rahma', 'Intan', 'Ayu'];
        $namaBelakang = ['Pratama', 'Saputra', 'Wijaya', 'Kusuma', 'Santoso', 'Hidayat', 'Nugroho', 'Setiawan', 'Firmansyah', 'Rahayu', 'Wibowo', 'Susanto', 'Handayani', 'Permata', 'Gunawan', 'Suryadi', 'Utami', 'Maulana', 'Yulianti', 'Ramadhan'];

        $institusiByLevel = [
            'S1' => ['Universitas Indonesia', 'Universitas Gadjah Mada', 'Universitas Padjadjaran', 'Universitas Airlangga', 'Universitas Negeri Jakarta', 'Universitas Muhammadiyah Jakarta', 'UIN Syarif Hidayatullah', 'Universitas Brawijaya', 'Universitas Diponegoro'],
            'S2' => ['Universitas Indonesia (Pascasarjana)', 'Universitas Gadjah Mada (Pascasarjana)', 'Universitas Negeri Jakarta (Pascasarjana)', 'UIN Syarif Hidayatullah (Pascasarjana)'],
            'S3' => ['Universitas Indonesia (Doktoral)', 'Universitas Gadjah Mada (Doktoral)', 'Universitas Negeri Jakarta (Doktoral)'],
            'D3' => ['Politeknik Negeri Jakarta', 'Politeknik Negeri Malang', 'Akademi Kimia Analisis', 'Politeknik Negeri Sriwijaya'],
            'SMA' => ['SMA Negeri 1', 'SMA Negeri 3', 'SMK Negeri 2', 'SMA Muhammadiyah 1', 'MA Negeri 2'],
            'SMP' => ['SMP Negeri 4', 'SMP Negeri 7', 'MTs Negeri 1'],
        ];

        $kategoriPtOptions = ['Perguruan Tinggi Negeri', 'Perguruan Tinggi Swasta', 'Lain-lain'];

        // A candidate's highest level implies they completed every level below
        // it too, so their record carries a kategori/IPK for each rung climbed.
        $tangga = ['D3' => ['D3'], 'S1' => ['S1'], 'S2' => ['S1', 'S2'], 'S3' => ['S1', 'S2', 'S3']];
        $levelsToFill = $tangga[$pendidikanLabel] ?? [];

        $programByJenjang = $posisi['prodi'];

        for ($i = 0; $i < $jumlah; $i++) {
            $isPria = random_int(0, 1) === 1;
            $nama = ($isPria ? $namaDepanL[array_rand($namaDepanL)] : $namaDepanP[array_rand($namaDepanP)]).' '.$namaBelakang[array_rand($namaBelakang)];

            $institusi = $institusiByLevel[$pendidikanLabel][array_rand($institusiByLevel[$pendidikanLabel])];
            $akreditasi = ['A', 'B', 'B', 'C'][array_rand(['A', 'B', 'B', 'C'])];
            $tahunLulus = random_int(2015, 2024);

            $ipkByLevel = [];
            $kategoriByLevel = [];
            $programStudiByLevel = [];
            $institusiByLevelFilled = [];
            foreach ($levelsToFill as $level) {
                $ipkByLevel[$level] = number_format(random_int(300, 390) / 100, 2);
                $kategoriByLevel[$level] = $kategoriPtOptions[array_rand($kategoriPtOptions)];
                $programStudiByLevel[$level] = $programByJenjang ? $programByJenjang[array_rand($programByJenjang)] : null;
                $institusiByLevelFilled[$level] = $institusiByLevel[$level][array_rand($institusiByLevel[$level])];
            }

            $tanggalLahir = Carbon::parse('2026-08-19')->subYears(random_int(23, 34))->subDays(random_int(0, 364));
            $slug = Str::slug($nama).random_int(10, 999);

            [$idTahap, $idStatus] = $this->tahapAcak();

            $pelamar = Pelamar::create([
                'id_loker' => $loker->id_loker,
                'nama' => $nama,
                'nik' => (string) random_int(1000000000000000, 9999999999999999),
                'tanggal_lahir' => $tanggalLahir,
                'jenis_kelamin' => $isPria ? 'L' : 'P',
                'no_hp' => '08'.random_int(1000000000, 9999999999),
                'email' => $slug.'@gmail.com',
                'alamat' => 'Jl. Contoh No. '.random_int(1, 99).', '.$loker->wilayah,
                'pernah_rekrutmen_sebelumnya' => 'Tidak',
                'pernah_bekerja_di_al_azhar' => 'Tidak',
                'id_pendidikan_terakhir' => $loker->id_pendidikan_terakhir,
                'institusi' => in_array($pendidikanLabel, ['S1', 'S2', 'S3'], true) ? null : $institusi,
                'institusi_s1' => $institusiByLevelFilled['S1'] ?? null,
                'institusi_s2' => $institusiByLevelFilled['S2'] ?? null,
                'institusi_s3' => $institusiByLevelFilled['S3'] ?? null,
                'program_studi' => $programStudiByLevel['D3'] ?? null,
                'program_studi_s1' => $programStudiByLevel['S1'] ?? null,
                'program_studi_s2' => $programStudiByLevel['S2'] ?? null,
                'program_studi_s3' => $programStudiByLevel['S3'] ?? null,
                'kategori_perguruan_tinggi_d3' => $kategoriByLevel['D3'] ?? null,
                'kategori_perguruan_tinggi_s1' => $kategoriByLevel['S1'] ?? null,
                'kategori_perguruan_tinggi_s2' => $kategoriByLevel['S2'] ?? null,
                'kategori_perguruan_tinggi_s3' => $kategoriByLevel['S3'] ?? null,
                'akreditasi' => $programStudiByLevel ? $akreditasi : null,
                'tahun_lulus' => $tahunLulus,
                'ipk_d3' => $ipkByLevel['D3'] ?? null,
                'ipk_s1' => $ipkByLevel['S1'] ?? null,
                'ipk_s2' => $ipkByLevel['S2'] ?? null,
                'ipk_s3' => $ipkByLevel['S3'] ?? null,
                'cv_upload' => self::PLACEHOLDER_PDF,
                'ijazah_upload' => self::PLACEHOLDER_PDF,
                'ktp_upload' => self::PLACEHOLDER_IMG,
                'transkrip_nilai_upload' => in_array($pendidikanLabel, ['S1', 'S2', 'S3'], true) ? null : self::PLACEHOLDER_PDF,
                'transkrip_nilai_s1_upload' => isset($ipkByLevel['S1']) ? self::PLACEHOLDER_PDF : null,
                'transkrip_nilai_s2_upload' => isset($ipkByLevel['S2']) ? self::PLACEHOLDER_PDF : null,
                'transkrip_nilai_s3_upload' => isset($ipkByLevel['S3']) ? self::PLACEHOLDER_PDF : null,
                'pas_foto_upload' => self::PLACEHOLDER_IMG,
                'surat_lamaran_upload' => self::PLACEHOLDER_PDF,
                'sim_upload' => $posisi['tema'] === 'driver' ? self::PLACEHOLDER_IMG : null,
                'sertifikat_gada_pratama_upload' => $posisi['tema'] === 'satpam' ? self::PLACEHOLDER_PDF : null,
                'bersedia_ditempatkan' => true,
                'id_status_pelamar' => $idStatus,
                'id_tahap_rekrutmen' => $idTahap,
                'tanggal_apply' => $loker->start_time->copy()->addDays(random_int(0, 4)),
            ]);

            RiwayatTahapPelamar::create([
                'id_pelamar' => $pelamar->id_pelamar,
                'id_tahap_rekrutmen' => TahapRekrutmen::SELEKSI_BERKAS,
                'id_status_pelamar' => StatusPelamar::SCREENING,
                'catatan' => 'Lamaran diajukan oleh pelamar.',
                'created_by' => null,
            ]);
        }
    }

    /** Weighted-random pipeline position for a dummy pelamar. @return array{int,int} */
    private function tahapAcak(): array
    {
        $roll = random_int(1, 100);

        return match (true) {
            $roll <= 55 => [TahapRekrutmen::SELEKSI_BERKAS, StatusPelamar::SCREENING],
            $roll <= 75 => [TahapRekrutmen::TES_TULIS, StatusPelamar::ONGOING],
            $roll <= 90 => [TahapRekrutmen::WAWANCARA, StatusPelamar::ONGOING],
            $roll <= 95 => [TahapRekrutmen::SELEKSI_BERKAS, StatusPelamar::TIDAK_LOLOS],
            default => [TahapRekrutmen::TERIMA_SK, StatusPelamar::DITERIMA],
        };
    }
}
