<?php
namespace App\Services;

use App\Core\Database;

class SecurityMonitor
{
    /**
     * Log security event
     */
    public static function log($userId, $eventType, $endpoint, $details = [], $severity = 'medium')
    {
        $db = Database::getInstance();
        $ip = SecurityValidator::getClientIp();

        $db->query(
            "INSERT INTO security_logs (user_id, ip_address, event_type, endpoint, details, severity) 
             VALUES (:uid, :ip, :event, :endpoint, :details, :severity)",
            [
                'uid' => $userId,
                'ip' => $ip,
                'event' => $eventType,
                'endpoint' => $endpoint,
                'details' => json_encode($details),
                'severity' => $severity
            ]
        );

        // Auto-ban on critical events
        if ($severity === 'critical') {
            self::handleCriticalEvent($userId, $ip, $eventType);
        }
    }

    /**
     * Detect suspicious patterns
     */
    public static function detectSuspiciousActivity($userId)
    {
        $db = Database::getInstance();

        // Check for multiple violations in short time
        $sql = "SELECT COUNT(*) as violation_count 
                FROM security_logs 
                WHERE user_id = :uid 
                AND severity IN ('high', 'critical')
                AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)";
        
        $result = $db->query($sql, ['uid' => $userId])->fetch();

        if ($result['violation_count'] >= 3) {
            self::log($userId, 'suspicious_pattern_detected', '', [
                'violations' => $result['violation_count']
            ], 'critical');
            
            return true;
        }

        return false;
    }

    /**
     * Handle critical security events
     */
    private static function handleCriticalEvent($userId, $ip, $eventType)
    {
        // Auto-ban for honeypot access
        if ($eventType === 'honeypot_accessed') {
            SecurityValidator::banIp($ip, 'Honeypot trap triggered', 86400 * 7); // 7 days
        }

        // Flag user account
        if ($userId) {
            $db = Database::getInstance();
            $db->query(
                "UPDATE users SET is_flagged = 1, flag_reason = :reason WHERE user_id = :uid",
                ['uid' => $userId, 'reason' => $eventType]
            );
        }
    }

    /**
     * Check for impossible transactions
     */
    public static function validateTransaction($userId, $amount, $resourceType)
    {
        // Check if amount is impossibly large
        if ($amount > 1000000) {
            self::log($userId, 'impossible_transaction', '', [
                'amount' => $amount,
                'resource' => $resourceType
            ], 'critical');
            return false;
        }

        // Check transaction frequency
        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) as recent_transactions 
                FROM user_resource_logs 
                WHERE user_id = :uid 
                AND created_at > DATE_SUB(NOW(), INTERVAL 1 SECOND)";
        
        $result = $db->query($sql, ['uid' => $userId])->fetch();

        if ($result['recent_transactions'] > 5) {
            self::log($userId, 'rapid_fire_transactions', '', [
                'count' => $result['recent_transactions']
            ], 'high');
            return false;
        }

        return true;
    }

    /**
     * Get security report for user (admin function)
     */
    public static function getUserReport($userId)
    {
        $db = Database::getInstance();
        
        return $db->query(
            "SELECT * FROM security_logs 
             WHERE user_id = :uid 
             ORDER BY created_at DESC 
             LIMIT 100",
            ['uid' => $userId]
        )->fetchAll();
    }

    /**
     * Clean up old security logs (run via cron)
     */
    public static function cleanup()
    {
        $db = Database::getInstance();
        // Keep logs for 30 days
        $db->query("DELETE FROM security_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
    }
}
