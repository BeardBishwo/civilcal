<?php
namespace App\Middleware;

class SecurityMiddleware {
    public function handle($request, $next) {
        // Basic security headers
        if (!headers_sent()) {
            header('X-Frame-Options: DENY');
            header('X-XSS-Protection: 1; mode=block');
            header('X-Content-Type-Options: nosniff');
            // Reasonable default CSP; adjust as needed per app assets usage
            $csp = "default-src 'self'; img-src 'self' data: https:; style-src 'self' 'unsafe-inline' https:; script-src 'self' https: 'unsafe-inline'; font-src 'self' https: data:; connect-src 'self' https:; frame-ancestors 'none'";
            header('Content-Security-Policy: ' . $csp);
            // HSTS only if HTTPS
            if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
                header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
            }
            header('Referrer-Policy: no-referrer-when-downgrade');
            header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        }
        return $next($request);
    }
}
