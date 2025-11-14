<?php
/**
 * Test Working Login Endpoint
 */

echo "🔐 TESTING WORKING LOGIN ENDPOINT\n";
echo "=================================\n\n";

// Test the working login endpoint
$url = 'http://127.0.0.1:59643/working_login.php';
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
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt'); // Save cookies
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt'); // Use cookies

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
        echo "✅ Working login endpoint is successful!\n";
        $result = json_decode($response, true);
        if ($result && isset($result['success']) && $result['success']) {
            echo "🎉 Login successful!\n";
            echo "👤 User: {$result['user']['username']} ({$result['user']['email']})\n";
            echo "🔑 Full Name: {$result['user']['full_name']}\n";
            echo "👑 Admin: " . ($result['user']['is_admin'] ? 'Yes' : 'No') . "\n";
        } else {
            echo "❌ Login failed: " . ($result['error'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "❌ HTTP Error Code: $code\n";
    }
}

echo "\n✨ Test complete!\n";
?>
