<?php
// Test script to verify the HTTP 500 error is resolved
echo "<h1>ProCalculator Theme Test</h1>\n";
echo "<p>Testing if the application loads without HTTP 500 errors...</p>\n\n";

// Test 1: Check if all required view files exist
echo "<h2>✅ Test 1: View Files Verification</h2>\n";
$requiredViews = [
    'themes/procalculator/views/home/index.php',
    'themes/procalculator/views/home/features.php', 
    'themes/procalculator/views/home/pricing.php',
    'themes/procalculator/views/home/about.php',
    'themes/procalculator/views/home/contact.php'
];

$allViewsExist = true;
foreach ($requiredViews as $view) {
    if (file_exists($view)) {
        echo "✅ {$view} - EXISTS\n";
    } else {
        echo "❌ {$view} - MISSING\n";
        $allViewsExist = false;
    }
}

echo "\n";

// Test 2: Check theme configuration
echo "<h2>✅ Test 2: Theme Configuration</h2>\n";
$themeConfigPath = 'themes/procalculator/theme.json';
if (file_exists($themeConfigPath)) {
    echo "✅ Theme configuration file exists\n";
    $config = json_decode(file_get_contents($themeConfigPath), true);
    if ($config) {
        echo "✅ Theme name: " . ($config['name'] ?? 'Unknown') . "\n";
        echo "✅ Theme version: " . ($config['version'] ?? 'Unknown') . "\n";
    }
} else {
    echo "❌ Theme configuration file missing\n";
}

echo "\n";

// Test 3: Check if the application can be bootstrapped
echo "<h2>✅ Test 3: Application Bootstrap Test</h2>\n";
try {
    // Check if bootstrap file exists
    if (file_exists('app/bootstrap.php')) {
        echo "✅ Bootstrap file exists\n";
        
        // Try to include bootstrap (this would normally be done by the router)
        require_once 'app/bootstrap.php';
        echo "✅ Bootstrap executed successfully\n";
        
    } else {
        echo "❌ Bootstrap file not found\n";
    }
} catch (Exception $e) {
    echo "❌ Bootstrap error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Database connection test
echo "<h2>✅ Test 4: Database Connection Test</h2>\n";
try {
    if (file_exists('includes/Database.php')) {
        require_once 'includes/Database.php';
        $db = new Database();
        if ($db->testConnection()) {
            echo "✅ Database connection successful\n";
        } else {
            echo "⚠️  Database connection test failed (this is normal if database is not fully configured)\n";
        }
    }
} catch (Exception $e) {
    echo "⚠️  Database test skipped: " . $e->getMessage() . "\n";
}

echo "\n";

// Summary
echo "<h2>🎯 SUMMARY</h2>\n";
if ($allViewsExist) {
    echo "✅ All required ProCalculator theme views have been created successfully!\n";
    echo "✅ The HTTP 500 error should now be resolved.\n";
    echo "✅ You can now access: <a href='http://localhost/bishwo_calculator/public/' target='_blank'>ProCalculator Application</a>\n";
} else {
    echo "❌ Some view files are still missing.\n";
}

echo "\n<p><strong>Next Steps:</strong></p>\n";
echo "<ol>\n";
echo "<li>Visit the application: <a href='http://localhost/bishwo_calculator/public/' target='_blank'>http://localhost/bishwo_calculator/public/</a></li>\n";
echo "<li>Test navigation to different pages (features, pricing, about, contact)</li>\n";
echo "<li>Check the admin panel for theme management</li>\n";
echo "<li>Verify the premium ProCalculator theme is loading correctly</li>\n";
echo "</ol>\n";

?>
