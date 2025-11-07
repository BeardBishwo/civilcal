<?php
class Middleware {
    /**
     * Apply all security middleware checks
     */
    public static function apply(array $options = []): void {
        $defaults = [
            'requireAuth' => true,
            'requireVerified' => false,
            'checkCsrf' => true,
            'rateLimit' => true,
            'rateLimitAction' => 'api',
            'requireHttps' => REQUIRE_HTTPS
        ];
        
        $options = array_merge($defaults, $options);
        
        // Force HTTPS in production
        if ($options['requireHttps']) {
            Security::enforceHttps();
        }
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check authentication
        if ($options['requireAuth'] && !isset($_SESSION['user_id'])) {
            http_response_code(401);
            self::jsonError('Authentication required');
        }
        
        // Check email verification
        if ($options['requireVerified']) {
            Security::requireVerifiedEmail();
        }
        
        // Check CSRF token for browser submissions
        if ($options['checkCsrf'] && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            if (!Security::validateCsrfToken()) {
                http_response_code(403);
                self::jsonError('Invalid CSRF token');
            }
        }
        
        // Check rate limit
        if ($options['rateLimit']) {
            $identifier = $_SESSION['user_id'] ?? $_SERVER['REMOTE_ADDR'];
            if (!Security::checkRateLimit($options['rateLimitAction'], $identifier)) {
                http_response_code(429);
                self::jsonError('Rate limit exceeded');
            }
        }
    }
    
    /**
     * Apply tenant access middleware
     */
    public static function requireTenant(): void {
        if (!isset($_SESSION['tenant_id'])) {
            http_response_code(403);
            self::jsonError('Tenant context required');
        }
    }
    
    /**
     * Apply admin access middleware
     */
    public static function requireAdmin(): void {
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            http_response_code(403);
            self::jsonError('Admin access required');
        }
    }
    
    /**
     * Output JSON error and exit
     */
    private static function jsonError(string $message): void {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => $message]);
        exit;
    }
}