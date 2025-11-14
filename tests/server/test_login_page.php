<?php
/**
 * Test Login Page Content
 */

echo "🔐 TESTING LOGIN PAGE CONTENT\n";
echo "=============================\n\n";

$url = 'http://localhost/Bishwo_Calculator/login';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "📊 HTTP Code: $code\n";
echo "📄 Content length: " . strlen($response) . " bytes\n\n";

// Look for PHP errors/warnings in the response
if (preg_match_all('/<b>(Warning|Fatal error|Notice|Parse error)<\/b>:(.+?)<br \/>/i', $response, $matches, PREG_SET_ORDER)) {
    echo "❌ PHP Errors/Warnings found:\n";
    echo "==============================\n";
    foreach ($matches as $match) {
        echo "🔴 " . trim(strip_tags($match[0])) . "\n";
    }
    echo "\n";
} else {
    echo "✅ No PHP errors found in HTML\n\n";
}

// Check for specific error patterns
$error_patterns = [
    'Internal Server Error' => 'Server configuration issue',
    'session_start()' => 'Session configuration problem',
    'headers already sent' => 'Output before headers',
    'Undefined' => 'Undefined variables/keys',
    'Fatal error' => 'Critical PHP error'
];

echo "🔍 Checking for specific error patterns:\n";
echo "========================================\n";
foreach ($error_patterns as $pattern => $description) {
    if (stripos($response, $pattern) !== false) {
        echo "❌ Found '$pattern': $description\n";
    } else {
        echo "✅ No '$pattern' found\n";
    }
}

echo "\n✨ Test complete!\n";
?>
