<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * trustProxies('*') is needed for X-Forwarded-Proto behind the Cloudflare
 * Tunnel, but it also makes $request->ip() follow X-Forwarded-For, which the
 * visitor controls: rotating it gave every request a fresh IP and so a fresh
 * rate-limit bucket (login, lamaran, cek-nik, passkey). Cloudflare puts the
 * real visitor in CF-Connecting-IP; otherwise the TCP peer is used. Dropping
 * X-Forwarded-For makes ip() (and so every throttle) use that address.
 */
class ResolveClientIp
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->remove('X-Forwarded-For');

        $cfIp = $request->headers->get('CF-Connecting-IP');
        if ($cfIp !== null && filter_var($cfIp, FILTER_VALIDATE_IP) !== false) {
            $request->server->set('REMOTE_ADDR', $cfIp);
        }

        return $next($request);
    }
}
