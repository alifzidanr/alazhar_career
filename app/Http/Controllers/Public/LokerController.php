<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Loker;
use App\Models\PendidikanTerakhir;
use App\Models\TahapRekrutmen;
use App\Models\UnitKerja;
use Illuminate\View\View;

class LokerController extends Controller
{
    public function index(): View
    {
        $lokerTerbaru = Loker::dibuka()->orderByDesc('start_time')->take(6)->get();
        $totalLowongan = Loker::dibuka()->count();
        $lokerUntukPencarian = Loker::dibuka()->orderBy('judul_loker')->get(['id_loker', 'judul_loker', 'lokasi']);
        $tahapRekrutmen = TahapRekrutmen::orderBy('id_tahap_rekrutmen')->get();

        return view('public.index', compact('lokerTerbaru', 'totalLowongan', 'lokerUntukPencarian', 'tahapRekrutmen'));
    }

    public function list(): View
    {
        $lokerList = Loker::dibuka()->with('kriteria')->orderByDesc('start_time')->get();

        $lokasiOptions = Loker::dibuka()->whereNotNull('lokasi')->distinct()->orderBy('lokasi')->pluck('lokasi');
        $unitOptions = UnitKerja::orderBy('nama_unit')->get();

        return view('public.lowongan', compact('lokerList', 'lokasiOptions', 'unitOptions'));
    }

    public function show(Loker $loker): View
    {
        $loker->load(['kriteria.kriteria', 'jenjang']);

        $kriteriaByBobot = $loker->kriteria->groupBy('bobot');

        $pendidikanList = PendidikanTerakhir::orderBy('id_pendidikan_terakhir')->get();

        // "Sampai tahap apa" only offers the first 4 stages (Tugas Sementara, Terima SK,
        // and Migrasi Data are excluded since a past applicant wouldn't self-report those).
        $tahapList = TahapRekrutmen::whereIn('id_tahap_rekrutmen', [
            TahapRekrutmen::SELEKSI_BERKAS,
            TahapRekrutmen::TES_TULIS,
            TahapRekrutmen::WAWANCARA,
            TahapRekrutmen::ORIENTASI,
        ])->orderBy('id_tahap_rekrutmen')->get();

        return view('public.show', compact('loker', 'kriteriaByBobot', 'pendidikanList', 'tahapList'));
    }
}
