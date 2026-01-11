<?php

/**
 * Bishwo Calculator - Application Bootstrap
 * Initializes core application components and configuration
 */

// Define base paths
define("BASE_PATH", dirname(__DIR__));
define("APP_PATH", BASE_PATH . "/app");
define("CONFIG_PATH", BASE_PATH . "/config");
define("STORAGE_PATH", BASE_PATH . "/storage");

// Load Composer autoloader (for vendor packages like Google2FA)
require_once BASE_PATH . '/vendor/autoload.php';

// Load application helpers
if (file_exists(BASE_PATH . '/app/Helpers/functions.php')) {
    require_once BASE_PATH . '/app/Helpers/functions.php';
}

// Load .env file
if (file_exists(BASE_PATH . '/.env')) {
    try {
        $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
        $dotenv->load();
    } catch (\Exception $e) {
        // Silently fail if dotenv cannot be loaded
    }
}

// Autoloader for App classes
spl_autoload_register(function ($class) {
    $prefix = "App\\";
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = APP_PATH . "/" . str_replace("\\", "/", $relative_class) . ".php";

    if (file_exists($file)) {
        require $file;
    }
});

// Load configuration
$appConfig = require_once CONFIG_PATH . "/app.php";
$dbConfig = require_once BASE_PATH . "/app/Config/config.php";

// Define debug constant
$__debug = $appConfig["app"]["debug"] ?? true;
define("APP_DEBUG", $__debug);

// Ensure storage/logs exists
$__logsDir = STORAGE_PATH . "/logs";
if (!is_dir($__logsDir)) {
    @mkdir($__logsDir, 0755, true);
}

// Set error reporting and PHP error logging
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set("display_errors", "1");
} else {
    // Production settings
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set("display_errors", "0");
    ini_set("display_startup_errors", "0");
}
ini_set("log_errors", "1");
ini_set("error_log", $__logsDir . "/php_error.log");

// Initialize Monitoring (Sentry)
\App\Services\MonitoringService::init();

// Global error/exception/shutdown handlers
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }

    \App\Core\Logger::error($errstr, [
        "type" => $errno,
        "file" => $errfile,
        "line" => $errline,
    ]);
    return false;
});

set_exception_handler(function ($e) {
    \App\Services\MonitoringService::captureException($e);

    if (defined('APP_DEBUG') && APP_DEBUG) {
        http_response_code(500);
        echo "Exception: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, "UTF-8");
    } else {
        http_response_code(500);
        // Load clean error page
        if (file_exists(BASE_PATH . '/themes/admin/views/errors/500.php')) {
            require BASE_PATH . '/themes/admin/views/errors/500.php';
        } else {
            echo "<h1>Server Error</h1><p>Something went wrong. Please try again later.</p>";
        }
    }
});

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && $error["type"] === E_ERROR) {
        // Fatal error
        \App\Core\Logger::error($error["message"], [
            "type" => $error["type"],
            "file" => $error["file"],
            "line" => $error["line"],
        ]);
        // Ideally send to Sentry too if possible
    }
});

// Check if application is installed
$lockFile = STORAGE_PATH . '/install.lock';
$installedLockFile = STORAGE_PATH . '/installed.lock';
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$isInstallRoute = strpos($requestUri, '/install') !== false;

if (!file_exists($lockFile) && !file_exists($installedLockFile) && !$isInstallRoute) {
    // Only redirect if we are not already in the installer
    // Using app_base_url if available, otherwise fallback
    $redirectUrl = function_exists('app_base_url') ? app_base_url('/install/index.php') : '/install/index.php';
    header('Location: ' . $redirectUrl);
    exit;
}
