<?php

namespace App\Support;

use App\Models\Pelamar;
use App\Models\StatusPelamar;
use App\Models\TahapRekrutmen;

/**
 * Canned WhatsApp/email message templates for each recruitment stage outcome.
 * Admins may also send an empty/manual message instead of picking one of these.
 */
class NotifikasiTemplates
{
    /**
     * @return array<string, array{label: string, subject: string, body: string}>
     */
    public static function all(): array
    {
        return [
            'lolos_seleksi_berkas' => [
                'label' => 'Undangan Tes Tulis Online',
                'subject' => 'Undangan Tes Tulis - :loker',
                'body' => "Assalamualaikum Wr. Wb.\n\nBerdasarkan berkas lamaran yang telah dikirimkan,\nDengan ini Bagian Kepegawaian YPI Al Azhar mengundang :nama untuk mengikuti Seleksi Tes Tulis Online untuk posisi :loker pada:\n\nHari/Tanggal : [isi hari, tanggal]\nPukul        : [isi jam] WIB s.d. selesai\nMeeting ID   : [isi meeting ID]\nPasscode     : [isi passcode]\n\nKepala Bagian Kepegawaian YPI Al Azhar\nttd\nNgadiman, M.Pd.\n\nWassalamu'alaikum Wr. Wb.\n\nMohon konfirmasi kehadirannya.\nMohon hadir tepat waktu.\n\nPERATURAN\n1. Materi tes tulis ada 3 soal, yaitu Agama Islam, Bahasa Inggris, dan Bidang Studi. Link soal akan diberikan saat hari ujian via chat zoom.\n2. Tes tulis dilaksanakan melalui zoom. Peserta harus menggunakan dua device: satu untuk mengawasi (zoom aktif), satu untuk mengerjakan soal.\n3. Format display name di zoom menggunakan kode awalan \"P\" kemudian nama, lalu bidang studi. Contoh: P_Nama_BidangStudi. Format yang tidak sesuai akan dinyatakan tidak mengikuti ujian.\n4. Tidak ada toleransi penambahan waktu pengerjaan soal apabila telat bergabung di zoom.\n5. Jadwal tidak bisa direschedule.",
            ],
            'lolos_tes_tulis' => [
                'label' => 'Undangan Tes Wawancara',
                'subject' => 'Undangan Tes Wawancara - :loker',
                'body' => "Assalamualaikum Wr. Wb.\n\nBerdasarkan hasil seleksi Tes Tulis yang telah :nama ikuti,\nDengan ini Bagian Kepegawaian YPI Al Azhar mengundang :nama untuk mengikuti Seleksi Tes Wawancara dan Microteaching/Praktik (bila berlaku) untuk posisi :loker pada:\n\nHari/Tanggal : [isi hari, tanggal]\nPukul        : [isi jam] WIB s.d. selesai\nMeeting ID   : [isi meeting ID, bila online]\nPasscode     : [isi passcode, bila online]\nTempat       : [isi lokasi, bila offline]\n\nKepala Bagian Kepegawaian YPI Al Azhar\nttd\nNgadiman, M.Pd.\n\nWassalamu'alaikum Wr. Wb.\n\nMohon menyiapkan bahan untuk microteaching (bila berlaku) dan mohon konfirmasi kehadirannya.\n\nPERATURAN\n- Format display name di zoom menggunakan kode awalan \"P\" kemudian nama, lalu bidang studi. Contoh: P_Nama_BidangStudi. Format yang tidak sesuai akan dinyatakan tidak mengikuti ujian. (khusus online)\n- Pakaian rapi sopan, perempuan wajib berhijab, bersepatu. (khusus offline)\n\nnote:\n1. Mohon hadir 10 menit lebih awal dari jadwal yang telah ditentukan.\n2. Tes wawancara terdiri dari beberapa bagian: Wawancara Agama, Wawancara Umum, dan Microteaching/Praktik (bila berlaku).\n3. Masing-masing tes wawancara berbeda penguji.\n4. Mekanisme wawancara online akan diundang di masing-masing breakout room penguji secara bergantian. Bila sudah selesai silakan leave room breakout, bukan leave meeting.\n5. Jadwal tidak dapat direschedule.",
            ],
            'lolos_wawancara' => [
                'label' => 'Info Orientasi',
                'subject' => 'Info Orientasi - :loker',
                'body' => "Assalamualaikum bapak/ibu,\n\nDengan ini disampaikan kepada :nama.\n\nBerdasarkan hasil tes, :nama akan menjalani Orientasi di [isi unit kerja/sekolah penempatan]. :nama bisa memulai orientasi pada [isi hari, tanggal]. :nama bisa langsung ke tempat bertugas yang sudah ditentukan, dan di sana bisa langsung menemui Kepala Sekolah/Kepala Unit.\n\nUntuk Surat Orientasi akan menyusul.\n\nMohon hadir jam [isi jam].\n\nTerimakasih.\n\nnote :\n- Orientasi dilaksanakan selama [isi durasi] hari kerja.\n- Orientasi masih termasuk ke dalam tahapan seleksi untuk :loker.",
            ],
            'dicadangkan' => [
                'label' => 'Info Kandidat Cadangan',
                'subject' => 'Info Lamaran :loker - Kandidat Cadangan',
                'body' => "Assalamu'alaikum Wr. Wb.\n\nDengan ini disampaikan kepada :nama.\n\nBerdasarkan hasil seleksi tes yang telah :nama ikuti sebelumnya, saat ini :nama terdaftar sebagai Kandidat Cadangan untuk :loker. :nama akan dihubungi kembali apabila ada kebutuhan. Kami mengucapkan terima kasih atas kesediaan :nama mengikuti tahapan seleksi yang kami adakan.\n\nTerimakasih.\n\nnote :\nMasa berlaku status cadangan ini sampai [isi bulan, tahun berlaku]. Bila sampai [isi bulan, tahun berlaku] tidak mendapat panggilan, berarti status cadangan ini hangus. Bila ingin melamar kembali dipersilakan.",
            ],
            'lolos_orientasi' => [
                'label' => 'Lolos Orientasi',
                'subject' => 'Info Lamaran :loker - Lolos Orientasi',
                'body' => "Assalamu'alaikum Wr. Wb.\n\nDengan ini disampaikan kepada :nama.\n\nSelamat, :nama dinyatakan LOLOS Masa Orientasi untuk :loker dan berhak melanjutkan ke tahap Tugas Sementara. Informasi lebih lanjut akan kami sampaikan melalui kontak ini.\n\nTerimakasih.",
            ],
            'lolos_tugas_sementara' => [
                'label' => 'Lolos Tugas Sementara',
                'subject' => 'Info Lamaran :loker - Lolos Tugas Sementara',
                'body' => "Assalamu'alaikum Wr. Wb.\n\nDengan ini disampaikan kepada :nama.\n\nSelamat, :nama dinyatakan LOLOS Tugas Sementara untuk :loker. Proses selanjutnya adalah penerbitan SK dari Bagian Kepegawaian.\n\nTerimakasih.",
            ],
            'terima_sk' => [
                'label' => 'Terima SK dari Kepegawaian',
                'subject' => 'Selamat Bergabung - :loker',
                'body' => "Assalamu'alaikum Wr. Wb.\n\nDengan ini disampaikan kepada :nama.\n\nSelamat! :nama dinyatakan DITERIMA untuk :loker. Surat Keputusan (SK) akan segera kami terbitkan/serahkan. Mohon menghubungi Bagian Kepegawaian untuk proses selanjutnya.\n\nTerimakasih.",
            ],
            'tidak_lolos' => [
                'label' => 'Tidak Lolos Tahap',
                'subject' => 'Info Lamaran :loker',
                'body' => "Assalamu'alaikum Wr. Wb.\n\nDengan ini disampaikan kepada :nama.\n\nBerdasarkan hasil seleksi tes yang telah :nama ikuti sebelumnya untuk :loker, mohon maaf saat ini hasil tes :nama belum bisa untuk lanjut ke tahap seleksi berikutnya (:tahap). Kami mengucapkan terima kasih atas kesediaan :nama mengikuti tahapan tes yang kami adakan.\n\nTerimakasih.",
            ],
            'ditolak' => [
                'label' => 'Ditolak',
                'subject' => 'Info Lamaran :loker',
                'body' => "Assalamu'alaikum Wr. Wb.\n\nDengan ini disampaikan kepada :nama.\n\nTerima kasih telah melamar pada lowongan :loker. Mohon maaf, saat ini kami belum dapat melanjutkan proses lamaran :nama pada tahap :tahap. Semoga sukses selalu.\n\nTerimakasih.",
            ],
            'mundur' => [
                'label' => 'Konfirmasi Mengundurkan Diri',
                'subject' => 'Konfirmasi Pengunduran Diri - :loker',
                'body' => "Assalamu'alaikum Wr. Wb.\n\nDengan ini disampaikan kepada :nama.\n\nKami telah menerima informasi pengunduran diri :nama dari proses rekrutmen :loker pada tahap :tahap. Terima kasih atas partisipasi :nama.\n\nTerimakasih.",
            ],
        ];
    }

