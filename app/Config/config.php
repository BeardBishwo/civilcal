<?php

/**
 * Bishwo Calculator - Application Configuration
 * Core settings for the application
 */

// Environment detection
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

// Database configuration
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'bishwo_calculator');
define('DB_USER', 'root');
define('DB_PASS', '');

// Admin settings
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'password');

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
