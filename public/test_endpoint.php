<?php
// Direct endpoint test with full error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Direct Endpoint Test</h1>";
echo "<pre>";

// Load the application
define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/app/bootstrap.php';

// Start session
session_start();
$_SESSION['user_id'] = 1;

// Generate nonce
$nonceService = new \App\Services\NonceService();
$nonceData = $nonceService->generate(1, 'firm_create');

// Generate CSRF
$csrf = \App\Services\Security::generateCsrfToken();

echo "Session ID: " . session_id() . "\n";
echo "User ID: " . ($_SESSION['user_id'] ?? 'NONE') . "\n";
echo "CSRF Token: " . substr($csrf, 0, 30) . "...\n";
echo "Nonce: " . substr($nonceData['nonce'], 0, 30) . "...\n\n";

// Simulate POST
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'name' => 'Direct Test Firm',
    'description' => 'Testing directly',
    'nonce' => $nonceData['nonce'],
    'trap_answer' => '',
    'csrf_token' => $csrf
];

echo "Calling FirmController::create()...\n\n";

try {
    $controller = new \App\Controllers\Quiz\FirmController();
    $controller->create();
} catch (Throwable $e) {
    echo "ERROR:\n";
    echo "Type: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "</pre>";
