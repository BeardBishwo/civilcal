<?php

/**
 * URL Verification Script
 * Tests all application URLs to ensure they are accessible and return expected status codes
 */

require_once __DIR__ . '/../app/bootstrap.php';

// Color output helpers
function colorize($text, $color)
{
    $colors = [
        'green' => "\033[32m",
        'red' => "\033[31m",
        'yellow' => "\033[33m",
        'blue' => "\033[34m",
        'reset' => "\033[0m"
    ];
    return $colors[$color] . $text . $colors['reset'];
}

// Test a URL and return status
function testUrl($url, $description, $cookies = null)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    if ($cookies) {
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    $status = 'UNKNOWN';
    $color = 'yellow';

    if ($error) {
        $status = 'ERROR: ' . $error;
        $color = 'red';
    } elseif ($httpCode == 200) {
        $status = 'OK (200)';
        $color = 'green';
    } elseif ($httpCode == 302 || $httpCode == 301) {
        $status = 'REDIRECT (' . $httpCode . ')';
        $color = 'blue';
    } elseif ($httpCode == 404) {
        $status = 'NOT FOUND (404)';
        $color = 'red';
    } elseif ($httpCode == 500) {
        $status = 'SERVER ERROR (500)';
        $color = 'red';
    } else {
        $status = 'HTTP ' . $httpCode;
        $color = 'yellow';
    }

    printf("%-50s %s\n", $description, colorize($status, $color));

    return [
        'url' => $url,
        'description' => $description,
        'status_code' => $httpCode,
        'status' => $status,
        'error' => $error
    ];
}

echo "\n" . colorize("=== Bishwo Calculator URL Verification ===", "blue") . "\n\n";

$baseUrl = 'http://localhost/Bishwo_Calculator';
$results = [];

// First, login to get session cookies
echo colorize("Step 1: Authenticating...", "blue") . "\n";
$loginUrl = $baseUrl . '/login';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$loginPage = curl_exec($ch);
curl_close($ch);

// Extract CSRF token
preg_match('/name="csrf_token" value="([^"]+)"/', $loginPage, $matches);
$csrfToken = $matches[1] ?? '';

// Extract session cookie
preg_match('/Set-Cookie: ([^;]+)/', $loginPage, $cookieMatches);
$sessionCookie = $cookieMatches[1] ?? '';

if (!$csrfToken || !$sessionCookie) {
    echo colorize("Failed to get CSRF token or session cookie\n", "red");
    exit(1);
}

// Perform login
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'email' => 'uniquebishwo@gmail.com',
    'password' => 'c9PU7XAsAADYk_A',
    'csrf_token' => $csrfToken
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIE, $sessionCookie);
$loginResponse = curl_exec($ch);
curl_close($ch);

// Extract auth cookies from login response
preg_match_all('/Set-Cookie: ([^;]+)/', $loginResponse, $allCookies);
$cookies = implode('; ', $allCookies[1] ?? []);

if (strpos($loginResponse, '"success":true') !== false) {
    echo colorize("✓ Login successful\n\n", "green");
} else {
    echo colorize("✗ Login failed\n", "red");
    echo "Response: " . substr($loginResponse, -200) . "\n";
    exit(1);
}

// Test URLs
echo colorize("Step 2: Testing URLs...", "blue") . "\n\n";

echo colorize("--- Public Routes ---", "yellow") . "\n";
$results[] = testUrl($baseUrl . '/', 'Homepage', null);
$results[] = testUrl($baseUrl . '/login', 'Login Page', null);
$results[] = testUrl($baseUrl . '/register', 'Register Page', null);

echo "\n" . colorize("--- Authenticated Routes ---", "yellow") . "\n";
$results[] = testUrl($baseUrl . '/dashboard', 'User Dashboard', $cookies);
$results[] = testUrl($baseUrl . '/profile', 'User Profile', $cookies);

