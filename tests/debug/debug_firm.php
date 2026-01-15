<?php
// Save this as debug_firm.php in root
define('APP_ROOT', __DIR__);

// Mock session
session_start();
$_SESSION['user_id'] = 1; // Assume generic user
$_SESSION['role'] = 'admin';

// Load bootstrap logic (mimicking public/index.php but capturing errors)
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // We need to verify what public/index.php loads.
    // Assuming it loads ../app/init.php or similar.
    // Based on previous view, public/index.php is the entry point.
    // Let's attempt to load the autoloader/init manually if we can infer it.

    // Check if vendor autoload exists
    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';
    }

    // Check for app/init.php
    if (file_exists(__DIR__ . '/app/init.php')) {
        require_once __DIR__ . '/app/init.php';
    } else {
        // Fallback or guess
        echo "Could not find app/init.php\n";
    }

    echo "Bootstrap loaded.\n";

    echo "Instantiating FirmController...\n";
    $controller = new \App\Controllers\Quiz\FirmController();
    echo "Controller Instantiated.\n";

    echo "Calling create() method (simulated)...\n";
    // Mock POST data
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST['name'] = 'DebugFirm';
    $_POST['nonce'] = 'skip';

    $controller->create();
    echo "\nCreate executed successfully.\n";
} catch (\Throwable $e) {
    echo "\nCRASHED:\n";
    echo $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}