    public static function suggestedKey(Pelamar $pelamar): ?string
    {
        $tahap = $pelamar->id_tahap_rekrutmen;
        $status = $pelamar->id_status_pelamar;

        if ($status === StatusPelamar::DITOLAK) {
            return 'ditolak';
        }

        if ($status === StatusPelamar::MUNDUR) {
            return 'mundur';
        }

        if ($status === StatusPelamar::TIDAK_LOLOS) {
            return 'tidak_lolos';
        }

        if ($status === StatusPelamar::DICADANGKAN) {
            return 'dicadangkan';
        }

        if ($status === StatusPelamar::SCREENING) {
            return null;
        }

        if ($status === StatusPelamar::DITERIMA) {
            return 'terima_sk';
        }

        // status = ongoing/migrated, keyed by the stage they just entered (i.e. the stage they just cleared)
        return match ($tahap) {
            TahapRekrutmen::TES_TULIS => 'lolos_seleksi_berkas',
            TahapRekrutmen::WAWANCARA => 'lolos_tes_tulis',
            TahapRekrutmen::ORIENTASI => 'lolos_wawancara',
            TahapRekrutmen::TUGAS_SEMENTARA => 'lolos_orientasi',
            TahapRekrutmen::TERIMA_SK => 'lolos_tugas_sementara',
            TahapRekrutmen::MIGRASI_DATA => 'terima_sk',
            default => null,
        };
    }

    /**
     * @return array{subject: string, body: string}
     */
    public static function render(string $key, Pelamar $pelamar): array
    {
        $template = self::all()[$key] ?? ['subject' => '', 'body' => ''];

        $replacements = [
            ':nama' => $pelamar->namaLengkap(),
            ':loker' => $pelamar->loker->judul_loker ?? '-',
            ':tahap' => $pelamar->tahapRekrutmen->tahap_rekrutmen ?? '-',
        ];

        return [
            'subject' => strtr($template['subject'], $replacements),
            'body' => strtr($template['body'], $replacements),
        ];
    }
}
