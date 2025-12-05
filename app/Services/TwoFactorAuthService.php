<?php

namespace App\Services;

use App\Core\Database;
use Exception;

/**
 * Two-Factor Authentication Service
 * 
 * Handles 2FA setup, verification, recovery codes, and trusted devices
 */
class TwoFactorAuthService
{
    private $google2fa;
    private $db;
    
    public function __construct()
    {
        // Use fully qualified class name to avoid autoload issues
        $this->google2fa = new \PragmaRX\Google2FA\Google2FA();
        $this->db = Database::getInstance();
    }
    
    /**
     * Generate a new 2FA secret for a user
     */
    public function generateSecret()
    {
        return $this->google2fa->generateSecretKey();
    }
    
    /**
     * Get QR code URL for Google Authenticator
     */
    public function getQRCodeUrl($userEmail, $secret, $appName = 'Bishwo Calculator')
    {
        return $this->google2fa->getQRCodeUrl(
            $appName,
            $userEmail,
            $secret
        );
    }
    
    /**
     * Generate QR code as inline SVG
     */
    public function getQRCodeInline($userEmail, $secret, $appName = 'Bishwo Calculator')
    {
        $url = $this->getQRCodeUrl($userEmail, $secret, $appName);
        
        // Use BaconQrCode to generate SVG
        $writer = new \BaconQrCode\Writer(
            new \BaconQrCode\Renderer\ImageRenderer(
                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
                new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
            )
        );
        
        return $writer->writeString($url);
    }
    
    /**
     * Verify a 2FA code
     */
    public function verifyCode($secret, $code)
    {
        return $this->google2fa->verifyKey($secret, $code);
    }
    
