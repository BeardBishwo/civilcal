<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Models\User;

class AdminMiddleware
{
    public function handle($request, $next)
    {
        // Start session if not already started
        // Start session securely
        \App\Services\Security::startSession();

        // Check if user is authenticated and get fresh data
        $user = $this->getAuthenticatedUser();
        if (!$user) {
            $this->redirectToLogin();
        }

        // Check if user is admin using fresh DB data
        if (!$this->isAdminUser($user)) {
            $this->showAccessDenied();
        }

        return $next($request);
    }

    /**
     * Check if user is authenticated and return fresh user data
     */
    private function getAuthenticatedUser()
    {
        // Check if user session exists
        if (empty($_SESSION['user_id']) && empty($_SESSION['user'])) {
            return false;
        }

        // Verify user still exists in database
        $userModel = new User();
        $userId = $_SESSION['user_id'] ?? ($_SESSION['user']['id'] ?? null);

        if (!$userId) {
            return false;
        }

        $user = $userModel->find($userId);
        if (!$user) {
            // Clear invalid session
            unset($_SESSION['user_id'], $_SESSION['user'], $_SESSION['is_admin']);
            return false;
        }

        return $user;
    }

    /**
     * Check if authenticated user has admin privileges
     */
    private function isAdminUser($user)
    {
        if (!$user) return false;

        // Check admin status from fresh database roles
        $isAdmin = (isset($user['is_admin']) && $user['is_admin']) ||
                   (isset($user['role']) && in_array($user['role'], ['admin', 'super_admin']));

        return $isAdmin;
    }

    /**
     * Redirect to login page
     */
    protected function redirectToLogin()
    {
        $redirectUrl = \app_base_url("/login");
        if (!empty($_SERVER['REQUEST_URI'])) {
            $redirectUrl .= "?redirect=" . urlencode($_SERVER['REQUEST_URI']);
        }
        header("Location: " . $redirectUrl);
        http_response_code(302);
        exit;
    }

    /**
     * Show access denied page
     */
    protected function showAccessDenied()
    {
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
}
