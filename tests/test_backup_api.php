<?php
// Test script to check if the backup API is working

require_once __DIR__ . '/app/bootstrap.php';

// Start session
session_start();

// Set a test user ID to simulate admin access
$_SESSION['user_id'] = 1; // Assuming user ID 1 is an admin

// Include the AdminController
require_once __DIR__ . '/app/Controllers/Api/AdminController.php';

$controller = new \App\Controllers\Api\AdminController();

try {
    // Test the createBackup method directly
    echo "Testing backup creation...\n";
    // We can't actually call the method directly because it sends headers
    // But we can check if the method exists
    if (method_exists($controller, 'createBackup')) {
        echo "✓ createBackup method exists\n";
    } else {
        echo "✗ createBackup method does not exist\n";
    }
    
    echo "Testing route definition...\n";
    // Check if the route is defined in routes.php
    $routesContent = file_get_contents(__DIR__ . '/app/routes.php');
    if (strpos($routesContent, '/api/admin/backup/create') !== false) {
        echo "✓ Route is defined in routes.php\n";
    } else {
        echo "✗ Route is not defined in routes.php\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>