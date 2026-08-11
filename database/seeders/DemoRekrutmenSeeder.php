<?php

namespace Database\Seeders;

use App\Models\Jenjang;
use App\Models\Kriteria;
use App\Models\Loker;
use App\Models\Lokasi;
use App\Models\LogNotifikasi;
use App\Models\Orientasi;
use App\Models\Pelamar;
use App\Models\RiwayatTahapPelamar;
use App\Models\StatusPelamar;
use App\Models\TahapRekrutmen;
use App\Models\TesTulis;
use App\Models\TugasSementara;
use App\Models\UnitKerja;
use App\Models\Wawancara;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Replaces all loker/pelamar data with a realistic demo set modeled on actual
 * YPI Al Azhar recruitment posters (positions, locations, and persyaratan).
 * Every loker is left "dibuka" and dated relative to today so the public site
 * has a live, exercisable mix of new/normal/closing-soon listings.
 */
class DemoRekrutmenSeeder extends Seeder
{
    private const PLACEHOLDER_IMG = 'placeholders/blank.png';

    private const PLACEHOLDER_PDF = 'placeholders/blank.pdf';

    public function run(): void
    {
        $this->wipeExistingData();

        $jenjangId = $this->ensureJenjang();
        $unitId = $this->ensureUnitKerja();
        $this->ensureLokasi();
        $kriteriaId = fn (string $text) => $this->kriteria($text);

        $today = Carbon::parse('2026-08-11');

        $sharedJadetabekPersyaratan = [
            ['35 Tahun', 'wajib'],
            ['Pendidikan Minimal S1 Linier', 'wajib'],
            ['IPK Minimal 3.00 dari 4.00', 'wajib'],
            ['Akreditasi Minimal B (Baik Sekali)', 'wajib'],
            ['Terampil Bahasa Inggris (Lisan & Tulisan)', 'wajib'],
            ['Memiliki Pengalaman Kerja', 'diutamakan'],
            ['Berpenampilan Islami', 'wajib'],
            ['Sehat Jasmani & Rohani', 'wajib'],
            ['Lulus Tes', 'wajib'],
        ];

        $lokers = [
            [
                'judul_loker' => 'Driver',
                'lokasi' => 'Pangkalpinang',
                'jenjang' => 'Driver',
                'pendidikan' => 3, // SMA
                'unit' => null,
                'deskripsi_loker' => 'Mengemudikan kendaraan operasional yayasan, menjaga kebersihan dan kelaikan kendaraan, serta siap bertugas sesuai jadwal.',
                'end_offset' => 5, 'start_offset' => -20,
                'persyaratan' => [
                    ['35 Tahun', 'wajib'],
                    ['Pendidikan Minimal SMA/Sederajat', 'wajib'],
                    ['Memiliki SIM A', 'wajib'],
                    ['Memiliki Pengalaman Kerja', 'diutamakan'],
                    ['Berpenampilan Islami', 'wajib'],
                    ['Sehat Jasmani & Rohani', 'wajib'],
                    ['Tidak Merokok & Tidak Bertato', 'wajib'],
                    ['Lulus Tes', 'wajib'],
                ],
                'pelamar' => 4,
            ],
            [
                'judul_loker' => 'Staf Humas',
                'lokasi' => 'Jadetabek',
                'jenjang' => 'Staf Humas',
                'pendidikan' => 7, // S1
                'unit' => null,
                'deskripsi_loker' => 'Mengelola komunikasi publik, media sosial, dan dokumentasi kegiatan yayasan.',
                'end_offset' => 30, 'start_offset' => -10,
                'persyaratan' => [
                    ['35 Tahun', 'wajib'],
                    ['S1 Ilmu Komunikasi/Hubungan Masyarakat/Manajemen Pemasaran/Jurnalistik/Penyiaran/Linier', 'wajib'],
                    ['IPK Minimal 3.00 dari 4.00', 'wajib'],
                    ['Akreditasi Minimal B (Baik Sekali)', 'wajib'],
                    ['Memiliki Keterampilan Edit Video, Membuat Konten, Menguasai Media Sosial', 'wajib'],
                    ['Terampil Bahasa Inggris (Lisan & Tulisan)', 'wajib'],
                    ['Berpenampilan Islami', 'wajib'],
                    ['Sehat Jasmani & Rohani', 'wajib'],
                ],
                'pelamar' => 5,
            ],
            [
                'judul_loker' => 'Tata Usaha (TU IT)',
                'lokasi' => 'Bengkulu',
                'jenjang' => 'Tata Usaha',
                'pendidikan' => 7,
                'unit' => null,
                'deskripsi_loker' => 'Mengelola infrastruktur IT, jaringan, dan dukungan teknis operasional unit.',
                'end_offset' => 45, 'start_offset' => -5,
                'persyaratan' => [
                    ['35 Tahun', 'wajib'],
                    ['S1 Teknik Informatika/Sistem Informasi/Ilmu Komputer/Linier', 'wajib'],
                    ['IPK Minimal 3.00 dari 4.00', 'wajib'],
                    ['Akreditasi Minimal B (Baik Sekali)', 'wajib'],
                    ['Terampil Bahasa Inggris (Lisan & Tulisan)', 'wajib'],
                    ['Memiliki Kemampuan Komunikasi yang Baik', 'wajib'],
                    ['Memiliki Pengalaman Kerja', 'diutamakan'],
                    ['Berpenampilan Islami', 'wajib'],
                    ['Sehat Jasmani & Rohani', 'wajib'],
                ],
                'pelamar' => 3,
            ],
            [
                'judul_loker' => 'TU PSB',
                'lokasi' => 'Sidoarjo',
                'jenjang' => 'Tata Usaha',
                'pendidikan' => 7,
                'unit' => null,
                'deskripsi_loker' => 'Mengelola administrasi Penerimaan Siswa Baru (PSB) dan layanan informasi pendaftaran.',
                'end_offset' => 21, 'start_offset' => -15,
                'persyaratan' => [
                    ['Pria', 'wajib'],
                    ['35 Tahun', 'wajib'],
                    ['S1 Ilmu Perpustakaan/Teknologi Pendidikan/Linier', 'wajib'],
                    ['IPK Minimal 3.00 dari 4.00', 'wajib'],
                    ['Akreditasi Minimal B (Baik Sekali)', 'wajib'],
                    ['Terampil Bahasa Inggris (Lisan & Tulisan)', 'wajib'],
                    ['Memiliki Pengalaman Mengajar', 'diutamakan'],
                    ['Berpenampilan Islami', 'wajib'],
                    ['Sehat Jasmani & Rohani', 'wajib'],
                    ['Lulus Tes', 'wajib'],
                ],
                'pelamar' => 3,
            ],
            [
                'judul_loker' => 'Guru PAI (SD)',
                'lokasi' => 'Malang',
                'jenjang' => 'Guru SD',
                'pendidikan' => 7,
                'unit' => null,
                'deskripsi_loker' => 'Mengajar Pendidikan Agama Islam untuk siswa SD, membina akhlak dan praktik ibadah harian.',
                'end_offset' => 25, 'start_offset' => -30,
                'persyaratan' => [
                    ['35 Tahun', 'wajib'],
                    ['S1 Pendidikan Agama Islam/Ilmu Agama Islam/Linier', 'wajib'],
                    ['Akreditasi Minimal B (Baik Sekali)', 'wajib'],
                    ['IPK Minimal 3.00 dari 4.00', 'wajib'],
                    ['Terampil Bahasa Inggris (Lisan & Tulisan)', 'wajib'],
                    ['Memiliki Pengalaman Mengajar', 'diutamakan'],
                    ['Berpenampilan Islami', 'wajib'],
                    ['Sehat Jasmani & Rohani', 'wajib'],
                    ['Lulus Tes', 'wajib'],
                ],
                'pelamar' => 4,
            ],
            [
                'judul_loker' => 'Guru Damping (SD)',
                'lokasi' => 'Jadetabek',
                'jenjang' => 'Guru SD',
                'pendidikan' => 7,
                'unit' => null,
                'deskripsi_loker' => 'Mendampingi siswa berkebutuhan khusus dalam kegiatan belajar mengajar sehari-hari.',
                'end_offset' => 40, 'start_offset' => -3,
                'persyaratan' => $sharedJadetabekPersyaratan,
                'pelamar' => 3,
            ],
            [
                'judul_loker' => 'Guru Bahasa Indonesia (SMP)',
                'lokasi' => 'Jadetabek',
                'jenjang' => 'Guru SMP',
                'pendidikan' => 7,
                'unit' => null,
                'deskripsi_loker' => 'Mengajar Bahasa Indonesia, mengembangkan literasi dan minat baca siswa SMP.',
                'end_offset' => 40, 'start_offset' => -3,
                'persyaratan' => $sharedJadetabekPersyaratan,
                'pelamar' => 3,
            ],
            [
                'judul_loker' => 'Guru IPS (SMP)',
                'lokasi' => 'Jadetabek',
                'jenjang' => 'Guru SMP',
                'pendidikan' => 7,
                'unit' => null,
                'deskripsi_loker' => 'Mengajar Ilmu Pengetahuan Sosial untuk siswa SMP.',
                'end_offset' => 40, 'start_offset' => -3,
                'persyaratan' => $sharedJadetabekPersyaratan,
                'pelamar' => 2,
            ],
            [
                'judul_loker' => 'Guru Matematika (SMP)',
                'lokasi' => 'Jadetabek',
                'jenjang' => 'Guru SMP',
                'pendidikan' => 7,
                'unit' => null,
                'deskripsi_loker' => 'Mengajar Matematika kelas VII-IX dan membina kemampuan numerasi siswa.',
                'end_offset' => 40, 'start_offset' => -3,
                'persyaratan' => $sharedJadetabekPersyaratan,
                'pelamar' => 4,
            ],
            [
                'judul_loker' => 'Guru Matematika (SMA)',
                'lokasi' => 'Jadetabek',
                'jenjang' => 'Guru SMA',
                'pendidikan' => 7,
                'unit' => null,
                'deskripsi_loker' => 'Mengajar Matematika kelas X-XII dan membina tim olimpiade matematika.',
                'end_offset' => 40, 'start_offset' => -3,
                'persyaratan' => $sharedJadetabekPersyaratan,
                'pelamar' => 3,
            ],
            [
                'judul_loker' => 'Guru Bahasa Arab (SMA)',
                'lokasi' => 'Jadetabek',
                'jenjang' => 'Guru SMA',
                'pendidikan' => 7,
                'unit' => null,
                'deskripsi_loker' => 'Mengajar Bahasa Arab untuk siswa SMA, termasuk kaidah nahwu-sharaf dasar.',
                'end_offset' => 40, 'start_offset' => -3,
                'persyaratan' => $sharedJadetabekPersyaratan,
                'pelamar' => 2,
            ],
            [
                'judul_loker' => 'Janitor',
                'lokasi' => 'Jadetabek',
                'jenjang' => 'Janitor',
                'pendidikan' => 2, // SMP
                'unit' => null,
                'deskripsi_loker' => 'Menjaga kebersihan dan kerapian lingkungan unit, termasuk ruang kelas, kantor, dan area umum.',
                'end_offset' => 50, 'start_offset' => -8,
                'persyaratan' => [
                    ['35 Tahun', 'wajib'],
                    ['Pendidikan Minimal SMP/SLTP/Sederajat', 'wajib'],
                    ['Memiliki Pengalaman Kerja', 'diutamakan'],
                    ['Berpenampilan Islami', 'wajib'],
                    ['Sehat Jasmani & Rohani', 'wajib'],
                    ['Lulus Tes', 'wajib'],
                ],
                'pelamar' => 5,
            ],
            [
                'judul_loker' => 'Guru Seni Rupa',
                'lokasi' => 'DKI Jakarta',
                'jenjang' => 'Guru SMA',
                'pendidikan' => 7,
                'unit' => 'SMA IB DKI Jakarta',
                'deskripsi_loker' => 'Mengajar Seni Rupa, membimbing kreativitas siswa dalam karya dua dan tiga dimensi.',
                'end_offset' => 60, 'start_offset' => -12,
                'persyaratan' => [
                    ['35 Tahun', 'wajib'],
                    ['Minimal S1 Pendidikan Seni Rupa', 'wajib'],
                    ['IPK Minimal 3.00 dari 4.00', 'wajib'],
                    ['Akreditasi Minimal B (Baik Sekali)', 'wajib'],
                    ['Terampil Bahasa Inggris (Lisan & Tulisan)', 'wajib'],
                    ['Minimal TOEFL 550/IELTS 6', 'wajib'],
                    ['Memiliki Pengalaman Kerja', 'diutamakan'],
                    ['Berpenampilan Islami', 'wajib'],
                    ['Sehat Jasmani & Rohani', 'wajib'],
                ],
                'pelamar' => 3,
            ],
            [
                'judul_loker' => 'Laboran',
                'lokasi' => 'DKI Jakarta',
                'jenjang' => 'Laboran',
                'pendidikan' => 6, // D3
                'unit' => 'SMA IB DKI Jakarta',
                'deskripsi_loker' => 'Mengelola dan menyiapkan peralatan laboratorium IPA untuk kegiatan praktikum siswa.',
                'end_offset' => 60, 'start_offset' => -12,
                'persyaratan' => [
                    ['35 Tahun', 'wajib'],
                    ['Minimal D3 IPA/Kimia/Biologi/Fisika', 'wajib'],
                    ['IPK Minimal 3.00 dari 4.00', 'wajib'],
                    ['Akreditasi Minimal B (Baik Sekali)', 'wajib'],
                    ['Terampil Bahasa Inggris (Lisan & Tulisan)', 'wajib'],
                    ['Minimal TOEFL 500/IELTS 5.5', 'wajib'],
                    ['Memiliki Pengalaman Kerja', 'diutamakan'],
                    ['Berpenampilan Islami', 'wajib'],
                    ['Sehat Jasmani & Rohani', 'wajib'],
                ],
                'pelamar' => 2,
            ],
            [
                'judul_loker' => 'TU Keuangan',
                'lokasi' => 'Pangkalpinang',
                'jenjang' => 'Tata Usaha',
                'pendidikan' => 7,
                'unit' => null,
                'deskripsi_loker' => 'Mengelola administrasi keuangan unit, pencatatan transaksi, dan pelaporan berkala.',
                'end_offset' => 35, 'start_offset' => -18,
                'persyaratan' => [
                    ['35 Tahun', 'wajib'],
                    ['S1 Manajemen Keuangan/Akuntansi/Ekonomi', 'wajib'],
                    ['IPK Minimal 3.00 dari 4.00', 'wajib'],
                    ['Akreditasi Minimal B (Baik Sekali)', 'wajib'],
                    ['Terampil Bahasa Inggris (Lisan & Tulisan)', 'wajib'],
                    ['Memiliki Pengalaman Kerja', 'diutamakan'],
                    ['Berpenampilan Islami', 'wajib'],
                    ['Sehat Jasmani & Rohani', 'wajib'],
                    ['Lulus Tes', 'wajib'],
                ],
                'pelamar' => 3,
            ],
            [
                'judul_loker' => 'Guru PJOK (SD)',
                'lokasi' => 'Depok',
                'jenjang' => 'Guru SD',
                'pendidikan' => 7,
                'unit' => 'Al Fauzien Depok',
                'deskripsi_loker' => 'Mengajar Pendidikan Jasmani, Olahraga, dan Kesehatan untuk siswa SD.',
                'end_offset' => 3, 'start_offset' => -40,
                'persyaratan' => [
                    ['35 Tahun', 'wajib'],
                    ['S1 Pendidikan Jasmani/Olahraga/Linier', 'wajib'],
                    ['Akreditasi Minimal B (Baik Sekali)', 'wajib'],
                    ['IPK Minimal 3.00 dari 4.00', 'wajib'],
                    ['Terampil Bahasa Inggris (Lisan & Tulisan)', 'wajib'],
                    ['Memiliki Pengalaman Mengajar', 'diutamakan'],
                    ['Berpenampilan Islami', 'wajib'],
                    ['Sehat Jasmani & Rohani', 'wajib'],
                    ['Lulus Tes', 'wajib'],
                ],
                'pelamar' => 4,
            ],
            [
                'judul_loker' => 'Tata Usaha',
                'lokasi' => 'Jadetabek',
                'jenjang' => 'Tata Usaha',
                'pendidikan' => 7,
                'unit' => null,
                'deskripsi_loker' => 'Mengelola administrasi umum unit, korespondensi, dan kearsipan dokumen.',
                'end_offset' => 6, 'start_offset' => -25,
                'persyaratan' => [
                    ['35 Tahun', 'wajib'],
                    ['S1 Manajemen/Ilmu Ekonomi/Ilmu Administrasi/Ilmu Komunikasi/Linier', 'wajib'],
                    ['IPK Minimal 3.00 dari 4.00', 'wajib'],
                    ['Akreditasi Minimal B (Baik Sekali)', 'wajib'],
                    ['Terampil Bahasa Inggris (Lisan & Tulisan)', 'wajib'],
                    ['Memiliki Kemampuan Komunikasi yang Baik', 'wajib'],
                    ['Memiliki Pengalaman Kerja', 'diutamakan'],
                    ['Berpenampilan Islami', 'wajib'],
                    ['Sehat Jasmani & Rohani', 'wajib'],
                ],
                'pelamar' => 5,
            ],
            [
                'judul_loker' => 'Satpam',
                'lokasi' => 'Malang',
                'jenjang' => 'Satpam',
                'pendidikan' => 3,
                'unit' => null,
                'deskripsi_loker' => 'Menjaga keamanan dan ketertiban lingkungan unit selama jam operasional.',
                'end_offset' => 2, 'start_offset' => -33,
                'persyaratan' => [
                    ['35 Tahun', 'wajib'],
                    ['Pendidikan Minimal SMA', 'wajib'],
                    ['Memiliki Ijazah Gada Pratama', 'wajib'],
                    ['Tinggi Badan Minimal 167cm', 'wajib'],
                    ['Memiliki Pengalaman Kerja', 'diutamakan'],
                    ['Berpenampilan Islami', 'wajib'],
                    ['Sehat Jasmani & Rohani', 'wajib'],
                    ['Lulus Tes', 'wajib'],
                ],
                'pelamar' => 4,
            ],
        ];

        foreach ($lokers as $def) {
            $loker = Loker::create([
                'judul_loker' => $def['judul_loker'],
                'deskripsi_loker' => $def['deskripsi_loker'],
                'lokasi' => $def['lokasi'],
                'id_pendidikan_terakhir' => $def['pendidikan'],
                'id_jenjang' => $jenjangId[$def['jenjang']],
                'status_loker' => 'dibuka',
                'start_time' => $today->copy()->addDays($def['start_offset']),
                'end_time' => $today->copy()->addDays($def['end_offset']),
            ]);

            foreach ($def['persyaratan'] as [$text, $bobot]) {
                $loker->kriteria()->create([
                    'id_kriteria' => $kriteriaId($text),
                    'id_unit_kerja' => $def['unit'] ? $unitId[$def['unit']] : null,
                    'bobot' => $bobot,
                ]);
            }

            $this->buatPelamar($loker, $def['pelamar']);
        }
    }

