<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loker;
use App\Models\Lokasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LokasiController extends Controller
{
    public function index(): View
    {
        $lokasiList = Lokasi::orderBy('nama_lokasi')->get()->map(function (Lokasi $lokasi) {
            $lokasi->dipakai_count = Loker::where('lokasi', $lokasi->nama_lokasi)->count();

            return $lokasi;
        });

        return view('admin.lokasi.index', compact('lokasiList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_lokasi' => ['required', 'string', 'max:150', 'unique:lokasi,nama_lokasi'],
        ]);

        Lokasi::create($data);

        return back()->with('status', 'Lokasi berhasil ditambahkan.');
    }

    public function update(Request $request, Lokasi $lokasi): RedirectResponse
    {
        $data = $request->validate([
            'nama_lokasi' => ['required', 'string', 'max:150', 'unique:lokasi,nama_lokasi,'.$lokasi->id_lokasi.',id_lokasi'],
        ]);

        Loker::where('lokasi', $lokasi->nama_lokasi)->update(['lokasi' => $data['nama_lokasi']]);

        $lokasi->update($data);

        return back()->with('status', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Lokasi $lokasi): RedirectResponse
    {
        $lokasi->delete();

        return back()->with('status', 'Lokasi berhasil dihapus.');
    }
}
