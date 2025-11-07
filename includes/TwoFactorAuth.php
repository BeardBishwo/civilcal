<?php
require_once __DIR__ . '/vendor/autoload.php';

class TwoFactorAuth {
    private static $ga = null;
    
    /**
     * Initialize Google Authenticator
     */
    private static function init(): void {
        if (self::$ga === null) {
            self::$ga = new PHPGangsta_GoogleAuthenticator();
        }
    }
    
    /**
     * Generate new 2FA secret
     */
    public static function generateSecret(): string {
        self::init();
        return self::$ga->createSecret();
    }
    
    /**
     * Get QR code URL for 2FA setup
     */
    public static function getQrCodeUrl(string $name, string $secret): string {
        self::init();
        return self::$ga->getQRCodeGoogleUrl(
            $name,
            $secret,
            TOTP_ISSUER
        );
    }
    
    /**
     * Verify 2FA code
     */
    public static function verifyCode(string $secret, string $code): bool {
        self::init();
        return self::$ga->verifyCode(
            $secret,
            $code,
            TOTP_WINDOW
        );
    }
    
    /**
     * Enable 2FA for a user
     */
    public static function enable(int $userId, string $secret, string $code): bool {
        // Verify code first
        if (!self::verifyCode($secret, $code)) {
            return false;
        }
        
        try {
            $pdo = get_db();
            
            // Update user record
            $stmt = $pdo->prepare('
                UPDATE users 
                SET 
                    two_factor_secret = :secret,
                    two_factor_enabled = 1,
                    two_factor_enabled_at = NOW()
                WHERE id = :id
            ');
            
            return $stmt->execute([
                ':secret' => $secret,
                ':id' => $userId
            ]);
        } catch (Exception $e) {
            error_log('2FA enable error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Disable 2FA for a user
     */
    public static function disable(int $userId, string $code): bool {
        try {
            $pdo = get_db();
            
            // Get user's current secret
            $stmt = $pdo->prepare('SELECT two_factor_secret FROM users WHERE id = ?');
            $stmt->execute([$userId]);
            $secret = $stmt->fetchColumn();
            
            // Verify code matches current secret
            if (!$secret || !self::verifyCode($secret, $code)) {
                return false;
            }
            
            // Update user record
            $stmt = $pdo->prepare('
                UPDATE users 
                SET 
                    two_factor_secret = NULL,
                    two_factor_enabled = 0,
                    two_factor_enabled_at = NULL
                WHERE id = :id
            ');
            
            return $stmt->execute([':id' => $userId]);
        } catch (Exception $e) {
            error_log('2FA disable error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generate backup codes for a user
     */
    public static function generateBackupCodes(int $userId): array {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = bin2hex(random_bytes(4));
        }
        
        try {
            $pdo = get_db();
            
            // Store hashed backup codes
            $stmt = $pdo->prepare('
                UPDATE users 
                SET backup_codes = :codes 
                WHERE id = :id
            ');
            
            $hashedCodes = array_map('password_hash', $codes, array_fill(0, 8, PASSWORD_DEFAULT));
            $stmt->execute([
                ':codes' => json_encode($hashedCodes),
                ':id' => $userId
            ]);
            
            return $codes;
        } catch (Exception $e) {
            error_log('Backup codes generation error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Verify a backup code
     */
    public static function verifyBackupCode(int $userId, string $code): bool {
        try {
            $pdo = get_db();
            
            // Get user's backup codes
            $stmt = $pdo->prepare('SELECT backup_codes FROM users WHERE id = ?');
            $stmt->execute([$userId]);
            $backupCodes = json_decode($stmt->fetchColumn(), true) ?? [];
            
            // Check each backup code
            foreach ($backupCodes as $index => $hashedCode) {
                if (password_verify($code, $hashedCode)) {
                    // Remove used backup code
                    unset($backupCodes[$index]);
                    
                    // Update remaining codes
                    $stmt = $pdo->prepare('
                        UPDATE users 
                        SET backup_codes = :codes 
                        WHERE id = :id
                    ');
                    $stmt->execute([
                        ':codes' => json_encode(array_values($backupCodes)),
                        ':id' => $userId
                    ]);
                    
                    return true;
                }
            }
            
            return false;
        } catch (Exception $e) {
            error_log('Backup code verification error: ' . $e->getMessage());
            return false;
        }
    }
}