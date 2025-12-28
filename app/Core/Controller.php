<?php

namespace App\Core;

class Controller
{
    protected $db;
    protected $auth;
    protected $theme;
    protected $view;
    protected $session;

    public function __construct()
    {
        // Load configuration first
        require_once __DIR__ . '/../Config/config.php';
        require_once __DIR__ . '/../Config/db.php';
        require_once __DIR__ . '/../Helpers/functions.php';

        // Initialize database connection
        $this->db = \App\Core\Database::getInstance();

        // Initialize authentication
        if (class_exists('App\Core\Auth')) {
            $this->auth = new Auth();
        }

        // Initialize View object
        if (class_exists('App\Core\View')) {
            $this->view = new View();
        }
    }

    /**
     * Render a view wrapped in a layout
     * 
     * @param string $layoutName The layout file (e.g., 'admin/layout')
     * @param array $data Data to pass to both view and layout
     */
    protected function layout($layoutName, $data = [])
    {
        // Start output buffering to capture the view content
        ob_start();

        // If there's a specific view to render, do it now
        // The calling file (view) will output its content, which we'll capture

        // Get the buffered content (this will be set by the calling view file)
        $content = ob_get_clean();

        // Extract data for layout use
        extract($data);

        // Build layout path
        $layoutPath = __DIR__ . '/../Views/' . str_replace('.', '/', $layoutName) . '.php';

        if (file_exists($layoutPath)) {
            include $layoutPath;
        } else {
            echo "<h1>Layout Error</h1>";
            echo "<p>Layout file not found: " . htmlspecialchars($layoutName) . "</p>";
            echo "<p>Expected path: " . htmlspecialchars($layoutPath) . "</p>";
        }
    }

    /**
     * Send JSON response
     * 
     * @param array $data The data to send as JSON
     * @param int $statusCode HTTP status code (default 200)
     */
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Redirect to a URL
     * 
     * @param string $url The URL to redirect to
     */
    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Generate CSRF Token and store in session
     * @return string
     */
    protected function generateCsrfToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_expiry'] = time() + 3600; // 1 hour
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validate CSRF Token
     * @param string $token
     * @return bool
     */
    /**
     * Render a view
     * 
     * @param string $viewName The view file (e.g., 'home')
     * @param array $data Data to pass to view
     */
    protected function view($viewName, $data = [])
    {
        if (isset($this->view)) {
            $this->view->render($viewName, $data);
        } else {
            // Fallback if View service not loaded
            extract($data);
            $viewPath = __DIR__ . '/../Views/' . str_replace('.', '/', $viewName) . '.php';
            if (file_exists($viewPath)) {
                include $viewPath;
            } else {
                echo "<h1>View Error</h1>";
                echo "<p>View file not found: " . htmlspecialchars($viewName) . "</p>";
            }
        }
    }

    /**
     * Require authentication for a method
     */
    protected function requireAuth()
    {
        if (isset($this->auth) && !$this->auth->check()) {
            // Store return URL
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            $this->redirect('/login');
        } elseif (!isset($this->auth) && !isset($_SESSION['user_id'])) {
             // Fallback auth check
             $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
             $this->redirect('/login');
        }
    }

    /**
     * Require admin privileges for a method
     */
    protected function requireAdmin()
    {
        $this->requireAuth();
        
        if (isset($this->auth) && !$this->auth->isAdmin()) {
            $this->redirect('/');
        } elseif (!isset($this->auth)) {
            // Fallback admin check
            $role = $_SESSION['role'] ?? 'user';
            if ($role !== 'admin' && $role !== 'super_admin') {
                $this->redirect('/');
            }
        }
    }

    protected function validateCsrfToken($token)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $valid = !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
        $notExpired = empty($_SESSION['csrf_expiry']) || time() <= $_SESSION['csrf_expiry'];

        return $valid && $notExpired;
    }

    /**
     * Get calculator categories for sidebar
     */
    protected function getConverterCategories()
    {
        // Check if DB is initialized
        if (isset($this->db)) {
            return $this->db->find('calc_unit_categories', [], 'display_order ASC');
        }
        return [];
    }
}
