<?php

namespace App\Services;

class Security
{
    private static array $rateLimit = [];

    /**
     * Start a secure session
     */
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // Set session name
        session_name("BishwoCalSecureSess");

        // Set secure cookie params
        if (!headers_sent()) {
            // Force secure if HTTPS is on, otherwise auto-detect
            $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

            $domain = $_SERVER['HTTP_HOST'] ?? '';
            if (strpos($domain, ':') !== false) {
                $domain = parse_url('http://' . $domain, PHP_URL_HOST);
            }

            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => $domain,
                'secure' => $secure, // Only send over HTTPS if available
                'httponly' => true, // JS cannot access cookie
                'samesite' => 'Lax' // Prevents CSRF
            ]);
        }

        session_start();

        // Track activity for session timeout
        if (isset($_SESSION['user_id'])) {
            self::enforceSessionTimeout();
            $_SESSION['last_activity'] = time();
        }

        // Prevent Session Fixation (init check)
        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = true;
        }
    }

    /**
     * Enforce session timeout based on settings
     */
    public static function enforceSessionTimeout(): void
    {
        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
            return;
        }

        $timeoutMinutes = (int)SettingsService::get('session_timeout', '120');
        $timeoutSeconds = $timeoutMinutes * 60;

        if (time() - $_SESSION['last_activity'] > $timeoutSeconds) {
            // Session expired
            session_unset();
            session_destroy();

            // Redirect if it's a web request
            if (!self::isApiRequest()) {
                $scriptName = $_SERVER['SCRIPT_NAME'];
                $basePath = dirname(dirname($scriptName));
                if ($basePath === '/' || $basePath === '\\') {
                    $basePath = '';
                }
                header('Location: ' . $basePath . '/login?timeout=1');
                exit;
            } else {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode(['success' => false, 'error' => 'Session expired']);
                exit;
            }
        }
    }

    /**
     * Check if current request is an API request
     */
    private static function isApiRequest(): bool
    {
        return (
            (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
            (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) ||
            (isset($_SERVER['REQUEST_URI']) && (strpos($_SERVER['REQUEST_URI'], '/api/') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/') !== false))
        );
    }
    /**
     * Set standard security headers
     */
    public static function setSecureHeaders(): void
    {
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

        // Enforce HTTPS HSTS (if on HTTPS)
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }

    /**
     * Check CSRF token from either POST data or X-CSRF-Token header
     */
    public static function validateCsrfToken(string $token = null): bool
    {
        // Check if CSRF protection is globally enabled
        $csrfEnabled = \App\Services\SettingsService::get('csrf_protection', '1') === '1';
        if (!$csrfEnabled) {
            return true;
        }

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
    public static function generateCsrfToken(): string
    {
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
    public static function checkRateLimit(string $action, string $identifier = ''): bool
    {
        $key = $action . ':' . ($identifier ?: self::getClientIdentifier());
        // Fix: RATE_LIMIT_MAX_REQUESTS could be an int or an array
        $limit = 1000;
        if (defined('RATE_LIMIT_MAX_REQUESTS')) {
            $configuredLimit = RATE_LIMIT_MAX_REQUESTS;
            if (is_array($configuredLimit)) {
                $limit = $configuredLimit[$action] ?? 1000;
            } else {
                $limit = (int)$configuredLimit;
            }
        }
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
    private static function getClientIdentifier(): string
    {
        // Use session ID if available
        if (session_status() === PHP_SESSION_ACTIVE) {
            return session_id();
        }

        // Fallback to IP + user agent
        return md5(($_SERVER['REMOTE_ADDR'] ?? '') . ($_SERVER['HTTP_USER_AGENT'] ?? ''));
    }

    /**
     * Enforce HTTPS in production based on settings
     */
    public static function enforceHttps(): void
    {
        // Only run if headers haven't been sent
        if (headers_sent()) return;

        $forceHttps = SettingsService::get('force_https', '0') === '1';

        if ($forceHttps && !isset($_SERVER['HTTPS']) && ($_SERVER['SERVER_NAME'] !== 'localhost')) {
            $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header('Location: ' . $redirect, true, 301);
            exit;
        }
    }

    /**
     * Validate password based on security settings
     */
    public static function validatePassword(string $password): array
    {
        $minLength = (int)SettingsService::get('password_min_length', '8');
        $complexity = SettingsService::get('password_complexity', 'low');

        if (strlen($password) < $minLength) {
            return ['valid' => false, 'error' => "Password must be at least {$minLength} characters long."];
        }

        if ($complexity === 'medium') {
            if (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
                return ['valid' => false, 'error' => 'Password must contain at least one uppercase letter and one number.'];
            }
        } elseif ($complexity === 'high') {
            if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^A-Za-z0-9]/', $password)) {
                return ['valid' => false, 'error' => 'Password must contain uppercase, lowercase, numbers, and special characters.'];
            }
        }

        return ['valid' => true];
    }

    public static function sanitize($input)
    {
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
    public static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Verify that the current user has a verified email
     */
    public static function requireVerifiedEmail(): void
    {
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
    public static function checkTenantAccess(int $tenantId): bool
    {
        self::startSession();
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['tenant_id'])) {
            return false;
        }

        return $_SESSION['tenant_id'] === $tenantId;
    }

    /**
     * Require tenant access or die
     */
    public static function requireTenantAccess(int $tenantId): void
    {
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
    public static function logSecurityEvent(string $event, array $details = []): void
    {
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
