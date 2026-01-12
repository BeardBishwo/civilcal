<?php

namespace App\Services;

use Exception;

/**
 * Installer Service - Handles post-installation cleanup
 */
class InstallerService
{
    /**
     * Check if installer should be auto-deleted on first login
     */
    public static function shouldAutoDelete()
    {
        // Check .env file for AUTO_DELETE_INSTALLER setting
        $envFile = __DIR__ . '/../../.env';
        if (file_exists($envFile)) {
            $content = file_get_contents($envFile);
            if (preg_match('/AUTO_DELETE_INSTALLER\s*=\s*true/i', $content)) {
                return true;
            }
        }
        
        // Check if install folder exists and lock file exists (fresh install)
        return file_exists(__DIR__ . '/../../install') && 
               file_exists(__DIR__ . '/../../storage/install.lock');
    }
    
    /**
     * Delete installer folder recursively
     */
    public static function deleteInstaller()
    {
        $installDir = __DIR__ . '/../../install';
        
        if (!is_dir($installDir)) {
            return false;
        }
        
        try {
            self::deleteDirectory($installDir);
            
            // Log the deletion
            error_log('[Bishwo Calculator] Installer folder automatically deleted after first admin login');
            
            return true;
        } catch (Exception $e) {
            error_log('[Bishwo Calculator] Failed to auto-delete installer: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Recursively delete directory and contents
     */
    private static function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }
        
        $files = array_diff(scandir($dir), array('.', '..'));
        
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            
            if (is_dir($path)) {
                self::deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
        
        return rmdir($dir);
    }
    
    /**
     * Check if this is the first admin login after installation
     */
    public static function isFirstAdminLogin($userId)
    {
        // Simply check if installer folder exists and hasn't been processed
        // We delete it when an admin logs in for the first time
        return file_exists(__DIR__ . '/../../install') && !self::isInstallerProcessed();
    }
    
    /**
     * Mark installer as processed (prevent repeated deletion attempts)
     */
    public static function markInstallerProcessed()
    {
        $processedFile = __DIR__ . '/../../storage/installer.processed';
        file_put_contents($processedFile, date('Y-m-d H:i:s'));
    }
    
    /**
     * Check if installer has been processed
     */
    /**
     * Check if installer has been processed
     */
    public static function isInstallerProcessed()
    {
        return file_exists(__DIR__ . '/../../storage/installer.processed');
    }

    /**
     * Attempt to automatically delete installer if conditions are met
     * 
     * @param array $user User data array or object
     * @return bool True if deleted or already processed
     */
    public static function attemptCleanup($user)
    {
        // Auto-cleanup disabled as per user request
        return false;
        
        $user = (array) $user;
        $role = $user['role'] ?? '';
        $isAdmin = $role === 'admin' || $role === 'super_admin' || !empty($user['is_admin']);

        if (!$isAdmin) {
            return false;
        }

        if (self::shouldAutoDelete() && !self::isInstallerProcessed()) {
            if (self::deleteInstaller()) {
                self::markInstallerProcessed();
                error_log("[Bishwo Calculator] Installer auto-deleted after admin login: " . ($user['email'] ?? 'unknown'));
                return true;
            }
        }

        return false;
    }
}
?>
