<?php
/**
 * Security and compliance constants
 * In production, these should be loaded from environment variables or secrets manager
 */

// Data retention periods (in days)
define('RETENTION_PERIOD_LOGS', 90);
define('RETENTION_PERIOD_HISTORY', 365);
define('RETENTION_PERIOD_CONTACTS', 730);
define('RETENTION_PERIOD_DELETED_ACCOUNTS', 30);

// 2FA settings
define('REQUIRE_2FA_ADMIN', true);
define('ALLOW_2FA_USERS', true);
define('TOTP_ISSUER', 'AEC Calculator');
define('TOTP_WINDOW', 1); // Time steps to allow (30 seconds each)

// Backup settings
define('BACKUP_RETENTION_DAYS', 30);
define('BACKUP_MIN_INTERVAL', 86400); // 24 hours
define('BACKUP_ENCRYPTION_KEY', getenv('BACKUP_ENCRYPTION_KEY')); // Required in production

// Security headers
define('CSP_POLICY', "default-src 'self'; " .
    "script-src 'self' 'unsafe-inline' https://www.paypal.com https://www.google-analytics.com; " .
    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
    "font-src 'self' https://fonts.gstatic.com; " .
    "img-src 'self' data: https:; " .
    "connect-src 'self' https://www.paypal.com https://www.google-analytics.com; " .
    "frame-src 'self' https://www.paypal.com; " .
    "frame-ancestors 'none'; " .
    "form-action 'self'; " .
    "base-uri 'self'");

// CORS settings (for API)
define('ALLOWED_ORIGINS', [
    'http://localhost',
    'https://aeccalculator.com'
]);

// Rate limiting
define('RATE_LIMIT_RULES', [
    'login' => ['limit' => 5, 'window' => 300],    // 5 attempts per 5 minutes
    'register' => ['limit' => 3, 'window' => 3600], // 3 attempts per hour
    '2fa' => ['limit' => 3, 'window' => 300],      // 3 attempts per 5 minutes
    'api' => ['limit' => 100, 'window' => 60],     // 100 requests per minute
    'admin' => ['limit' => 30, 'window' => 60]      // 30 requests per minute for admin
]);

// Password requirements
define('PASSWORD_MIN_LENGTH', 12);
define('PASSWORD_REQUIRE_MIXED_CASE', true);
define('PASSWORD_REQUIRE_NUMBER', true);
define('PASSWORD_REQUIRE_SPECIAL', true);
define('PASSWORD_HISTORY_SIZE', 5);
define('PASSWORD_MAX_AGE_DAYS', 90);

// Session security
define('SESSION_LIFETIME', 7200);           // 2 hours
define('SESSION_REGEN_PROBABILITY', 0.01);  // 1% chance to regenerate on each request
define('SESSION_MAX_LIFETIME', 86400);      // Force logout after 24 hours
define('REMEMBER_ME_DAYS', 30);

// Compliance flags
define('GDPR_ENABLED', true);
define('CCPA_ENABLED', true);
define('REQUIRE_PRIVACY_CONSENT', true);
define('REQUIRE_MARKETING_CONSENT', false);