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
        
        // Initialize session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Initialize database connection using global function
        $this->db = get_db();
        
        // Initialize authentication (if Auth class exists)
        if (class_exists('Auth')) {
            $this->auth = new Auth();
        }
        
        // Initialize theme manager (if method exists)
        if (method_exists($this, 'initTheme')) {
            $this->initTheme();
        }
        
        // Initialize view (if View class exists)
        if (class_exists('\App\Core\View')) {
            try {
                $this->view = new \App\Core\View();
            } catch (Exception $e) {
                error_log("View initialization failed: " . $e->getMessage());
                $this->view = null;
            }
        } else {
            error_log("View class not found");
            $this->view = null;
        }
        
        // Initialize session (if Session class exists)
        if (class_exists('Session')) {
            $this->session = new Session();
        }
    }


    /**
     * Initialize theme system
     */
    private function initTheme() {
        // Set default category if not set
        if (!isset($_SESSION['default_category'])) {
            $_SESSION['default_category'] = 'home';
        }
        
        // Set default page title if not set
        if (!isset($_SESSION['default_title'])) {
            $_SESSION['default_title'] = 'Bishwo Calculator - Professional Engineering Calculations';
        }
    }
    
    /**
     * Set current page category for theme styling
     */
    protected function setCategory($category) {
        $_SESSION['current_category'] = $category;
        $this->theme = $category;
    }
    
    /**
     * Set page title
     */
    protected function setTitle($title) {
        $_SESSION['page_title'] = $title;
    }
    
    /**
     * Set page description
     */
    protected function setDescription($description) {
        $_SESSION['page_description'] = $description;
    }
    
    /**
     * Set page keywords
     */
    protected function setKeywords($keywords) {
        $_SESSION['page_keywords'] = $keywords;
    }
    
    /**
     * Render view with theme integration
     */
    protected function view($view, $data = []) {
        // Set default data
        $data['current_category'] = isset($_SESSION['current_category']) ? $_SESSION['current_category'] : 'home';
        $data['page_title'] = isset($_SESSION['page_title']) ? $_SESSION['page_title'] : 'Bishwo Calculator';
        $data['page_description'] = isset($_SESSION['page_description']) ? $_SESSION['page_description'] : 'Professional engineering calculations and design tools';
        $data['page_keywords'] = isset($_SESSION['page_keywords']) ? $_SESSION['page_keywords'] : 'engineering, calculator, design, construction';
        
        // Add theme metadata
        $data['theme_metadata'] = $this->view->getThemeMetadata();
        $data['active_theme'] = $this->view->getActiveTheme();
        $data['theme_config'] = $this->view->getThemeConfig();
        
        // Clear session data for next request
        unset($_SESSION['current_category'], $_SESSION['page_title'], $_SESSION['page_description'], $_SESSION['page_keywords']);
        
        // Render the view
        $this->view->render($view, $data);
    }
    
    /**
     * Render view with admin layout
     */
    protected function adminView($view, $data = []) {
        $data['layout'] = 'admin';
        $data['current_category'] = 'admin';
        $this->view($view, $data);
    }
    
    /**
     * Render view with auth layout
     */
    protected function authView($view, $data = []) {
        $data['layout'] = 'auth';
        $data['current_category'] = 'auth';
        $this->view($view, $data);
    }
    
    /**
     * Render JSON response
     */
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Render plain text response
     */
    protected function plain($text, $status = 200) {
        http_response_code($status);
        header('Content-Type: text/plain');
        echo $text;
        exit;
    }
    
    /**
     * Redirect to URL
     */
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
    
    /**
     * Get current user
     */
    protected function getUser() {
        // Placeholder - will be implemented when Auth class is complete
        return isset($_SESSION['user']) ? $_SESSION['user'] : null;
    }
    
    /**
     * Check if user is authenticated
     */
    protected function isAuthenticated() {
        // Placeholder - will be implemented when Auth class is complete
        return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
    }
    
    /**
     * Check if user has role
     */
    protected function hasRole($role) {
        // Placeholder - will be implemented when Auth class is complete
        $user = $this->getUser();
        return $user && isset($user['role']) && $user['role'] === $role;
    }
    
    /**
     * Require authentication
     */
    protected function requireAuth() {
        if (!$this->isAuthenticated()) {
            $this->redirect('/auth/login');
        }
    }
    
    /**
     * Require specific role
     */
    protected function requireRole($role) {
        $this->requireAuth();
        if (!$this->hasRole($role)) {
            $this->redirect('/unauthorized');
        }
    }
    
    /**
     * Send error response
     * 
     * @param string $message Error message
     * @param int $status HTTP status code
     * @param array $data Additional error data
     */
    protected function error($message, $status = 400, $data = []) {
        $response = [
            'success' => false,
            'error' => [
                'message' => $message,
                'code' => $status
            ]
        ];
        
        if (!empty($data)) {
            $response['error']['data'] = $data;
        }
        
        $this->json($response, $status);
    }
    
    /**
     * Send success response
     * 
     * @param string $message Success message
     * @param mixed $data Response data
     * @param int $status HTTP status code
     */
    protected function success($message = 'Success', $data = null, $status = 200) {
        $response = [
            'success' => true,
            'message' => $message
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        $this->json($response, $status);
    }
}
?>
