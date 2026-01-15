<?php
// debug_middleware.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/app/bootstrap.php';

// Mock Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';
$_SESSION['csrf_token'] = 'test_token'; // Mock CSRF token

echo "Simulating Router Dispatch...\n";

// Mock Request
$_SERVER['REQUEST_URI'] = '/api/firms/create';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['name'] = 'DebugFirmMiddleware';
$_POST['nonce'] = 'skip';
// Add CSRF token for middleware
$_POST['csrf_token'] = 'test_token';

try {
    // Instantiate Router
    $router = new \App\Core\Router();

    // Manually add the route to avoid parsing entire routes.php if it has issues,
    // OR verify if loading routes.php is safe.
    // Let's load routes.php to be authentic.
    require __DIR__ . '/app/routes.php';

    echo "Routes loaded. Dispatching...\n";

    ob_start();
    $router->dispatch();
    $output = ob_get_clean();

    echo "Dispatch execution complete. Output length: " . strlen($output) . "\n";
    echo "Output snippet: " . substr($output, 0, 300) . "\n";
} catch (\Throwable $e) {
    echo "\nROUTER CRASH:\n";
    echo $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}
