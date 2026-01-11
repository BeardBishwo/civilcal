<?php

namespace App\Middleware;

use App\Services\CacheService;

/**
 * Rate Limit Middleware
 * 
 * Prevents abuse by limiting the number of requests a user/IP can make
 * within a specific time window.
 */
class RateLimitMiddleware
{
    private $cache;
    private $limit;
    private $window;

    /**
     * @param int $limit Number of requests allowed
     * @param int $window Time window in seconds
     */
    public function __construct($limit = 60, $window = 60)
    {
        $this->cache = CacheService::getInstance();
        $this->limit = $limit;
        $this->window = $window;
    }

    /**
     * Handle the incoming request.
     *
     * @param array $request
     * @param callable $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        // Identify client by IP
        // If user is logged in, you might prefer User ID
        $identifier = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        // Allow loopback to bypass (optional)
        if ($identifier === '127.0.0.1' || $identifier === '::1') {
            return $next($request);
        }

        // Fixed Window Strategy
        // Key changes every 60 seconds (or whatever window is)
        // e.g. rate_limit:127.0.0.1:2894500
        $timestamp = floor(time() / $this->window);
        $key = "rate_limit:{$identifier}:{$timestamp}";

        // Get current count
        $current = (int)$this->cache->get($key);

        if ($current >= $this->limit) {
            $this->sendTooManyRequests();
            return false;
        }

        // Increment count
        // If it's the first request in this window, set the TTL
        if ($current === 0) {
            $this->cache->set($key, 1, $this->window);
        } else {
            // Just increment. Since the key is time-based, it technically expires automatically
            // when the time window passes, but we keep the TTL for cache cleanup.
            // We do NOT reset the TTL to full window here to avoid "infinite ban"
            // if the cache driver supports TOUCH on set.
            // However, since we change the key every minute, simply setting it again with
            // the *same* window TTL is fine, OR we can calculate remaining time.
            // But simplifying: with a unique key per minute, we don't need complex TTL logic.
            // We just need to ensure the key exists for at least the window duration.
            $this->cache->set($key, $current + 1, $this->window);
        }

        return $next($request);
    }

    private function sendTooManyRequests()
    {
        http_response_code(429);
        header('Retry-After: ' . $this->window);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Too Many Requests',
            'message' => 'You have exceeded the request limit. Please try again later.'
        ]);
        exit;
    }
}
