<?php
/**
 * Authentication API Testing: Remember Me Token Flow
 * Tests remember me token generation, validation, and persistence
 */

echo "🔐 AUTHENTICATION API TESTING: REMEMBER ME TOKENS\n";
echo "=================================================\n\n";

$loginUrl = 'http://localhost/Bishwo_Calculator/api/login';
$checkTokenUrl = 'http://localhost/Bishwo_Calculator/api/check-remember';
$cookieJar = tempnam(sys_get_temp_dir(), 'remember_test_cookies');

echo "🧪 Test 1: Login WITHOUT Remember Me\n";
echo str_repeat("-", 37) . "\n";

// Test login without remember me
$loginData = [
    'username_email' => 'admin',
    'password' => 'admin123',
    'remember_me' => false
];

$result1 = performRememberMeRequest($loginUrl, 'POST', $loginData, $cookieJar);

if ($result1['success']) {
    echo "✅ Login successful without remember me\n";
    echo "   Remember Token Set: " . ($result1['remember_token_set'] ? 'Yes' : 'No') . "\n";
    
    if (!$result1['remember_token_set']) {
        echo "✅ Correctly no remember token set\n";
    } else {
        echo "❌ Remember token was set when it shouldn't be\n";
    }
} else {
    echo "❌ Login failed: {$result1['error']}\n";
}

// Clear cookies for next test
file_put_contents($cookieJar, '');

echo "\n🧪 Test 2: Login WITH Remember Me\n";
echo str_repeat("-", 34) . "\n";

// Test login with remember me
$loginDataRemember = [
    'username_email' => 'admin',
    'password' => 'admin123',
    'remember_me' => true
];

$result2 = performRememberMeRequest($loginUrl, 'POST', $loginDataRemember, $cookieJar);

if ($result2['success']) {
    echo "✅ Login successful with remember me\n";
    echo "   Remember Token Set: " . ($result2['remember_token_set'] ? 'Yes' : 'No') . "\n";
    
    if ($result2['remember_token_set']) {
        echo "✅ Remember token correctly set\n";
        
        // Extract token from headers
        $rememberToken = extractRememberToken($result2['headers']);
        if ($rememberToken) {
            echo "   Token Length: " . strlen($rememberToken) . " characters\n";
            echo "   Token Format: " . (ctype_xdigit($rememberToken) ? 'Valid hex' : 'Invalid format') . "\n";
        }
    } else {
        echo "❌ Remember token was not set when it should be\n";
    }
} else {
    echo "❌ Login with remember me failed: {$result2['error']}\n";
}

echo "\n🧪 Test 3: Remember Token Validation\n";
echo str_repeat("-", 37) . "\n";

// Test remember token validation (if endpoint exists)
if ($result2['remember_token_set']) {
    $tokenValidationResult = performRememberMeRequest($checkTokenUrl, 'GET', null, $cookieJar);
    
    echo "   Token Validation HTTP Code: {$tokenValidationResult['http_code']}\n";
    
    if ($tokenValidationResult['http_code'] === 200) {
        echo "✅ Remember token validation endpoint accessible\n";
    } else {
        echo "ℹ️ Remember token validation endpoint may not be implemented\n";
    }
} else {
    echo "⏭️ Skipping token validation - no token to validate\n";
}

echo "\n🧪 Test 4: Cookie Persistence Test\n";
echo str_repeat("-", 35) . "\n";

// Test if remember token persists across requests
if ($result2['remember_token_set']) {
    // Make another request to see if token is sent back
    $persistenceResult = performRememberMeRequest($loginUrl, 'POST', $loginDataRemember, $cookieJar);
    
    echo "   Persistence Test HTTP Code: {$persistenceResult['http_code']}\n";
    
    // Check if the cookie was sent in the request (this would be in request headers, hard to test with cURL)
    $cookieContent = file_get_contents($cookieJar);
    $hasRememberCookie = strpos($cookieContent, 'remember_token') !== false;
    
    echo "   Cookie Jar Contains Remember Token: " . ($hasRememberCookie ? 'Yes' : 'No') . "\n";
    
    if ($hasRememberCookie) {
        echo "✅ Remember token persists in cookie jar\n";
    } else {
        echo "❌ Remember token not persisting\n";
    }
} else {
    echo "⏭️ Skipping persistence test - no token to test\n";
}

echo "\n🧪 Test 5: Token Security Properties\n";
echo str_repeat("-", 37) . "\n";

