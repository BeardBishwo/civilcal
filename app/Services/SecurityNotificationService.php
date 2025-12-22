<?php

namespace App\Services;

use App\Core\Database;
use App\Models\User;
use MaxMind\Db\Reader;
use Exception;

class SecurityNotificationService
{
    private $geoIpReader;
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        
        // Initialize GeoIP reader if database exists
        $geoIpDbPath = __DIR__ . '/../../storage/app/GeoLite2-City.mmdb';
        if (file_exists($geoIpDbPath)) {
            try {
                $this->geoIpReader = new Reader($geoIpDbPath);
            } catch (Exception $e) {
                error_log('GeoIP reader initialization failed: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * Check if this is a new IP address for the user and send notification if needed
     */
    public function checkAndNotifyNewLogin($userId, $ipAddress)
    {
        try {
            // Check if this is a new IP for this user
            if ($this->isNewIpAddress($userId, $ipAddress)) {
                // Get user details
                $userModel = new User();
                $user = $userModel->find($userId);
                
                // Only notify for admin users if setting is enabled
                if ($user && ($user['role'] === 'admin' || $user['role'] === 'super_admin')) {
                    $notifyEnabled = \App\Services\SettingsService::get('admin_ip_notification', '0') === '1';
                    if ($notifyEnabled) {
                        $this->sendSecurityNotification($user, $ipAddress);
                    }
                }
                
                // Record this IP address for the user
                $this->recordIpAddress($userId, $ipAddress);
            }
        } catch (Exception $e) {
            error_log('Security notification check failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Check if this is a new IP address for the user
     */
    private function isNewIpAddress($userId, $ipAddress)
    {
        try {
            $stmt = $this->db->getPdo()->prepare("
                SELECT COUNT(*) as count 
                FROM user_ip_addresses 
                WHERE user_id = ? AND ip_address = ?
            ");
            $stmt->execute([$userId, $ipAddress]);
            $result = $stmt->fetch();
            
            return ($result['count'] == 0);
        } catch (Exception $e) {
            error_log('IP address check failed: ' . $e->getMessage());
            // If we can't check, assume it's new for security
            return true;
        }
    }
    
    /**
     * Record IP address for the user
     */
    private function recordIpAddress($userId, $ipAddress)
    {
        try {
            // Create table if it doesn't exist
            $this->createIpAddressesTable();
            
            $stmt = $this->db->getPdo()->prepare("
                INSERT INTO user_ip_addresses (user_id, ip_address, first_seen, last_seen)
                VALUES (?, ?, NOW(), NOW())
                ON DUPLICATE KEY UPDATE last_seen = NOW()
            ");
            $stmt->execute([$userId, $ipAddress]);
        } catch (Exception $e) {
            error_log('IP address recording failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Create the user_ip_addresses table if it doesn't exist
     */
    private function createIpAddressesTable()
    {
        try {
            $sql = "
                CREATE TABLE IF NOT EXISTS user_ip_addresses (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    ip_address VARCHAR(45) NOT NULL,
                    first_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_user_id (user_id),
                    INDEX idx_ip_address (ip_address),
                    UNIQUE KEY unique_user_ip (user_id, ip_address)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ";
            $this->db->getPdo()->exec($sql);
        } catch (Exception $e) {
            error_log('IP addresses table creation failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Send security notification email to admin
     */
    private function sendSecurityNotification($user, $ipAddress)
    {
        try {
            // Get admin email (you might want to make this configurable)
            $adminEmail = defined('ADMIN_EMAIL') ? constant('ADMIN_EMAIL') : $user['email'];
            
            // Get IP location information
            $locationInfo = $this->getLocationInfo($ipAddress);
            
            // Prepare email content
            $subject = "Security Alert: New Login Detected";
            
            $body = $this->generateNotificationEmail($user, $ipAddress, $locationInfo);
            
            // Send email using EmailManager
            if (class_exists('\App\Services\EmailManager')) {
                $emailManager = new \App\Services\EmailManager();
                $emailManager->sendEmail($adminEmail, $subject, $body);
            } else {
                // Fallback to basic mail function
                $headers = [
                    'From: ' . (defined('APP_NAME') ? constant('APP_NAME') : 'Bishwo Calculator') . ' <' . $adminEmail . '>',
                    'Reply-To: ' . $adminEmail,
                    'X-Mailer: PHP/' . phpversion(),
                    'MIME-Version: 1.0',
                    'Content-Type: text/html; charset=UTF-8'
                ];
                
                mail($adminEmail, $subject, $body, implode("\r\n", $headers));
            }
        } catch (Exception $e) {
            error_log('Security notification email failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get location information for IP address
     */
    private function getLocationInfo($ipAddress)
    {
        if (!$this->geoIpReader) {
            return [
                'country' => 'Unknown',
                'city' => 'Unknown',
                'latitude' => null,
                'longitude' => null
            ];
        }
        
        try {
            $record = $this->geoIpReader->get($ipAddress);
            
            if ($record) {
                return [
                    'country' => $record['country']['names']['en'] ?? 'Unknown',
                    'city' => $record['city']['names']['en'] ?? 'Unknown',
                    'latitude' => $record['location']['latitude'] ?? null,
                    'longitude' => $record['location']['longitude'] ?? null
                ];
            }
        } catch (Exception $e) {
            error_log('GeoIP lookup failed: ' . $e->getMessage());
        }
        
        return [
            'country' => 'Unknown',
            'city' => 'Unknown',
            'latitude' => null,
            'longitude' => null
        ];
    }
    
    /**
     * Generate HTML email content for security notification
     */
    private function generateNotificationEmail($user, $ipAddress, $locationInfo)
    {
        $dateTime = date('d-m-Y H:i');
        
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; padding: 30px; text-align: center; }
                .content { background: #f9f9f9; padding: 30px; }
                .footer { background: #f1f1f1; padding: 20px; text-align: center; font-size: 12px; color: #666; }
                .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                .info-table td { padding: 10px; border-bottom: 1px solid #ddd; }
                .info-table tr:last-child td { border-bottom: none; }
                .label { font-weight: bold; width: 30%; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🔒 Security Alert</h1>
                    <p>New Login Detected</p>
                </div>
                <div class="content">
                    <h2>Hello!</h2>
                    <p>A new login has been made from a new IP address. If this wasn\'t you, please secure your account immediately.</p>
                    
                    <table class="info-table">
                        <tr>
                            <td class="label">IP Address:</td>
                            <td>' . htmlspecialchars($ipAddress) . '</td>
                        </tr>
                        <tr>
                            <td class="label">Location:</td>
                            <td>' . htmlspecialchars($locationInfo['city'] . ', ' . $locationInfo['country']) . '</td>
                        </tr>
                        <tr>
                            <td class="label">Date & Time:</td>
                            <td>' . htmlspecialchars($dateTime) . '</td>
                        </tr>
                        <tr>
                            <td class="label">User:</td>
                            <td>' . htmlspecialchars($user['username'] ?? $user['email']) . '</td>
                        </tr>
                    </table>
                    
                    <p><strong>What to do if this wasn\'t you:</strong></p>
                    <ul>
                        <li>Change your password immediately</li>
                        <li>Enable two-factor authentication if not already enabled</li>
                        <li>Review your recent account activity</li>
                        <li>Contact support if you need assistance</li>
                    </ul>
                </div>
                <div class="footer">
                    <p>This security notification was generated by Bishwo Calculator</p>
                </div>
            </div>
        </body>
        </html>';
    }
}