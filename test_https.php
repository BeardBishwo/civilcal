<?php
require_once 'app/bootstrap.php';

use App\Services\SettingsService;
use App\Services\Security;

echo "Testing Force HTTPS Logic...\n";

// 1. Manually set force_https to '1' for test
SettingsService::set('force_https', '1');
$val = SettingsService::get('force_https');
echo "Current force_https setting: $val\n";

// 2. Mocking $_SERVER for non-https
$_SERVER['HTTPS'] = null;
$_SERVER['SERVER_NAME'] = 'example.com';
$_SERVER['HTTP_HOST'] = 'example.com';
$_SERVER['REQUEST_URI'] = '/test-path';

echo "Simulating request to http://example.com/test-path\n";

// We can't actually call enforceHttps() and let it exit, so we'll inspect the theory
$forceHttps = SettingsService::get('force_https', '0') === '1';
$isHttps = isset($_SERVER['HTTPS']);
$isLocalhost = $_SERVER['SERVER_NAME'] === 'localhost';

echo "Force HTTPS Enabled: " . ($forceHttps ? 'Yes' : 'No') . "\n";
echo "Is HTTPS: " . ($isHttps ? 'Yes' : 'No') . "\n";
echo "Is Localhost: " . ($isLocalhost ? 'Yes' : 'No') . "\n";

if ($forceHttps && !$isHttps && !$isLocalhost) {
    echo "RESULT: REDIRECT WOULD TRIGGER\n";
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    echo "REDIRECT TO: $redirect\n";
} else {
    echo "RESULT: NO REDIRECT\n";
}

// Reset setting to 0 to avoid breaking localhost access if user isn't on localhost
SettingsService::set('force_https', '0');
echo "Reset force_https to 0.\n";
