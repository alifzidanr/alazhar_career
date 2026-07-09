<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loker;
use App\Models\Pelamar;
use App\Models\StatusPelamar;
use App\Models\TahapRekrutmen;
use Illuminate\View\View;

class LamaranController extends Controller
{
    public function index(): View
    {
        $pelamarList = Pelamar::with(['loker', 'pendidikanTerakhir', 'statusPelamar', 'tahapRekrutmen'])
            ->orderByDesc('tanggal_apply')
            ->get();

        $lokerOptions = Loker::orderBy('judul_loker')->get();
        $tahapOptions = TahapRekrutmen::orderBy('id_tahap_rekrutmen')->get();
        $statusOptions = StatusPelamar::orderBy('id_status_pelamar')->get();

        return view('admin.lamaran.index', compact('pelamarList', 'lokerOptions', 'tahapOptions', 'statusOptions'));
    }
}