    /** Remove all loker-related data so the demo set starts clean. */
    private function wipeExistingData(): void
    {
        LogNotifikasi::query()->delete();
        RiwayatTahapPelamar::query()->delete();
        TesTulis::query()->delete();
        Wawancara::query()->delete();
        Orientasi::query()->delete();
        TugasSementara::query()->delete();
        Pelamar::query()->delete();
        DB::table('kriteria_loker')->delete();
        Loker::query()->delete();
    }

    /** @return array<string,int> jenjang name => id, creating any missing ones. */
    private function ensureJenjang(): array
    {
        foreach (['Staf Humas', 'Janitor', 'Laboran'] as $nama) {
            Jenjang::firstOrCreate(['nama_jenjang' => $nama]);
        }

        return Jenjang::pluck('id_jenjang', 'nama_jenjang')->toArray();
    }

    /** @return array<string,int> unit name => id, creating any missing ones. */
    private function ensureUnitKerja(): array
    {
        foreach (['SMA IB DKI Jakarta', 'Al Fauzien Depok'] as $nama) {
            UnitKerja::firstOrCreate(['nama_unit' => $nama]);
        }

        return UnitKerja::pluck('id_unit_kerja', 'nama_unit')->toArray();
    }

    private function ensureLokasi(): void
    {
        foreach (['Pangkalpinang', 'Jadetabek', 'Bengkulu', 'Sidoarjo', 'Malang', 'DKI Jakarta', 'Depok'] as $nama) {
            Lokasi::firstOrCreate(['nama_lokasi' => $nama]);
        }
    }

