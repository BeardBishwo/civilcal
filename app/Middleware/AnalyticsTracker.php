<?php

namespace App\Middleware;

use App\Core\Logger;

/**
 * Analytics Tracker Middleware
 * 
 * Tracks request duration, memory usage, and basic analytics.
 */
class AnalyticsTracker
{
    public function handle($request, $next)
    {
        $start = microtime(true);
        $startMemory = memory_get_usage();

        // Process request
        $response = $next($request);

        $end = microtime(true);
        $endMemory = memory_get_usage();

        $duration = round(($end - $start) * 1000, 2); // ms
        $memory = round(($endMemory - $startMemory) / 1024, 2); // KB
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        // Log slow requests (> 500ms)
        try {
            if ($duration > 500) {
                if (class_exists('App\Core\Logger')) {
                    Logger::warning("Slow Request: {$method} {$uri}", [
                        'duration_ms' => $duration,
                        'memory_kb' => $memory,
                        'ip' => $ip
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Silently ignore logging errors to prevent breaking the request
            // error_log("Analytics error: " . $e->getMessage()); 
        }

        // In a real APM, we would send this to StatsD or Prometheus
        // For now, we can log it to a separate 'metrics' channel if highly active,
        // or just debug log.
        // if (class_exists('App\Core\Logger')) Logger::get('metrics')->info("Request", compact('method', 'uri', 'duration', 'memory', 'ip'));

        return $response;
    }
}
