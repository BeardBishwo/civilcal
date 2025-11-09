<?php
/**
 * Complete debug version of index.php
 */

// Define application constant
define('BISHWO_CALCULATOR', true);

echo "🔍 COMPLETE INDEX.PHP DEBUG<br>";
echo "============================<br><br>";

// Load application bootstrap FIRST
echo "Step 1: Loading bootstrap...<br>";
$bootstrapPath = dirname(__DIR__) . '/app/bootstrap.php';
echo "Bootstrap path: $bootstrapPath<br>";
echo "Bootstrap exists: " . (file_exists($bootstrapPath) ? 'YES' : 'NO') . "<br>";

if (file_exists($bootstrapPath)) {
    require_once $bootstrapPath;
    echo "✅ Bootstrap loaded<br><br>";
} else {
    echo "❌ Bootstrap missing<br>";
    exit;
}

// Start session
echo "Step 2: Starting session...<br>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    echo "✅ Session started<br><br>";
} else {
    echo "✅ Session already active<br><br>";
}

// Check installation
echo "Step 3: Checking installation...<br>";
function isInstalled() {
    $configFile = BASE_PATH . '/config/installed.lock';
    $envFile = BASE_PATH . '/.env';
    
    $result = file_exists($configFile) && file_exists($envFile);
    echo "Config exists: " . (file_exists($configFile) ? 'YES' : 'NO') . "<br>";
    echo "Env exists: " . (file_exists($envFile) ? 'YES' : 'NO') . "<br>";
    echo "Installation status: " . ($result ? 'INSTALLED' : 'NOT INSTALLED') . "<br>";
    
    return $result;
}

$isInstalled = isInstalled();
echo "Installation check complete<br><br>";

// Skip install redirect for debugging
echo "Step 4: Skipping install redirect (debug mode)<br><br>";

// Initialize router
echo "Step 5: Initializing router...<br>";
try {
    $router = new \App\Core\Router();
    echo "✅ Router created: " . get_class($router) . "<br>";
} catch (Exception $e) {
    echo "❌ Router creation failed: " . $e->getMessage() . "<br>";
    echo "📍 File: " . $e->getFile() . "<br>";
    echo "📍 Line: " . $e->getLine() . "<br>";
    exit;
}

// Make router available globally
echo "Step 6: Setting global router...<br>";
$GLOBALS['router'] = $router;
echo "✅ Global router set<br><br>";

// Load routes
echo "Step 7: Loading routes...<br>";
$routesPath = BASE_PATH . '/app/routes.php';
echo "Routes file path: $routesPath<br>";
echo "Routes file exists: " . (file_exists($routesPath) ? 'YES' : 'NO') . "<br>";

if (file_exists($routesPath)) {
    try {
        require $routesPath;
        echo "✅ Routes loaded<br>";
        
        // Check if routes were added
        if (isset($router->routes)) {
            echo "Total routes loaded: " . count($router->routes) . "<br>";
        } else {
            echo "⚠️ Router routes property not accessible<br>";
        }
    } catch (Exception $e) {
        echo "❌ Routes loading failed: " . $e->getMessage() . "<br>";
        echo "📍 File: " . $e->getFile() . "<br>";
        echo "📍 Line: " . $e->getLine() . "<br>";
    }
} else {
    echo "❌ Routes file missing<br>";
}
echo "<br>";

// Dispatch the request
echo "Step 8: Dispatching request...<br>";
try {
    echo "Calling router->dispatch()...<br>";
    $router->dispatch();
    echo "✅ Dispatch completed<br>";
} catch (Exception $e) {
    echo "❌ Dispatch failed: " . $e->getMessage() . "<br>";
    echo "📍 File: " . $e->getFile() . "<br>";
    echo "📍 Line: " . $e->getLine() . "<br>";
    echo "📍 Trace:<br>" . nl2br($e->getTraceAsString()) . "<br>";
} catch (Error $e) {
    echo "❌ Fatal error during dispatch: " . $e->getMessage() . "<br>";
    echo "📍 File: " . $e->getFile() . "<br>";
    echo "📍 Line: " . $e->getLine() . "<br>";
    echo "📍 Trace:<br>" . nl2br($e->getTraceAsString()) . "<br>";
}

echo "<br>🎉 Debug completed!<br>";
?>
