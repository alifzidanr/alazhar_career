<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePelamarRequest;
use App\Models\Loker;
use App\Models\Pelamar;
use App\Models\RiwayatTahapPelamar;
use App\Models\StatusPelamar;
use App\Models\TahapRekrutmen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class LamaranController extends Controller
{
    public function store(StorePelamarRequest $request, Loker $loker): RedirectResponse
    {
        if ($loker->status_loker !== 'dibuka') {
            return back()->withErrors(['loker' => 'Lowongan ini sudah ditutup dan tidak menerima lamaran baru.']);
        }

        $data = $request->validated();

        $pelamar = DB::transaction(function () use ($data, $loker, $request) {
            $pelamar = Pelamar::create([
                'id_loker' => $loker->id_loker,
                'nama' => $data['nama'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'gelar' => $data['gelar'] ?? null,
                'no_hp' => $data['no_hp'],
                'email' => $data['email'],
                'alamat' => $data['alamat'],
                'id_pendidikan_terakhir' => $data['id_pendidikan_terakhir'],
                'institusi' => $data['institusi'],
                'program_studi' => $data['program_studi'],
                'akreditasi' => $data['akreditasi'],
                'tahun_lulus' => $data['tahun_lulus'],
                'ipk' => $data['ipk'],
                'id_status_pelamar' => StatusPelamar::SCREENING,
                'id_tahap_rekrutmen' => TahapRekrutmen::SELEKSI_BERKAS,
                'tanggal_apply' => now()->toDateString(),
                'cv_upload' => $request->file('cv_upload')->store('pelamar/cv', 'public'),
                'ijazah_upload' => $request->file('ijazah_upload')->store('pelamar/ijazah', 'public'),
                'ktp_upload' => $request->file('ktp_upload')->store('pelamar/ktp', 'public'),
                'transkrip_nilai_upload' => $request->file('transkrip_nilai_upload')->store('pelamar/transkrip', 'public'),
            ]);

            RiwayatTahapPelamar::create([
                'id_pelamar' => $pelamar->id_pelamar,
                'id_tahap_rekrutmen' => TahapRekrutmen::SELEKSI_BERKAS,
                'id_status_pelamar' => StatusPelamar::SCREENING,
                'catatan' => 'Lamaran diajukan oleh pelamar.',
                'created_by' => null,
            ]);

            return $pelamar;
        });

        return redirect()
            ->route('status.index')
            ->with('lamaran_sukses', 'Lamaran Anda untuk "'.$loker->judul_loker.'" berhasil dikirim. Tim HR akan menghubungi Anda melalui WhatsApp/email di '.$pelamar->no_hp.' / '.$pelamar->email.' untuk info selanjutnya.');
    }
}