if ($result2['remember_token_set']) {
    $rememberToken = extractRememberToken($result2['headers']);
    
    if ($rememberToken) {
        echo "🔍 Token Security Analysis:\n";
        echo "   Length: " . strlen($rememberToken) . " characters\n";
        echo "   Format: " . (ctype_xdigit($rememberToken) ? 'Hexadecimal' : 'Other') . "\n";
        echo "   Entropy: " . (strlen($rememberToken) >= 32 ? 'High (≥32 chars)' : 'Low (<32 chars)') . "\n";
        
        // Check cookie security flags in headers
        $cookieFlags = extractCookieFlags($result2['headers'], 'remember_token');
        echo "   HttpOnly: " . ($cookieFlags['httponly'] ? 'Yes' : 'No') . "\n";
        echo "   Secure: " . ($cookieFlags['secure'] ? 'Yes' : 'No') . "\n";
        echo "   SameSite: " . ($cookieFlags['samesite'] ?? 'Not Set') . "\n";
        echo "   Expires: " . ($cookieFlags['expires'] ?? 'Session') . "\n";
        
        if ($cookieFlags['httponly'] && strlen($rememberToken) >= 32) {
            echo "✅ Token security properties look good\n";
        } else {
            echo "⚠️ Token security could be improved\n";
        }
    }
} else {
    echo "⏭️ Skipping security analysis - no token to analyze\n";
}

// Cleanup
unlink($cookieJar);

echo "\n📊 REMEMBER ME TOKEN TEST SUMMARY\n";
echo "=================================\n";
echo "✅ Login without Remember Me: " . (!$result1['remember_token_set'] ? 'PASSED' : 'FAILED') . "\n";
echo "✅ Login with Remember Me: " . ($result2['remember_token_set'] ? 'PASSED' : 'FAILED') . "\n";
echo "✅ Token Format: " . (isset($rememberToken) && ctype_xdigit($rememberToken) ? 'PASSED' : 'FAILED') . "\n";
echo "✅ Token Security: " . (isset($cookieFlags) && $cookieFlags['httponly'] ? 'PASSED' : 'FAILED') . "\n";

echo "\n✨ Remember me token testing complete!\n";

function performRememberMeRequest($url, $method = 'GET', $data = null, $cookieJar = null) {
    $ch = curl_init();
    
    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_TIMEOUT => 10
    ];
    
    if ($cookieJar) {
        $options[CURLOPT_COOKIEJAR] = $cookieJar;
        $options[CURLOPT_COOKIEFILE] = $cookieJar;
    }
    
    if ($method === 'POST') {
        $options[CURLOPT_POST] = true;
        if ($data) {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
            $options[CURLOPT_HTTPHEADER] = [
                'Content-Type: application/json',
                'Accept: application/json'
            ];
        }
    }
    
    curl_setopt_array($ch, $options);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return [
            'success' => false,
            'error' => $error,
            'http_code' => $httpCode
        ];
    }
    
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    
    // Check for remember token in headers
    $rememberTokenSet = strpos($headers, 'Set-Cookie: remember_token=') !== false;
    
    $json = json_decode($body, true);
    
    return [
        'success' => $json['success'] ?? false,
        'message' => $json['message'] ?? '',
        'user' => $json['user'] ?? null,
        'error' => $json['error'] ?? null,
        'http_code' => $httpCode,
        'headers' => $headers,
        'body' => $body,
        'remember_token_set' => $rememberTokenSet
    ];
}

function extractRememberToken($headers) {
    if (preg_match('/Set-Cookie: remember_token=([^;]+)/', $headers, $matches)) {
        return $matches[1];
    }
    return null;
}

function extractCookieFlags($headers, $cookieName) {
    $pattern = "/Set-Cookie: {$cookieName}=([^;]+)(.*?)(?=\r?\n(?![ \t]))/s";
    if (preg_match($pattern, $headers, $matches)) {
        $cookieLine = $matches[0];
        
        return [
            'httponly' => stripos($cookieLine, 'HttpOnly') !== false,
            'secure' => stripos($cookieLine, 'Secure') !== false,
            'samesite' => preg_match('/SameSite=([^;]+)/i', $cookieLine, $sm) ? $sm[1] : null,
            'expires' => preg_match('/expires=([^;]+)/i', $cookieLine, $em) ? $em[1] : null
        ];
    }
    
    return [];
}
?>
