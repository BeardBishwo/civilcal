<?php
/**
 * Working Index File - Bishwo Calculator
 * This version bypasses autoloader issues to provide a working application
 */

// Define base path FIRST
define('BASE_PATH', __DIR__ . '/..');
define('BISHWO_CALCULATOR', true);

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

// Include core files manually to avoid autoloader issues
require_once BASE_PATH . '/app/Core/Controller.php';
require_once BASE_PATH . '/app/Core/Database.php';
require_once BASE_PATH . '/app/Core/Auth.php';
require_once BASE_PATH . '/app/Core/View.php';
require_once BASE_PATH . '/app/Core/Session.php';
require_once BASE_PATH . '/app/Services/CalculationService.php';
require_once BASE_PATH . '/app/Calculators/CalculatorFactory.php';
require_once BASE_PATH . '/app/Controllers/CalculatorController.php';

// Include theme manager
require_once BASE_PATH . '/app/Services/ThemeManager.php';

// Parse request - handle both methods
if (isset($_GET['url'])) {
    // URL parameter from .htaccess
    $path = $_GET['url'];
} else {
    // Direct REQUEST_URI method
    $request = $_SERVER['REQUEST_URI'] ?? '';
    
    // Remove base path from request
    $path = str_replace('/public', '', $request);
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
        // Handle errors with better error reporting
        error_log("Controller Error: " . $e->getMessage());
        
        // Show detailed error page
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
                    <p><strong>Controller:</strong> " . htmlspecialchars($controllerClass) . "</p>
                    <p><strong>Action:</strong> " . htmlspecialchars($actionMethod) . "</p>
                    <div class='mt-3'>
                        <a href='/' class='btn btn-primary'>Go Home</a>
                        <a href='index_working.php' class='btn btn-secondary'>Reload</a>
                        <a href='simple_test.php' class='btn btn-info'>Debug</a>
                    </div>
                </div>
            </div>
        </body>
        </html>";
    }
} else {
    // Controller not found - show 404
    http_response_code(404);
    
    try {
        // Create a simple 404 response using theme manager
        $themeManager = new \App\Services\ThemeManager();
        
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
    } catch (Exception $e) {
        // Fallback 404 if theme manager fails
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>404 - Not Found</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
        </head>
        <body>
            <div class='container mt-5'>
                <div class='alert alert-warning'>
                    <h4>Page Not Found</h4>
                    <p>The page '{$controllerName}' controller was not found.</p>
                    <p><a href='/' class='btn btn-primary'>Go Home</a></p>
                </div>
            </div>
        </body>
        </html>";
    }
}
?>
