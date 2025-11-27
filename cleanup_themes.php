<?php
/**
 * Theme Cleanup Script
 * Removes database records for themes that don't exist in filesystem
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Config/config.php';
require_once __DIR__ . '/app/Core/Database.php';

try {
    // Initialize database connection
    $db = \App\Core\Database::getInstance();
    $pdo = $db->getPdo();
    
    echo "=== Theme Cleanup Script ===\n";
    echo "Removing database records for themes that don't exist in filesystem...\n\n";
    
    // Get all themes from database
    $stmt = $pdo->query('SELECT id, name, is_premium FROM themes');
    $dbThemes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get filesystem themes
    $themesDir = __DIR__ . '/themes/';
    $publicThemesDir = __DIR__ . '/public/assets/themes/';
    
    $filesystemThemes = [];
    $premiumAssetThemes = [];
    
    // Get theme directories
    if (is_dir($themesDir)) {
        $dirs = array_filter(glob($themesDir . '*'), 'is_dir');
        foreach ($dirs as $dir) {
            $themeName = basename($dir);
            $filesystemThemes[] = $themeName;
        }
    }
    
    // Get premium theme assets
    if (is_dir($publicThemesDir)) {
        $dirs = array_filter(glob($publicThemesDir . '*'), 'is_dir');
        foreach ($dirs as $dir) {
            $themeName = basename($dir);
            $premiumAssetThemes[] = $themeName;
        }
    }
    
    echo "Found filesystem themes: " . implode(', ', $filesystemThemes) . "\n";
    echo "Found premium asset themes: " . implode(', ', $premiumAssetThemes) . "\n\n";
    
    $removedCount = 0;
    
    // Check each database theme
    foreach ($dbThemes as $theme) {
        $themeName = $theme['name'];
        $isPremium = $theme['is_premium'];
        
        // Valid theme if it exists in filesystem OR is premium with assets
        $isValid = in_array($themeName, $filesystemThemes) || 
                  ($isPremium && in_array($themeName, $premiumAssetThemes));
        
        if (!$isValid) {
            echo "Removing theme '{$themeName}' from database (not found in filesystem)\n";
            
            // Delete the theme record
            $deleteStmt = $pdo->prepare('DELETE FROM themes WHERE id = ?');
            $deleteStmt->execute([$theme['id']]);
            $removedCount++;
        }
    }
    
    echo "\n=== Cleanup Complete ===\n";
    echo "Removed {$removedCount} invalid theme records from database.\n";
    echo "Valid themes remaining: " . (count($dbThemes) - $removedCount) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>