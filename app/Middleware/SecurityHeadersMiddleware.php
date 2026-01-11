<?php

namespace App\Middleware;

/**
 * Security Headers Middleware
 * 
 * Adds essential security headers to every response to protect against
 * XSS, Clickjacking, and other common attacks.
 */
class SecurityHeadersMiddleware
{
    /**
     * Handle the incoming request.
     *
     * @param array $request
     * @param callable $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        // 1. Prevent Clickjacking
        header('X-Frame-Options: DENY');

        // 2. Protect against MIME sniffing
        header('X-Content-Type-Options: nosniff');

        // 3. Enable XSS Protection (browser fallback)
        header('X-XSS-Protection: 1; mode=block');

        // 4. Strict Transport Security (HSTS) - Force HTTPS
        // Only set this if we are actually on HTTPS to avoid lock-out issues during dev
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }

        // 5. Content Security Policy (Basic Enforced)
        // Adjust this policy based on your external assets (Google Fonts, CDNs, etc.)
        // 'unsafe-inline' and 'unsafe-eval' are often needed by legacy JS libraries; verify before tightening.
        // For now, we set a permissive but reporting policy or just basic restrictions.
        // header("Content-Security-Policy: default-src 'self' https: data: 'unsafe-inline' 'unsafe-eval';");

        // 6. Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');

        // 7. Permissions Policy (Feature Policy)
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

        return $next($request);
    }
}
