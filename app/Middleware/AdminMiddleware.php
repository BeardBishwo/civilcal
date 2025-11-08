<?php
namespace App\Middleware;

use App\Core\Auth;

class AdminMiddleware
{
    public function handle($request, $next)
    {
        // Create Auth instance
        $auth = new Auth();
        
        // Check if user is authenticated
        if (!$auth->check()) {
            // Redirect to login if not authenticated
            header("Location: /login");
            exit;
        }

        $user = $auth->user();
        
        // Check if user is admin
        if (!$user || !$this->isAdmin($user)) {
            http_response_code(403);
            echo '
            <!DOCTYPE html>
            <html>
            <head>
                <title>Access Denied</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            </head>
            <body class="bg-light">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="text-center mt-5">
                                <div class="card shadow">
                                    <div class="card-body p-5">
                                        <h1 class="text-danger mb-4">
                                            <i class="bi bi-shield-exclamation"></i>
                                        </h1>
                                        <h3 class="card-title mb-3">Access Denied</h3>
                                        <p class="card-text">You do not have permission to access the admin panel.</p>
                                        <p class="text-muted">Administrator privileges are required to view this page.</p>
                                        <div class="mt-4">
                                            <a href="/" class="btn btn-primary me-2">Go to Homepage</a>
                                            <a href="/logout" class="btn btn-outline-secondary">Logout</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </body>
            </html>';
            exit;
        }

        return $next($request);
    }
    
    /**
     * Check if user has admin privileges
     */
    private function isAdmin($user)
    {
        return $user && (isset($user['role']) && $user['role'] === 'admin');
    }
}
?>
