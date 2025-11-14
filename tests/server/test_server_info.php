<?php
/**
 * Test Server Information
 */

echo "🖥️ SERVER INFORMATION\n";
echo "====================\n\n";

// Test direct access to see server info
$url = 'http://localhost/Bishwo_Calculator/test_server_info.php';

echo "📡 Testing direct access to this script: $url\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);

$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ cURL Error: $error\n";
} else {
    echo "📊 HTTP Code: $code\n";
    if ($code === 200) {
        echo "✅ Direct script access works!\n\n";
        echo "📋 Server Info from Web Request:\n";
        echo $response;
    } else {
        echo "❌ Direct script access failed\n";
        echo "Response: " . substr($response, 0, 200) . "\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "📋 SERVER INFO FROM CLI:\n";
echo str_repeat("=", 50) . "\n";

echo "🔧 PHP Version: " . phpversion() . "\n";
echo "🌐 Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "📁 Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "\n";
echo "🏠 Script Filename: " . (__FILE__) . "\n";
echo "📍 Current Working Dir: " . getcwd() . "\n";
echo "🌍 Server Name: " . ($_SERVER['SERVER_NAME'] ?? 'Unknown') . "\n";
echo "🚪 Server Port: " . ($_SERVER['SERVER_PORT'] ?? 'Unknown') . "\n";

// Check if we're running under a web server
if (isset($_SERVER['REQUEST_METHOD'])) {
    echo "\n✅ Running under web server\n";
    echo "🔗 Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "\n";
    echo "🌐 HTTP Host: " . ($_SERVER['HTTP_HOST'] ?? 'Unknown') . "\n";
    echo "📡 Request Method: " . ($_SERVER['REQUEST_METHOD'] ?? 'Unknown') . "\n";
} else {
    echo "\n📱 Running from CLI\n";
}

echo "\n✨ Server info complete!\n";
?>
