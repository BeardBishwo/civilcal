<?php

namespace App\Middleware;

class AuthMiddleware
{
    public function handle($request, $next)
    {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
<<<<<<< HEAD
        
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
=======

        // Check if user is logged in via session
        $authenticated = false;
        $httpBasicAuthProvided = isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']);
        $httpBasicAuthFailed = false;

        // If HTTP Basic Auth is provided, prioritize it over session
        // This ensures that new credentials override any existing session
        if ($httpBasicAuthProvided) {
            $username = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];

            $userModel = new \App\Models\User();
            $user = $userModel->findByUsername($username);

>>>>>>> temp-branch
            if ($user) {
                $userArray = is_array($user) ? $user : (array) $user;
                if (password_verify($password, $userArray['password'])) {
                    // Clear old session and set new session for this request
                    session_unset(); // Clear all session variables
                    $_SESSION['user_id'] = $userArray['id'];
                    $_SESSION['user'] = $userArray;
                    $authenticated = true;
                } else {
                    // Wrong password - HTTP Basic Auth failed
                    $authenticated = false;
                    $httpBasicAuthFailed = true;
                }
            } else {
                // User not found - HTTP Basic Auth failed
                $authenticated = false;
                $httpBasicAuthFailed = true;
            }
        }
<<<<<<< HEAD
        
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
=======

        // Fallback to session-based auth if no HTTP Basic Auth provided
        if (!$authenticated && !$httpBasicAuthProvided) {
            if (!empty($_SESSION['user_id']) || !empty($_SESSION['user'])) {
                $authenticated = true;
            }
>>>>>>> temp-branch
        }

        if (!$authenticated) {
            // If HTTP Basic Auth was provided but failed, return 401
            if ($httpBasicAuthFailed) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Unauthorized - Invalid credentials']);
                exit;
            }
            // Check if this is an API request (JSON or has /api/ or /admin/ in path)
            // Admin routes use AJAX forms that expect JSON responses
            $isApiRequest = (
                (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
                (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) ||
                strpos($_SERVER['REQUEST_URI'], '/api/') !== false ||
                strpos($_SERVER['REQUEST_URI'], '/admin/') !== false
            );

            if ($isApiRequest) {
                // Return 401 for API requests
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            } else {
                // Redirect to login page for web requests
                // Get the base path from SCRIPT_NAME to handle subdirectory installations
                $scriptName = $_SERVER['SCRIPT_NAME'];
                $basePath = dirname(dirname($scriptName)); // Remove /index.php to get base path
                if ($basePath === '/' || $basePath === '\\') {
                    $basePath = '';
                }
                http_response_code(302);
                header('Location: ' . $basePath . '/login');
                exit;
            }
        }

        return $next($request);
    }
}
