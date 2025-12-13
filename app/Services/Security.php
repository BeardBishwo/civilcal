<?php

namespace App\Services;

class Security {
    private static array $rateLimit = [];
    
    /**
     * Start a secure session
     */
    public static function startSession(): void {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // Set session name
        session_name("BishwoCalSecureSess");

        // Set secure cookie params
        if (!headers_sent()) {
            $secure = defined('REQUIRE_HTTPS') ? REQUIRE_HTTPS : true;
            $domain = $_SERVER['HTTP_HOST'] ?? '';
            // Remove port from domain if present
            if (strpos($domain, ':') !== false) {
                $domain = parse_url('http://' . $domain, PHP_URL_HOST);
            }
            
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/', // Use root path to avoid fragmentation
                'domain' => $domain,
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }

        session_start();

        // Prevent Session Fixation (init check)
        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = true;
        }
    }

    /**
     * Set standard security headers
     */
    public static function setSecureHeaders(): void {
        if (headers_sent()) {
            return;
        }
        
        // Prevent clickjacking
        header('X-Frame-Options: SAMEORIGIN');
        // Block MIME sniffing
        header('X-Content-Type-Options: nosniff');
        // Enable XSS filtering (mostly legacy, but good for defense-in-depth)
        header('X-XSS-Protection: 1; mode=block');
        // Control referrer information
        header('Referrer-Policy: strict-origin-when-cross-origin');
        // Enforce HTTPS HSTS (if valid cert)
        // header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }

    /**
     * Check CSRF token from either POST data or X-CSRF-Token header
     */
    public static function validateCsrfToken(string $token = null): bool {
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            self::startSession();
        }

        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }

        // Check if token has expired
        if (isset($_SESSION['csrf_expiry']) && time() > $_SESSION['csrf_expiry']) {
            // Expired, but we don't clear generic token immediately to avoid UX fail on concurrent tabs
            // Ideally we rotate. For now, strict fail.
            return false;
        }

        $token = $token ?? $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        
        if (empty($token) || !is_string($token)) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Generate a new CSRF token and store in session
     */
    public static function generateCsrfToken(): string {
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            self::startSession();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_expiry'] = time() + 3600; // 1 hour
        } else {
            // Check expiry and rotate if needed
             if (isset($_SESSION['csrf_expiry']) && time() > $_SESSION['csrf_expiry']) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                $_SESSION['csrf_expiry'] = time() + 3600;
             }
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Rate limit check for various actions
     */
    public static function checkRateLimit(string $action, string $identifier = ''): bool {
        $key = $action . ':' . ($identifier ?: self::getClientIdentifier());
        $limit = defined('RATE_LIMIT_MAX_REQUESTS') && isset(RATE_LIMIT_MAX_REQUESTS[$action]) 
            ? RATE_LIMIT_MAX_REQUESTS[$action] 
            : 1000;
        $window = defined('RATE_LIMIT_WINDOW') ? RATE_LIMIT_WINDOW : 3600;
        
        $now = time();
        
        // Clean up old entries
        self::$rateLimit = array_filter(
            self::$rateLimit,
            fn($entry) => $entry['timestamp'] > ($now - $window)
        );
        
        // Count requests in window
        $count = count(array_filter(
            self::$rateLimit,
            fn($entry) => $entry['key'] === $key
        ));
        
        if ($count >= $limit) {
            return false;
        }
        
        // Record this request
        self::$rateLimit[] = [
            'key' => $key,
            'timestamp' => $now
        ];
        
        return true;
    }

    /**
     * Get a unique identifier for the current client
     */
    private static function getClientIdentifier(): string {
        // Use session ID if available
        if (session_status() === PHP_SESSION_ACTIVE) {
            return session_id();
        }
        
        // Fallback to IP + user agent
        return md5(($_SERVER['REMOTE_ADDR'] ?? '') . ($_SERVER['HTTP_USER_AGENT'] ?? ''));
    }

    /**
     * Enforce HTTPS in production
     */
    public static function enforceHttps(): void {
        $requireHttps = defined('REQUIRE_HTTPS') ? REQUIRE_HTTPS : true;
        if ($requireHttps && !isset($_SERVER['HTTPS']) && ($_SERVER['SERVER_NAME'] !== 'localhost')) {
            $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header('Location: ' . $redirect, true, 301);
            exit;
        }
    }

    /**
     * Clean and validate input
     */
    public static function sanitize($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        
        if (is_string($input)) {
            return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }
        
        return $input;
    }

    /**
     * Generate a secure random token
     */
    public static function generateToken(int $length = 32): string {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Verify that the current user has a verified email
     */
    public static function requireVerifiedEmail(): void {
        self::startSession();
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['email_verified']) || !$_SESSION['email_verified']) {
            http_response_code(403);
            if (isset($_SERVER['HTTP_ACCEPT']) && $_SERVER['HTTP_ACCEPT'] === 'application/json') {
                exit(json_encode(['error' => 'Email verification required']));
            }
            exit('Email verification required');
        }
    }

    /**
     * Check tenant access for the current user
     */
    public static function checkTenantAccess(int $tenantId): bool {
        self::startSession();
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['tenant_id'])) {
            return false;
        }
        
        return $_SESSION['tenant_id'] === $tenantId;
    }

    /**
     * Require tenant access or die
     */
    public static function requireTenantAccess(int $tenantId): void {
        if (!self::checkTenantAccess($tenantId)) {
            http_response_code(403);
            if (isset($_SERVER['HTTP_ACCEPT']) && $_SERVER['HTTP_ACCEPT'] === 'application/json') {
                exit(json_encode(['error' => 'Access denied']));
            }
            exit('Access denied');
        }
    }

    /**
     * Log a security event
     */
    public static function logSecurityEvent(string $event, array $details = []): void {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            'user_id' => $_SESSION['user_id'] ?? null,
            'tenant_id' => $_SESSION['tenant_id'] ?? null,
            'details' => $details
        ];
        
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            error_log(json_encode($logEntry));
        }
        
        // TODO: In production, send to proper logging service
    }
}
