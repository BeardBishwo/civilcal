<?php
namespace App\Middleware;

class AuthMiddleware {
    public function handle($request, $next) {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in via session
        if (empty($_SESSION['user_id']) && empty($_SESSION['user'])) {
            // Redirect to login page
            header('Location: /login');
            exit;
        }
        
        return $next($request);
    }
}
?>