    private function kriteria(string $text): int
    {
        return Kriteria::firstOrCreate(['teks_kriteria' => $text])->id_kriteria;
    }

    private function buatPelamar(Loker $loker, int $jumlah): void
    {
        $namaDepanL = ['Ahmad', 'Muhammad', 'Budi', 'Andi', 'Dedi', 'Eko', 'Fajar', 'Hendra', 'Irfan', 'Joko', 'Kurniawan', 'Lukman', 'Rizky', 'Sigit', 'Taufik', 'Wahyu', 'Yusuf', 'Bayu', 'Doni', 'Rian'];
        $namaDepanP = ['Siti', 'Dewi', 'Rina', 'Fitri', 'Nurul', 'Indah', 'Ratna', 'Wulan', 'Yuni', 'Astuti', 'Puspita', 'Maya', 'Lestari', 'Anita', 'Diah', 'Sari', 'Novita', 'Rahma', 'Intan', 'Ayu'];
        $namaBelakang = ['Pratama', 'Saputra', 'Wijaya', 'Kusuma', 'Santoso', 'Hidayat', 'Nugroho', 'Setiawan', 'Firmansyah', 'Rahayu', 'Wibowo', 'Susanto', 'Handayani', 'Permata', 'Gunawan', 'Suryadi', 'Utami', 'Maulana', 'Yulianti', 'Ramadhan'];

        $institusiS1 = ['Universitas Indonesia', 'Universitas Gadjah Mada', 'Universitas Padjadjaran', 'Universitas Airlangga', 'Universitas Negeri Jakarta', 'Universitas Muhammadiyah Jakarta', 'UIN Syarif Hidayatullah', 'Universitas Brawijaya', 'Universitas Diponegoro', 'Universitas Sriwijaya', 'Universitas Bengkulu', 'Universitas Negeri Malang'];
        $institusiD3 = ['Politeknik Negeri Jakarta', 'Politeknik Negeri Malang', 'Akademi Kimia Analisis', 'Politeknik Negeri Sriwijaya'];
        $institusiSma = ['SMA Negeri 1', 'SMA Negeri 3', 'SMK Negeri 2', 'SMA Muhammadiyah 1', 'MA Negeri 2'];
        $institusiSmp = ['SMP Negeri 4', 'SMP Negeri 7', 'MTs Negeri 1'];

        $programByJenjang = [
            'Driver' => ['IPS', 'IPA'],
            'Staf Humas' => ['Ilmu Komunikasi', 'Hubungan Masyarakat', 'Manajemen Pemasaran'],
            'Tata Usaha' => ['Manajemen', 'Ilmu Administrasi Negara', 'Ilmu Ekonomi', 'Akuntansi'],
            'Guru SD' => ['Pendidikan Guru Sekolah Dasar', 'Pendidikan Agama Islam', 'Pendidikan Jasmani Kesehatan dan Rekreasi'],
            'Guru SMP' => ['Pendidikan Bahasa Indonesia', 'Pendidikan IPS', 'Pendidikan Matematika'],
            'Guru SMA' => ['Pendidikan Matematika', 'Pendidikan Bahasa Arab', 'Pendidikan Seni Rupa'],
            'Janitor' => ['IPS', 'IPA'],
            'Laboran' => ['Kimia', 'Biologi', 'Fisika'],
            'Satpam' => ['IPS', 'IPA'],
        ];

        $jenjangNama = $loker->jenjang->nama_jenjang;
        $idPendidikan = $loker->id_pendidikan_terakhir;

        for ($i = 0; $i < $jumlah; $i++) {
            $isPria = random_int(0, 1) === 1;
            $nama = ($isPria ? $namaDepanL[array_rand($namaDepanL)] : $namaDepanP[array_rand($namaDepanP)]).' '.$namaBelakang[array_rand($namaBelakang)];

            $tahunLulus = random_int(2015, 2024);
            $akreditasiOptions = ['A', 'B', 'B', 'C'];
            $akreditasi = $akreditasiOptions[array_rand($akreditasiOptions)];
            $kategoriPtOptions = ['Perguruan Tinggi Negeri', 'Perguruan Tinggi Swasta', 'Lain-lain'];
            $kategoriPt = $kategoriPtOptions[array_rand($kategoriPtOptions)];
            $programStudi = $programByJenjang[$jenjangNama][array_rand($programByJenjang[$jenjangNama])] ?? '-';

            $ipkS1 = null;
            $ipkD3 = null;

            if ($idPendidikan === 7) {
                $institusi = $institusiS1[array_rand($institusiS1)];
                $ipkS1 = number_format(random_int(300, 390) / 100, 2);
            } elseif ($idPendidikan === 6) {
                $institusi = $institusiD3[array_rand($institusiD3)];
                $ipkD3 = number_format(random_int(300, 390) / 100, 2);
            } elseif ($idPendidikan === 3) {
                $institusi = $institusiSma[array_rand($institusiSma)].' '.$loker->lokasi;
                $kategoriPt = 'Lain-lain';
            } else {
                $institusi = $institusiSmp[array_rand($institusiSmp)].' '.$loker->lokasi;
                $kategoriPt = 'Lain-lain';
            }

            $tanggalLahir = Carbon::parse('2026-08-11')->subYears(random_int(23, 34))->subDays(random_int(0, 364));
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
                'alamat' => 'Jl. Contoh No. '.random_int(1, 99).', '.$loker->lokasi,
                'pernah_rekrutmen_sebelumnya' => 'Tidak',
                'pernah_bekerja_di_al_azhar' => 'Tidak',
                'id_pendidikan_terakhir' => $idPendidikan,
                'institusi' => $institusi,
                'program_studi' => $programStudi,
                'kategori_perguruan_tinggi' => $kategoriPt,
                'akreditasi' => $akreditasi,
                'tahun_lulus' => $tahunLulus,
                'ipk_s1' => $ipkS1,
                'ipk_d3' => $ipkD3,
                'cv_upload' => self::PLACEHOLDER_PDF,
                'ijazah_upload' => self::PLACEHOLDER_PDF,
                'ktp_upload' => self::PLACEHOLDER_IMG,
                'transkrip_nilai_upload' => self::PLACEHOLDER_PDF,
                'pas_foto_upload' => self::PLACEHOLDER_IMG,
                'surat_lamaran_upload' => self::PLACEHOLDER_PDF,
                'sim_upload' => $jenjangNama === 'Driver' ? self::PLACEHOLDER_IMG : null,
                'sertifikat_gada_pratama_upload' => $jenjangNama === 'Satpam' ? self::PLACEHOLDER_PDF : null,
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
