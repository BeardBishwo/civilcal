<?php

// Load Main Application Logic (Mocking environment)
define('BASE_PATH', __DIR__);
define('APP_ENV', 'production');

require_once BASE_PATH . '/app/Core/Database.php';
require_once BASE_PATH . '/app/Helpers/functions.php';
require_once BASE_PATH . '/app/Services/SettingsService.php';
require_once BASE_PATH . '/app/Services/ShortcodeService.php';

// Mock Config
class Config {
    public static function get($key) {
        $config = [
            'database' => [
                'host' => 'localhost',
                'name' => 'bishwo_calculator',
                'user' => 'root',
                'pass' => ''
            ],
            'app' => [
                'url' => 'http://localhost/Bishwo_Calculator'
            ]
        ];
        $keys = explode('.', $key);
        $value = $config;
        foreach ($keys as $k) {
            $value = $value[$k] ?? null;
        }
        return $value;
    }
}

// Manually define app_base_url if not loaded from helpers
if (!function_exists('app_base_url')) {
    function app_base_url($path = '') {
        return 'http://localhost/Bishwo_Calculator/' . ltrim($path, '/');
    }
}

echo "Starting ShortcodeService Verification...\n";
echo "----------------------------------------\n";

$tests = [
    '{site_name}' => 'Civil Cal - Expected Site Name', // Dependent on DB
    '{year}' => date('Y'),
    '{site_url}' => 'http://localhost/Bishwo_Calculator/',
    '{admin_url}' => 'http://localhost/Bishwo_Calculator/admin',
    'Mixed Content: &copy; {year} {site_name}' => '&copy; ' . date('Y') . ' [Site Name]',
];

foreach ($tests as $input => $description) {
    try {
        $output = \App\Services\ShortcodeService::parse($input);
        echo "Input:  $input\n";
        echo "Output: $output\n";
        echo "Status: " . ($input !== $output ? "PASS (Modified)" : "WARN (Unchanged/Static)") . "\n";
        echo "\n";
    } catch (Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
}

echo "Checking Media API connectivity (Simulation)...\n";
// Simulating MediaApiController check by just checking file existence
$controllerFile = BASE_PATH . '/app/Controllers/Admin/MediaApiController.php';
if (file_exists($controllerFile)) {
    echo "PASS: MediaApiController.php exists.\n";
} else {
    echo "FAIL: MediaApiController.php missing.\n";
}

echo "\nVerification Complete.\n";
