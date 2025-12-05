<?php

namespace App\Services;

use App\Core\Database;

/**
 * GDPR Compliance Service
 * Handles consent management, data export, and data deletion
 */
class GDPRService
{
    /**
     * Record user consent
     */
    public static function recordConsent($userId, $consentType, $consentGiven, $version = '1.0')
    {
        $db = Database::getInstance();
        
        $stmt = $db->prepare("
            INSERT INTO gdpr_consents 
            (user_id, consent_type, consent_given, consent_version, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $userId,
            $consentType,
            $consentGiven ? 1 : 0,
            $version,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
    
    /**
     * Get user consents
     */
    public static function getUserConsents($userId)
    {
        $db = Database::getInstance();
        
        $stmt = $db->prepare("
            SELECT * FROM gdpr_consents 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Check if user has given consent
     */
    public static function hasConsent($userId, $consentType)
    {
        $db = Database::getInstance();
        
        $stmt = $db->prepare("
            SELECT consent_given FROM gdpr_consents 
            WHERE user_id = ? AND consent_type = ?
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute([$userId, $consentType]);
        $result = $stmt->fetch();
        
        return $result ? (bool)$result['consent_given'] : false;
    }
    
    /**
     * Request data export
     */
    public static function requestDataExport($userId)
    {
        $db = Database::getInstance();
        
        // Check if there's already a pending request
        $stmt = $db->prepare("
            SELECT id FROM data_export_requests 
            WHERE user_id = ? AND request_type = 'export' AND status IN ('pending', 'processing')
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'You already have a pending export request'];
        }
        
        // Create new request
        $stmt = $db->prepare("
            INSERT INTO data_export_requests 
            (user_id, request_type, status, ip_address, expires_at)
            VALUES (?, 'export', 'pending', ?, DATE_ADD(NOW(), INTERVAL 7 DAY))
        ");
        
        $result = $stmt->execute([
            $userId,
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
        
        if ($result) {
            // Process export asynchronously or immediately
            $requestId = $db->lastInsertId();
            self::processDataExport($requestId, $userId);
            
            return ['success' => true, 'message' => 'Export request created successfully', 'request_id' => $requestId];
        }
        
        return ['success' => false, 'message' => 'Failed to create export request'];
    }
    
    /**
     * Process data export
     */
    public static function processDataExport($requestId, $userId)
    {
        $db = Database::getInstance();
        
        try {
            // Update status to processing
            $stmt = $db->prepare("UPDATE data_export_requests SET status = 'processing' WHERE id = ?");
            $stmt->execute([$requestId]);
            
            // Collect user data
            $userData = self::collectUserData($userId);
            
            // Create export file
            $filename = 'user_data_' . $userId . '_' . time() . '.json';
            $filepath = __DIR__ . '/../../storage/exports/' . $filename;
            
            // Ensure directory exists
            if (!is_dir(dirname($filepath))) {
                mkdir(dirname($filepath), 0755, true);
            }
            
            file_put_contents($filepath, json_encode($userData, JSON_PRETTY_PRINT));
            
            // Update request as completed
            $stmt = $db->prepare("
                UPDATE data_export_requests 
                SET status = 'completed', file_path = ?, completed_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$filepath, $requestId]);
            
            return true;
            
        } catch (\Exception $e) {
            // Update status to failed
            $stmt = $db->prepare("
                UPDATE data_export_requests 
                SET status = 'failed', error_message = ?
                WHERE id = ?
            ");
            $stmt->execute([$e->getMessage(), $requestId]);
            
            return false;
        }
    }
    
    /**
     * Collect all user data for export
     */
    private static function collectUserData($userId)
    {
        $db = Database::getInstance();
        
        $data = [];
        
        // User basic info
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if ($user) {
            unset($user['password']); // Never export password
            $data['user'] = $user;
        }
        
        // Calculation history
        $stmt = $db->prepare("SELECT * FROM calculation_history WHERE user_id = ?");
        $stmt->execute([$userId]);
        $data['calculations'] = $stmt->fetchAll();
        
        // Comments
        $stmt = $db->prepare("SELECT * FROM comments WHERE user_id = ?");
        $stmt->execute([$userId]);
        $data['comments'] = $stmt->fetchAll();
        
        // Consents
        $stmt = $db->prepare("SELECT * FROM gdpr_consents WHERE user_id = ?");
        $stmt->execute([$userId]);
        $data['consents'] = $stmt->fetchAll();
        
        // Activity logs
        $stmt = $db->prepare("SELECT * FROM activity_logs WHERE user_id = ?");
        $stmt->execute([$userId]);
        $data['activity_logs'] = $stmt->fetchAll();
        
        // Cookie preferences
        $stmt = $db->prepare("SELECT * FROM cookie_preferences WHERE user_id = ?");
        $stmt->execute([$userId]);
        $data['cookie_preferences'] = $stmt->fetchAll();
        
        $data['export_date'] = date('Y-m-d H:i:s');
        
        return $data;
    }
    
    /**
     * Request account deletion
     */
    public static function requestAccountDeletion($userId)
    {
        $db = Database::getInstance();
        
        // Check if there's already a pending request
        $stmt = $db->prepare("
            SELECT id FROM data_export_requests 
            WHERE user_id = ? AND request_type = 'delete' AND status IN ('pending', 'processing')
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'You already have a pending deletion request'];
        }
        
        // Create deletion request
        $stmt = $db->prepare("
            INSERT INTO data_export_requests 
            (user_id, request_type, status, ip_address)
            VALUES (?, 'delete', 'pending', ?)
        ");
        
        $result = $stmt->execute([
            $userId,
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
        
        return ['success' => $result, 'message' => $result ? 'Deletion request created' : 'Failed to create deletion request'];
    }
    
    /**
     * Process account deletion
     */
    public static function processAccountDeletion($userId)
    {
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();
            
            // Delete user data (cascading deletes will handle related records)
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            
            // Log the deletion
            $stmt = $db->prepare("
                INSERT INTO activity_logs (user_id, action, description, ip_address)
                VALUES (?, 'account_deleted', 'User account deleted per GDPR request', ?)
            ");
            $stmt->execute([$userId, $_SERVER['REMOTE_ADDR'] ?? null]);
            
            $db->commit();
            
            return true;
            
        } catch (\Exception $e) {
            $db->rollBack();
            return false;
        }
    }
    
    /**
     * Save cookie preferences
     */
    public static function saveCookiePreferences($userId, $preferences)
    {
        $db = Database::getInstance();
        
        $stmt = $db->prepare("
            INSERT INTO cookie_preferences 
            (user_id, session_id, necessary, functional, analytics, marketing, ip_address)
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                necessary = VALUES(necessary),
                functional = VALUES(functional),
                analytics = VALUES(analytics),
                marketing = VALUES(marketing),
                updated_at = NOW()
        ");
        
        return $stmt->execute([
            $userId,
            session_id(),
            $preferences['necessary'] ?? 1,
            $preferences['functional'] ?? 0,
            $preferences['analytics'] ?? 0,
            $preferences['marketing'] ?? 0,
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    }
    
    /**
     * Get cookie preferences
     */
    public static function getCookiePreferences($userId)
    {
        $db = Database::getInstance();
        
        $stmt = $db->prepare("
            SELECT * FROM cookie_preferences 
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        
        return $stmt->fetch();
    }
    
    /**
     * Log activity
     */
    public static function logActivity($userId, $action, $entityType = null, $entityId = null, $description = null, $oldValues = null, $newValues = null)
    {
        $db = Database::getInstance();
        
        $stmt = $db->prepare("
            INSERT INTO activity_logs 
            (user_id, action, entity_type, entity_id, description, old_values, new_values, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $userId,
            $action,
            $entityType,
            $entityId,
            $description,
            $oldValues ? json_encode($oldValues) : null,
            $newValues ? json_encode($newValues) : null,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
    
    /**
     * Get activity logs for user
     */
    public static function getActivityLogs($userId, $limit = 50)
    {
        $db = Database::getInstance();
        
        $stmt = $db->prepare("
            SELECT * FROM activity_logs 
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        
        return $stmt->fetchAll();
    }
}
