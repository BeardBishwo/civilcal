<?php

namespace App\Middleware;

use App\Core\Auth;

class AuthMiddleware
{
    public function handle($request, $next)
    {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check for session-based authentication (current system)
        $isAuthenticated = false;
        
        // Check HTTP Basic Auth (for API tests)
        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            $userModel = new \App\Models\User();
            $user = $userModel->findByUsername($_SERVER['PHP_AUTH_USER']);
            
            if ($user && password_verify($_SERVER['PHP_AUTH_PW'], $user->password)) {
                $isAuthenticated = true;
                
                // Set session for this request
                $_SESSION['user_id'] = $user->id;
                $_SESSION['username'] = $user->username;
                $_SESSION['user'] = (array) $user;
                $_SESSION['is_admin'] = $user->is_admin ?? false;
            }
        }
        
        // Check if user is logged in via session
        if (!$isAuthenticated && (!empty($_SESSION['user_id']) || !empty($_SESSION['user']))) {
            $isAuthenticated = true;
        }
        
        // Fallback: Check cookie-based authentication (Auth class)
        if (!$isAuthenticated) {
            $user = Auth::check();
            if ($user) {
                $isAuthenticated = true;
                $_SESSION['user_id'] = $user->id;
                $_SESSION['user'] = (array) $user;
                $_SESSION['is_admin'] = $user->is_admin ?? false;
            }
        }
        
        if (!$isAuthenticated) {
            $requestUri = $_SERVER['REQUEST_URI'] ?? '';
            
            // Determine if this should return 401 (API/endpoints) or 302 (browser pages)
            $isApiRequest = (
                strpos($requestUri, '/api/') !== false ||
                strpos($requestUri, '/profile') !== false ||
                (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
                isset($_SERVER['PHP_AUTH_USER']) // HTTP Basic Auth attempted
            );
            
            // Admin management endpoints (not dashboard) should return 401
            $isAdminEndpoint = (
                preg_match('#/admin/(users|settings|calculators|modules|plugins|themes|logs|backup)#', $requestUri)
            );
            
            if ($isApiRequest || $isAdminEndpoint) {
                // Return 401 for API/AJAX/Admin management endpoints
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Unauthorized', 'message' => 'Authentication required']);
                exit;
            }
            
            // Determine the correct base path for redirect
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $scriptDir = dirname($scriptName);
            if (substr($scriptDir, -7) === '/public') {
                $scriptDir = substr($scriptDir, 0, -7);
            }
            $basePath = ($scriptDir === '/' || $scriptDir === '') ? '' : $scriptDir;
            
            // Redirect to login page (for browser requests)
            header('Location: ' . $basePath . '/login');
            exit;
        }

        return $next($request);
    }
}
