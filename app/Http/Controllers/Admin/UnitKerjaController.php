<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnitKerja;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitKerjaController extends Controller
{
    public function index(): View
    {
        $unitKerjaList = UnitKerja::withCount(['kriteriaLoker', 'orientasi'])->orderBy('nama_unit')->get();

        return view('admin.unit-kerja.index', compact('unitKerjaList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_unit' => ['required', 'string', 'max:100', 'unique:unit_kerja,nama_unit'],
        ]);

        UnitKerja::create($data);

        return back()->with('status', 'Unit Kerja berhasil ditambahkan.');
    }

    public function update(Request $request, UnitKerja $unitKerja): RedirectResponse
    {
        $data = $request->validate([
            'nama_unit' => ['required', 'string', 'max:100', 'unique:unit_kerja,nama_unit,'.$unitKerja->id_unit_kerja.',id_unit_kerja'],
        ]);

        $unitKerja->update($data);

        return back()->with('status', 'Unit Kerja berhasil diperbarui.');
    }

    public function destroy(UnitKerja $unitKerja): RedirectResponse
    {
        if ($unitKerja->kriteriaLoker()->exists() || $unitKerja->orientasi()->exists()) {
            return back()->withErrors(['unit_kerja' => 'Unit Kerja tidak dapat dihapus karena masih dipakai oleh loker atau data orientasi pelamar.']);
        }

        $unitKerja->delete();

        return back()->with('status', 'Unit Kerja berhasil dihapus.');
    }
}
