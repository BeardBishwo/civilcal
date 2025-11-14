<?php
/**
 * Test Web Server Availability
 */

echo "🌐 TESTING WEB SERVER AVAILABILITY\n";
echo "==================================\n\n";

// Test common web server ports and addresses
$test_urls = [
    'http://localhost/',
    'http://localhost:80/',
    'http://localhost:8080/',
    'http://localhost:3000/',
    'http://127.0.0.1/',
    'http://127.0.0.1:80/',
    'http://127.0.0.1:8080/'
];

foreach ($test_urls as $url) {
    echo "📡 Testing: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request
    
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "   ❌ $error\n";
    } else {
        echo "   ✅ Status: $code\n";
        if ($code === 200) {
            echo "   🎉 Web server found!\n";
        }
    }
    echo "\n";
}

// Check if Laragon might be using a different port
echo "🔍 Checking for common Laragon configurations:\n";
echo "===============================================\n";

$laragon_urls = [
    'http://localhost/laragon/',
    'http://localhost/dashboard/',
    'http://laragon.test/',
    'http://localhost:8080/laragon/'
];

foreach ($laragon_urls as $url) {
    echo "📡 Testing Laragon: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "   ❌ $error\n";
    } else {
        echo "   ✅ Status: $code\n";
    }
    echo "\n";
}

echo "💡 TROUBLESHOOTING TIPS:\n";
echo "========================\n";
echo "1. Check if Laragon is running\n";
echo "2. Start Apache service in Laragon\n";
echo "3. Check Laragon's Apache port configuration\n";
echo "4. Verify document root points to C:\\laragon\\www\n";
echo "5. Check Windows firewall settings\n";
echo "6. Try accessing http://localhost in your browser\n";

echo "\n✨ Web server test complete!\n";
?>
