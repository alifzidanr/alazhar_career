<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogNotifikasi;
use App\Models\Pelamar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function send(Request $request, Pelamar $pelamar): RedirectResponse
    {
        $data = $request->validate([
            'channel' => ['required', 'in:whatsapp,email'],
            'template' => ['nullable', 'string', 'max:100'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:3000'],
        ]);

        $body = $data['body'] ?? '';

        if ($data['channel'] === 'whatsapp') {
            $nomor = $pelamar->whatsappNumber();

            if (! $nomor) {
                return back()->withErrors(['channel' => 'Pelamar ini tidak memiliki nomor WhatsApp.']);
            }

            LogNotifikasi::create([
                'id_pelamar' => $pelamar->id_pelamar,
                'channel' => 'whatsapp',
                'template' => $data['template'] ?? null,
                'pesan' => $body,
                'status_kirim' => 'terkirim',
                'created_by' => auth()->user()->name,
            ]);

            return redirect()->away('https://wa.me/'.$nomor.'?text='.rawurlencode($body));
        }

        if (! $pelamar->email) {
            return back()->withErrors(['channel' => 'Pelamar ini tidak memiliki alamat email.']);
        }

        $subject = $data['subject'] ?? '';

        LogNotifikasi::create([
            'id_pelamar' => $pelamar->id_pelamar,
            'channel' => 'email',
            'template' => $data['template'] ?? null,
            'pesan' => $subject !== '' ? "Subject: {$subject}\n\n{$body}" : $body,
            'status_kirim' => 'terkirim',
            'created_by' => auth()->user()->name,
        ]);

        $mailto = 'mailto:'.$pelamar->email.'?subject='.rawurlencode($subject).'&body='.rawurlencode($body);

        return redirect()->away($mailto);
    }
}
