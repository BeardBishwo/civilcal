<?php
/**
 * Bishwo Calculator - Main Application Entry Point
 * MVC Router with Theme System Integration
 */

// Define base path FIRST
define('BASE_PATH', __DIR__ . '/..');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if installation is completed
function isInstalled() {
    $configFile = BASE_PATH . '/config/installed.lock';
    $envFile = BASE_PATH . '/.env';
    
    return file_exists($configFile) && file_exists($envFile);
}

// Redirect to installer if not installed
if (!isInstalled() && !isset($_GET['install'])) {
    header('Location: /install/');
    exit;
}

// Include autoloader
require_once BASE_PATH . '/vendor/autoload.php';

// Include helper functions
require_once BASE_PATH . '/themes/default/helpers.php';

// Parse request - handle both methods
if (isset($_GET['url'])) {
    // URL parameter from .htaccess
    $path = $_GET['url'];
} else {
    // Direct REQUEST_URI method
    $request = $_SERVER['REQUEST_URI'];
    
    // Remove base path from request (adjust as needed)
    $basePath = '/bishwo_calculator';
    $path = str_replace($basePath, '', $request);
    $path = parse_url($path, PHP_URL_PATH);
}

// Remove leading slash and split by /
$path = ltrim($path, '/');
$segments = explode('/', $path);

// Router
$controllerName = !empty($segments[0]) ? ucfirst($segments[0]) : 'Calculator';
$actionName = !empty($segments[1]) ? $segments[1] : 'index';

// Default controller if not specified
if (empty($segments[0])) {
    $controllerName = 'Calculator';
    $actionName = 'index';
}

// Handle special cases for authentication
if ($controllerName === 'auth' || $controllerName === 'login' || $controllerName === 'register') {
    $controllerName = 'Auth';
    if ($actionName === 'register') {
        $actionName = 'register';
    } else {
        $actionName = 'login';
    }
}

// Map controller names to actual controller classes
$controllerMap = [
    'Calculator' => 'App\\Controllers\\CalculatorController',
    'Auth' => 'App\\Controllers\\AuthController',
    'User' => 'App\\Controllers\\UserController',
    'Profile' => 'App\\Controllers\\ProfileController',
    'History' => 'App\\Controllers\\HistoryController',
    'Admin' => 'App\\Controllers\\Admin\\DashboardController',
    'Api' => 'App\\Controllers\\ApiController'
];

// Determine controller class
if (isset($controllerMap[$controllerName])) {
    $controllerClass = $controllerMap[$controllerName];
} else {
    // Default to CalculatorController
    $controllerClass = 'App\\Controllers\\CalculatorController';
    $controllerName = 'Calculator';
}

// Check if controller exists
if (class_exists($controllerClass)) {
    try {
        // Create controller instance
        $controller = new $controllerClass();
        
        // Check if action method exists
        $actionMethod = $actionName;
        if (!method_exists($controller, $actionMethod)) {
            // If action doesn't exist, try index
            $actionMethod = 'index';
            if (!method_exists($controller, $actionMethod)) {
                throw new Exception("Action not found: {$actionName}");
            }
        }
        
        // Set route variables for potential use
        $_SESSION['current_controller'] = $controllerName;
        $_SESSION['current_action'] = $actionMethod;
        
        // Call the action
        $controller->$actionMethod();
        
    } catch (Exception $e) {
        // Handle errors
        error_log("Controller Error: " . $e->getMessage());
        
        // Show error page
        http_response_code(500);
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Error - Bishwo Calculator</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
        </head>
        <body>
            <div class='container mt-5'>
                <div class='alert alert-danger'>
                    <h4>Application Error</h4>
                    <p>Sorry, an error occurred while processing your request.</p>
                    <p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
                    <a href='/' class='btn btn-primary'>Go Home</a>
                </div>
            </div>
        </body>
        </html>";
    }
} else {
    // Controller not found - show 404
    http_response_code(404);
    
    // Create a simple 404 response using theme manager
    $themeManager = new App\Services\ThemeManager();
    
    $data = [
        'title' => '404 - Page Not Found',
        'subtitle' => 'The page you are looking for does not exist',
        'description' => 'Sorry, the page you requested could not be found on our server.',
        'current_category' => 'error',
        'page_title' => 'Page Not Found - Bishwo Calculator',
        'page_description' => 'The page you are looking for could not be found',
        'theme_metadata' => $themeManager->getThemeMetadata(),
        'active_theme' => $themeManager->getActiveTheme(),
        'theme_config' => $themeManager->getThemeConfig()
    ];
    
    $themeManager->renderView('404', $data);
}
?>
