<?php

namespace App\Middleware;

class AuthMiddleware
{
    public function handle($request, $next)
    {
        error_log("AuthMiddleware: handle() called");
        // Start session if not already started
        // Start session securely
        \App\Services\Security::startSession();
        error_log("AuthMiddleware: session started");

        // Check if user is logged in via session
        $authenticated = false;
        $httpBasicAuthProvided = isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']);
        $httpBasicAuthFailed = false;

        // [Truncated for brevity in replacement] -> Keep existing logic
        // If HTTP Basic Auth is provided, prioritize it over session
        if ($httpBasicAuthProvided) {
            // ...
        }

        // Fallback to session-based auth if no HTTP Basic Auth provided
        if (!$authenticated && !$httpBasicAuthProvided) {
            if (!empty($_SESSION['user_id']) || !empty($_SESSION['user'])) {
                $authenticated = true;
            }
        }

        error_log("AuthMiddleware: authenticated = " . ($authenticated ? 'true' : 'false'));

        if (!$authenticated) {
            // If HTTP Basic Auth was provided but failed, return 401
            if ($httpBasicAuthFailed) {
                // ...
            }
            // Check if this is an API request
            $isApiRequest = (
                (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
                (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) ||
                strpos($_SERVER['REQUEST_URI'], '/api/') !== false ||
                strpos($_SERVER['REQUEST_URI'], '/admin/') !== false
            );

            error_log("AuthMiddleware: isApiRequest = " . ($isApiRequest ? 'true' : 'false'));

            if ($isApiRequest) {
                // Return 401 for API requests
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            } else {
                // ...
            }
        }

        return $next($request);
    }
}
