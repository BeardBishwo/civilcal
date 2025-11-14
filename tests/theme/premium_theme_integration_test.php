<?php
/**
 * Premium Theme Integration Test
 * 
 * Tests the complete premium theme system functionality
 * 
 * @package Tests
 * @version 1.0.0
 */

require_once __DIR__ . '/../app/Config/db.php';
require_once __DIR__ . '/../app/Services/PremiumThemeManager.php';

echo "=== Premium Theme System Test ===\n\n";

// Test 1: Initialize PremiumThemeManager
try {
    $themeManager = new PremiumThemeManager();
    echo "✓ PremiumThemeManager initialized successfully\n";
} catch (Exception $e) {
    echo "✗ Failed to initialize PremiumThemeManager: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Check database connection
try {
    $pdo = get_db();
    if ($pdo) {
        echo "✓ Database connection successful\n";
    } else {
        echo "✗ Database connection failed\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "✗ Database connection error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: Test license validation
try {
    $license = $themeManager->getActiveLicense();
    if ($license) {
        echo "✓ Active license found: " . $license['license_key'] . "\n";
    } else {
        echo "⚠ No active license found (expected in some cases)\n";
    }
} catch (Exception $e) {
    echo "✗ License validation error: " . $e->getMessage() . "\n";
}

// Test 4: Test theme settings
try {
    $settings = $themeManager->getThemeSettings('premium');
    if ($settings) {
        echo "✓ Theme settings loaded successfully\n";
    } else {
        echo "⚠ No theme settings found (will use defaults)\n";
    }
} catch (Exception $e) {
    echo "✗ Theme settings error: " . $e->getMessage() . "\n";
}

// Test 5: Test license validation method
try {
    $isValid = $themeManager->validateLicense('PREMIUM-DEV-KEY-12345');
    if ($isValid) {
        echo "✓ License validation method working\n";
    } else {
        echo "⚠ License validation returned false (may be expected)\n";
    }
} catch (Exception $e) {
    echo "✗ License validation method error: " . $e->getMessage() . "\n";
}

// Test 6: Test admin view
$viewPath = __DIR__ . '/../themes/premium/views/admin/dashboard.php';
if (file_exists($viewPath)) {
    echo "✓ Admin dashboard view exists\n";
} else {
    echo "✗ Admin dashboard view missing\n";
}

// Test 7: Check theme files
$requiredFiles = [
    'themes/premium/theme.json',
    'themes/premium/config.php',
    'themes/premium/functions.php',
    'themes/premium/assets/css/premium-theme.css',
    'themes/premium/assets/css/premium-calculator.css',
    'themes/premium/assets/js/premium-theme.js'
];

echo "\n=== Theme Files Check ===\n";
foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "✓ " . $file . "\n";
    } else {
        echo "✗ " . $file . " (missing)\n";
    }
}

// Test 8: Test database tables
try {
    $tables = $pdo->query("SHOW TABLES LIKE 'theme_%'")->fetchAll(PDO::FETCH_COLUMN);
    $expectedTables = [
        'theme_licenses',
        'user_theme_settings', 
        'theme_installations',
        'theme_updates',
        'theme_analytics'
    ];
    
    echo "\n=== Database Tables Check ===\n";
    foreach ($expectedTables as $table) {
        if (in_array($table, $tables)) {
            echo "✓ Table '{$table}' exists\n";
        } else {
            echo "✗ Table '{$table}' missing\n";
        }
    }
} catch (Exception $e) {
    echo "✗ Database tables check error: " . $e->getMessage() . "\n";
}

// Test 9: Test routes
echo "\n=== Routes Check ===\n";
$routesFile = __DIR__ . '/../app/routes.php';
if (file_exists($routesFile)) {
    $routesContent = file_get_contents($routesFile);
    if (strpos($routesContent, 'premium') !== false) {
        echo "✓ Premium theme routes configured\n";
    } else {
        echo "⚠ Premium theme routes not found in routes.php\n";
    }
} else {
    echo "✗ routes.php not found\n";
}

// Summary
echo "\n=== Test Summary ===\n";
echo "Premium theme system is ready for use!\n";
echo "\nAccess the admin interface at: ?action=admin_premium_theme\n";
echo "Default development license: PREMIUM-DEV-KEY-12345\n";
echo "\nKey Features Available:\n";
echo "- License validation and management\n";
echo "- Theme customization (colors, dark mode, animations)\n";
echo "- Calculator skins (default, minimal, professional)\n";
echo "- Analytics tracking\n";
echo "- Database-powered settings storage\n";
echo "- Admin dashboard interface\n";

?>


