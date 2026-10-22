<?php

namespace App\Http\Controllers\WebAuthn;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laragear\WebAuthn\Http\Requests\AttestationRequest;
use Laragear\WebAuthn\Http\Requests\AttestedRequest;

class WebAuthnRegisterController
{
    /**
     * Returns a challenge to be verified by the user device.
     *
     * Userless (discoverable) credentials are required so the login page can
     * offer a one-tap "Masuk dengan Passkey" without asking for the email first.
     */
    public function options(AttestationRequest $request): Responsable
    {
        return $request
            ->userless()
            ->toCreate();
    }

    /**
     * Registers a device for further WebAuthn authentication.
     */
    public function register(AttestedRequest $request): JsonResponse
    {
        $alias = $request->validate(['alias' => 'nullable|string|max:100'])['alias'] ?? null;

        $request->save(['alias' => $alias ?: 'Passkey '.now()->format('d/m/Y')]);

        session()->flash('status', 'Passkey berhasil didaftarkan.');

        return response()->json(status: 201);
    }

    /**
     * Removes one of the current user's passkeys.
     */
    public function destroy(Request $request, string $credential): RedirectResponse
    {
        $request->user()->webAuthnCredentials()->whereKey($credential)->firstOrFail()->delete();

        return back()->with('status', 'Passkey berhasil dihapus.');
    }
}
