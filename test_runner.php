<?php

// Simple test runner to verify the AuthController fixes
require_once 'app/bootstrap.php';
require_once 'app/Controllers/Api/AuthController.php';
require_once 'app/Models/User.php';

// Turn off output buffering to prevent header errors
while (ob_get_level()) {
    ob_end_clean();
}

// Set up test environment
session_start();
$_SESSION = []; // Clear session

// Create test user
$userModel = new App\Models\User();
$testUser = [
    'username' => 'testuser_' . time(),
    'email' => 'test' . time() . '@example.com',
    'password' => password_hash('testpass123', PASSWORD_DEFAULT),
    'first_name' => 'Test',
    'last_name' => 'User',
    'role' => 'user',
    'is_admin' => 0
];

// Clean up any existing test user
$userModel->deleteByUsername($testUser['username']);
$userModel->deleteByEmail($testUser['email']);

// Create test user
$userId = $userModel->create($testUser);
$testUser['id'] = $userId;

echo "Test user created: " . $testUser['username'] . " (ID: " . $testUser['id'] . ")\n";

// Test 1: Login with POST data (should work now)
echo "\n=== Test 1: Login with POST data ===\n";
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'username_email' => $testUser['username'],
    'password' => 'testpass123'
];

$authController = new App\Controllers\Api\AuthController();
ob_start();
$authController->login();
$output = ob_get_clean();

echo "Response: " . $output . "\n";
$result = json_decode($output, true);
if (is_array($result) && isset($result['success']) && $result['success']) {
    echo "✅ Login with POST data: PASSED\n";
} else {
    echo "❌ Login with POST data: FAILED - " . (isset($result['error']) ? $result['error'] : 'Unknown error') . "\n";
}

// Test 2: Login with invalid credentials
echo "\n=== Test 2: Login with invalid credentials ===\n";
$_POST = [
    'username_email' => $testUser['username'],
    'password' => 'wrongpassword'
];

ob_start();
$authController->login();
$output = ob_get_clean();

echo "Response: " . $output . "\n";
$result = json_decode($output, true);
if (is_array($result) && isset($result['error']) && strpos($result['error'], 'Invalid username or password') !== false) {
    echo "✅ Invalid credentials test: PASSED\n";
} else {
    echo "❌ Invalid credentials test: FAILED\n";
}

// Test 3: Check username availability
echo "\n=== Test 3: Check username availability ===\n";
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['username'] = 'nonexistentuser_' . time();

ob_start();
$authController->checkUsername();
$output = ob_get_clean();

echo "Response: " . $output . "\n";
$result = json_decode($output, true);
if (is_array($result) && isset($result['available']) && $result['available']) {
    echo "✅ Username availability test: PASSED\n";
} else {
    echo "❌ Username availability test: FAILED\n";
}

// Clean up
$userModel->delete($testUser['id']);
echo "\nTest user cleaned up.\n";

echo "\n=== Summary ===\n";
echo "The AuthController fixes have been implemented to support both JSON and POST data input.\n";
echo "This should resolve the test failures related to JSON input handling.\n";