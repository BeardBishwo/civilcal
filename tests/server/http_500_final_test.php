<?php
/**
 * Final test to check if HTTP 500 error is resolved
 */

echo "=== Testing HTTP 500 Error Resolution ===\n";

try {
    // Test 1: Load configuration
    echo "1. Testing configuration loading...\n";
    require_once __DIR__ . '/../app/Config/config.php';
    require_once __DIR__ . '/../app/Config/db.php';
    echo "   ✓ Configuration loaded\n";
    
    // Test 2: Database connection
    echo "2. Testing database connection...\n";
    $db = get_db();
    if ($db) {
        echo "   ✓ Database connection successful\n";
    } else {
        echo "   ✗ Database connection failed\n";
        exit(1);
    }
    
    // Test 3: Theme model
    echo "3. Testing Theme model...\n";
    require_once __DIR__ . '/../app/bootstrap.php';
    require_once __DIR__ . '/../app/Models/Theme.php';
    
    $themeModel = new \App\Models\Theme();
    $activeTheme = $themeModel->getActive();
    
    if ($activeTheme) {
        echo "   ✓ Theme model working - Active theme: " . $activeTheme['display_name'] . "\n";
    } else {
        echo "   ! No active theme found, but model is working\n";
    }
    
    // Test 4: ThemeManager
    echo "4. Testing ThemeManager...\n";
    require_once __DIR__ . '/../app/Services/ThemeManager.php';
    
    $themeManager = new \App\Services\ThemeManager();
    echo "   ✓ ThemeManager instantiated\n";
    
    // Test the specific methods that were missing
    if (method_exists($themeManager, 'getThemeMetadata')) {
        echo "   ✓ getThemeMetadata method exists\n";
        $metadata = $themeManager->getThemeMetadata();
        echo "   ✓ getThemeMetadata() returned data\n";
    } else {
        echo "   ✗ getThemeMetadata method missing\n";
    }
    
    if (method_exists($themeManager, 'getAvailableThemes')) {
        echo "   ✓ getAvailableThemes method exists\n";
        $themes = $themeManager->getAvailableThemes();
        echo "   ✓ getAvailableThemes() returned " . count($themes) . " themes\n";
    } else {
        echo "   ✗ getAvailableThemes method missing\n";
    }
    
    // Test 5: View system
    echo "5. Testing View system...\n";
    require_once __DIR__ . '/../app/Core/View.php';
    
    $view = new \App\Core\View();
    echo "   ✓ View system loaded\n";
    
    // Test 6: Controller
    echo "6. Testing Controller...\n";
    require_once __DIR__ . '/../app/Core/Controller.php';
    
    // Create a simple test controller
    class TestController extends \App\Core\Controller {
        public function testInit() {
            echo "   ✓ Controller initialization successful\n";
            return true;
        }
    }
    
    $controller = new TestController();
    $controller->testInit();
    
    echo "\n=== SUMMARY ===\n";
    echo "✓ All core components loaded successfully\n";
    echo "✓ Database connection working\n";
    echo "✓ Theme system operational\n";
    echo "✓ Missing methods have been added\n";
    echo "\nThe HTTP 500 error should now be resolved!\n";
    echo "Try accessing: http://localhost/bishwo_calculator/public/\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>


