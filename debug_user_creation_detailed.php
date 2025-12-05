<?php
// Detailed debug script to identify user creation issues

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== DETAILED USER CREATION DEBUG ===\n\n";

// Include required files
require_once 'app/Core/Database.php';
require_once 'app/Models/User.php';
require_once 'app/Controllers/Admin/UserManagementController.php';

echo "1. Testing the exact scenario that fails...\n";

// Simulate the exact form data that would be submitted
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
$_SERVER['HTTP_X_CSRF_TOKEN'] = 'test-csrf-token';
$_SESSION['csrf_token'] = 'test-csrf-token';
$_SESSION['user_id'] = 1; // Simulate logged in admin

// Prepare test form data
$_POST = [
    'first_name' => 'Debug',
    'last_name' => 'User',
    'username' => 'debuguser_' . time(),
    'email' => 'debuguser_' . time() . '@example.com',
    'password' => 'testpass123',
    'password_confirmation' => 'testpass123',
    'role' => 'user',
    'is_active' => '1',
    'email_verified' => '1',
    'terms_agreed' => '1',
    'marketing_emails' => '0',
    'send_welcome_email' => '0',
    'csrf_token' => 'test-csrf-token'
];

echo "2. Simulated form data:\n";
foreach ($_POST as $key => $value) {
    echo "   $key: $value\n";
}

echo "\n3. Testing UserManagementController store method...\n";

try {
    // Create a mock controller instance
    $reflection = new ReflectionClass('App\\Controllers\\Admin\\UserManagementController');
    
    // Create controller without calling constructor
    $controller = $reflection->newInstanceWithoutConstructor();
    
    // Manually set required properties
    $controller->db = (new \App\Models\User())->getDb();
    $controller->view = new class {
        public function render($view, $data = []) { return ''; }
    };
    
    echo "   ✓ Controller created\n";
    
    // Call the store method directly
    ob_start();
    try {
        $result = $controller->store();
        echo "   ✓ Store method executed successfully\n";
        $output = ob_get_clean();
        if (!empty($output)) {
            echo "   Output received:\n";
            echo "   " . str_replace("\n", "\n   ", $output) . "\n";
        }
    } catch (Exception $e) {
        ob_end_clean();
        echo "   ❌ Store method failed: " . $e->getMessage() . "\n";
        echo "   File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
        echo "   Stack trace:\n";
        echo "   " . str_replace("\n", "\n   ", $e->getTraceAsString()) . "\n";
        
        // Additional debug info
        echo "\n4. Debug information:\n";
        echo "   HTTP_X_REQUESTED_WITH: " . ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? 'not set') . "\n";
        echo "   HTTP_X_CSRF_TOKEN: " . ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? 'not set') . "\n";
        echo "   Session csrf_token: " . ($_SESSION['csrf_token'] ?? 'not set') . "\n";
        echo "   POST csrf_token: " . ($_POST['csrf_token'] ?? 'not set') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Failed to test controller: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
