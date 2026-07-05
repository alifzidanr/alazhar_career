<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loker;
use App\Models\Pelamar;
use App\Models\StatusPelamar;
use App\Models\TahapRekrutmen;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LamaranController extends Controller
{
    public function index(Request $request): View
    {
        $query = Pelamar::with(['loker', 'pendidikanTerakhir', 'statusPelamar', 'tahapRekrutmen']);

        if ($request->filled('loker')) {
            $query->where('id_loker', $request->integer('loker'));
        }

        if ($request->filled('tahap')) {
            $query->where('id_tahap_rekrutmen', $request->integer('tahap'));
        }

        if ($request->filled('status')) {
            $query->where('id_status_pelamar', $request->integer('status'));
        }

        if ($request->filled('q')) {
            $query->where('nama', 'like', '%'.$request->string('q').'%');
        }

        $pelamarList = $query->orderByDesc('tanggal_apply')->paginate(20)->withQueryString();

        $lokerOptions = Loker::orderBy('judul_loker')->get();
        $tahapOptions = TahapRekrutmen::orderBy('id_tahap_rekrutmen')->get();
        $statusOptions = StatusPelamar::orderBy('id_status_pelamar')->get();

        return view('admin.lamaran.index', compact('pelamarList', 'lokerOptions', 'tahapOptions', 'statusOptions'));
    }
}
