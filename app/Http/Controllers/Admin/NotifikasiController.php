<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PelamarNotifikasi;
use App\Mail\SkOrientasiMail;
use App\Models\LogNotifikasi;
use App\Models\Pelamar;
use App\Support\NotifikasiTemplates;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NotifikasiController extends Controller
{
    public function sendSkOrientasi(Pelamar $pelamar): RedirectResponse
    {
        $orientasi = $pelamar->orientasi;

        if (! $orientasi?->sk_orientasi_upload) {
            return back()->withErrors(['channel' => 'SK Orientasi belum diunggah.']);
        }

        if (! $pelamar->email) {
            return back()->withErrors(['channel' => 'Pelamar ini tidak memiliki alamat email.']);
        }

        $subject = 'SK Orientasi - '.$pelamar->loker?->judul_loker;
        $body = "Assalamualaikum Wr. Wb.\n\nDengan ini disampaikan kepada bapak/ibu {$pelamar->nama},\n\nTerlampir Surat Keputusan (SK) Orientasi untuk posisi {$pelamar->loker?->judul_loker}. Mohon untuk dipelajari dan dipersiapkan.\n\nTerimakasih.\n\nKepala Bagian Kepegawaian YPI Al Azhar";

        try {
            Mail::to($pelamar->email)->send(new SkOrientasiMail(
                $subject,
                $body,
                $orientasi->sk_orientasi_upload,
                'SK Orientasi - '.$pelamar->namaLengkap().'.pdf',
            ));
            $statusKirim = 'terkirim';
        } catch (\Throwable $e) {
            report($e);
            $statusKirim = 'gagal';
        }

        LogNotifikasi::create([
            'id_pelamar' => $pelamar->id_pelamar,
            'channel' => 'email',
            'template' => 'sk_orientasi',
            'pesan' => "Subject: {$subject}\n\n{$body}",
            'status_kirim' => $statusKirim,
            'created_by' => auth()->user()->name,
        ]);

        if ($statusKirim === 'gagal') {
            return back()->withErrors(['channel' => 'Gagal mengirim email. Periksa konfigurasi SMTP dan coba lagi.']);
        }

        return back()->with('status', "SK Orientasi berhasil dikirim ke {$pelamar->email}.");
    }

    public function send(Request $request, Pelamar $pelamar): RedirectResponse
    {
        $data = $request->validate([
            'channel' => ['required', 'in:email'],
            'template' => ['nullable', 'string', 'max:100'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:3000'],
        ]);

        if (! $pelamar->email) {
            return back()->withErrors(['channel' => 'Pelamar ini tidak memiliki alamat email.']);
        }

        $subject = $data['subject'] ?? '';
        $body = $data['body'] ?? '';

        try {
            Mail::to($pelamar->email)->send(new PelamarNotifikasi($subject, $body));
            $statusKirim = 'terkirim';
        } catch (\Throwable $e) {
            report($e);
            $statusKirim = 'gagal';
        }

        LogNotifikasi::create([
            'id_pelamar' => $pelamar->id_pelamar,
            'channel' => 'email',
            'template' => $data['template'] ?? null,
            'pesan' => $subject !== '' ? "Subject: {$subject}\n\n{$body}" : $body,
            'status_kirim' => $statusKirim,
            'created_by' => auth()->user()->name,
        ]);

        if ($statusKirim === 'gagal') {
            return back()->withErrors(['channel' => 'Gagal mengirim email. Periksa konfigurasi SMTP dan coba lagi.']);
        }

        return back()->with('status', "Email berhasil dikirim ke {$pelamar->email}.");
    }

    public function bulkSend(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:pelamar,id_pelamar'],
            'channel' => ['required', 'in:email'],
            'template' => ['nullable', 'string', 'max:100'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:3000'],
        ]);

        $pelamarList = Pelamar::with(['loker', 'tahapRekrutmen'])->whereIn('id_pelamar', $data['ids'])->get();

        $terkirim = 0;
        $gagal = 0;
        $tanpaEmail = 0;

        foreach ($pelamarList as $pelamar) {
            if (! $pelamar->email) {
                $tanpaEmail++;

                continue;
            }

            // Each recipient gets their own :nama/:loker/:tahap substitution,
            // whether the subject/body came from a template or a manual message.
            $subject = NotifikasiTemplates::renderText($data['subject'], $pelamar);
            $body = NotifikasiTemplates::renderText($data['body'], $pelamar);

            try {
                Mail::to($pelamar->email)->send(new PelamarNotifikasi($subject, $body));
                $statusKirim = 'terkirim';
                $terkirim++;
            } catch (\Throwable $e) {
                report($e);
                $statusKirim = 'gagal';
                $gagal++;
            }

            LogNotifikasi::create([
                'id_pelamar' => $pelamar->id_pelamar,
                'channel' => 'email',
                'template' => $data['template'] ?? null,
                'pesan' => "Subject: {$subject}\n\n{$body}",
                'status_kirim' => $statusKirim,
                'created_by' => auth()->user()->name,
            ]);
        }

        $message = "{$terkirim} email berhasil dikirim.";
        if ($gagal > 0) {
            $message .= " {$gagal} gagal terkirim.";
        }
        if ($tanpaEmail > 0) {
            $message .= " {$tanpaEmail} pelamar dilewati karena tidak memiliki alamat email.";
        }

        return back()->with('status', $message);
    }
}
