<?php
<?php
/**
 * Test for renderPartial method specifically
 */

require_once __DIR__ . '/../app/bootstrap.php';

try {
    echo "=== Testing renderPartial Method ===\n";
    
    // Test 1: Check if method exists
    $themeManager = new \App\Services\ThemeManager();
    echo "1. Testing renderPartial method existence...\n";
    
    if (method_exists($themeManager, 'renderPartial')) {
        echo "   ✓ renderPartial method exists\n";
    } else {
        echo "   ✗ renderPartial method does NOT exist\n";
        exit(1);
    }
    
    // Test 2: Check if method is callable
    echo "2. Testing renderPartial method callable...\n";
    if (is_callable([$themeManager, 'renderPartial'])) {
        echo "   ✓ renderPartial method is callable\n";
    } else {
        echo "   ✗ renderPartial method is NOT callable\n";
        exit(1);
    }
    
    // Test 3: Try to call the method (should not cause fatal error)
    echo "3. Testing renderPartial method execution...\n";
    try {
        // This should not cause a fatal error
        ob_start();
        $themeManager->renderPartial('header', []);
        ob_end_clean();
        echo "   ✓ renderPartial method executed without fatal error\n";
    } catch (Error $e) {
        echo "   ✗ Fatal error occurred: " . $e->getMessage() . "\n";
        exit(1);
    }
    
    echo "\n=== renderPartial Method Test PASSED ===\n";
    echo "The fatal error should now be resolved!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
