<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Jenjang;
use App\Models\Loker;
use App\Models\PendidikanTerakhir;
use App\Models\TahapRekrutmen;
use App\Models\UnitKerja;
use Illuminate\View\View;

class LokerController extends Controller
{
    public function index(): View
    {
        $lokerTerbaru = Loker::dibuka()->with('jenjang')->orderByDesc('start_time')->take(3)->get();
        $totalLowongan = Loker::dibuka()->count();
        $tahapRekrutmen = TahapRekrutmen::orderBy('id_tahap_rekrutmen')->get();
        $wilayahOptions = Loker::dibuka()->whereNotNull('wilayah')->distinct()->orderBy('wilayah')->pluck('wilayah');
        $jenjangOptions = Jenjang::orderBy('nama_jenjang')->get();
        $lokerUntukPencarian = Loker::dibuka()->orderBy('judul_loker')->get(['id_loker', 'judul_loker', 'wilayah']);

        return view('public.index', compact('lokerTerbaru', 'totalLowongan', 'tahapRekrutmen', 'wilayahOptions', 'jenjangOptions', 'lokerUntukPencarian'));
    }

    public function list(): View
    {
        $lokerList = Loker::dibuka()->with(['kriteria', 'jenjang'])->orderByDesc('start_time')->get();

        $wilayahOptions = Loker::dibuka()->whereNotNull('wilayah')->distinct()->orderBy('wilayah')->pluck('wilayah');
        $unitOptions = UnitKerja::orderBy('nama_unit')->get();
        $jenjangOptions = Jenjang::orderBy('nama_jenjang')->get();

        return view('public.lowongan', compact('lokerList', 'wilayahOptions', 'unitOptions', 'jenjangOptions'));
    }

    public function show(Loker $loker): View
    {
        $loker->load(['kriteria.kriteria', 'jenjang']);

        $kriteriaByBobot = $loker->kriteria->groupBy('bobot');

        // Only offer pendidikan levels at or above the loker's jenjang minimum,
        // so an applicant can't select an education level the job doesn't allow.
        // D3 is excluded as a selectable option on the application form entirely.
        $pendidikanList = PendidikanTerakhir::orderBy('id_pendidikan_terakhir')
            ->where('pendidikan_terakhir', '!=', 'D3')
            ->when($loker->jenjang?->id_pendidikan_minimum, fn ($query, $minimum) => $query->where('id_pendidikan_terakhir', '>=', $minimum))
            ->get();

        // "Sampai tahap apa" excludes Seleksi Berkas (every applicant clears that stage just
        // by applying, so it's not a meaningful self-reported answer) as well as Tugas
        // Sementara, Terima SK, and Migrasi Data (a past applicant wouldn't self-report those).
        $tahapList = TahapRekrutmen::whereIn('id_tahap_rekrutmen', [
            TahapRekrutmen::TES_TULIS,
            TahapRekrutmen::WAWANCARA,
            TahapRekrutmen::ORIENTASI,
        ])->orderBy('id_tahap_rekrutmen')->get();

        return view('public.show', compact('loker', 'kriteriaByBobot', 'pendidikanList', 'tahapList'));
    }
}
