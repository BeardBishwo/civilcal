<?php
/**
 * Test Main Page Access
 */

echo "🌐 TESTING MAIN PAGE ACCESS\n";
echo "===========================\n\n";

// Test the main page
$urls = [
    'http://localhost/Bishwo_Calculator/',
    'http://localhost/Bishwo_Calculator/index.php',
    'http://localhost/Bishwo_Calculator/login'
];

foreach ($urls as $url) {
    echo "📡 Testing URL: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ cURL Error: $error\n";
    } else {
        echo "📊 HTTP Code: $code\n";
        
        if ($code === 200) {
            echo "✅ Page loads successfully\n";
            echo "📄 Content length: " . strlen($response) . " bytes\n";
            
            // Check if it's an error page
            if (stripos($response, 'Internal Server Error') !== false) {
                echo "❌ Contains 'Internal Server Error'\n";
            } elseif (stripos($response, 'Fatal error') !== false) {
                echo "❌ Contains 'Fatal error'\n";
            } elseif (stripos($response, 'Warning') !== false) {
                echo "⚠️ Contains PHP warnings\n";
            } else {
                echo "✅ Appears to be valid content\n";
            }
        } else {
            echo "❌ HTTP Error: $code\n";
            echo "📝 Response preview: " . substr($response, 0, 200) . "...\n";
        }
    }
    echo "\n";
}

echo "✨ Test complete!\n";
?>
