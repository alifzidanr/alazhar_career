<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Loker;
use App\Models\PendidikanTerakhir;
use App\Models\TahapRekrutmen;
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
        $lokerList = Loker::dibuka()->orderByDesc('start_time')->get();

        return view('public.lowongan', compact('lokerList'));
    }

    public function show(Loker $loker): View
    {
        $loker->load(['kriteria.kriteria']);

        $kriteriaByBobot = $loker->kriteria->groupBy('bobot');

        $pendidikanList = PendidikanTerakhir::orderBy('id_pendidikan_terakhir')->get();

        return view('public.show', compact('loker', 'kriteriaByBobot', 'pendidikanList'));
    }
}
