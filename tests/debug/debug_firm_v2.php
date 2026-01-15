<?php
// debig_firm_v2.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Correctly load bootstrap
require_once __DIR__ . '/app/bootstrap.php';

echo "Bootstrap loaded. BASE_PATH is " . BASE_PATH . "\n";

// Mock Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';

try {
    echo "Instantiating FirmController...\n";
    $controller = new \App\Controllers\Quiz\FirmController();
    echo "Controller Instantiated.\n";

    echo "Calling create() method via simulation...\n";
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST['name'] = 'DebugFirmValid';
    $_POST['nonce'] = 'skip';

    // Capture output to prevent header error
    ob_start();
    $controller->create();
    $output = ob_get_clean();

    echo "Create executed. Output length: " . strlen($output) . "\n";
    echo "Output snippet: " . substr($output, 0, 100) . "\n";
} catch (\Throwable $e) {
    echo "\nCRASHED:\n";
    echo $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}
