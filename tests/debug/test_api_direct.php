<?php
// Direct API test with full error reporting
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/api_test_error.log');
error_reporting(E_ALL);

// Start output buffering to catch any output
ob_start();

try {
    echo "=== Direct API Test ===\n\n";

    // Load bootstrap
    define('BASE_PATH', __DIR__);
    require_once __DIR__ . '/app/bootstrap.php';

    // Start session
    session_start();
    $_SESSION['user_id'] = 1;

    // Simulate POST request
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST['name'] = 'Test Firm Direct';
    $_POST['description'] = 'Direct API test';
    $_POST['nonce'] = 'skip_nonce_for_test';
    $_POST['trap_answer'] = '';

    echo "1. Creating FirmController instance...\n";
    $controller = new \App\Controllers\Quiz\FirmController();
    echo "   ✓ Controller created\n\n";

    echo "2. Calling create() method...\n";

    // Temporarily bypass nonce validation for testing
    $reflection = new ReflectionClass($controller);
    $nonceProperty = $reflection->getProperty('nonceService');
    $nonceProperty->setAccessible(true);

    // Create a mock nonce service
    $mockNonce = new class {
        public function validateAndConsume($nonce, $userId, $type)
        {
            echo "   [MOCK] Nonce validation bypassed for testing\n";
            return true;
        }
    };

    $nonceProperty->setValue($controller, $mockNonce);

    $controller->create();

    echo "\n3. Method executed successfully\n";
} catch (Throwable $e) {
    echo "\n✗ ERROR CAUGHT:\n";
    echo "   Type: " . get_class($e) . "\n";
    echo "   Message: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "   Trace:\n";
    echo $e->getTraceAsString() . "\n";
}

$output = ob_get_clean();
echo $output;

// Also save to file
file_put_contents(__DIR__ . '/api_test_output.txt', $output);
echo "\n\nOutput saved to api_test_output.txt\n";
