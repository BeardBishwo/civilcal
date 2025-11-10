<?php
namespace App\Middleware;

class GuestMiddleware {
    public function handle(): bool {
        // Allow guests (users not logged in)
        // If user is logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            // Get base path from script name
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $scriptDir = dirname($scriptName);
            if (substr($scriptDir, -7) === '/public') {
                $scriptDir = substr($scriptDir, 0, -7);
            }
            $basePath = ($scriptDir === '/' || $scriptDir === '') ? '' : $scriptDir;
            
            header('Location: ' . $basePath . '/dashboard');
            exit;
        }
        return true;
    }
}
