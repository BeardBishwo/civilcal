<?php
namespace App\Middleware;

class RateLimitMiddleware {
    private function key(string $route): string {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $minute = date('YmdHi');
        return $ip . '|' . $route . '|' . $minute;
    }

    private function storageDir(): string {
        $base = defined('STORAGE_PATH') ? STORAGE_PATH : (defined('BASE_PATH') ? BASE_PATH . '/storage' : sys_get_temp_dir());
        return rtrim($base, '/\\') . '/cache/ratelimit';
    }

    private function incr(string $key): int {
        $dir = $this->storageDir();
        if (!is_dir($dir)) { @mkdir($dir, 0755, true); }
        $file = $dir . '/' . sha1($key) . '.cnt';
        $count = 0;
        if (is_file($file)) {
            $count = (int)@file_get_contents($file);
        }
        $count++;
        @file_put_contents($file, (string)$count, LOCK_EX);
        return $count;
    }

    public function handle($request, $next) {
        // Check if global rate limiting is enabled
        $rateLimitEnabled = \App\Services\SettingsService::get('rate_limiting', '1') === '1';
        if (!$rateLimitEnabled) {
            return $next($request);
        }

        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

        // Check if API is enabled
        $apiEnabled = \App\Services\SettingsService::get('api_enabled', '1') === '1';
        if (!$apiEnabled && (strpos($uri, '/api') === 0 || strpos($uri, '/api/') === 0)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'API is currently disabled']);
            return;
        }

        // Get limits from settings or defaults
        $baseLimit = (int)\App\Services\SettingsService::get('api_rate_limit', 60);
        $limit = ($method === 'GET') ? $baseLimit * 2 : $baseLimit;

        // Tighten for plugin admin endpoints
        if (strpos($uri, '/admin/plugins') === 0) {
            $limit = ($method === 'GET') ? 60 : 30;
        }
        $key = $this->key($uri . '|' . $method);
        $count = $this->incr($key);
        header('X-RateLimit-Limit: ' . $limit);
        header('X-RateLimit-Remaining: ' . max(0, $limit - $count));
        if ($count > $limit) {
            http_response_code(429);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Too Many Requests']);
            return;
        }
        return $next($request);
    }
}
