<?php
namespace App\Core;

class Router {
    public $routes = [];
    public $middleware = [];
    
    // Middleware mapping
    private $middlewareMap = [
        'auth' => '\App\Middleware\AuthMiddleware',
        'guest' => '\App\Middleware\GuestMiddleware',
        'admin' => '\App\Middleware\AdminMiddleware',
        'cors' => '\App\Middleware\CorsMiddleware',
        'csrf' => '\App\Middleware\CsrfMiddleware',
        'security' => '\App\Middleware\SecurityMiddleware',
        'ratelimit' => '\App\Middleware\RateLimitMiddleware'
    ];
    
    public function add($method, $uri, $controller, $middleware = []) {
        $mw = $middleware;
        if (!in_array('security', $mw, true)) {
            $mw[] = 'security';
        }
        if (stripos($uri, '/api') === 0 && !in_array('cors', $mw, true)) {
            $mw[] = 'cors';
        }
        if (strtoupper($method) === 'OPTIONS' && !in_array('cors', $mw, true)) {
            $mw[] = 'cors';
        }
        // Attach CSRF middleware for state-changing methods, except installer
        $upperMethod = strtoupper($method);
        if (in_array($upperMethod, ['POST','PUT','PATCH','DELETE'], true)
            && stripos($uri, '/install') !== 0
            && stripos($uri, '/api') !== 0
            && !in_array('csrf', $mw, true)) {
            $mw[] = 'csrf';
        }
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'middleware' => $mw
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
            $matches = $this->matchRoute($route, $uri, $method);
            if ($matches !== false) {
                return $this->callRoute($route, $matches);
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
        $pattern = preg_replace('/\{([a-z_][a-z0-9_]*)\}/i', '([^/]+)', $route['uri']);
        $pattern = "#^$pattern$#";
        
        if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
            return $matches; // return captured parameters
        }
        return false;
    }
    
    public function callRoute($route, $matches = []) {
        // Extract route parameters (exclude the full match at index 0)
        $params = array_slice($matches, 1);
        
        // Build request array
        $request = [
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
            'uri' => parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH),
            'params' => $params,
            'get' => $_GET,
            'post' => $_POST,
            'cookies' => $_COOKIE,
            'server' => $_SERVER,
        ];
        
        // Execute middleware
        $pipeline = [];
        foreach ($route['middleware'] as $middlewareName) {
            // Map middleware name to class
            $middlewareClass = $this->middlewareMap[$middlewareName] ?? null;
            
            if ($middlewareClass && class_exists($middlewareClass)) {
                $middleware = new $middlewareClass();
                // Support both legacy boolean middlewares and pipeline middlewares
                if (method_exists($middleware, 'handle')) {
                    try {
                        $ref = new \ReflectionMethod($middleware, 'handle');
                        if ($ref->getNumberOfParameters() >= 2) {
                            $pipeline[] = $middleware; // will be called as handle($request, $next)
                        } else {
                            // Legacy: handle() returns bool
                            if ($middleware->handle() === false) {
                                return; // blocked
                            }
                        }
                    } catch (\ReflectionException $e) {
                        // Fallback to legacy behavior
                        if ($middleware->handle() === false) {
                            return;
                        }
                    }
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
        
        // Final controller invoker
        $controllerInvoker = function($req) use ($controllerClass, $method, $params) {
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                return call_user_func_array([$controller, $method], $params);
            } else {
                http_response_code(500);
                echo "Controller not found: {$controllerClass}";
                return null;
            }
        };
        
        // Build middleware pipeline (LIFO)
        $next = $controllerInvoker;
        foreach (array_reverse($pipeline) as $mw) {
            $next = function($req) use ($mw, $next) {
                return $mw->handle($req, $next);
            };
        }
        
        return $next($request);
    }
}
?>
