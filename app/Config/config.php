<?php

/**
 * Bishwo Calculator - Application Configuration
 * Core settings for the application
 */

// Environment detection
// Manual .env loading for reliability within config scope
if (defined('BASE_PATH') && file_exists(BASE_PATH . '/.env')) {
    $envLines = file(BASE_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envLines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            // Populate $_ENV if not exists
            if (!array_key_exists($name, $_ENV)) {
                $_ENV[$name] = $value;
            }
            // Also populate getenv for broader compatibility
            if (getenv($name) === false) {
                putenv(sprintf('%s=%s', $name, $value));
            }
        }
    }
}

if (!defined('ENVIRONMENT')) {
    if (isset($_SERVER['APP_ENV'])) {
        define('ENVIRONMENT', $_SERVER['APP_ENV']);
    } elseif (getenv('APP_ENV')) {
        define('ENVIRONMENT', getenv('APP_ENV'));
    } else {
        define('ENVIRONMENT', 'development');
    }
}

// App configuration
define('APP_NAME', 'Bishwo Calculator');

// Auto-detect base path for flexible installation (main domain, subdomain, or subfolder)
// Check if APP_BASE is defined in environment
if (getenv('APP_BASE')) {
    define('APP_BASE', getenv('APP_BASE'));
} else {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $scriptDir = dirname($scriptName);

    // Remove /public suffix if present (since app uses public directory)
    if (substr($scriptDir, -7) === '/public') {
        $scriptDir = substr($scriptDir, 0, -7);
    }

    // Normalize root path to empty string
    if ($scriptDir === '/' || $scriptDir === '\\' || $scriptDir === '.') {
        $scriptDir = '';
    }

    // For Laragon setup, ensure we have the correct base path
    if (empty($scriptDir) && defined('BASE_PATH')) {
        $basePath = str_replace('\\', '/', BASE_PATH);
        $docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? '');
        if (!empty($docRoot) && strpos($basePath, $docRoot) === 0) {
            $scriptDir = str_replace($docRoot, '', $basePath);
        }
    }

    define('APP_BASE', $scriptDir);
}

// Auto-detect APP_URL
if (getenv('APP_URL')) {
    define('APP_URL', getenv('APP_URL'));
} else {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    define('APP_URL', $protocol . '://' . $host . APP_BASE);
}

// Security settings
define('REQUIRE_HTTPS', false);
define('CSRF_EXPIRY', 7200); // 2 hours

// Helper to get environment variable safely
if (!function_exists('env')) {
    function env($key, $default = null)
    {
        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }
        return $default;
    }
}

// Database configuration
// Using environment variables with fallbacks
define('DB_HOST', env('DB_HOST', '127.0.0.1'));
define('DB_NAME', env('DB_NAME') ?: env('DB_DATABASE') ?: 'bishwo_calculator');
define('DB_USER', env('DB_USER') ?: env('DB_USERNAME') ?: 'root');
define('DB_PASS', env('DB_PASS') ?: env('DB_PASSWORD') ?: '');

// Admin settings
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'password');
define('ADMIN_EMAIL', 'admin@newsbishwo.com');

// Error reporting
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Helper function to get app URL
function get_app_url()
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $protocol . '://' . $host . APP_BASE;
}
