<?php
namespace App\Services;

use App\Core\Database;

class SecurityValidator
{
    // Whitelist of valid resource keys
    private static $validResources = [
        'coins', 'bricks', 'steel', 'cement', 'sand', 
        'wood_logs', 'wood_planks'
    ];

    // Whitelist of valid bundle keys
    private static $validBundles = [
        'brick_bundle', 'steel_bundle', 'log_bundle', 'plank_bundle'
    ];

    /**
     * Validate resource key
     */
    public static function validateResource($resource)
    {
        if (!in_array($resource, self::$validResources)) {
            SecurityMonitor::log(
                $_SESSION['user_id'] ?? null,
                'invalid_resource_key',
                $_SERVER['REQUEST_URI'] ?? '',
                ['attempted_resource' => $resource],
                'high'
            );
            return false;
        }
        return true;
    }

    /**
     * Validate bundle key
     */
    public static function validateBundle($bundleKey)
    {
        // Fetch valid bundles from settings
        $bundles = SettingsService::get('economy_bundles', []);
        
        if (!isset($bundles[$bundleKey])) {
            SecurityMonitor::log(
                $_SESSION['user_id'] ?? null,
                'invalid_bundle_key',
                $_SERVER['REQUEST_URI'] ?? '',
                ['attempted_bundle' => $bundleKey],
                'high'
            );
            return false;
        }
        return true;
    }

    /**
     * Sanitize and validate integer input
     */
    public static function validateInteger($value, $min = 1, $max = PHP_INT_MAX)
    {
        $value = filter_var($value, FILTER_VALIDATE_INT);
        
        if ($value === false || $value < $min || $value > $max) {
            SecurityMonitor::log(
                $_SESSION['user_id'] ?? null,
                'invalid_integer_input',
                $_SERVER['REQUEST_URI'] ?? '',
                ['value' => $value, 'min' => $min, 'max' => $max],
                'medium'
            );
            return false;
        }
        return $value;
    }

    /**
     * Validate purchase amount (prevent absurd quantities)
     */
    public static function validatePurchaseAmount($amount)
    {
        $amount = self::validateInteger($amount, 1, 1000);
        
        if ($amount === false) {
            return false;
        }

        // Flag suspicious large purchases
        if ($amount > 100) {
            SecurityMonitor::log(
                $_SESSION['user_id'] ?? null,
                'large_purchase_attempt',
                $_SERVER['REQUEST_URI'] ?? '',
                ['amount' => $amount],
                'medium'
            );
        }

        return $amount;
    }

    /**
     * Check if IP is banned
     */
    public static function isIpBanned($ip)
    {
        $db = Database::getInstance();
        $banned = $db->findOne('banned_ips', ['ip_address' => $ip]);
        
        if (!$banned) {
            return false;
        }

        // Check if ban has expired
        if (!$banned['is_permanent'] && $banned['expires_at']) {
            if (strtotime($banned['expires_at']) < time()) {
                // Ban expired, remove it
                $db->query("DELETE FROM banned_ips WHERE id = :id", ['id' => $banned['id']]);
                return false;
            }
        }

        return true;
    }

    /**
     * Ban an IP address
     */
    public static function banIp($ip, $reason, $duration = null)
    {
        $db = Database::getInstance();
        
        $expiresAt = $duration ? date('Y-m-d H:i:s', time() + $duration) : null;
        $isPermanent = $duration === null ? 1 : 0;

        $db->query(
            "INSERT INTO banned_ips (ip_address, reason, expires_at, is_permanent) 
             VALUES (:ip, :reason, :expires, :permanent)
             ON DUPLICATE KEY UPDATE reason = :reason, expires_at = :expires, is_permanent = :permanent",
            [
                'ip' => $ip,
                'reason' => $reason,
                'expires' => $expiresAt,
                'permanent' => $isPermanent
            ]
        );

        SecurityMonitor::log(null, 'ip_banned', '', [
            'ip' => $ip,
            'reason' => $reason,
            'duration' => $duration
        ], 'critical');
    }

    /**
     * Get client IP address
     */
    /**
     * Get client IP address
     * NOTE: We rely strictly on REMOTE_ADDR unless we are behind a known trusted proxy.
     * Spoofing X-Forwarded-For is a common attack vector.
     */
    public static function getClientIp()
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
