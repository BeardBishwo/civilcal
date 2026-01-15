<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Set a fake user ID for testing
$_SESSION['user_id'] = 1;

// Load the application
define('BASE_PATH', __DIR__);
require_once __DIR__ . '/app/bootstrap.php';

// Test the FirmController create method
try {
    $controller = new \App\Controllers\Quiz\FirmController();

    // Simulate POST data
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST['name'] = 'Test Firm';
    $_POST['description'] = 'Test Description';
    $_POST['nonce'] = 'test_nonce';
    $_POST['trap_answer'] = '';

    echo "Testing FirmController::create()...\n\n";
    $controller->create();
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
