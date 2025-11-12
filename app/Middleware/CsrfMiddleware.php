<?php
namespace App\Middleware;

class CsrfMiddleware {
    protected function ensureToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_expiry'] = time() + 3600; // 1 hour
        }
    }

    protected function validToken(?string $token): bool {
        if (empty($_SESSION['csrf_token']) || empty($_SESSION['csrf_expiry'])) {
            return false;
        }
        if (time() > ($_SESSION['csrf_expiry'] ?? 0)) {
            unset($_SESSION['csrf_token'], $_SESSION['csrf_expiry']);
            return false;
        }
        return $token && hash_equals($_SESSION['csrf_token'], $token);
    }

    public function handle($request, $next) {
        $this->ensureToken();

        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if (in_array($method, ['POST','PUT','PATCH','DELETE'], true)) {
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

        // Expose token for front-end via header
        if (!headers_sent() && !empty($_SESSION['csrf_token'])) {
            header('X-CSRF-Token: ' . $_SESSION['csrf_token']);
        }

        return $next($request);
    }
}
