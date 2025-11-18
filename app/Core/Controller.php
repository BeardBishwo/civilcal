<?php
namespace App\Core;

class Controller {
    protected $db;
    protected $auth;
    protected $theme;
    protected $view;
    protected $session;
    
    public function __construct() {
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
    
    protected function view($view, $data = []) {
        // Extract data for template use
        extract($data);
        
        // Build view path
        $viewPath = __DIR__ . '/../Views/' . str_replace('.', '/', $view) . '.php';
        
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "<h1>View Error</h1>";
            echo "<p>View file not found: " . htmlspecialchars($view) . "</p>";
            echo "<p>Expected path: " . htmlspecialchars($viewPath) . "</p>";
        }
    }
}
?>
