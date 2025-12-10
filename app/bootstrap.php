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
    $dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
    $dotenv->load();
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
    return false;
});

set_exception_handler(function ($e) {
    \App\Services\Logger::exception($e);

    if (APP_DEBUG) {
        http_response_code(500);
        echo "Exception: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, "UTF-8");
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
