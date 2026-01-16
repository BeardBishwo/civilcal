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
        'security_headers' => '\App\Middleware\SecurityHeadersMiddleware',
        'maintenance' => '\App\Middleware\MaintenanceMiddleware',
        'analytics' => '\App\Middleware\AnalyticsTracker'
    ];

    public function add($method, $uri, $controller, $middleware = [])
    {
        $mw = $middleware;
        if (!in_array('maintenance', $mw, true)) {
            $mw[] = 'maintenance';
        }
        if (!in_array('security_headers', $mw, true)) {
            $mw[] = 'security_headers';
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
        if (in_array($methodUpper, ['POST', 'PUT', 'PATCH', 'DELETE'], true) && !in_array('csrf', $mw, true)) {
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

        // Ensure URI starts with /
        if ($uri !== '/' && strpos($uri, '/') !== 0) {
            $uri = '/' . $uri;
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
                if ($middlewareClass && class_exists($middlewareClass)) {
                    try {
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
                    } catch (\Throwable $e) {
                        error_log("Middleware instantiation failed for {$middlewareName}: " . $e->getMessage());
                        // Skip broken middleware
                        continue;
                    }
                } else {
                    error_log("Middleware Class not found: {$middlewareName}");
                }
            }
        }

        // Execute pipeline middleware
        $next = function ($request) use ($route, $params) {
            return $this->executeController($route['controller'], $params);
        };

        if (empty($pipeline)) {
            return $next($request);
        }

        // Run through pipeline in reverse order
        foreach (array_reverse($pipeline) as $middleware) {
            $next = function ($request) use ($middleware, $next) {
                return $middleware->handle($request, $next);
            };
        }

        // Execute the pipeline
        return $next($request);
    }

    private function executeController($controllerStr, $params = [])
    {
        list($controllerClass, $method) = explode('@', $controllerStr);

        if (strpos($controllerClass, '\\') === false && strpos($controllerClass, 'Admin') !== 0 && strpos($controllerClass, 'Api') !== 0) {
            $controllerClass = "App\\Controllers\\{$controllerClass}";
        } elseif (strpos($controllerClass, 'App\\') === false && strpos($controllerClass, '\\') !== 0) {
            // Only prepend if it's not already fully qualified or explicitly relative to root
            $controllerClass = "App\\Controllers\\{$controllerClass}";
        }
        // If it starts with \ or App\, we use it as is.

        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();
            try {
                return call_user_func_array([$controller, $method], $params);
            } catch (\Throwable $e) {
                http_response_code(500);
                error_log("Controller Exception in {$controllerClass}@{$method}: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                echo "Internal Server Error: " . $e->getMessage();
                return null;
            }
        }

        http_response_code(500);
        echo "Controller not found: {$controllerClass}";
        return null;
    }
}
