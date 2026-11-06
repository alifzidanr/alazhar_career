<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Baseline browser hardening: no framing by other sites (clickjacking on
 * the admin panel), no MIME sniffing of served berkas, and no full URLs
 * leaking to other origins via Referer.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN', false);
        $response->headers->set('X-Content-Type-Options', 'nosniff', false);
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin', false);
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()', false);

        return $response;
    }
}
