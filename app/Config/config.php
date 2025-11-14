<?php
// Environment settings
define('ENVIRONMENT', getenv('APP_ENV') ?: 'development'); // 'development', 'staging', 'production'

// Enhanced approach: determine APP_BASE dynamically from request context
// Works with both .test domains and subdirectory installations
function get_app_base() {
    // For .test domains (document root), use empty base
    if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], '.test') !== false) {
        return '';
    }
    
    // For subdirectory installations, detect from SCRIPT_NAME
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $baseDir = dirname($scriptName);
    
    // Remove /public from path if present (for clean URLs)
    if (substr($baseDir, -7) === '/public') {
        $baseDir = substr($baseDir, 0, -7);
    }
    
    // Normalize path
    if ($baseDir === '/' || $baseDir === '\\' || $baseDir === '.') {
        return '';
    }
    
    return $baseDir;
}

define('APP_BASE', get_app_base());

// Generate dynamic APP_URL based on current request
function get_app_url() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base = defined('APP_BASE') ? APP_BASE : '';
    return $protocol . '://' . $host . $base;
}

define('APP_URL', getenv('APP_URL') ?: get_app_url());

// Security settings
define('REQUIRE_HTTPS', ENVIRONMENT === 'production');
define('CSRF_EXPIRY', 7200); // 2 hours
define('RATE_LIMIT_WINDOW', 3600); // 1 hour
define('RATE_LIMIT_MAX_REQUESTS', [
    'login' => 10,       // Max login attempts per hour
    'register' => 5,     // Max registration attempts per hour
    'reset' => 3,        // Max password reset requests per hour
    'contact' => 10,     // Max contact form submissions per hour
    'api' => 1000        // Max API requests per hour per token
]);

// Session is now configured and started in bootstrap.php

// Database configuration
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('DB_NAME') ?: 'aec_calculator');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// Admin settings
define('ADMIN_USER', getenv('ADMIN_USER') ?: 'admin');
define('ADMIN_PASS', getenv('ADMIN_PASS') ?: 'password');

// Mail configuration
define('MAIL_ENABLED', getenv('MAIL_ENABLED') ?: false);
define('MAIL_FROM', getenv('MAIL_FROM') ?: 'noreply@example.com');
define('MAIL_FROM_NAME', getenv('MAIL_FROM_NAME') ?: 'AEC Calculator');
define('MAIL_REPLY_TO', getenv('MAIL_REPLY_TO') ?: 'support@example.com');

// SMTP settings (required for reliable email delivery)
define('MAIL_SMTP_HOST', getenv('MAIL_SMTP_HOST') ?: 'smtp.example.com');
define('MAIL_SMTP_PORT', getenv('MAIL_SMTP_PORT') ?: 587);
define('MAIL_SMTP_USER', getenv('MAIL_SMTP_USER') ?: 'smtp-user');
define('MAIL_SMTP_PASS', getenv('MAIL_SMTP_PASS') ?: 'smtp-pass');
define('MAIL_SMTP_SECURE', getenv('MAIL_SMTP_SECURE') ?: 'tls'); // tls or ssl

// PayPal settings
define('PAYPAL_CLIENT_ID', getenv('PAYPAL_CLIENT_ID') ?: '');
define('PAYPAL_SECRET', getenv('PAYPAL_SECRET') ?: '');
define('PAYPAL_MODE', getenv('PAYPAL_MODE') ?: 'sandbox'); // 'sandbox' or 'live'

// Error reporting
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Anti-abuse and rate limiting
define('FAILED_LOGIN_DELAY', 2); // Seconds to wait after failed login
define('MAX_FAILED_LOGINS', 5); // After this many failures, require password reset

// Tenant settings
define('MAX_USERS_PER_TENANT', 50);
define('STORAGE_LIMIT_PER_TENANT', 1024 * 1024 * 100); // 100MB per tenant

// Set secure headers
if (!headers_sent()) {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('X-XSS-Protection: 1; mode=block');
    if (ENVIRONMENT === 'production') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

?>