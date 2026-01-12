<?php

namespace App\Middleware;

class SecurityHeadersMiddleware
{
    public function handle($request, $next)
    {
        // Add headers
        if (!headers_sent()) {
            header('X-Frame-Options: SAMEORIGIN');
            header('X-Content-Type-Options: nosniff');
            header('X-XSS-Protection: 1; mode=block');
            header('Referrer-Policy: strict-origin-when-cross-origin');
        }

        return $next($request);
    }
}
