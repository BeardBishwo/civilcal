<?php
namespace App\Middleware;

use App\Core\Auth;

class AdminMiddleware
{
    public function handle($request, $next)
    {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is authenticated
        $isAuthenticated = false;
        $isAdmin = false;
        
        // Check HTTP Basic Auth (for API tests)
        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            $userModel = new \App\Models\User();
            $user = $userModel->findByUsername($_SERVER['PHP_AUTH_USER']);
            
            if ($user && password_verify($_SERVER['PHP_AUTH_PW'], $user->password)) {
                $isAuthenticated = true;
                $isAdmin = ($user->is_admin ?? false) || ($user->role ?? '') === 'admin' || ($user->role ?? '') === 'super_admin';
                
                // Set session for this request
                $_SESSION['user_id'] = $user->id;
                $_SESSION['username'] = $user->username;
                $_SESSION['user'] = (array) $user;
                $_SESSION['is_admin'] = $isAdmin;
            }
        }
        
        // Check session-based authentication
        if (!$isAuthenticated && (!empty($_SESSION['user_id']) || !empty($_SESSION['user']))) {
            $isAuthenticated = true;
            // Check admin status from session
            $isAdmin = !empty($_SESSION['is_admin']) || 
                      (!empty($_SESSION['user']['is_admin']) && $_SESSION['user']['is_admin']) ||
                      (!empty($_SESSION['user']['role']) && in_array($_SESSION['user']['role'], ['admin', 'super_admin']));
        }
        
        // Fallback: Check cookie-based authentication
        if (!$isAuthenticated) {
            $user = Auth::check();
            if ($user) {
                $isAuthenticated = true;
                $isAdmin = Auth::isAdmin();
                // Sync session with cookie auth
                $_SESSION['user_id'] = $user->id;
                $_SESSION['username'] = $user->username;
                $_SESSION['user'] = (array) $user;
                $_SESSION['is_admin'] = $isAdmin;
            }
        }
        
        if (!$isAuthenticated) {
            // Admin routes should always return 401 for unauthenticated access
            // This is appropriate for admin management endpoints
            http_response_code(401);
            
            // Check if request prefers JSON response
            $prefersJson = (
                isset($_SERVER['PHP_AUTH_USER']) || // Basic auth was attempted
                (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
                (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
            );
            
            if ($prefersJson) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Authentication required', 'debug' => 'AdminMiddleware_v2']);
            } else {
                // Return simple HTML for browser requests
                echo '<!DOCTYPE html><html><head><title>401 Unauthorized</title></head><body><h1>401 Unauthorized</h1><p>Authentication required to access admin panel.</p><p><a href="/login">Login</a></p><p style="color:gray;font-size:10px;">AdminMiddleware_v2</p></body></html>';
            }
            exit;
        }
        
        // Check if user is admin
        if (!$isAdmin) {
            // Check if this is an API request
            $isApiRequest = (
                strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false ||
                (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
            );
            
            if ($isApiRequest) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Forbidden', 'message' => 'Admin privileges required']);
                exit;
            }
            
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
}
?>
