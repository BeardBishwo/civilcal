<?php

namespace App\Middleware;

use App\Services\SettingsService;

class MaintenanceMiddleware
{
    public function handle($request, $next)
    {
        $maintenanceMode = SettingsService::get('maintenance_mode', '0') === '1';
        $isAdminPath = strpos($request['uri'], '/admin') === 0;
        $isLoginPath = strpos($request['uri'], '/login') === 0;

        if ($maintenanceMode && !$isAdminPath && !$isLoginPath) {
            // Allow logged in admins to see the site anyway
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                return $next($request);
            }

            $message = SettingsService::get('maintenance_message', 'Site under maintenance. We\'ll be back soon!');
            
            http_response_code(503);
            
            // Try to load a custom maintenance view if it exists
            $maintenanceView = BASE_PATH . '/themes/default/views/errors/maintenance.php';
            if (file_exists($maintenanceView)) {
                require $maintenanceView;
            } else {
                echo "<h1>Maintenance Mode</h1>";
                echo "<p>" . htmlspecialchars($message) . "</p>";
            }
            exit;
        }

        return $next($request);
    }
}
