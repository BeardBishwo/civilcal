<?php
namespace App\Middleware;

use App\Core\Auth;

class AuthMiddleware {
    public function handle($request, $next) {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check for session-based authentication (current system)
        $isAuthenticated = false;
        
        // Check if user is logged in via session
        if (!empty($_SESSION['user_id']) || !empty($_SESSION['user'])) {
            $isAuthenticated = true;
        }
        
        // Fallback: Check cookie-based authentication (Auth class)
        if (!$isAuthenticated) {
            $user = Auth::check();
            if ($user) {
                $isAuthenticated = true;
                // Sync session with cookie auth if needed
                $_SESSION['user_id'] = $user->id;
                $_SESSION['username'] = $user->username;
                $_SESSION['user'] = (array) $user;
                $_SESSION['is_admin'] = $user->is_admin ?? false;
            }
        }
        
        if (!$isAuthenticated) {
            // Determine the correct base path for redirect
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $scriptDir = dirname($scriptName);
            if (substr($scriptDir, -7) === '/public') {
                $scriptDir = substr($scriptDir, 0, -7);
            }
            $basePath = ($scriptDir === '/' || $scriptDir === '') ? '' : $scriptDir;
            
            // Redirect to login page
            header('Location: ' . $basePath . '/login');
            exit;
        }
        
        return $next($request);
    }
}
