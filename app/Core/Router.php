<?php

namespace App\Core;

class Router
{
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
        'ratelimit' => '\App\Middleware\RateLimitMiddleware',
        'maintenance' => '\App\Middleware\MaintenanceMiddleware',
        'analytics' => '\App\Middleware\AnalyticsTracker'
    ];

    public function add($method, $uri, $controller, $middleware = [])
    {
        $mw = $middleware;
        if (!in_array('maintenance', $mw, true)) {
            $mw[] = 'maintenance';
        }
        if (!in_array('security', $mw, true)) {
            $mw[] = 'security';
        }
        if (!in_array('analytics', $mw, true)) {
            $mw[] = 'analytics';
        }
        if (stripos($uri, '/api') === 0) {
            if (!in_array('cors', $mw, true)) {
                $mw[] = 'cors';
            }
            if (!in_array('ratelimit', $mw, true)) {
                $mw[] = 'ratelimit';
            }
        }
        if (strtoupper($method) !== 'OPTIONS' && !in_array('cors', $mw, true)) {
            $mw[] = 'cors';
        }

        $methodUpper = strtoupper($method);
        if (in_array($methodUpper, ['POST','PUT','PATCH','DELETE'], true) && !in_array('csrf', $mw, true)) {
            $mw[] = 'csrf';
        }

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'middleware' => $mw
        ];
    }

    public function dispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        // Fix subdirectory installations by removing base path
        $basePath = $this->getBasePath();

        if ($basePath && stripos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
            if (empty($uri)) {
                $uri = '/';
            }
        }

        foreach ($this->routes as $route) {
            $matches = $this->matchRoute($route, $uri, $method);
            if ($matches !== false) {
                return $this->callRoute($route, $matches);
            }
        }

        http_response_code(404);
        
        // Try to load custom 404 view
        $viewPath = BASE_PATH . '/themes/admin/views/errors/404.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "<h1>404 - Page Not Found</h1>";
        }
    }


    public function getBasePath()
    {
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $scriptDir = dirname($scriptName);

        if (substr($scriptDir, -7) === '/public') {
            $scriptDir = substr($scriptDir, 0, -7);
        }

        if ($scriptDir !== '/' && $scriptDir !== '') {
            return rtrim($scriptDir, '/');
        }

        return null;
    }

    public function matchRoute($route, $uri, $method)
    {
        // Allow HEAD requests to match GET routes
        if ($method === 'HEAD' && $route['method'] === 'GET') {
            // Match
        } elseif ($route['method'] !== $method) {
            return false;
        }

        $pattern = preg_replace('/\{([a-z_][a-z0-9_]*)\}/i', '([^/]+)', $route['uri']);
        $pattern = "#^$pattern$#";

        if (preg_match($pattern, $uri, $matches)) {
            return $matches;
        }
        return false;
    }

    public function callRoute($route, $matches = [])
    {
        $params = array_slice($matches, 1);

        $request = [
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
            'uri' => parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH),
            'get' => $_GET,
            'post' => $_POST,
            'cookies' => $_COOKIE,
            'server' => $_SERVER
        ];

        $pipeline = [];
        foreach ($route['middleware'] as $middlewareName) {
            $middlewareClass = $this->middlewareMap[$middlewareName] ?? null;

            if ($middlewareClass && class_exists($middlewareClass)) {
                $middleware = new $middlewareClass();

                if (method_exists($middleware, 'handle')) {
                    try {
                        $ref = new \ReflectionMethod($middleware, 'handle');
                        if ($ref->getNumberOfParameters() >= 2) {
                            $pipeline[] = $middleware;
                        } else {
                            // Legacy middleware with no parameters - execute immediately
                            if ($middleware->handle() === false) {
                                return;
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

        // Execute pipeline middleware
        $next = function ($request) use ($route, $params) {
            list($controllerClass, $method) = explode('@', $route['controller']);

            if (strpos($controllerClass, '\\') === false) {
                $controllerClass = "App\\Controllers\\{$controllerClass}";
            } else {
                $controllerClass = "App\\Controllers\\{$controllerClass}";
            }

            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                return call_user_func_array([$controller, $method], $params);
            }

            http_response_code(500);
            echo "Controller not found: {$controllerClass}";
            return null;
        };

        // Run through pipeline in reverse order
        foreach (array_reverse($pipeline) as $middleware) {
            $next = function ($request) use ($middleware, $next) {
                return $middleware->handle($request, $next);
            };
        }

        // Execute the pipeline
        if (!empty($pipeline)) {
            return $next($request);
        }

        list($controllerClass, $method) = explode('@', $route['controller']);

        if (strpos($controllerClass, '\\') === false) {
            $controllerClass = "App\\Controllers\\{$controllerClass}";
        } else {
            $controllerClass = "App\\Controllers\\{$controllerClass}";
        }

        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();
            return call_user_func_array([$controller, $method], $params);
        }

        http_response_code(500);
        echo "Controller not found: {$controllerClass}";
        return null;
    }
}
