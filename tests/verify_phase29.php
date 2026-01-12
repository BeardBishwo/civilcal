<?php
// tests/verify_phase29.php

define('APP_ROOT', __DIR__ . '/..');
$_SERVER['DOCUMENT_ROOT'] = 'c:/laragon/www/Bishwo_Calculator';
require_once APP_ROOT . '/app/bootstrap.php';

if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user'] = ['id' => 1, 'role' => 'user'];

// Helper for isolated tests
function run_isolated_test($scriptCode)
{
    $tempFile = __DIR__ . '/temp_test_' . uniqid() . '.php';
    $fullScript = "<?php
    define('APP_ROOT', '" . str_replace('\\', '/', APP_ROOT) . "');
    \$_SERVER['DOCUMENT_ROOT'] = 'c:/laragon/www/Bishwo_Calculator';
    require_once APP_ROOT . '/app/bootstrap.php';
    if (session_status() === PHP_SESSION_NONE) session_start();
    \$_SESSION['user_id'] = 1; 
    \$_SESSION['user'] = ['id' => 1, 'role' => 'user'];
    \$_SESSION['csrf_token'] = 'valid_token';
    \$_SESSION['csrf_expiry'] = time() + 3600;

    $scriptCode
    ";

    file_put_contents($tempFile, $fullScript);
    $output = shell_exec("php $tempFile");
    unlink($tempFile);
    return $output;
}

echo "=== Verify Phase 29: Shop & Project Security ===\n";

// TEST 1: ProjectController CSRF Rejection
echo "\nTest 1: ProjectController Store - Missing CSRF\n";
$output = run_isolated_test("
    \$_SERVER['REQUEST_METHOD'] = 'POST';
    (new \App\Controllers\ProjectController())->store();
");
if (strpos($output, 'Invalid CSRF') !== false) {
    echo "PASS: Rejected missing CSRF\n";
} else {
    echo "FAIL: Did not reject missing CSRF. Output: $output\n";
}

// TEST 2: ProjectController Delete - Missing CSRF
echo "\nTest 2: ProjectController Delete - Missing CSRF\n";
$output = run_isolated_test("
    \$_SERVER['REQUEST_METHOD'] = 'POST';
    (new \App\Controllers\ProjectController())->delete(1);
");
if (strpos($output, 'Invalid CSRF') !== false) {
    echo "PASS: Rejected missing CSRF\n";
} else {
    echo "FAIL: Did not reject missing CSRF. Output: $output\n";
}

// TEST 3: ShopController Nonce Rejection
echo "\nTest 3: ShopController Purchase - Missing/Invalid Nonce\n";
$output = run_isolated_test("
    \$_SERVER['REQUEST_METHOD'] = 'POST';
    file_put_contents('php://input', json_encode(['item_id' => 1, 'nonce' => 'invalid']));
    (new \App\Controllers\ShopController())->purchase();
");
if (strpos($output, 'Invalid security token') !== false) {
    echo "PASS: Rejected invalid Nonce\n";
} else {
    echo "FAIL: Did not reject invalid Nonce. Output: $output\n";
}


// TEST 4: ShopController Check Structure (Static Analysis check)
// Since we can't easily mock the DB Transaction + Locking in this simple script without setting up
// a real item and user balance, we will check if the Purchase Logic contains the specific Locking instruction.
$shopContent = file_get_contents(APP_ROOT . '/app/Controllers/ShopController.php');
if (strpos($shopContent, 'FOR UPDATE') !== false && strpos($shopContent, 'beginTransaction') !== false) {
    echo "\nTest 4: ShopController Locking Logic Present\n";
    echo "PASS: 'FOR UPDATE' and 'beginTransaction' found in code.\n";
} else {
    echo "\nTest 4: ShopController Locking Logic Present\n";
    echo "FAIL: Locking logic not found source code.\n";
}

echo "\n=== Verification Complete ===\n";
