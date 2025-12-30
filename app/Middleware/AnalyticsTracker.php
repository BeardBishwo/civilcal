<?php

namespace App\Middleware;

use App\Models\Analytics;

class AnalyticsTracker
{
    public function handle($request, $next)
    {
        // Track the visit
        try {
            $this->trackPageView();
        } catch (\Exception $e) {
            // Silently fail to ensure analytics never breaks the site
            // In development you might want to log this:
            // error_log("Analytics Error: " . $e->getMessage());
        }

        // Proceed with request
        return $next($request);
    }

    private function trackPageView()
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Exclude static assets, API calls, and system paths
        $exclusions = [
            '/api/', 
            '/assets/', 
            '/storage/', 
            'favicon.ico', 
            '.css', 
            '.js', 
            '.png', 
            '.jpg', 
            '.svg'
        ];
        
        foreach ($exclusions as $exclude) {
            if (strpos($uri, $exclude) !== false) {
                return;
            }
        }
        
        // Clean URL (remove query string for category matching)
        $cleanPath = parse_url($uri, PHP_URL_PATH);
        
        // Determine category
        $category = 'page';
        if (strpos($cleanPath, '/calculators/') !== false) {
            $category = 'calculator';
        } elseif (strpos($cleanPath, '/admin') !== false) {
            $category = 'admin';
        } elseif ($cleanPath === '/' || $cleanPath === '/index.php') {
            $category = 'home';
        }

        // Get User ID
        $userId = $_SESSION['user_id'] ?? null;

        $analytics = new Analytics();
        $analytics->track([
            'event_type' => 'page_view',
            'event_category' => $category,
            'page_url' => $uri,
            'user_id' => $userId,
            'session_id' => session_id(),
            'ip_address' => $this->getIpAddress(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'referrer' => $_SERVER['HTTP_REFERER'] ?? null
        ]);
    }

    private function getIpAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? null;
        }
    }
}
