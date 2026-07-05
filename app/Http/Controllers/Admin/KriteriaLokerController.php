<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KriteriaLoker;
use App\Models\Loker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KriteriaLokerController extends Controller
{
    public function store(Request $request, Loker $loker): RedirectResponse
    {
        $data = $request->validate([
            'id_kriteria' => ['nullable', 'exists:kriteria,id_kriteria'],
            'bobot' => ['required', 'in:wajib,diutamakan,nilai_tambah'],
        ]);

        $loker->kriteria()->create($data);

        return back()->with('status', 'Kriteria berhasil ditambahkan.');
    }

    public function destroy(Loker $loker, KriteriaLoker $kriteria): RedirectResponse
    {
        abort_unless($kriteria->id_loker === $loker->id_loker, 404);

        $kriteria->delete();

        return back()->with('status', 'Kriteria berhasil dihapus.');
    }
}
