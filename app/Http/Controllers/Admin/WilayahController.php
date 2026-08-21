<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loker;
use App\Models\Wilayah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WilayahController extends Controller
{
    public function index(): View
    {
        $wilayahList = Wilayah::orderBy('nama_wilayah')->get()->map(function (Wilayah $wilayah) {
            $wilayah->dipakai_count = Loker::where('wilayah', $wilayah->nama_wilayah)->count();

            return $wilayah;
        });

        return view('admin.wilayah.index', compact('wilayahList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_wilayah' => ['required', 'string', 'max:150', 'unique:wilayah,nama_wilayah'],
        ]);

        Wilayah::create($data);

        return back()->with('status', 'Wilayah berhasil ditambahkan.');
    }

    public function update(Request $request, Wilayah $wilayah): RedirectResponse
    {
        $data = $request->validate([
            'nama_wilayah' => ['required', 'string', 'max:150', 'unique:wilayah,nama_wilayah,'.$wilayah->id_wilayah.',id_wilayah'],
        ]);

        Loker::where('wilayah', $wilayah->nama_wilayah)->update(['wilayah' => $data['nama_wilayah']]);

        $wilayah->update($data);

        return back()->with('status', 'Wilayah berhasil diperbarui.');
    }

    public function destroy(Wilayah $wilayah): RedirectResponse
    {
        $wilayah->delete();

        return back()->with('status', 'Wilayah berhasil dihapus.');
    }
}
