<?php
/**
 * HTTP 500 Error Resolution Test
 * 
 * This test verifies that the main issues causing the HTTP 500 error have been resolved
 */

// Test 1: Database Configuration
echo "=== Testing Database Configuration ===\n";

try {
    require_once __DIR__ . '/../includes/config.php';
    require_once __DIR__ . '/../includes/db.php';
    
    $db = get_db();
    if ($db) {
        echo "✓ Database connection successful\n";
        
        // Test themes table exists
        $stmt = $db->prepare("SHOW TABLES LIKE 'themes'");
        $stmt->execute();
        if ($stmt->fetch()) {
            echo "✓ Themes table exists\n";
            
            // Test themes data
            $stmt = $db->query("SELECT COUNT(*) as count FROM themes");
            $result = $stmt->fetch();
            echo "✓ Themes table has {$result['count']} records\n";
            
        } else {
            echo "✗ Themes table not found\n";
        }
    } else {
        echo "✗ Database connection failed\n";
    }
} catch (Exception $e) {
    echo "✗ Database test error: " . $e->getMessage() . "\n";
}

// Test 2: Theme Model
echo "\n=== Testing Theme Model ===\n";

try {
    require_once __DIR__ . '/../app/bootstrap.php';
    require_once __DIR__ . '/../app/Models/Theme.php';
    
    $themeModel = new \App\Models\Theme();
    $themes = $themeModel->getAll();
    echo "✓ Theme model loaded successfully\n";
    echo "✓ Found " . count($themes) . " themes\n";
    
    $activeTheme = $themeModel->getActive();
    if ($activeTheme) {
        echo "✓ Active theme: " . $activeTheme['display_name'] . "\n";
    } else {
        echo "! No active theme found\n";
    }
    
} catch (Exception $e) {
    echo "✗ Theme model test error: " . $e->getMessage() . "\n";
}

// Test 3: Controller
echo "\n=== Testing Controller ===\n";

try {
    require_once __DIR__ . '/../app/bootstrap.php';
    require_once __DIR__ . '/../app/Core/Controller.php';
    
    // Mock a simple controller to test initialization
    class TestController extends \App\Core\Controller {
        public function testInit() {
            echo "✓ Controller initialized successfully\n";
            echo "✓ Session status: " . (session_status() === PHP_SESSION_ACTIVE ? "Active" : "Not Active") . "\n";
            return true;
        }
    }
    
    $controller = new TestController();
    $controller->testInit();
    
} catch (Exception $e) {
    echo "✗ Controller test error: " . $e->getMessage() . "\n";
}

// Test 4: View System
echo "\n=== Testing View System ===\n";

try {
    require_once __DIR__ . '/../app/bootstrap.php';
    require_once __DIR__ . '/../app/Core/View.php';
    
    $view = new \App\Core\View();
    echo "✓ View system loaded successfully\n";
    
} catch (Exception $e) {
    echo "✗ View system test error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Summary ===\n";
echo "If all tests show ✓, the HTTP 500 error should be resolved.\n";
echo "Check error logs in debug/logs/ if any issues remain.\n";
?>
