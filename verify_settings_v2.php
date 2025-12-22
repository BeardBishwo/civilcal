<?php
// Verification Script for Settings Persistence V2
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Core/Container.php';

// Initialize App
$container = \App\Core\Container::create();
$db = \App\Core\Database::getInstance();

// Helper to simulate POST request via internal method call (since we are CLI)
function test_save_advanced() {
    echo "\nTesting Advanced Settings Save logic...\n";
    
    // Mock $_POST
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_POST = [
        'csrf_token' => 'mock_token', // We might need to bypass CSRF or mock session
        'cache_enabled' => '1',
        'force_https' => '1',
        'api_enabled' => '1'
    ];
    
    // We need to bypass CSRF verification for this test script
    // Or we can manually insert a token into session
    $_SESSION['csrf_token'] = 'mock_token';
    
    // Instantiate Controller
    $controller = new \App\Controllers\Admin\SettingsController();
    
    // Capture output
    ob_start();
    try {
        $controller->saveAdvanced();
    } catch (ExitException $e) {
        // Expected if it exits
    }
    $output = ob_get_clean();
    
    echo "Controller Output: $output\n";
    
    // Verify DB
    $db = \App\Core\Database::getInstance();
    
    // Check cache_enabled (should be in 'performance' group)
    $stmt = $db->prepare("SELECT setting_value, setting_group FROM settings WHERE setting_key = ?");
    $stmt->execute(['cache_enabled']);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "cache_enabled: Value=" . ($res['setting_value'] ?? 'NULL') . ", Group=" . ($res['setting_group'] ?? 'NULL') . " (Expected: performance)\n";
    
    // Check force_https (should be in 'security' group)
    $stmt->execute(['force_https']);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "force_https: Value=" . ($res['setting_value'] ?? 'NULL') . ", Group=" . ($res['setting_group'] ?? 'NULL') . " (Expected: security)\n";
    
    // Check api_enabled (should be in 'api' group)
    $stmt->execute(['api_enabled']);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "api_enabled: Value=" . ($res['setting_value'] ?? 'NULL') . ", Group=" . ($res['setting_group'] ?? 'NULL') . " (Expected: api)\n";
}

// Clean up previous test
$db->query("DELETE FROM settings WHERE setting_key IN ('cache_enabled', 'force_https', 'api_enabled')");

test_save_advanced();
