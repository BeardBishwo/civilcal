<?php
/**
 * Test Browser URLs
 */

echo "🌐 TESTING BROWSER URLS\n";
echo "=======================\n\n";

// Test various URL patterns that might be accessed
$urls = [
    'http://localhost/Bishwo_Calculator/',
    'http://localhost/Bishwo_Calculator/login',
    'http://localhost/Bishwo_Calculator/index.php',
    'http://localhost/Bishwo_Calculator/public/',
    'http://localhost/Bishwo_Calculator/public/index.php',
    'http://127.0.0.1/Bishwo_Calculator/',
    'http://127.0.0.1/Bishwo_Calculator/login'
];

foreach ($urls as $url) {
    echo "📡 Testing: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't follow redirects
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HEADER, true); // Include headers in output
    curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request only
    
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "   ❌ Error: $error\n";
    } else {
        echo "   📊 Status: $code\n";
        
        // Check for redirects
        if ($code >= 300 && $code < 400) {
            if (preg_match('/Location: (.+)/i', $response, $matches)) {
                echo "   🔄 Redirects to: " . trim($matches[1]) . "\n";
            }
        }
    }
    echo "\n";
}

echo "✨ Test complete!\n";
?>
