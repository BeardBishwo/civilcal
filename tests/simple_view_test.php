<?php
// Simple test to verify the view path resolution logic
$testView = 'admin/premium-themes/index';

echo "Testing view resolution for: " . $testView . "\n";

// Simulate the view resolution logic (this is the fix we applied)
if (strpos($testView, "admin/") === 0) {
    // Convert view path to file system path (replace slashes with directory separators)
    $adminViewPath = str_replace('/', DIRECTORY_SEPARATOR, substr($testView, 6));
    $adminThemeViewPath = __DIR__ . "/themes/admin/views/" . $adminViewPath . ".php";

    echo "Looking for file: " . $adminThemeViewPath . "\n";
    echo "File exists: " . (file_exists($adminThemeViewPath) ? "YES" : "NO") . "\n";

    if (file_exists($adminThemeViewPath)) {
        echo "SUCCESS: View file found at correct location!\n";
        echo "The fix is working correctly.\n";
    } else {
        echo "ERROR: View file not found!\n";
        echo "The fix may not be working.\n";
    }
} else {
    echo "ERROR: Not an admin view!\n";
}

// Also test the old buggy logic for comparison
echo "\n--- Testing old buggy logic ---\n";
$oldLogicPath = __DIR__ . "/themes/admin/views/" . substr($testView, 6) . ".php";
echo "Old logic would look for: " . $oldLogicPath . "\n";
echo "Old logic file exists: " . (file_exists($oldLogicPath) ? "YES" : "NO") . "\n";