    /**
     * Enable 2FA for a user
     */
    public function enable($userId, $secret, $verificationCode)
    {
        // Verify the code first
        if (!$this->verifyCode($secret, $verificationCode)) {
            throw new Exception('Invalid verification code');
        }
        
        // Generate recovery codes
        $recoveryCodes = $this->generateRecoveryCodes();
        
        // Save to database
        $stmt = $this->db->getPdo()->prepare("
            UPDATE users 
            SET two_factor_enabled = 1,
                two_factor_secret = ?,
                two_factor_recovery_codes = ?,
                two_factor_confirmed_at = NOW(),
                updated_at = NOW()
            WHERE id = ?
        ");
        
        $result = $stmt->execute([
            $secret,
            json_encode($recoveryCodes),
            $userId
        ]);
        
        if (!$result) {
            throw new Exception('Failed to enable 2FA');
        }
        
        // Log activity
        $this->logActivity($userId, '2fa_enabled', 'Two-factor authentication enabled');
        
        return [
            'success' => true,
            'recovery_codes' => $recoveryCodes
        ];
    }
    
    /**
     * Disable 2FA for a user
     */
    public function disable($userId, $password)
    {
        // Verify password before disabling
        $stmt = $this->db->getPdo()->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception('Invalid password');
        }
        
        // Disable 2FA
        $stmt = $this->db->getPdo()->prepare("
            UPDATE users 
            SET two_factor_enabled = 0,
                two_factor_secret = NULL,
                two_factor_recovery_codes = NULL,
                two_factor_confirmed_at = NULL,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        $result = $stmt->execute([$userId]);
        
        if (!$result) {
            throw new Exception('Failed to disable 2FA');
        }
        
        // Remove all trusted devices
        $this->revokeAllTrustedDevices($userId);
        
        // Log activity
        $this->logActivity($userId, '2fa_disabled', 'Two-factor authentication disabled');
        
        return ['success' => true];
    }
    
    /**
     * Generate recovery codes
     */
    public function generateRecoveryCodes($count = 8)
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(4)));
        }
        return $codes;
    }
    
    /**
     * Verify a recovery code
     */
    public function verifyRecoveryCode($userId, $code)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT two_factor_recovery_codes 
            FROM users 
            WHERE id = ? AND two_factor_enabled = 1
        ");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return false;
        }
        
        $recoveryCodes = json_decode($user['two_factor_recovery_codes'] ?? '[]', true);
        
        if (!is_array($recoveryCodes)) {
            return false;
        }
        
        // Check if code exists
        $key = array_search(strtoupper($code), $recoveryCodes);
        
        if ($key === false) {
            return false;
        }
        
        // Remove used code
        unset($recoveryCodes[$key]);
        $recoveryCodes = array_values($recoveryCodes); // Re-index array
        
        // Update database
        $stmt = $this->db->getPdo()->prepare("
            UPDATE users 
            SET two_factor_recovery_codes = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        $stmt->execute([json_encode($recoveryCodes), $userId]);
        
        // Log activity
        $this->logActivity($userId, '2fa_recovery_used', 'Recovery code used for login');
        
        return true;
    }
    
    /**
     * Regenerate recovery codes
     */
    public function regenerateRecoveryCodes($userId, $password)
    {
        // Verify password
        $stmt = $this->db->getPdo()->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception('Invalid password');
        }
        
        // Generate new codes
        $newCodes = $this->generateRecoveryCodes();
        
        // Update database
        $stmt = $this->db->getPdo()->prepare("
            UPDATE users 
            SET two_factor_recovery_codes = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        $result = $stmt->execute([json_encode($newCodes), $userId]);
        
        if (!$result) {
            throw new Exception('Failed to regenerate recovery codes');
        }
        
        // Log activity
        $this->logActivity($userId, '2fa_recovery_regenerated', 'Recovery codes regenerated');
        
        return $newCodes;
    }
    
    /**
     * Add a trusted device
     */
    public function addTrustedDevice($userId, $deviceName, $daysValid = 30)
    {
        $deviceFingerprint = $this->generateDeviceFingerprint();
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$daysValid} days"));
        
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO trusted_devices 
            (user_id, device_name, device_fingerprint, ip_address, user_agent, last_used_at, expires_at)
            VALUES (?, ?, ?, ?, ?, NOW(), ?)
        ");
        
        $result = $stmt->execute([
            $userId,
            $deviceName,
            $deviceFingerprint,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null,
            $expiresAt
        ]);
        
        if ($result) {
            // Set cookie for device recognition
            setcookie('device_token', $deviceFingerprint, time() + ($daysValid * 86400), '/', '', true, true);
            
            $this->logActivity($userId, 'trusted_device_added', "Trusted device added: $deviceName");
        }
        
        return $result;
    }
    
    /**
     * Check if current device is trusted
     */
    public function isDeviceTrusted($userId)
    {
        $deviceToken = $_COOKIE['device_token'] ?? null;
        
        if (!$deviceToken) {
            return false;
        }
        
        $stmt = $this->db->getPdo()->prepare("
            SELECT id FROM trusted_devices 
            WHERE user_id = ? 
            AND device_fingerprint = ? 
            AND is_active = 1 
            AND expires_at > NOW()
        ");
        
        $stmt->execute([$userId, $deviceToken]);
        
        $device = $stmt->fetch();
        
        if ($device) {
            // Update last used timestamp
            $updateStmt = $this->db->getPdo()->prepare("
                UPDATE trusted_devices 
                SET last_used_at = NOW() 
                WHERE id = ?
            ");
            $updateStmt->execute([$device['id']]);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Get user's trusted devices
     */
    public function getTrustedDevices($userId)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT id, device_name, ip_address, last_used_at, trusted_at, expires_at, is_active
            FROM trusted_devices 
            WHERE user_id = ? 
            ORDER BY last_used_at DESC
        ");
        
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Revoke a trusted device
     */
    public function revokeTrustedDevice($userId, $deviceId)
    {
        $stmt = $this->db->getPdo()->prepare("
            UPDATE trusted_devices 
            SET is_active = 0 
            WHERE id = ? AND user_id = ?
        ");
        
        return $stmt->execute([$deviceId, $userId]);
    }
    
    /**
     * Revoke all trusted devices
     */
    public function revokeAllTrustedDevices($userId)
    {
        $stmt = $this->db->getPdo()->prepare("
            UPDATE trusted_devices 
            SET is_active = 0 
            WHERE user_id = ?
        ");
        
        return $stmt->execute([$userId]);
    }
    
    /**
     * Generate device fingerprint
     */
    private function generateDeviceFingerprint()
    {
        $components = [
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
            $_SERVER['REMOTE_ADDR'] ?? ''
        ];
        
        return hash('sha256', implode('|', $components) . uniqid('', true));
    }
    
    /**
     * Log user activity
     */
    private function logActivity($userId, $activityType, $description, $metadata = null)
    {
        try {
            $stmt = $this->db->getPdo()->prepare("
                INSERT INTO user_activity_logs 
                (user_id, activity_type, activity_description, ip_address, user_agent, metadata)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $userId,
                $activityType,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null,
                $metadata ? json_encode($metadata) : null
            ]);
        } catch (Exception $e) {
            error_log('Failed to log activity: ' . $e->getMessage());
        }
    }
    
    /**
     * Get user's 2FA status
     */
    public function getStatus($userId)
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT two_factor_enabled, two_factor_confirmed_at, two_factor_recovery_codes
            FROM users 
            WHERE id = ?
        ");
        
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return null;
        }
        
        $recoveryCodes = json_decode($user['two_factor_recovery_codes'] ?? '[]', true);
        
        return [
            'enabled' => (bool) $user['two_factor_enabled'],
            'confirmed_at' => $user['two_factor_confirmed_at'],
            'recovery_codes_remaining' => is_array($recoveryCodes) ? count($recoveryCodes) : 0
        ];
    }
}
