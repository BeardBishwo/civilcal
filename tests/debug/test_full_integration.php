<?php
// Full integration test simulating the exact browser request
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Full Integration Test ===\n\n";

define('BASE_PATH', __DIR__);
require_once __DIR__ . '/app/bootstrap.php';

// Start session
session_start();
$_SESSION['user_id'] = 1;

// Step 1: Generate nonce (like the page does)
echo "1. Generating nonce for firm_create...\n";
$nonceService = new \App\Services\NonceService();
$nonceData = $nonceService->generate(1, 'firm_create');
echo "   ✓ Nonce: " . substr($nonceData['nonce'], 0, 20) . "...\n\n";

// Step 2: Generate CSRF token
$csrfToken = \App\Services\Security::generateCsrfToken();
echo "2. Generated CSRF token: " . substr($csrfToken, 0, 20) . "...\n\n";

// Step 3: Simulate the exact POST request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/Bishwo_Calculator/api/firms/create';
$_POST = [
    'name' => 'Integration Test Firm',
    'description' => 'Full test',
    'nonce' => $nonceData['nonce'],
    'trap_answer' => '',
    'csrf_token' => $csrfToken
];

echo "3. Simulating POST request to /api/firms/create...\n";
echo "   POST data prepared\n\n";

// Step 4: Call the controller directly
echo "4. Creating FirmController and calling create()...\n";
try {
    // Capture output
    ob_start();

    $controller = new \App\Controllers\Quiz\FirmController();
    $controller->create();

    $output = ob_get_clean();

    echo "   ✓ Controller executed successfully\n";
    echo "   Response:\n";
    echo "   " . str_replace("\n", "\n   ", $output) . "\n";

    // Try to decode as JSON
    $json = json_decode($output, true);
    if ($json) {
        echo "\n   Parsed JSON:\n";
        echo "   - Success: " . ($json['success'] ? 'true' : 'false') . "\n";
        echo "   - Message: " . ($json['message'] ?? 'N/A') . "\n";
        if (isset($json['redirect'])) {
            echo "   - Redirect: " . $json['redirect'] . "\n";
        }
    }
} catch (Throwable $e) {
    echo "   ✗ Exception caught:\n";
    echo "   Type: " . get_class($e) . "\n";
    echo "   Message: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\n   Stack trace:\n";
    foreach (explode("\n", $e->getTraceAsString()) as $line) {
        echo "   " . $line . "\n";
    }
}

echo "\n=== Test Complete ===\n";
