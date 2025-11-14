<?php
/**
 * Simple server test to diagnose web server issues
 */

echo "🌐 WEB SERVER TEST\n";
echo "==================\n\n";

echo "✅ PHP is working!\n";
echo "📊 PHP Version: " . PHP_VERSION . "\n";
echo "🕐 Current Time: " . date('Y-m-d H:i:s') . "\n";
echo "📁 Document Root: " . $_SERVER['DOCUMENT_ROOT'] ?? 'Not set' . "\n";
echo "🌍 Server Name: " . $_SERVER['SERVER_NAME'] ?? 'Not set' . "\n";
echo "🔌 Server Port: " . $_SERVER['SERVER_PORT'] ?? 'Not set' . "\n\n";

echo "🔍 Testing basic functionality...\n";

// Test 1: Can we include files?
try {
    if (file_exists('app/bootstrap.php')) {
        echo "✅ Bootstrap file exists\n";
    } else {
        echo "❌ Bootstrap file missing\n";
    }
} catch (Exception $e) {
    echo "❌ Error checking bootstrap: " . $e->getMessage() . "\n";
}

// Test 2: Can we connect to database?
try {
    define('BISHWO_CALCULATOR', true);
    require_once 'app/bootstrap.php';
    
    $db = \App\Core\Database::getInstance();
    $pdo = $db->getPdo();
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    
    echo "✅ Database connection working\n";
    echo "👥 Users in database: " . $result['count'] . "\n";
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

// Test 3: Can we instantiate controllers?
try {
    $authController = new \App\Controllers\Api\AuthController();
    echo "✅ AuthController can be instantiated\n";
} catch (Exception $e) {
    echo "❌ AuthController error: " . $e->getMessage() . "\n";
}

echo "\n🎯 CONCLUSION:\n";
echo "==============\n";
echo "If you see this message, PHP and basic functionality work.\n";
echo "The 500 error is likely in the web server routing or .htaccess.\n\n";

echo "🔧 NEXT STEPS:\n";
echo "==============\n";
echo "1. Check Apache error logs\n";
echo "2. Verify .htaccess configuration\n";
echo "3. Test direct API access\n";
echo "4. Restart Apache server\n\n";

echo "✨ TEST COMPLETE!\n";
?>
