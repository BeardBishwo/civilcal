<?php

namespace App\Services;

use App\Core\Database;
use ZipArchive;
use Exception;

/**
 * Data Export Service (GDPR Compliance)
 * 
 * Handles user data export requests for GDPR compliance
 */
class DataExportService
{
    private $db;
    private $exportDir;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->exportDir = BASE_PATH . '/storage/exports';
        
        // Create export directory if it doesn't exist
        if (!is_dir($this->exportDir)) {
            mkdir($this->exportDir, 0755, true);
        }
    }
    
    /**
     * Request a data export
     */
    public function requestExport($userId)
    {
        // Check if there's already a pending request
        $stmt = $this->db->getPdo()->prepare("
            SELECT id FROM data_export_requests 
            WHERE user_id = ? 
            AND status IN ('pending', 'processing')
            AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ");
        
        $stmt->execute([$userId]);
        
        if ($stmt->fetch()) {
            throw new Exception('You already have a pending export request. Please wait.');
        }
        
        // Create export request
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO data_export_requests 
            (user_id, request_type, status, expires_at)
            VALUES (?, 'export', 'pending', DATE_ADD(NOW(), INTERVAL 7 DAY))
        ");
        
        $result = $stmt->execute([$userId]);
        
        if (!$result) {
            throw new Exception('Failed to create export request');
        }
        
        $requestId = $this->db->getPdo()->lastInsertId();
        
        // Log activity
        $this->logActivity($userId, 'data_export_requested', 'User requested data export');
        
        // Process the export immediately (for smaller datasets)
        // For production, you might want to use a queue
        try {
            $this->processExport($requestId);
        } catch (Exception $e) {
            error_log('Export processing failed: ' . $e->getMessage());
        }
        
        return $requestId;
    }
    
    /**
     * Process an export request
     */
    public function processExport($requestId)
    {
        // Get request details
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM data_export_requests 
            WHERE id = ? AND status = 'pending'
        ");
        
        $stmt->execute([$requestId]);
        $request = $stmt->fetch();
        
        if (!$request) {
            throw new Exception('Export request not found or already processed');
        }
        
        $userId = $request['user_id'];
        
        // Update status to processing
        $this->updateExportStatus($requestId, 'processing');
        
        try {
            // Collect all user data
            $userData = $this->collectUserData($userId);
            
            // Create ZIP file
            $filename = "user_data_{$userId}_" . date('Y-m-d_H-i-s') . ".zip";
            $filePath = $this->exportDir . '/' . $filename;
            
            $zip = new ZipArchive();
            if ($zip->open($filePath, ZipArchive::CREATE) !== true) {
                throw new Exception('Failed to create export file');
            }
            
            // Add JSON file with all data
            $zip->addFromString('user_data.json', json_encode($userData, JSON_PRETTY_PRINT));
            
            // Add CSV files for different data types
            $this->addCSVToZip($zip, 'profile.csv', $userData['profile']);
            $this->addCSVToZip($zip, 'calculations.csv', $userData['calculations']);
            $this->addCSVToZip($zip, 'activity_log.csv', $userData['activity_logs']);
            
            // Add README
            $zip->addFromString('README.txt', $this->getReadmeContent());
            
            $zip->close();
            
            $fileSize = filesize($filePath);
            
            // Update request as completed
            $stmt = $this->db->getPdo()->prepare("
                UPDATE data_export_requests 
                SET status = 'completed',
                    file_path = ?,
                    file_size = ?,
                    completed_at = NOW()
                WHERE id = ?
            ");
            
            $stmt->execute([$filename, $fileSize, $requestId]);
            
            // Log activity
            $this->logActivity($userId, 'data_export_completed', 'User data export completed');
            
            return [
                'success' => true,
                'file_path' => $filename,
                'file_size' => $fileSize
            ];
            
        } catch (Exception $e) {
            // Update request as failed
            $stmt = $this->db->getPdo()->prepare("
                UPDATE data_export_requests 
                SET status = 'failed',
                    error_message = ?
                WHERE id = ?
            ");
            
            $stmt->execute([$e->getMessage(), $requestId]);
            
            throw $e;
        }
    }
    
    /**
     * Collect all user data
     */
    private function collectUserData($userId)
    {
        $data = [];
        
        // 1. Profile information
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $profile = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        // Remove sensitive fields
        unset($profile['password']);
        unset($profile['two_factor_secret']);
        unset($profile['two_factor_recovery_codes']);
        
        $data['profile'] = [$profile];
        
        // 2. Calculation history
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM calculation_history 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        $data['calculations'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // 3. Login sessions
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM login_sessions 
            WHERE user_id = ? 
            ORDER BY login_time DESC 
            LIMIT 100
        ");
        $stmt->execute([$userId]);
        $data['login_sessions'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // 4. Trusted devices
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM trusted_devices 
            WHERE user_id = ? 
            ORDER BY trusted_at DESC
        ");
        $stmt->execute([$userId]);
        $data['trusted_devices'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // 5. Activity logs
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM user_activity_logs 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT 1000
        ");
        $stmt->execute([$userId]);
        $data['activity_logs'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // 6. Login attempts
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM login_attempts 
            WHERE user_id = ? 
            ORDER BY attempted_at DESC 
            LIMIT 100
        ");
        $stmt->execute([$userId]);
        $data['login_attempts'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // 7. Shares (if table exists)
        try {
            $stmt = $this->db->getPdo()->prepare("
                SELECT * FROM shares 
                WHERE user_id = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$userId]);
            $data['shares'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $data['shares'] = [];
        }
        
        // 8. Comments (if table exists)
        try {
            $stmt = $this->db->getPdo()->prepare("
                SELECT * FROM comments 
                WHERE user_id = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$userId]);
            $data['comments'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $data['comments'] = [];
        }
        
        return $data;
    }
    
    /**
     * Add CSV data to ZIP
     */
    private function addCSVToZip($zip, $filename, $data)
    {
        if (empty($data)) {
            return;
        }
        
        $csv = fopen('php://temp', 'r+');
        
        // Add headers
        $headers = array_keys($data[0]);
        fputcsv($csv, $headers);
        
        // Add data rows
        foreach ($data as $row) {
            fputcsv($csv, $row);
        }
        
        rewind($csv);
        $csvContent = stream_get_contents($csv);
        fclose($csv);
        
        $zip->addFromString($filename, $csvContent);
    }
    
    /**
     * Get README content
     */
    private function getReadmeContent()
    {
        return "Your Personal Data Export
========================

This archive contains all your personal data stored in Bishwo Calculator.

Files Included:
- user_data.json: Complete data in JSON format
- profile.csv: Your profile information
- calculations.csv: Your calculation history
- activity_log.csv: Your activity logs
- README.txt: This file

Data Protection Rights:
Under GDPR and data protection regulations, you have the right to:
- Access your personal data
- Rectify inaccurate data
- Erase your data (right to be forgotten)
- Restrict processing of your data
- Data portability
- Object to processing

For more information or to exercise your rights, please contact us.

Export Date: " . date('Y-m-d H:i:s') . "
";
    }
    
    /**
     * Get export requests for a user
     */
    public function getExportRequests($userId)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT id, request_type, status, file_size, requested_at, completed_at, expires_at
            FROM data_export_requests 
            WHERE user_id = ? 
            ORDER BY requested_at DESC 
            LIMIT 10
        ");
        
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Download an export file
     */
    public function downloadExport($requestId, $userId)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM data_export_requests 
            WHERE id = ? AND user_id = ? AND status = 'completed'
        ");
        
        $stmt->execute([$requestId, $userId]);
        $request = $stmt->fetch();
        
        if (!$request) {
            throw new Exception('Export not found or not ready');
        }
        
        // Check if file has expired
        if (strtotime($request['expires_at']) < time()) {
            throw new Exception('Export file has expired');
        }
        
        $filePath = $this->exportDir . '/' . $request['file_path'];
        
        if (!file_exists($filePath)) {
            throw new Exception('Export file not found');
        }
        
        // Log download
        $this->logActivity($userId, 'data_export_downloaded', 'User downloaded data export');
        
        return $filePath;
    }
    
    /**
     * Clean up expired exports
     */
    public function cleanupExpiredExports()
    {
        $stmt = $this->db->getPdo()->query("
            SELECT file_path FROM data_export_requests 
            WHERE expires_at < NOW() AND file_path IS NOT NULL
        ");
        
        $expired = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $deletedCount = 0;
        
        foreach ($expired as $request) {
            $filePath = $this->exportDir . '/' . $request['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
                $deletedCount++;
            }
        }
        
        // Delete database records
        $this->db->getPdo()->exec("
            DELETE FROM data_export_requests 
            WHERE expires_at < NOW()
        ");
        
        return $deletedCount;
    }
    
    /**
     * Update export status
     */
    private function updateExportStatus($requestId, $status)
    {
        $stmt = $this->db->getPdo()->prepare("
            UPDATE data_export_requests 
            SET status = ? 
            WHERE id = ?
        ");
        
        return $stmt->execute([$status, $requestId]);
    }
    
    /**
     * Log activity
     */
    private function logActivity($userId, $activityType, $description)
    {
        try {
            $stmt = $this->db->getPdo()->prepare("
                INSERT INTO user_activity_logs 
                (user_id, activity_type, activity_description, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $userId,
                $activityType,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        } catch (Exception $e) {
            error_log('Failed to log activity: ' . $e->getMessage());
        }
    }
}
