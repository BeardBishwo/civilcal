<?php

namespace App\Middleware;

class SecurityMiddleware
{
    public function handle($request, $next)
    {
        // 1. IP Whitelisting for Admin Routes
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($uri, '/admin') !== false) {
            $ipWhitelistEnabled = \App\Services\SettingsService::get('ip_whitelist_enabled', '0') === '1';
            
            if ($ipWhitelistEnabled) {
                $whitelist = \App\Services\SettingsService::get('ip_whitelist', '');
                $whitelistedIps = array_filter(array_map('trim', explode(',', $whitelist)));
                $clientIp = $_SERVER['REMOTE_ADDR'] ?? '';

                // Always allow localhost
                $whitelistedIps[] = '127.0.0.1';
                $whitelistedIps[] = '::1';

                if (!empty($clientIp) && !in_array($clientIp, $whitelistedIps)) {
                    error_log("Access denied to admin for IP: {$clientIp}");
                    http_response_code(403);
                    header('Content-Type: text/plain');
                    die("Access Denied: Your IP ({$clientIp}) is not whitelisted for admin access.");
                }
            }
        }

        // 2. Security Headers (only if enabled in settings)
        $headersEnabled = \App\Services\SettingsService::get('security_headers', '1') === '1';
        
        if ($headersEnabled && !headers_sent()) {
            header('X-Frame-Options: SAMEORIGIN');
            header('X-XSS-Protection: 1; mode=block');
            header('X-Content-Type-Options: nosniff');
            
            // Comprehensive CSP
            $csp = "default-src 'self'; img-src 'self' data: https: cdn.ckeditor.com; style-src 'self' 'unsafe-inline' https: cdn.ckeditor.com cdnjs.cloudflare.com; script-src 'self' https: 'unsafe-inline' cdn.ckeditor.com cdn.jsdelivr.net; font-src 'self' https: data: cdnjs.cloudflare.com; connect-src 'self' https:; frame-ancestors 'self'";
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
