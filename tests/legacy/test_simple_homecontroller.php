<?php
/**
 * Simple HomeController loading test
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 SIMPLE HOMECONTROLLER TEST\n";
echo "============================\n\n";

echo "Step 1: Setting up BASE_PATH...\n";
define('BASE_PATH', __DIR__);
echo "✅ BASE_PATH: " . BASE_PATH . "\n\n";

echo "Step 2: Loading bootstrap...\n";
try {
    require_once BASE_PATH . '/app/bootstrap.php';
    echo "✅ Bootstrap loaded\n\n";
} catch (Exception $e) {
    echo "❌ Bootstrap failed: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n\n";
    exit(1);
}

echo "Step 3: Testing class autoloading...\n";
try {
    $classExists = class_exists('\App\Controllers\HomeController');
    echo "✅ HomeController class exists: " . ($classExists ? 'YES' : 'NO') . "\n\n";
} catch (Exception $e) {
    echo "❌ Class check failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

echo "Step 4: Testing Controller base class...\n";
try {
    $baseExists = class_exists('\App\Core\Controller');
    echo "✅ Controller class exists: " . ($baseExists ? 'YES' : 'NO') . "\n\n";
} catch (Exception $e) {
    echo "❌ Controller class check failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

echo "Step 5: Testing dependencies...\n";
try {
    $dbExists = class_exists('\App\Core\Database');
    echo "✅ Database class exists: " . ($dbExists ? 'YES' : 'NO') . "\n\n";
    
    $authExists = class_exists('\App\Core\Auth');
    echo "✅ Auth class exists: " . ($authExists ? 'YES' : 'NO') . "\n\n";
    
    $viewExists = class_exists('\App\Core\View');
    echo "✅ View class exists: " . ($viewExists ? 'YES' : 'NO') . "\n\n";
    
    $sessionExists = class_exists('\App\Core\Session');
    echo "✅ Session class exists: " . ($sessionExists ? 'YES' : 'NO') . "\n\n";
    
} catch (Exception $e) {
    echo "❌ Dependency check failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

echo "Step 6: Testing Database singleton...\n";
try {
    $db = \App\Core\Database::getInstance();
    echo "✅ Database singleton works\n\n";
} catch (Exception $e) {
    echo "❌ Database singleton failed: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n\n";
    exit(1);
}

echo "Step 7: Attempting HomeController instantiation...\n";
try {
    $controller = new \App\Controllers\HomeController();
    echo "✅ HomeController instantiated successfully!\n\n";
} catch (Exception $e) {
    echo "❌ HomeController instantiation failed: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
    echo "📍 Trace:\n" . $e->getTraceAsString() . "\n\n";
    exit(1);
}

echo "🎉 All tests passed!\n";
?>


