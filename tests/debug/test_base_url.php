<?php

/**
 * Test script to verify APP_BASE auto-detection
 */

// Simulate different installation scenarios
$testCases = [
    [
        'name' => 'Subdirectory Installation (Current)',
        'SCRIPT_NAME' => '/Bishwo_Calculator/public/index.php',
        'expected' => '/Bishwo_Calculator'
    ],
    [
        'name' => 'Root Installation',
        'SCRIPT_NAME' => '/public/index.php',
        'expected' => ''
    ],
    [
        'name' => 'Root without public',
        'SCRIPT_NAME' => '/index.php',
        'expected' => ''
    ],
    [
        'name' => 'Different Subdirectory',
        'SCRIPT_NAME' => '/calculator/public/index.php',
        'expected' => '/calculator'
    ],
    [
        'name' => 'Nested Subdirectory',
        'SCRIPT_NAME' => '/apps/calculator/public/index.php',
        'expected' => '/apps/calculator'
    ]
];

echo "=== APP_BASE Auto-Detection Test ===\n\n";

foreach ($testCases as $test) {
    $scriptName = $test['SCRIPT_NAME'];
    $scriptDir = dirname($scriptName);

    // Remove /public suffix if present
    if (substr($scriptDir, -7) === '/public') {
        $scriptDir = substr($scriptDir, 0, -7);
    }

    // Normalize root path to empty string
    if ($scriptDir === '/' || $scriptDir === '\\' || $scriptDir === '.') {
        $scriptDir = '';
    }

    $passed = $scriptDir === $test['expected'] ? '✓ PASS' : '✗ FAIL';

    echo "Test: {$test['name']}\n";
    echo "  SCRIPT_NAME: {$test['SCRIPT_NAME']}\n";
    echo "  Expected: '{$test['expected']}'\n";
    echo "  Got: '{$scriptDir}'\n";
    echo "  Result: {$passed}\n\n";
}

// Test current installation
echo "=== Current Installation ===\n";
require_once __DIR__ . '/app/Config/config.php';
echo "APP_BASE: '" . APP_BASE . "'\n";
echo "APP_URL: '" . APP_URL . "'\n\n";

// Test URL helpers
require_once __DIR__ . '/app/Helpers/functions.php';
echo "=== URL Helper Tests ===\n";
echo "app_base_url('/'): " . app_base_url('/') . "\n";
echo "app_base_url('/admin'): " . app_base_url('/admin') . "\n";
echo "app_base_url('/admin/dashboard'): " . app_base_url('/admin/dashboard') . "\n";
echo "asset_url('css/admin.css'): " . asset_url('css/admin.css') . "\n";