echo "\n" . colorize("--- Admin Routes ---", "yellow") . "\n";
$results[] = testUrl($baseUrl . '/admin', 'Admin Home', $cookies);
$results[] = testUrl($baseUrl . '/admin/dashboard', 'Admin Dashboard', $cookies);
$results[] = testUrl($baseUrl . '/admin/users', 'User Management', $cookies);
$results[] = testUrl($baseUrl . '/admin/users/create', 'Create User', $cookies);
$results[] = testUrl($baseUrl . '/admin/users/roles', 'User Roles', $cookies);

echo "\n" . colorize("--- Analytics Routes ---", "yellow") . "\n";
$results[] = testUrl($baseUrl . '/admin/analytics', 'Analytics Overview', $cookies);
$results[] = testUrl($baseUrl . '/admin/analytics/overview', 'Analytics Overview Alt', $cookies);
$results[] = testUrl($baseUrl . '/admin/analytics/users', 'User Analytics', $cookies);
$results[] = testUrl($baseUrl . '/admin/analytics/calculators', 'Calculator Analytics', $cookies);

echo "\n" . colorize("--- Content Management ---", "yellow") . "\n";
$results[] = testUrl($baseUrl . '/admin/content', 'Content Home', $cookies);
$results[] = testUrl($baseUrl . '/admin/content/pages', 'Pages Management', $cookies);
$results[] = testUrl($baseUrl . '/admin/content/menus', 'Menu Management', $cookies);
$results[] = testUrl($baseUrl . '/admin/content/media', 'Media Management', $cookies);

echo "\n" . colorize("--- Settings Routes ---", "yellow") . "\n";
$results[] = testUrl($baseUrl . '/admin/settings', 'Settings Home', $cookies);
$results[] = testUrl($baseUrl . '/admin/settings/general', 'General Settings', $cookies);
$results[] = testUrl($baseUrl . '/admin/settings/email', 'Email Settings', $cookies);
$results[] = testUrl($baseUrl . '/admin/settings/security', 'Security Settings', $cookies);

echo "\n" . colorize("--- Modules & Themes ---", "yellow") . "\n";
$results[] = testUrl($baseUrl . '/admin/modules', 'Modules Management', $cookies);
$results[] = testUrl($baseUrl . '/admin/themes', 'Themes Management', $cookies);

echo "\n" . colorize("--- Debug Tools ---", "yellow") . "\n";
$results[] = testUrl($baseUrl . '/admin/debug', 'Debug Dashboard', $cookies);
$results[] = testUrl($baseUrl . '/admin/debug/error-logs', 'Error Logs', $cookies);
$results[] = testUrl($baseUrl . '/admin/debug/tests', 'System Tests', $cookies);
$results[] = testUrl($baseUrl . '/admin/debug/live-errors', 'Live Errors', $cookies);

echo "\n" . colorize("--- System Routes ---", "yellow") . "\n";
$results[] = testUrl($baseUrl . '/admin/system-status', 'System Status', $cookies);
$results[] = testUrl($baseUrl . '/admin/activity', 'Activity Log', $cookies);

// Summary
echo "\n" . colorize("=== Summary ===", "blue") . "\n";
$total = count($results);
$ok = count(array_filter($results, fn($r) => $r['status_code'] == 200));
$errors = count(array_filter($results, fn($r) => $r['status_code'] >= 400 || $r['error']));
$redirects = count(array_filter($results, fn($r) => $r['status_code'] >= 300 && $r['status_code'] < 400));

echo "Total URLs tested: $total\n";
echo colorize("OK (200): $ok\n", "green");
echo colorize("Redirects (3xx): $redirects\n", "blue");
echo colorize("Errors (4xx/5xx): $errors\n", $errors > 0 ? "red" : "green");

// List failed URLs
if ($errors > 0) {
    echo "\n" . colorize("Failed URLs:", "red") . "\n";
    foreach ($results as $result) {
        if ($result['status_code'] >= 400 || $result['error']) {
            echo "  - {$result['description']}: {$result['status']}\n";
        }
    }
}

echo "\n";
