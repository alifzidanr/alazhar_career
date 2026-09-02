<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PelamarNotifikasi;
use App\Models\LogNotifikasi;
use App\Models\Pelamar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NotifikasiController extends Controller
{
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
}
