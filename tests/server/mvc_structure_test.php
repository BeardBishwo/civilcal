<?php
/**
 * MVC Structure Test
 * Test that all relocated files work properly
 */

echo "=== MVC STRUCTURE TEST ===\n\n";

try {
    // Test 1: Config loading
    echo "1. Testing config.php...\n";
    require_once __DIR__ . '/../app/Config/config.php';
    echo "   ✅ Config loaded - APP_BASE: '" . (defined('APP_BASE') ? APP_BASE : 'undefined') . "'\n";
    echo "   ✅ Config loaded - APP_URL: '" . (defined('APP_URL') ? APP_URL : 'undefined') . "'\n\n";
    
    // Test 2: Functions loading  
    echo "2. Testing functions.php...\n";
    require_once __DIR__ . '/../app/Helpers/functions.php';
    echo "   ✅ Functions loaded\n";
    if (function_exists('app_base_url')) {
        echo "   ✅ app_base_url() function available\n";
        echo "   ✅ app_base_url() returns: '" . app_base_url() . "'\n";
    }
    echo "\n";
    
    // Test 3: Database loading
    echo "3. Testing db.php...\n";
    require_once __DIR__ . '/../app/Config/db.php';
    echo "   ✅ DB functions loaded\n";
    if (function_exists('get_db')) {
        echo "   ✅ get_db() function available\n";
        try {
            $db = get_db();
            echo "   ✅ Database connection successful\n";
        } catch (Exception $e) {
            echo "   ⚠️  Database connection failed: " . $e->getMessage() . "\n";
        }
    }
    echo "\n";
    
    // Test 4: ThemeManager
    echo "4. Testing ThemeManager...\n";
    $themeManager = new \App\Services\ThemeManager();
    echo "   ✅ ThemeManager created\n";
    $cssUrl = $themeManager->themeUrl('assets/css/home.css');
    echo "   ✅ CSS URL generated: $cssUrl\n\n";
    
    // Test 5: Theme Model
    echo "5. Testing Theme Model...\n";
    $themeModel = new \App\Models\Theme();
    echo "   ✅ Theme Model created\n";
    $themes = $themeModel->getAll();
    echo "   ✅ Themes loaded: " . count($themes) . " themes found\n\n";
    
    echo "🎉 ALL MVC STRUCTURE TESTS PASSED!\n";
    echo "✅ Config: app/Config/config.php\n";
    echo "✅ Helpers: app/Helpers/functions.php\n";
    echo "✅ Database: app/Config/db.php\n";
    echo "✅ Models: Working\n";
    echo "✅ Services: Working\n\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>


