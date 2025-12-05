<?php
// Simple API test
require_once 'app/bootstrap.php';

// Mock a POST request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'username_email' => 'uniquebishwo@gmail.com',
    'password' => 'c9PU7XAsAADYk_A'
];

// Start session
session_start();

echo "Testing API login...\n";

try {
    $controller = new \App\Controllers\Api\AuthController();
    echo "Controller created successfully\n";
    
    // Capture output
    ob_start();
    $controller->login();
    $output = ob_get_clean();
    
    echo "Output: " . $output . "\n";
    
} catch (Exception $e) {
    echo "❌ API login failed: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "API test complete.\n";
?>