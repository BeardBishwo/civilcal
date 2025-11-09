<?php
namespace App\Core;

class Router {
    public $routes = [];
    public $middleware = [];
    
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
        if ($basePath && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
            if (empty($uri)) {
                $uri = '/';
            }
        }
        
        foreach ($this->routes as $route) {
            if ($this->matchRoute($route, $uri, $method)) {
                return $this->callRoute($route);
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        echo "404 - Page Not Found";
    }
    
    public function getBasePath() {
        // Detect base path for subdirectory installations
        $scriptName = $_SERVER['SCRIPT_NAME']; // e.g., /bishwo_calculator/public/index.php
        $scriptDir = dirname($scriptName); // e.g., /bishwo_calculator/public
        
        // Check if this is a subdirectory installation
        if ($scriptDir !== '/') {
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
        foreach ($route['middleware'] as $middlewareClass) {
            $middleware = new $middlewareClass();
            if (!$middleware->handle()) {
                return; // Middleware blocked the request
            }
        }
        
        // Parse controller@method
        list($controllerClass, $method) = explode('@', $route['controller']);
        $controllerClass = "App\\Controllers\\{$controllerClass}";
        
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();
            call_user_func([$controller, $method]);
        }
    }
}
?>
