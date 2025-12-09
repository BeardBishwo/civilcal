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
        $this->db = get_db();

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
    protected function validateCsrfToken($token)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $valid = !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
        $notExpired = empty($_SESSION['csrf_expiry']) || time() <= $_SESSION['csrf_expiry'];

        return $valid && $notExpired;
    }
}
