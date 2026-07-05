<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KriteriaController extends Controller
{
    public function index(): View
    {
        $kriteriaList = Kriteria::withCount('kriteriaLoker')->orderBy('teks_kriteria')->get();

        return view('admin.kriteria.index', compact('kriteriaList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'teks_kriteria' => ['required', 'string', 'max:255', 'unique:kriteria,teks_kriteria'],
        ]);

        Kriteria::create($data);

        return back()->with('status', 'Kriteria berhasil ditambahkan.');
    }

    public function update(Request $request, Kriteria $kriteria): RedirectResponse
    {
        $data = $request->validate([
            'teks_kriteria' => ['required', 'string', 'max:255', 'unique:kriteria,teks_kriteria,'.$kriteria->id_kriteria.',id_kriteria'],
        ]);

        $kriteria->update($data);

        return back()->with('status', 'Kriteria berhasil diperbarui.');
    }

    public function destroy(Kriteria $kriteria): RedirectResponse
    {
        $kriteria->delete();

        return back()->with('status', 'Kriteria berhasil dihapus.');
    }
}
