<?php
namespace App\Core;

class Router {
    protected $routes = [];
    protected $middleware = [];
    
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
        
        foreach ($this->routes as $route) {
            if ($this->matchRoute($route, $uri, $method)) {
                return $this->callRoute($route);
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        echo "404 - Page Not Found";
    }
    
    protected function matchRoute($route, $uri, $method) {
        // Convert route URI to regex pattern
        $pattern = preg_replace('/\{([a-z]+)\}/', '([^/]+)', $route['uri']);
        $pattern = "#^$pattern$#";
        
        return $route['method'] === $method && preg_match($pattern, $uri, $matches);
    }
    
    protected function callRoute($route) {
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
