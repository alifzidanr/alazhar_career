# Al-Azhar Career

Aplikasi rekrutmen pegawai YPI Al-Azhar. Pelamar melihat lowongan dan mengirim lamaran secara daring; admin kepegawaian memproses pelamar melalui setiap tahap rekrutmen hingga datanya siap dimigrasikan ke HRIS (simpeg-proto).

## Fitur

### Halaman publik

- Daftar dan detail lowongan kerja (loker) yang sedang dibuka.
- Formulir lamaran dengan unggah berkas: CV, ijazah, KTP, pas foto, surat lamaran, transkrip nilai (S1/S2/S3), serta SIM dan sertifikat sesuai kebutuhan loker.
- Pengecekan NIK sebelum melamar agar pelamar tidak mendaftar ganda.

### Panel admin (`/admin`)

- **Dashboard** ringkasan pelamar dan lowongan.
- **Pelamar**: pencarian dan filter, detail pelamar, pratinjau berkas, unduh seluruh berkas sebagai satu PDF, catatan, serta ekspor ke Excel.
- **Tahap rekrutmen**, dengan pencatatan riwayat setiap perpindahan tahap:
  1. Seleksi Berkas
  2. Tes Tulis
  3. Wawancara
  4. Orientasi
  5. Tugas Sementara
  6. Terima SK
  7. Migrasi Data
- **Status pelamar** (mis. lolos, tidak lolos, dicadangkan, ditolak, mundur, diterima), dapat diubah per pelamar maupun massal.
- **Notifikasi email** ke pelamar, termasuk pengiriman SK orientasi; setiap pengiriman tercatat di log notifikasi.
- **Master data**: lowongan beserta kriterianya, kriteria, jenjang, wilayah, unit kerja, dan akun admin.
- **Login dengan passkey (WebAuthn)** selain email dan kata sandi.

## Teknologi

- PHP 8.5, Laravel 13
- MySQL (database aplikasi dan database HRIS)
- Blade, Alpine.js, Tailwind CSS, Vite
- `maatwebsite/excel` untuk ekspor Excel, `setasign/fpdi-fpdf` untuk menggabungkan berkas PDF, `laragear/webauthn` untuk passkey

## Instalasi lokal

Kebutuhan: PHP 8.5, Composer, Node.js beserta npm, dan MySQL.

```bash
git clone https://github.com/TD-YPI-AL-AZHAR/alazhar_career.git
cd alazhar_career
composer install
cp .env.example .env
php artisan key:generate
```

Sesuaikan `.env`:

```dotenv
APP_NAME="Al-Azhar Career"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=alazhar_career
DB_USERNAME=root
DB_PASSWORD=

# Database HRIS (simpeg-proto), dipakai untuk membaca data referensi HRIS
# secara langsung. Host, user, dan password mengikuti koneksi utama bila
# tidak diisi (lihat koneksi `hris` di config/database.php).
DB_HRIS_DATABASE=db_data22

# Pengiriman email notifikasi ke pelamar
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="${APP_NAME}"
```

Lalu jalankan migrasi dan build aset:

```bash
php artisan migrate
php artisan db:seed --class=HrisReferenceSeeder
npm install
npm run build
```

`HrisReferenceSeeder` mengisi salinan lokal data referensi HRIS (status kepegawaian, status keaktifan, status nikah, bidang diampu) yang dipakai saat migrasi data pegawai. Seeder `LokerSampleSeeder*`, `DemoRekrutmenSeeder`, `DummyRiwayatOrientasiSeeder`, dan `MockScaleSeeder` hanya berisi data contoh untuk pengembangan; jangan dijalankan di server produksi.

### Membuat akun admin pertama

Akun admin disimpan di tabel `admin_kepegawaian`. Akun pertama dibuat lewat Tinker:

```bash
php artisan tinker --execute 'App\Models\User::forceCreate(["nama" => "Admin", "email" => "admin@example.com", "password" => "ganti-kata-sandi"]);'
```

Admin dengan `id_admin` 1 adalah akun pemilik: tidak tampil di daftar admin lain dan tidak bisa dihapus. Akun admin berikutnya dapat dibuat dari menu **Manajemen Pengguna** di panel admin.

### Menjalankan aplikasi

```bash
composer run dev
```

Perintah ini menjalankan server Laravel, antrean (queue), log, dan Vite sekaligus. Aplikasi terbuka di `http://localhost:8000`, dan panel admin di `http://localhost:8000/login`.

## Pengujian

```bash
php artisan test
```

## Deploy

Aplikasi berjalan di server internal `192.168.13.10` (direktori `/home/ypia/alazhar_career`, port 8081, PHP-FPM 8.5). Langkah pembaruan, dijalankan sebagai user `ypia`:

```bash
cd /home/ypia/alazhar_career
git fetch origin main
git reset --hard origin/main
php8.5 /usr/local/bin/composer install --no-dev --optimize-autoloader --no-interaction   # bila composer.lock berubah
php8.5 artisan migrate --force                                                         # bila ada migrasi baru
npm run build
php8.5 artisan optimize:clear
```

Gunakan `php8.5` secara eksplisit, karena `php` bawaan di server masih versi 8.4.
