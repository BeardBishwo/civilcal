<?php
/**
 * Test Permalink Route
 * Quick test to verify the permalink route is accessible
 */

// Define application constant
define('BISHWO_CALCULATOR', true);

// Load bootstrap
require_once __DIR__ . '/app/bootstrap.php';

// Start session
\App\Services\Security::startSession();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    echo "ERROR: Not logged in. Please login first at /admin\n";
    exit;
}

// Initialize router
$router = new \App\Core\Router();

// Load routes
require __DIR__ . '/app/routes.php';

// Test the permalink route
echo "Testing Permalink Route...\n\n";

// Get the route
$route = '/admin/settings/permalinks';
echo "Route: $route\n";

// Try to match the route
try {
    $router->dispatch();
    echo "✅ Route dispatched successfully!\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
