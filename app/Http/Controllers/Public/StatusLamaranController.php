<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Pelamar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatusLamaranController extends Controller
{
    public function index(): View
    {
        return view('public.status');
    }

    public function search(Request $request): View|RedirectResponse
    {
        $data = $request->validate([
            'email' => ['nullable', 'required_without:no_hp', 'email'],
            'no_hp' => ['nullable', 'required_without:email', 'string'],
        ]);

        $hasilList = Pelamar::with(['loker', 'statusPelamar', 'tahapRekrutmen'])
            ->when(! empty($data['email']), fn ($query) => $query->where('email', $data['email']))
            ->when(! empty($data['no_hp']), fn ($query) => $query->where('no_hp', $data['no_hp']))
            ->orderByDesc('tanggal_apply')
            ->get();

        if ($hasilList->isEmpty()) {
            return back()->withInput()->withErrors([
                'email' => 'Tidak ditemukan lamaran dengan email/no. WhatsApp tersebut. Pastikan data yang dimasukkan sama persis dengan saat mendaftar.',
            ]);
        }

        return view('public.status', ['hasilList' => $hasilList]);
    }
}
