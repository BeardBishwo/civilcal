<?php
namespace App\Core;

class Router {
    public $routes = [];
    public $middleware = [];
    
    // Middleware mapping
    private $middlewareMap = [
        'auth' => '\App\Middleware\AuthMiddleware',
        'guest' => '\App\Middleware\GuestMiddleware',
        'admin' => '\App\Middleware\AdminMiddleware'
    ];
    
    public function add($method, $uri, $controller, $middleware = []) {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'middleware' => $middleware
        ];
    }
    
    public function dispatch() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Fix subdirectory installations by removing base path
        $basePath = $this->getBasePath();
        if ($basePath && stripos($uri, $basePath) === 0) { // Use stripos for case-insensitive
            $uri = substr($uri, strlen($basePath));
            if (empty($uri)) {
                $uri = '/';
            }
        }
        
        // Debug: Log the route matching attempt
        $debugMode = defined('APP_DEBUG') && APP_DEBUG;
        
        foreach ($this->routes as $route) {
            if ($this->matchRoute($route, $uri, $method)) {
                return $this->callRoute($route);
            }
        }
        
        // 404 Not Found - Show debug info in development
        http_response_code(404);
        if ($debugMode) {
            echo "<h1>404 - Page Not Found</h1>";
            echo "<p><strong>Requested URI:</strong> {$_SERVER['REQUEST_URI']}</p>";
            echo "<p><strong>Matched URI:</strong> $uri</p>";
            echo "<p><strong>Method:</strong> $method</p>";
            echo "<p><strong>Base Path:</strong> " . ($basePath ?? 'none') . "</p>";
            echo "<p><strong>Registered Routes:</strong></p>";
            echo "<ul>";
            foreach ($this->routes as $route) {
                echo "<li>{$route['method']} {$route['uri']} → {$route['controller']}</li>";
            }
            echo "</ul>";
        } else {
            echo "404 - Page Not Found";
        }
    }
    
    public function getBasePath() {
        // Detect base path for subdirectory installations
        $scriptName = $_SERVER['SCRIPT_NAME']; // e.g., /bishwo_calculator/public/index.php
        $scriptDir = dirname($scriptName); // e.g., /bishwo_calculator/public
        
        // Remove /public from the path since we're hiding it
        if (substr($scriptDir, -7) === '/public') {
            $scriptDir = substr($scriptDir, 0, -7);
        }
        
        // Check if this is a subdirectory installation
        if ($scriptDir !== '/' && $scriptDir !== '') {
            return rtrim($scriptDir, '/');
        }
        
        return null;
    }
    
    public function matchRoute($route, $uri, $method) {
        // Convert route URI to regex pattern
        $pattern = preg_replace('/\{([a-z]+)\}/', '([^/]+)', $route['uri']);
        $pattern = "#^$pattern$#";
        
        return $route['method'] === $method && preg_match($pattern, $uri, $matches);
    }
    
    public function callRoute($route) {
        // Execute middleware
        foreach ($route['middleware'] as $middlewareName) {
            // Map middleware name to class
            $middlewareClass = $this->middlewareMap[$middlewareName] ?? null;
            
            if ($middlewareClass && class_exists($middlewareClass)) {
                $middleware = new $middlewareClass();
                if (!$middleware->handle()) {
                    return; // Middleware blocked the request
                }
            }
        }
        
        // Parse controller@method
        list($controllerClass, $method) = explode('@', $route['controller']);
        
        // Handle admin controllers (already have namespace)
        if (strpos($controllerClass, '\\') === false) {
            $controllerClass = "App\\Controllers\\{$controllerClass}";
        } else {
            $controllerClass = "App\\Controllers\\{$controllerClass}";
        }
        
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();
            call_user_func([$controller, $method]);
        } else {
            http_response_code(500);
            echo "Controller not found: {$controllerClass}";
        }
    }
}
?>
