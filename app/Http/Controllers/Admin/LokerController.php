<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLokerRequest;
use App\Models\Jenjang;
use App\Models\Kriteria;
use App\Models\Loker;
use App\Models\PendidikanTerakhir;
use App\Models\Wilayah;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LokerController extends Controller
{
    public function index(): View
    {
        $lokerList = Loker::withCount('pelamar')->orderByDesc('created_at')->get();
        $wilayahOptions = Wilayah::orderBy('nama_wilayah')->get();

        return view('admin.loker.index', compact('lokerList', 'wilayahOptions'));
    }

    public function create(): View
    {
        $wilayahList = Wilayah::orderBy('nama_wilayah')->get();
        $pendidikanList = PendidikanTerakhir::orderBy('id_pendidikan_terakhir')->get();
        $jenjangList = Jenjang::orderBy('nama_jenjang')->get();

        return view('admin.loker.create', compact('wilayahList', 'pendidikanList', 'jenjangList'));
    }

    public function store(StoreLokerRequest $request): RedirectResponse
    {
        $loker = Loker::create($request->validated());

        return redirect()->route('admin.loker.edit', $loker)->with('status', 'Loker berhasil dibuat. Tambahkan kriteria di bawah ini.');
    }

    public function edit(Loker $loker): View
    {
        $loker->load(['kriteria.kriteria']);
        $kriteriaList = Kriteria::orderBy('teks_kriteria')->get();
        $wilayahList = Wilayah::orderBy('nama_wilayah')->get();
        $pendidikanList = PendidikanTerakhir::orderBy('id_pendidikan_terakhir')->get();
        $jenjangList = Jenjang::orderBy('nama_jenjang')->get();

        return view('admin.loker.edit', compact('loker', 'kriteriaList', 'wilayahList', 'pendidikanList', 'jenjangList'));
    }

    public function update(StoreLokerRequest $request, Loker $loker): RedirectResponse
    {
        $loker->update($request->validated());

        return back()->with('status', 'Loker berhasil diperbarui.');
    }

    public function destroy(Loker $loker): RedirectResponse
    {
        if ($loker->pelamar()->exists()) {
            return back()->withErrors(['loker' => 'Loker tidak dapat dihapus karena sudah memiliki pelamar. Tutup lowongan ini sebagai gantinya.']);
        }

        $loker->delete();

        return redirect()->route('admin.loker.index')->with('status', 'Loker berhasil dihapus.');
    }
}
