<?php

namespace App\Middleware;

class CsrfMiddleware
{
    protected function ensureToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_expiry'] = time() + 3600; // 1 hour
        }
    }

    protected function validToken(?string $token): bool
    {
        if (empty($_SESSION['csrf_token']) || empty($_SESSION['csrf_expiry'])) {
            return false;
        }
        if (time() > ($_SESSION['csrf_expiry'] ?? 0)) {
            unset($_SESSION['csrf_token'], $_SESSION['csrf_expiry']);
            return false;
        }
        return $token && hash_equals($_SESSION['csrf_token'], $token);
    }

    public function handle($request, $next)
    {
        $this->ensureToken();

        $uri = $request['uri'] ?? '';
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
<<<<<<< HEAD
        if (in_array($method, ['POST','PUT','PATCH','DELETE'], true)) {
            // Skip CSRF validation for API requests (JSON content-type with PUT/PATCH/DELETE)
            $isJsonApi = (
                (isset($_SERVER['CONTENT_TYPE']) && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) &&
                in_array($method, ['PUT', 'PATCH', 'DELETE'])
            ) || (
                !empty($_SESSION['api_authenticated']) // Session marked as API login
            );
            
            if (!$isJsonApi) {
                $headerToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
                $formToken = $_POST['csrf_token'] ?? null;
                $token = $headerToken ?: $formToken;

                if (!$this->validToken($token)) {
                    http_response_code(419);
                    $isJson = (($_SERVER['HTTP_ACCEPT'] ?? '') && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
                           || (($_SERVER['CONTENT_TYPE'] ?? '') && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
                           || stripos($request['uri'] ?? '', '/api') === 0;
                    if ($isJson) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => 'Invalid or missing CSRF token']);
                    } else {
                        echo 'Invalid or missing CSRF token';
                    }
                    return null;
=======
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true) && strpos($uri, '/api') === false) {
            $headerToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            $formToken = $_POST['csrf_token'] ?? null;
            $token = $headerToken ?: $formToken;

            if (!$this->validToken($token)) {
                http_response_code(419);
                // Always return JSON for AJAX forms (admin panel uses class="ajax-form")
                // Check for XMLHttpRequest header or admin routes in addition to API routes
                // NOTE: URI includes base path like /Bishwo_Calculator/admin/... so use strpos instead of stripos === 0
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
>>>>>>> temp-branch
                }
            }
        }

        // Expose token for front-end via header
        if (!headers_sent() && !empty($_SESSION['csrf_token'])) {
            header('X-CSRF-Token: ' . $_SESSION['csrf_token']);
        }

        return $next($request);
    }
}
