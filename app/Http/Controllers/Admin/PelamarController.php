<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelamar;
use App\Models\RiwayatTahapPelamar;
use App\Models\StatusPelamar;
use App\Models\TahapRekrutmen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PelamarController extends Controller
{
    public function index(Request $request): View
    {
        $tahapAktif = $request->integer('tahap', 0);

        $query = Pelamar::with(['loker', 'statusPelamar', 'tahapRekrutmen']);

        if ($tahapAktif !== 0) {
            $query->where('id_tahap_rekrutmen', $tahapAktif);
        }

        $pelamarList = $query->orderByDesc('tanggal_apply')->get();

        $tahapOptions = TahapRekrutmen::orderBy('id_tahap_rekrutmen')->get();

        $counts = Pelamar::selectRaw('id_tahap_rekrutmen, count(*) as total')
            ->groupBy('id_tahap_rekrutmen')
            ->pluck('total', 'id_tahap_rekrutmen');

        $totalSemua = $counts->sum();

        return view('admin.pelamar.index', compact('pelamarList', 'tahapOptions', 'tahapAktif', 'counts', 'totalSemua'));
    }

    public function show(Pelamar $pelamar): View
    {
        $pelamar->load([
            'loker',
            'pendidikanTerakhir',
            'statusPelamar',
            'tahapRekrutmen',
            'riwayat.tahapRekrutmen',
            'riwayat.statusPelamar',
            'logNotifikasi',
            'cadanganDari',
            'kandidatCadangan',
        ]);

        $statusOptions = StatusPelamar::orderBy('id_status_pelamar')->get();

        return view('admin.pelamar.show', compact('pelamar', 'statusOptions'));
    }

    public function updateStatus(Request $request, Pelamar $pelamar): RedirectResponse
    {
        $data = $request->validate([
            'id_status_pelamar' => ['required', 'exists:status_pelamar,id_status_pelamar'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'id_pelamar_cadangan_dari' => ['nullable', 'exists:pelamar,id_pelamar'],
        ]);

        DB::transaction(function () use ($data, $pelamar, $request) {
            $pelamar->id_status_pelamar = $data['id_status_pelamar'];

            if ((int) $data['id_status_pelamar'] === StatusPelamar::DICADANGKAN) {
                $pelamar->id_pelamar_cadangan_dari = $data['id_pelamar_cadangan_dari'] ?? null;
            }

            if ($request->filled('catatan')) {
                $pelamar->catatan = $data['catatan'];
            }

            $pelamar->save();

            RiwayatTahapPelamar::create([
                'id_pelamar' => $pelamar->id_pelamar,
                'id_tahap_rekrutmen' => $pelamar->id_tahap_rekrutmen,
                'id_status_pelamar' => $pelamar->id_status_pelamar,
                'catatan' => $data['catatan'] ?? null,
                'created_by' => auth()->user()->name,
            ]);
        });

        return back()->with('status', 'Status pelamar berhasil diperbarui.');
    }

    public function advanceTahap(Pelamar $pelamar): RedirectResponse
    {
        if (in_array($pelamar->id_status_pelamar, [StatusPelamar::MUNDUR, StatusPelamar::DICADANGKAN], true)) {
            return back()->withErrors(['tahap' => 'Pelamar berstatus "mundur" atau "dicadangkan" tidak dapat dilanjutkan ke tahap berikutnya.']);
        }

        if ($pelamar->id_tahap_rekrutmen >= TahapRekrutmen::MIGRASI_DATA) {
            return back()->withErrors(['tahap' => 'Pelamar sudah berada di tahap akhir (Migrasi Data).']);
        }

        DB::transaction(function () use ($pelamar) {
            $pelamar->id_tahap_rekrutmen += 1;
            $pelamar->id_status_pelamar = TahapRekrutmen::statusAwalUntuk($pelamar->id_tahap_rekrutmen);
            $pelamar->save();

            RiwayatTahapPelamar::create([
                'id_pelamar' => $pelamar->id_pelamar,
                'id_tahap_rekrutmen' => $pelamar->id_tahap_rekrutmen,
                'id_status_pelamar' => $pelamar->id_status_pelamar,
                'catatan' => 'Dilanjutkan ke tahap "'.$pelamar->tahapRekrutmen()->first()->tahap_rekrutmen.'".',
                'created_by' => auth()->user()->name,
            ]);
        });

        return back()->with('status', 'Pelamar berhasil dilanjutkan ke tahap berikutnya.');
    }

    public function regressTahap(Pelamar $pelamar): RedirectResponse
    {
        if ($pelamar->id_tahap_rekrutmen <= TahapRekrutmen::SELEKSI_BERKAS) {
            return back()->withErrors(['tahap' => 'Pelamar sudah berada di tahap paling awal (Seleksi Berkas).']);
        }

        DB::transaction(function () use ($pelamar) {
            $pelamar->id_tahap_rekrutmen -= 1;
            $pelamar->id_status_pelamar = TahapRekrutmen::statusAwalUntuk($pelamar->id_tahap_rekrutmen);
            $pelamar->save();

            RiwayatTahapPelamar::create([
                'id_pelamar' => $pelamar->id_pelamar,
                'id_tahap_rekrutmen' => $pelamar->id_tahap_rekrutmen,
                'id_status_pelamar' => $pelamar->id_status_pelamar,
                'catatan' => 'Dimundurkan ke tahap "'.$pelamar->tahapRekrutmen()->first()->tahap_rekrutmen.'".',
                'created_by' => auth()->user()->name,
            ]);
        });

        return back()->with('status', 'Pelamar berhasil dimundurkan ke tahap sebelumnya.');
    }

    public function updateCatatan(Request $request, Pelamar $pelamar): RedirectResponse
    {
        $data = $request->validate([
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($data, $pelamar) {
            $pelamar->catatan = $data['catatan'] ?? null;
            $pelamar->save();

            RiwayatTahapPelamar::create([
                'id_pelamar' => $pelamar->id_pelamar,
                'id_tahap_rekrutmen' => $pelamar->id_tahap_rekrutmen,
                'id_status_pelamar' => $pelamar->id_status_pelamar,
                'catatan' => $data['catatan'] ?? '(catatan dikosongkan)',
                'created_by' => auth()->user()->name,
            ]);
        });

        return back()->with('status', 'Catatan berhasil disimpan.');
    }
}
