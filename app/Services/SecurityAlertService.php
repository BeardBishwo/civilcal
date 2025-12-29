<?php

namespace App\Services;

use App\Core\Database;
use App\Models\User;
use Exception;

/**
 * SecurityAlertService - Manages security alerts and notifications
 * 
 * Handles:
 * - New location detection
 * - Email notifications for security events
 * - Location tracking
 */
class SecurityAlertService
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Check for new login location and send alert if needed
     * 
     * @param int $userId
     * @param array $locationData
     * @return bool
     */
    public function checkNewLocation($userId, $locationData)
    {
        try {
            $country = $locationData['country'] ?? null;
            $region = $locationData['region'] ?? null;
            $city = $locationData['city'] ?? null;
            
            // Skip if no location data
            if (!$country) {
                return false;
            }
            
            // Check if this location has been seen before
            $isNewLocation = $this->isNewLocation($userId, $country, $region, $city);
            
            if ($isNewLocation) {
                // Record the new location
                $this->recordLocation($userId, $country, $region, $city);
                
                // Send email notification if enabled
                if ($this->shouldSendLocationAlert($userId)) {
                    $this->sendNewLocationEmail($userId, $locationData);
                }
                
                return true;
            } else {
                // Update last_seen and increment count
                $this->updateLocation($userId, $country, $region, $city);
            }
            
            return false;
        } catch (Exception $e) {
            error_log('SecurityAlertService::checkNewLocation error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if location is new for user
     * 
     * @param int $userId
     * @param string $country
     * @param string $region
     * @param string $city
     * @return bool
     */
    private function isNewLocation($userId, $country, $region, $city)
    {
        $sensitivity = SettingsService::get('location_alert_sensitivity', 'city');
        
        $sql = "SELECT COUNT(*) as count FROM user_login_locations WHERE user_id = ?";
        $params = [$userId];
        
        // Build query based on sensitivity level
        if ($sensitivity === 'country') {
            $sql .= " AND country = ?";
            $params[] = $country;
        } elseif ($sensitivity === 'region') {
            $sql .= " AND country = ? AND region = ?";
            $params[] = $country;
            $params[] = $region;
        } else { // city (default)
            $sql .= " AND country = ? AND region = ? AND city = ?";
            $params[] = $country;
            $params[] = $region;
            $params[] = $city;
        }
        
        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['count'] == 0;
    }
    
    /**
     * Record new location for user
     * 
     * @param int $userId
     * @param string $country
     * @param string $region
     * @param string $city
     */
    private function recordLocation($userId, $country, $region, $city)
    {
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO user_login_locations (user_id, country, region, city, login_count)
            VALUES (?, ?, ?, ?, 1)
        ");
        $stmt->execute([$userId, $country, $region, $city]);
    }
    
    /**
     * Update existing location record
     * 
     * @param int $userId
     * @param string $country
     * @param string $region
     * @param string $city
     */
    private function updateLocation($userId, $country, $region, $city)
    {
        $stmt = $this->db->getPdo()->prepare("
            UPDATE user_login_locations 
            SET last_seen = NOW(), login_count = login_count + 1
            WHERE user_id = ? AND country = ? AND region = ? AND city = ?
        ");
        $stmt->execute([$userId, $country, $region, $city]);
    }
    
    /**
     * Check if location alerts are enabled for user
     * 
     * @param int $userId
     * @return bool
     */
    private function shouldSendLocationAlert($userId)
    {
        // Check global setting
        $globalEnabled = SettingsService::get('enable_location_alerts', '1') === '1';
        if (!$globalEnabled) {
            return false;
        }
        
        // Check if user is admin (admins always get alerts)
        $userModel = new User();
        $user = $userModel->find($userId);
        
        if ($user && ($user['role'] === 'admin' || $user['role'] === 'super_admin')) {
            return true;
        }
        
        // Check user preference (future enhancement)
        // For now, send to all users if globally enabled
        return true;
    }
    
    /**
     * Send new location email notification
     * 
     * @param int $userId
     * @param array $locationData
     */
    private function sendNewLocationEmail($userId, $locationData)
    {
        try {
            $userModel = new User();
            $user = $userModel->find($userId);
            
            if (!$user || !$user['email']) {
                return;
            }
            
            $location = $this->formatLocation($locationData);
            $ipAddress = $locationData['ip_address'] ?? 'Unknown';
            $timestamp = date('F j, Y \a\t g:i A');
            
            $subject = 'New Login Location Detected';
            $body = $this->generateLocationAlertEmail($user, $location, $ipAddress, $timestamp);
            
            // Send email using EmailManager
            if (class_exists('\App\Services\EmailManager')) {
                $emailManager = new \App\Services\EmailManager();
                $emailManager->sendEmail($user['email'], $subject, $body);
            }
        } catch (Exception $e) {
            error_log('SecurityAlertService::sendNewLocationEmail error: ' . $e->getMessage());
        }
    }
    
    /**
     * Format location for display
     * 
     * @param array $locationData
     * @return string
     */
    private function formatLocation($locationData)
    {
        $parts = array_filter([
            $locationData['city'] ?? null,
            $locationData['region'] ?? null,
            $locationData['country'] ?? null
        ]);
        
        return implode(', ', $parts) ?: 'Unknown Location';
    }
    
    /**
     * Generate HTML email for new location alert
     * 
     * @param array $user
     * @param string $location
     * @param string $ipAddress
     * @param string $timestamp
     * @return string
     */
    private function generateLocationAlertEmail($user, $location, $ipAddress, $timestamp)
    {
        $username = htmlspecialchars($user['username'] ?? $user['email']);
        $siteName = SettingsService::get('site_title', 'Bishwo Calculator');
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
                .alert-box { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; }
                .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                .info-table td { padding: 10px; border-bottom: 1px solid #ddd; }
                .info-table tr:last-child td { border-bottom: none; }
                .label { font-weight: bold; width: 30%; }
                .btn { display: inline-block; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 6px; margin-top: 20px; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🔐 New Login Location</h1>
                    <p>Security Alert</p>
                </div>
                <div class="content">
                    <h2>Hello ' . $username . ',</h2>
                    <p>We detected a login to your account from a new location.</p>
                    
                    <div class="alert-box">
                        <strong>⚠️ Was this you?</strong><br>
                        If you recognize this activity, you can safely ignore this email.
                    </div>
                    
                    <table class="info-table">
                        <tr>
                            <td class="label">Location:</td>
                            <td>' . htmlspecialchars($location) . '</td>
                        </tr>
                        <tr>
                            <td class="label">IP Address:</td>
                            <td>' . htmlspecialchars($ipAddress) . '</td>
                        </tr>
                        <tr>
                            <td class="label">Time:</td>
                            <td>' . htmlspecialchars($timestamp) . '</td>
                        </tr>
                    </table>
                    
                    <p><strong>If this wasn\'t you:</strong></p>
                    <ul>
                        <li>Change your password immediately</li>
                        <li>Enable two-factor authentication</li>
                        <li>Review your recent account activity</li>
                        <li>Contact support if you need assistance</li>
                    </ul>
                    
                    <a href="' . app_base_url('/account/security') . '" class="btn">Review Account Security</a>
                </div>
                <div class="footer">
                    <p>This security notification was sent by ' . htmlspecialchars($siteName) . '</p>
                    <p>If you have questions, please contact our support team.</p>
                </div>
            </div>
        </body>
        </html>';
    }
    
    /**
     * Create security alert record
     * 
     * @param int $userId
     * @param string $alertType
     * @param string $riskLevel
     * @param string $description
     * @param array $metadata
     * @return int|false Alert ID or false on failure
     */
    public function createAlert($userId, $alertType, $riskLevel, $description, $metadata = [])
    {
        try {
            $stmt = $this->db->getPdo()->prepare("
                INSERT INTO security_alerts (user_id, alert_type, risk_level, description, metadata)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $userId,
                $alertType,
                $riskLevel,
                $description,
                json_encode($metadata)
            ]);
            
            return $this->db->getPdo()->lastInsertId();
        } catch (Exception $e) {
            error_log('SecurityAlertService::createAlert error: ' . $e->getMessage());
            return false;
        }
    }
}
