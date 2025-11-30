<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Config/config.php';
require_once __DIR__ . '/app/Core/Database.php';

try {
    // Initialize database connection
    $db = \App\Core\Database::getInstance();
    $pdo = $db->getPdo();
    
    echo "=== ALL THEMES FROM DATABASE ===\n";
    $stmt = $pdo->query('SELECT name, status, is_premium FROM themes');
    $dbThemes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($dbThemes as $theme) {
        echo "- " . $theme['name'] . " (Status: " . $theme['status'] . ", Premium: " . ($theme['is_premium'] ? 'Yes' : 'No') . ")\n";
    }
    
    echo "\n=== FILESYSTEM CHECK ===\n";
    $themesDir = __DIR__ . '/themes/';
    $publicThemesDir = __DIR__ . '/public/assets/themes/';
    
    echo "Themes directory contents:\n";
    if (is_dir($themesDir)) {
        $dirs = array_filter(glob($themesDir . '*'), 'is_dir');
        foreach ($dirs as $dir) {
            $themeName = basename($dir);
            echo "- $themeName (theme directory)\n";
        }
    }
    
    echo "\nPublic themes directory contents:\n";
    if (is_dir($publicThemesDir)) {
        $dirs = array_filter(glob($publicThemesDir . '*'), 'is_dir');
        foreach ($dirs as $dir) {
            $themeName = basename($dir);
            echo "- $themeName (public assets)\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>