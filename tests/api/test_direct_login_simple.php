<?php
/**
 * Test Direct Login Endpoint
 */

echo "🔐 TESTING DIRECT LOGIN ENDPOINT\n";
echo "================================\n\n";

// Test the direct login endpoint
$url = 'http://127.0.0.1:59643/direct_login.php';
$data = json_encode([
    'username_email' => 'admin',
    'password' => 'admin123'
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
    echo "📝 Response:\n";
    echo $response . "\n\n";
    
    if ($code === 200) {
        echo "✅ Direct login endpoint is working!\n";
        $result = json_decode($response, true);
        if ($result && isset($result['success']) && $result['success']) {
            echo "🎉 Login successful!\n";
        } else {
            echo "❌ Login failed: " . ($result['error'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "❌ HTTP Error Code: $code\n";
    }
}

// Also test with a known user from the database
echo "\n🔍 Testing with known users...\n";

// Test with uniquebishwo (from the user list we saw earlier)
$testUsers = [
    ['username' => 'uniquebishwo', 'password' => 'password123'],
    ['username' => 'admin', 'password' => 'admin123'],
    ['username' => 'engineer@engicalpro.com', 'password' => 'password123']
];

foreach ($testUsers as $testUser) {
    echo "\n👤 Testing: {$testUser['username']}\n";
    
    $data = json_encode([
        'username_email' => $testUser['username'],
        'password' => $testUser['password']
    ]);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($code === 200) {
        $result = json_decode($response, true);
        if ($result && isset($result['success']) && $result['success']) {
            echo "   ✅ Login successful!\n";
        } else {
            echo "   ❌ Login failed: " . ($result['error'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "   ❌ HTTP Error: $code\n";
    }
}

echo "\n✨ Test complete!\n";
?>
