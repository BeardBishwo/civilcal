<?php
// Test notification API endpoint directly
require_once __DIR__ . '/app/bootstrap.php';

// Mock admin session
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}
$_SESSION['user_id'] = 3;
$_SESSION['role'] = 'admin';

echo "=== TESTING NOTIFICATION API ===\n\n";

// Test 1: Instantiate controller
try {
    $controller = new \App\Controllers\Admin\NotificationController();
    echo "✓ Admin NotificationController instantiated\n";
} catch (Exception $e) {
    echo "✗ Failed to instantiate controller: " . $e->getMessage() . "\n";
    exit;
}

// Test 2: Call getUnreadCount
echo "\nTesting getUnreadCount()...\n";
ob_start();
try {
    $controller->getUnreadCount();
    $output = ob_get_clean();
    echo "Response: " . $output . "\n";
    
    $json = json_decode($output, true);
    if ($json && isset($json['success'])) {
        echo "✓ Valid JSON response\n";
        echo "  Success: " . ($json['success'] ? 'true' : 'false') . "\n";
        echo "  Unread count: " . ($json['unread_count'] ?? 'N/A') . "\n";
    } else {
        echo "✗ Invalid JSON response\n";
    }
} catch (Exception $e) {
    ob_end_clean();
    echo "✗ Exception: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== END TEST ===\n";
