<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::orderBy('nama')->get()
            ->filter(fn (User $u) => ! $u->isProtected() || $u->id_admin === $request->user()->id_admin);

        return view('admin.user.index', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:admin_kepegawaian,email'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        User::create([
            'nama' => $data['nama'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return back()->with('status', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:admin_kepegawaian,email,'.$user->id_admin.',id_admin'],
        ]);

        $user->update($data);

        return back()->with('status', 'Pengguna berhasil diperbarui.');
    }

    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return back()->with('status', 'Password pengguna berhasil diubah.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id_admin === $request->user()->id_admin) {
            return back()->withErrors(['nama' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
        }

        if ($user->isProtected()) {
            return back()->withErrors(['nama' => 'Akun ini tidak dapat dihapus.']);
        }

        $user->delete();

        return back()->with('status', 'Pengguna berhasil dihapus.');
    }
}
