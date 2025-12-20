<?php

namespace App\Middleware;

class CsrfMiddleware
{
    protected function ensureToken()
    {
        \App\Services\Security::generateCsrfToken();
    }

    protected function validToken(?string $token): bool
    {
        return \App\Services\Security::validateCsrfToken($token);
    }

    public function handle($request, $next)
    {
        $this->ensureToken();

        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $headerToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            $formToken = $_POST['csrf_token'] ?? null;
            $token = $headerToken ?: $formToken;


            // DEBUG: Log token details
            /*
            error_log("CSRF DEBUG - Request Method: " . $method);
            error_log("CSRF DEBUG - Header token: " . ($headerToken ?? 'NULL'));
            error_log("CSRF DEBUG - Form token: " . ($formToken ?? 'NULL'));
            */

            // Let the security service handle detailed validation checks inside validateCsrfToken
            // But here we need to manually pass the found token from headers/post if we want to be explicit,
            // OR just let Security::validateCsrfToken(null) find it itself.
            // Current middleware logic extracts it manually.
            
            if (!\App\Services\Security::validateCsrfToken($token)) {
                http_response_code(419);
                // Always return JSON for AJAX forms (admin panel uses class="ajax-form")
                // Check for XMLHttpRequest header or admin routes in addition to API routes
                // NOTE: URI includes base path like /admin/... so use strpos
                $uri = $request['uri'] ?? '';
                $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
                $isAdminRoute = strpos($uri, '/admin') !== false;
                $isApiRoute = strpos($uri, '/api') !== false;
                $isJson = $isAjax
                    || $isAdminRoute
                    || $isApiRoute
                    || (($_SERVER['HTTP_ACCEPT'] ?? '') && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
                    || (($_SERVER['CONTENT_TYPE'] ?? '') && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== false);
                if ($isJson) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Invalid or missing CSRF token']);
                } else {
                    echo 'Invalid or missing CSRF token';
                }
                return null;
            }
        }

        // Expose token for front-end via header
        if (!headers_sent()) {
            $token = \App\Services\Security::generateCsrfToken(); // Ensure one exists
            header('X-CSRF-Token: ' . $token);
        }

        return $next($request);
    }
}
