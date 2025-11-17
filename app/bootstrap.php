<?php
// Application Bootstrap
// This file initializes the Bishwo Calculator application

// Define base paths
define("BASE_PATH", dirname(__DIR__));
define("APP_PATH", BASE_PATH . "/app");
define("CONFIG_PATH", BASE_PATH . "/config");
define("STORAGE_PATH", BASE_PATH . "/storage");

// Load environment variables from .env file
if (file_exists(BASE_PATH . '/.env')) {
    $lines = file(BASE_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                $value = substr($value, 1, -1);
            }
            
            // Set environment variable
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}

// Autoloader for App classes
spl_autoload_register(function ($class) {
    $prefix = "App\\";
    $base_dir = APP_PATH . "/";

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace("\\", "/", $relative_class) . ".php";

    if (file_exists($file)) {
        require $file;
    }
});

// Load configuration
$appConfig = require_once CONFIG_PATH . "/app.php";
$dbConfig = require_once BASE_PATH . "/app/Config/config.php";

// Define debug constant (config holds under 'app' key)
$__debug = $appConfig["app"]["debug"] ?? true;
define("APP_DEBUG", $__debug);

// Ensure storage/logs exists
$__logsDir = STORAGE_PATH . "/logs";
if (!is_dir($__logsDir)) {
    @mkdir($__logsDir, 0755, true);
}

// Set error reporting and PHP error logging
if ($__debug) {
    error_reporting(E_ALL);
    ini_set("display_errors", "1");
} else {
    error_reporting(0);
    ini_set("display_errors", "0");
}
ini_set("log_errors", "1");
ini_set("error_log", $__logsDir . "/php_error.log");

// Global error/exception/shutdown handlers
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    \App\Services\Logger::error($errstr, [
        "type" => $errno,
        "file" => $errfile,
        "line" => $errline,
    ]);
    return false; // allow PHP's internal handler as well
});

set_exception_handler(function ($e) {
    \App\Services\Logger::exception($e);
    if (defined("APP_DEBUG") && APP_DEBUG) {
        http_response_code(500);
        echo "Exception: " .
            htmlspecialchars($e->getMessage(), ENT_QUOTES, "UTF-8");
    } else {
        http_response_code(500);
        try {
            $view = new \App\Core\View();
            $view->render("errors/500", ["title" => "Server Error"]);
        } catch (\Throwable $t) {
            echo "An error occurred. Please try again later.";
        }
    }
});

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && $error["type"] === E_ERROR) {
        \App\Services\Logger::error($error["message"], [
            "type" => $error["type"],
            "file" => $error["file"],
            "line" => $error["line"],
        ]);
    }
});

// Load helper functions BEFORE any views are rendered
require_once APP_PATH . "/Helpers/functions.php";

// Configure and start session
if (session_status() === PHP_SESSION_NONE) {
    // Session security configuration - must be set before session_start()
    @ini_set("session.cookie_httponly", "1");
    @ini_set("session.use_only_cookies", "1");
    @ini_set("session.cookie_samesite", "Lax");
    if (defined("ENVIRONMENT") && ENVIRONMENT === "production") {
        @ini_set("session.cookie_secure", "1");
    }
    @session_start();
}
?>
