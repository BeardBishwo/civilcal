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
define('APP_BASE', '/Bishwo_Calculator');
define('APP_URL', 'http://localhost/Bishwo_Calculator');

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
function get_app_url() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $protocol . '://' . $host . APP_BASE;
}
?>