<?php

/**
 * Verification Script for Phase 27 - Isolated Process Version
 */

require_once __DIR__ . '/../app/bootstrap.php';

// Verification Script for Phase 27 - Robust Version

require_once __DIR__ . '/../app/bootstrap.php';

use App\Models\User;

function run_test_step($name, $closure)
{
    echo "Running Test: $name... ";
    try {
        if ($closure()) {
            echo "\033[32mPASS\033[0m\n";
        } else {
            echo "\033[31mFAIL\033[0m\n";
        }
    } catch (Exception $e) {
        echo "\033[31mERROR: " . $e->getMessage() . "\033[0m\n";
    }
}

// 1. AJAX Check Verification
run_test_step("AJAX Availability Check", function () {
    $tempFile = __DIR__ . '/temp_ajax.php';
    file_put_contents($tempFile, "<?php
require_once '" . __DIR__ . "/../app/bootstrap.php';
if (session_status() === PHP_SESSION_NONE) session_start();
\$_GET['username'] = 'admin';
(new \App\Controllers\AuthController())->checkUsernameAvailability();
");

    $output = shell_exec("php $tempFile");
    @unlink($tempFile);

    $data = json_decode($output, true);
    return isset($data['available']);
});

// 2. User Creation Verification
run_test_step("Interactive User Creation", function () {
    $testUser = 'ver27_' . bin2hex(random_bytes(3));

    // Create the script that will potentially exit()
    $tempFile = __DIR__ . '/temp_create.php';
    file_put_contents($tempFile, "<?php
require_once '" . __DIR__ . "/../app/bootstrap.php';
if (session_status() === PHP_SESSION_NONE) session_start();

\$_SESSION['pending_google_user'] = [
    'email' => '{$testUser}@example.com',
    'first_name' => 'Verify',
    'last_name' => 'Phase27'
];
\$_POST['username'] = '$testUser';

// This will create user and likely exit()
(new \App\Controllers\AuthController())->processGoogleConfirm();
");

    // Run it (ignoring output as we check DB side effect)
    shell_exec("php $tempFile");
    @unlink($tempFile);

    // Check main DB for the user
    $userModel = new User();
    $user = $userModel->findByUsername($testUser);

    if ($user) {
        // Cleanup
        $db = \App\Core\Database::getInstance();
        $db->prepare("DELETE FROM users WHERE id = ?")->execute([$user->id]);
        return true;
    }
    return false;
});
