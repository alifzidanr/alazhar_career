<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLokerRequest;
use App\Models\Kriteria;
use App\Models\Loker;
use App\Models\Lokasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LokerController extends Controller
{
    public function index(): View
    {
        $lokerList = Loker::withCount('pelamar')->orderByDesc('created_at')->get();
        $lokasiOptions = Lokasi::orderBy('nama_lokasi')->get();

        return view('admin.loker.index', compact('lokerList', 'lokasiOptions'));
    }

    public function create(): View
    {
        $lokasiList = Lokasi::orderBy('nama_lokasi')->get();

        return view('admin.loker.create', compact('lokasiList'));
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
        $lokasiList = Lokasi::orderBy('nama_lokasi')->get();

        return view('admin.loker.edit', compact('loker', 'kriteriaList', 'lokasiList'));
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
