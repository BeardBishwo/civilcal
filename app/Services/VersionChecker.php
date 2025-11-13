<?php
/**
 * Version Checker Class
 * Handles version checking and update notifications
 */

class VersionChecker
{
    /**
     * Check for available updates
     * @return array|null Update information or null if no updates
     */
    public static function checkForUpdates()
    {
        // For now, return null (no updates available)
        // This can be expanded to check remote versions if needed
        return null;
    }

    /**
     * Get current application version
     * @return string Current version
     */
    public static function getCurrentVersion()
    {
        $versionFile = __DIR__ . '/../version.json';
        if (file_exists($versionFile)) {
            $versionData = json_decode(file_get_contents($versionFile), true);
            return $versionData['version'] ?? '1.0.0';
        }
        return '1.0.0';
    }

    /**
     * Check if updates are enabled
     * @return bool Whether update checking is enabled
     */
    public static function isUpdateCheckEnabled()
    {
        return defined('ENABLE_UPDATE_CHECK') && ENABLE_UPDATE_CHECK;
    }
}
?>
