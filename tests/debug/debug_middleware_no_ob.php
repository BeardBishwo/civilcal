<?php
// debug_middleware_no_ob.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/app/bootstrap.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Do NOT set user_id, to force Auth failure and see output
// $_SESSION['user_id'] = 1;

// Mock Request to trigger API Auth flow
$_SERVER['REQUEST_URI'] = '/api/firms/create';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['name'] = 'DebugFirmMiddleware';
$_POST['nonce'] = 'skip';
// Add invalid CSRF token to trigger CSRF failure?
// Or mock correct one to trigger Auth failure?
// Let's trigger Auth failure first (clean 401 expected).
// To pass CSRF, we need to mock it in Security service
$_SESSION['csrf_token'] = 'test_token';
$_POST['csrf_token'] = 'test_token';
$_SERVER['HTTP_X_CSRF_TOKEN'] = 'test_token';

try {
    $router = new \App\Core\Router();
    require __DIR__ . '/app/routes.php';

    echo "Dispatching...\n";
    $router->dispatch();
    echo "\nDispatch finished (this should not be reached if exit called).\n";
} catch (\Throwable $e) {
    echo "\nROUTER CRASH:\n";
    echo $e->getMessage() . "\n";
}
