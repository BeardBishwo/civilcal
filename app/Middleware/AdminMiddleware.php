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
        
        // Check session-based authentication first
        if (!empty($_SESSION['user_id']) || !empty($_SESSION['user'])) {
            $isAuthenticated = true;
            // Check admin status from session
            $isAdmin = !empty($_SESSION['is_admin']) || 
                      (!empty($_SESSION['user']['is_admin']) && $_SESSION['user']['is_admin']) ||
                      (!empty($_SESSION['user']['role']) && in_array($_SESSION['user']['role'], ['admin', 'super_admin']));
        }
        
        // Check HTTP Basic Auth
        if (!$isAuthenticated && isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            $userModel = new \App\Models\User();
            $user = $userModel->findByUsername($_SERVER['PHP_AUTH_USER']);
            if ($user) {
                $userArray = is_array($user) ? $user : (array) $user;
                if (password_verify($_SERVER['PHP_AUTH_PW'], $userArray['password'])) {
                    $isAuthenticated = true;
                    $role = $userArray['role'] ?? 'user';
                    $isAdminRole = $userArray['is_admin'] ?? 0;
                    $isAdmin = ($isAdminRole == 1) || in_array($role, ['admin', 'super_admin']);
                    
                    // Set session for subsequent requests
                    $_SESSION['user_id'] = $userArray['id'];
                    $_SESSION['username'] = $userArray['username'];
                    $_SESSION['user'] = $userArray;
                    $_SESSION['is_admin'] = $isAdmin;
                }
            }
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
            // Redirect to login if not authenticated
            $redirectUrl = app_base_url("/login");
            if (!empty($_SERVER['REQUEST_URI'])) {
                $redirectUrl .= "?redirect=" . urlencode($_SERVER['REQUEST_URI']);
            }
            header("Location: " . $redirectUrl);
            http_response_code(302);
            exit;
        }
        
        // Check if user is admin - return 403 for authenticated non-admin users
        if (!$isAdmin) {
            // Check if this is an API/JSON request
            $isApiRequest = (
                (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
                (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) ||
                strpos($_SERVER['REQUEST_URI'], '/api/') !== false
            );
            
            http_response_code(403);
            
            if ($isApiRequest) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Forbidden - Admin access required']);
            } else {
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
            }
            exit;
        }

        return $next($request);
    }
}
?>
