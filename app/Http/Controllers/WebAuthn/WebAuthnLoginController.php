<?php

namespace App\Http\Controllers\WebAuthn;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Laragear\WebAuthn\Http\Requests\AssertedRequest;
use Laragear\WebAuthn\Http\Requests\AssertionRequest;

class WebAuthnLoginController
{
    /**
     * Returns the challenge to assertion. No email is sent, so the device
     * offers whichever discoverable passkeys it holds for this site.
     */
    public function options(AssertionRequest $request): Responsable
    {
        return $request->toVerify();
    }

    /**
     * Log the user in.
     */
    public function login(AssertedRequest $request): JsonResponse
    {
        if (! $request->login()) {
            return response()->json(['message' => 'Passkey tidak dikenali.'], 422);
        }

        return response()->json([
            'redirect' => redirect()->intended(route('admin.pelamar.index', absolute: false))->getTargetUrl(),
        ]);
    }
}
