<?php
namespace App\Middleware;

class CorsMiddleware {
    public function handle($request, $next) {
        // Load CORS config
        $services = [];
        if (defined('CONFIG_PATH')) {
            $servicesPath = CONFIG_PATH . '/services.php';
            if (file_exists($servicesPath)) {
                $loaded = include $servicesPath;
                if (is_array($loaded)) { $services = $loaded; }
            }
        }
        $cors = $services['cors'] ?? [];
        $allowedOrigins = $cors['allowed_origins'] ?? ['*'];
        $allowedMethods = $cors['allowed_methods'] ?? ['GET','POST','PUT','PATCH','DELETE','OPTIONS'];
        $allowedHeaders = $cors['allowed_headers'] ?? ['Content-Type','X-Requested-With','X-CSRF-Token','Authorization'];
        $maxAge = (int)($cors['max_age'] ?? 86400);
        $allowCredentials = (bool)($cors['allow_credentials'] ?? true);
        $origin = $_SERVER['HTTP_ORIGIN'] ?? null;

        $usingWildcard = false;
        if (in_array('*', $allowedOrigins, true)) {
            header('Access-Control-Allow-Origin: *');
            $usingWildcard = true;
        } elseif ($origin && in_array($origin, $allowedOrigins, true)) {
            header('Access-Control-Allow-Origin: ' . $origin);
            header('Vary: Origin');
        }

        header('Access-Control-Allow-Methods: ' . implode(', ', $allowedMethods));
        header('Access-Control-Allow-Headers: ' . implode(', ', $allowedHeaders));
        if (!$usingWildcard && $allowCredentials) {
            header('Access-Control-Allow-Credentials: true');
        }
        header('Access-Control-Max-Age: ' . $maxAge);

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
            http_response_code(204);
            exit;
        }

        return $next($request);
    }
}
