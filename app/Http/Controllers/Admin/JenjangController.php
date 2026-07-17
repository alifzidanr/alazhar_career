<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jenjang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JenjangController extends Controller
{
    public function index(): View
    {
        $jenjangList = Jenjang::withCount('loker')->orderBy('nama_jenjang')->get();

        return view('admin.jenjang.index', compact('jenjangList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_jenjang' => ['required', 'string', 'max:100', 'unique:jenjang,nama_jenjang'],
        ]);

        Jenjang::create($data);

        return back()->with('status', 'Jenjang berhasil ditambahkan.');
    }

    public function update(Request $request, Jenjang $jenjang): RedirectResponse
    {
        $data = $request->validate([
            'nama_jenjang' => ['required', 'string', 'max:100', 'unique:jenjang,nama_jenjang,'.$jenjang->id_jenjang.',id_jenjang'],
        ]);

        $jenjang->update($data);

        return back()->with('status', 'Jenjang berhasil diperbarui.');
    }

    public function destroy(Jenjang $jenjang): RedirectResponse
    {
        if ($jenjang->loker()->exists()) {
            return back()->withErrors(['jenjang' => 'Jenjang tidak dapat dihapus karena masih dipakai oleh loker.']);
        }

        $jenjang->delete();

        return back()->with('status', 'Jenjang berhasil dihapus.');
    }
}
