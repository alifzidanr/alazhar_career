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
                'label' => 'Lolos Seleksi Berkas',
                'subject' => 'Info Lamaran :loker - Lolos Seleksi Berkas',
                'body' => "Yth. :nama,\n\nSelamat, Anda dinyatakan LOLOS seleksi berkas untuk lowongan :loker. Anda berhak mengikuti Tes Tulis. Jadwal akan kami informasikan melalui kontak ini.\n\nTerima kasih,\nHR Al Azhar",
            ],
            'lolos_tes_tulis' => [
                'label' => 'Lolos Tes Tulis',
                'subject' => 'Info Lamaran :loker - Lolos Tes Tulis',
                'body' => "Yth. :nama,\n\nSelamat, Anda dinyatakan LOLOS Tes Tulis untuk lowongan :loker. Anda berhak mengikuti tahap Wawancara. Jadwal akan kami informasikan melalui kontak ini.\n\nTerima kasih,\nHR Al Azhar",
            ],
            'lolos_wawancara' => [
                'label' => 'Lolos Wawancara',
                'subject' => 'Info Lamaran :loker - Lolos Wawancara',
                'body' => "Yth. :nama,\n\nSelamat, Anda dinyatakan LOLOS Wawancara untuk lowongan :loker. Anda berhak mengikuti Masa Orientasi. Informasi lebih lanjut akan kami sampaikan melalui kontak ini.\n\nTerima kasih,\nHR Al Azhar",
            ],
            'dicadangkan' => [
                'label' => 'Dicadangkan (dari Wawancara)',
                'subject' => 'Info Lamaran :loker - Kandidat Cadangan',
                'body' => "Yth. :nama,\n\nTerima kasih telah mengikuti Wawancara untuk lowongan :loker. Saat ini Anda kami tempatkan sebagai KANDIDAT CADANGAN. Kami akan menghubungi kembali apabila dibutuhkan.\n\nTerima kasih,\nHR Al Azhar",
            ],
            'lolos_orientasi' => [
                'label' => 'Lolos Orientasi',
                'subject' => 'Info Lamaran :loker - Lolos Orientasi',
                'body' => "Yth. :nama,\n\nSelamat, Anda dinyatakan LOLOS Masa Orientasi untuk lowongan :loker. Anda berhak melanjutkan ke Tugas Sementara.\n\nTerima kasih,\nHR Al Azhar",
            ],
            'lolos_tugas_sementara' => [
                'label' => 'Lolos Tugas Sementara',
                'subject' => 'Info Lamaran :loker - Lolos Tugas Sementara',
                'body' => "Yth. :nama,\n\nSelamat, Anda dinyatakan LOLOS Tugas Sementara untuk lowongan :loker. Proses selanjutnya adalah penerbitan SK dari HR.\n\nTerima kasih,\nHR Al Azhar",
            ],
            'terima_sk' => [
                'label' => 'Terima SK dari HR',
                'subject' => 'Selamat Bergabung - :loker',
                'body' => "Yth. :nama,\n\nSelamat! Anda dinyatakan DITERIMA untuk lowongan :loker. Surat Keputusan (SK) akan segera kami terbitkan/serahkan. Mohon menghubungi HR untuk proses selanjutnya.\n\nTerima kasih,\nHR Al Azhar",
            ],
            'tidak_lolos' => [
                'label' => 'Tidak Lolos Tahap',
                'subject' => 'Info Lamaran :loker',
                'body' => "Yth. :nama,\n\nTerima kasih telah mengikuti proses rekrutmen :loker. Mohon maaf, saat ini Anda belum dapat kami lanjutkan pada tahap :tahap. Semoga sukses selalu.\n\nTerima kasih,\nHR Al Azhar",
            ],
            'ditolak' => [
                'label' => 'Ditolak',
                'subject' => 'Info Lamaran :loker',
                'body' => "Yth. :nama,\n\nTerima kasih telah melamar pada lowongan :loker. Mohon maaf, saat ini kami belum dapat melanjutkan proses lamaran Anda. Semoga sukses selalu.\n\nTerima kasih,\nHR Al Azhar",
            ],
            'mundur' => [
                'label' => 'Konfirmasi Mengundurkan Diri',
                'subject' => 'Konfirmasi Pengunduran Diri - :loker',
                'body' => "Yth. :nama,\n\nKami telah menerima informasi pengunduran diri Anda dari proses rekrutmen :loker. Terima kasih atas partisipasi Anda.\n\nTerima kasih,\nHR Al Azhar",
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
