<?php
// Simpler test - just check if the endpoint is accessible
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "=== Testing Firm Creation Flow ===\n\n";

// Load app
define('BASE_PATH', __DIR__);
require_once __DIR__ . '/app/bootstrap.php';

// Start session with user
session_start();
$_SESSION['user_id'] = 1;

// Create a real nonce
echo "1. Creating nonce...\n";
$nonceService = new \App\Services\NonceService();
$nonceData = $nonceService->generate(1, 'firm_create');
echo "   ✓ Nonce created: " . substr($nonceData['nonce'], 0, 20) . "...\n\n";

// Simulate POST
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['name'] = 'Test Firm';
$_POST['description'] = 'Test Description';
$_POST['nonce'] = $nonceData['nonce'];
$_POST['trap_answer'] = '';

echo "2. Creating controller and calling create()...\n";
try {
    $controller = new \App\Controllers\Quiz\FirmController();
    $controller->create();
    echo "\n✓ Success!\n";
} catch (Throwable $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
