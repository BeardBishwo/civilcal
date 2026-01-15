<?php
// Test CSRF middleware directly
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Testing CSRF Middleware ===\n\n";

define('BASE_PATH', __DIR__);
require_once __DIR__ . '/app/bootstrap.php';

// Start session
session_start();
$_SESSION['user_id'] = 1;

// Generate CSRF token
$csrfToken = \App\Services\Security::generateCsrfToken();
echo "1. Generated CSRF token: " . substr($csrfToken, 0, 20) . "...\n";
echo "   Session CSRF token: " . substr($_SESSION['csrf_token'] ?? 'NONE', 0, 20) . "...\n\n";

// Simulate POST request with CSRF token
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['csrf_token'] = $csrfToken;
$_POST['name'] = 'Test Firm';
$_POST['description'] = 'Test';
$_POST['nonce'] = 'test_nonce';
$_POST['trap_answer'] = '';

echo "2. Testing CSRF validation...\n";
$isValid = \App\Services\Security::validateCsrfToken($csrfToken);
echo "   Result: " . ($isValid ? "VALID ✓" : "INVALID ✗") . "\n\n";

// Test the middleware
echo "3. Testing CsrfMiddleware::handle()...\n";
try {
    $middleware = new \App\Middleware\CsrfMiddleware();
    $request = [
        'method' => 'POST',
        'uri' => '/api/firms/create',
        'post' => $_POST
    ];

    $nextCalled = false;
    $next = function ($req) use (&$nextCalled) {
        $nextCalled = true;
        return "Next called successfully";
    };

    $result = $middleware->handle($request, $next);

    if ($nextCalled) {
        echo "   ✓ Middleware passed, next() was called\n";
    } else {
        echo "   ✗ Middleware blocked the request\n";
        echo "   Result: " . print_r($result, true) . "\n";
    }
} catch (Throwable $e) {
    echo "   ✗ Exception in middleware:\n";
    echo "   Type: " . get_class($e) . "\n";
    echo "   Message: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
