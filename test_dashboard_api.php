<?php
// Test the dashboard API endpoint
define('BISHWO_CALCULATOR', true);
require_once __DIR__ . '/app/bootstrap.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Mock admin user in session for testing
$_SESSION['is_admin'] = true;
$_SESSION['user'] = [
    'id' => 1,
    'username' => 'admin',
    'role' => 'admin',
    'is_admin' => true
];

$controller = new \App\Controllers\Admin\DashboardController();

// Capture the output
ob_start();
$controller->getDashboardData();
$output = ob_get_clean();

echo "Output:\n";
echo $output;

echo "\n\nHeaders:\n";
foreach (headers_list() as $header) {
    echo $header . "\n";
}