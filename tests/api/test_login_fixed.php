<?php
/**
 * Test Login After Session Fix
 */

echo "🔐 TESTING LOGIN AFTER SESSION FIX\n";
echo "==================================\n\n";

// Test the login API endpoint - use correct local server URL
$url = 'http://localhost/Bishwo_Calculator/api/login';
$data = json_encode([
    'username_email' => 'admin',
    'password' => 'admin123',
    'remember_me' => 0
]);

echo "📡 Testing URL: $url\n";
echo "📝 Test Data: admin / admin123\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ cURL Error: $error\n";
} else {
    echo "📊 HTTP Code: $code\n";
    echo "📝 Raw Response:\n";
    echo $response . "\n\n";
    
    $json = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "❌ JSON Decode Error: " . json_last_error_msg() . "\n";
    } else {
        echo "📦 Decoded JSON:\n";
        print_r($json);
        echo "\n";
    }
}

echo "\n✨ Test complete!\n";
