<?php

namespace App\Middleware;

class CsrfMiddleware
{
    public function handle($request, $next)
    {
        // Generate CSRF token if not exists
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        $uri = $request['uri'] ?? '';
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        
        if (in_array($method, ['POST','PUT','PATCH','DELETE'], true)) {
            // Skip CSRF validation for API requests (JSON content-type with PUT/PATCH/DELETE)
            $isJsonApi = (
                (isset($_SERVER['CONTENT_TYPE']) && stripos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) &&
                in_array($method, ['PUT', 'PATCH', 'DELETE'])
            ) || (
                !empty($_SESSION['api_authenticated']) // Session marked as API login
            ) || (
                strpos($uri, '/api/') !== false // API routes
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
                }
            }
        }

        // Expose token for front-end via header
        if (!headers_sent() && !empty($_SESSION['csrf_token'])) {
            header('X-CSRF-Token: ' . $_SESSION['csrf_token']);
        }

        return $next($request);
    }

    private function validToken($token)
    {
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}