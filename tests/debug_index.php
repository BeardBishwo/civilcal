<?php
/**
 * Debug version of index.php to check installation detection
 */

// Define application constant
define('BISHWO_CALCULATOR', true);

echo "🔍 DEBUG: Installation Check<br>";
echo "===================<br><br>";

// Load application bootstrap FIRST
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

// Now check BASE_PATH
echo "BASE_PATH: " . (defined('BASE_PATH') ? BASE_PATH : 'NOT DEFINED') . "<br><br>";

// Check installation files
if (defined('BASE_PATH')) {
    $configFile = BASE_PATH . '/config/installed.lock';
    $envFile = BASE_PATH . '/.env';
    
    echo "Config file path: $configFile<br>";
    echo "Config file exists: " . (file_exists($configFile) ? 'YES' : 'NO') . "<br>";
    echo "Env file path: $envFile<br>";
    echo "Env file exists: " . (file_exists($envFile) ? 'YES' : 'NO') . "<br><br>";
    
    $isInstalled = file_exists($configFile) && file_exists($envFile);
    echo "Is Installed: " . ($isInstalled ? 'YES' : 'NO') . "<br><br>";
} else {
    echo "❌ BASE_PATH not defined, cannot check installation<br>";
}

if (defined('BASE_PATH')) {
    echo "Directory contents of BASE_PATH:<br>";
    $files = scandir(BASE_PATH);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "- $file<br>";
        }
    }
}
?>


