<?php
/**
 * Quick page verification script
 */
error_reporting(E_ALL);
ini_set('display_errors', 0);

$pages = [
    '/' => 'Homepage',
    '/help' => 'Help Center',
    '/developers' => 'Developer Docs',
    '/login' => 'Login',
    '/register' => 'Register',
    '/civil' => 'Civil Engineering',
];

$results = [];
foreach ($pages as $path => $name) {
    $url = "http://localhost:8000{$path}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    $results[] = [
        'name' => $name,
        'path' => $path,
        'status' => $code,
        'ok' => ($code === 200 || $code === 302),
        'error' => $error
    ];
}

// Output results
echo "=== Website Verification Report ===\n\n";
$passed = 0;
$failed = 0;

foreach ($results as $r) {
    $status = $r['ok'] ? '✓ PASS' : '✗ FAIL';
    echo "{$status} | {$r['name']} ({$r['path']}) - HTTP {$r['status']}\n";
    if ($r['ok']) $passed++; else $failed++;
    if ($r['error']) echo "  Error: {$r['error']}\n";
}

echo "\n=== Summary ===\n";
echo "Passed: {$passed}\n";
echo "Failed: {$failed}\n";
echo "Total: " . count($results) . "\n";
echo "Success Rate: " . round(($passed / count($results)) * 100) . "%\n";
?>
