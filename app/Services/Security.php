<?php
class Security {
    private static array $rateLimit = [];
    
    /**
     * Check CSRF token from either POST data or X-CSRF-Token header
     */
    public static function validateCsrfToken(string $token = null): bool {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_expiry'])) {
            return false;
        }

        // Check if token has expired
        if (time() > $_SESSION['csrf_expiry']) {
            unset($_SESSION['csrf_token'], $_SESSION['csrf_expiry']);
            return false;
        }

        $token = $token ?? $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        return $token && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Generate a new CSRF token and store in session
     */
    public static function generateCsrfToken(): string {
        if (isset($_SESSION['csrf_token']) && isset($_SESSION['csrf_expiry']) && time() < $_SESSION['csrf_expiry']) {
            return $_SESSION['csrf_token'];
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_expiry'] = time() + CSRF_EXPIRY;
        return $token;
    }

    /**
     * Rate limit check for various actions
     */
    public static function checkRateLimit(string $action, string $identifier = ''): bool {
        $key = $action . ':' . ($identifier ?: self::getClientIdentifier());
        $limit = RATE_LIMIT_MAX_REQUESTS[$action] ?? 1000;
        $window = RATE_LIMIT_WINDOW;
        
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
        return md5($_SERVER['REMOTE_ADDR'] . ($_SERVER['HTTP_USER_AGENT'] ?? ''));
    }

    /**
     * Enforce HTTPS in production
     */
    public static function enforceHttps(): void {
        if (REQUIRE_HTTPS && !isset($_SERVER['HTTPS'])) {
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
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['email_verified']) || !$_SESSION['email_verified']) {
            http_response_code(403);
            if ($_SERVER['HTTP_ACCEPT'] === 'application/json') {
                exit(json_encode(['error' => 'Email verification required']));
            }
            exit('Email verification required');
        }
    }

    /**
     * Check tenant access for the current user
     */
    public static function checkTenantAccess(int $tenantId): bool {
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
            if ($_SERVER['HTTP_ACCEPT'] === 'application/json') {
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
            'ip' => $_SERVER['REMOTE_ADDR'],
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
