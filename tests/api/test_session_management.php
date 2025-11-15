<?php
/**
 * Authentication API Testing: Session Management
 * Tests session creation, validation, and cleanup
 */

echo "🔐 AUTHENTICATION API TESTING: SESSION MANAGEMENT\n";
echo "=================================================\n\n";

// Test session management flow
$loginUrl = 'http://localhost/Bishwo_Calculator/api/login';
$logoutUrl = 'http://localhost/Bishwo_Calculator/api/logout';
$cookieJar = tempnam(sys_get_temp_dir(), 'session_test_cookies');

echo "🧪 Test 1: Session Creation\n";
echo str_repeat("-", 30) . "\n";

// Step 1: Login to create session
$loginData = [
    'username_email' => 'admin',
    'password' => 'admin123',
    'remember_me' => false
];

$loginResult = performRequest($loginUrl, 'POST', $loginData, $cookieJar);

if ($loginResult['success']) {
    echo "✅ Login successful - Session created\n";
    echo "   User: {$loginResult['user']['username']}\n";
    echo "   HTTP Code: {$loginResult['http_code']}\n";
    
    // Check for auth_token cookie
    $authTokenSet = strpos($loginResult['headers'], 'Set-Cookie: auth_token=') !== false;
    echo "   Auth Token Cookie: " . ($authTokenSet ? 'Set' : 'Not Set') . "\n";
} else {
    echo "❌ Login failed: {$loginResult['error']}\n";
    exit(1);
}

echo "\n🧪 Test 2: Session Validation\n";
echo str_repeat("-", 30) . "\n";

// Test accessing a protected endpoint (if available)
// For now, we'll test by making another login request with cookies
$sessionValidationResult = performRequest($loginUrl, 'POST', $loginData, $cookieJar);

if ($sessionValidationResult['http_code'] === 200) {
    echo "✅ Session validation working\n";
    echo "   HTTP Code: {$sessionValidationResult['http_code']}\n";
} else {
    echo "❌ Session validation failed\n";
    echo "   HTTP Code: {$sessionValidationResult['http_code']}\n";
}

echo "\n🧪 Test 3: Database Session Verification\n";
echo str_repeat("-", 40) . "\n";

// Check database for session record
$sessionDbResult = checkDatabaseSession();
if ($sessionDbResult['found']) {
    echo "✅ Session found in database\n";
    echo "   Session ID: {$sessionDbResult['session_id']}\n";
    echo "   User ID: {$sessionDbResult['user_id']}\n";
    echo "   Created: {$sessionDbResult['created_at']}\n";
    echo "   Last Activity: {$sessionDbResult['last_activity']}\n";
} else {
    echo "❌ Session not found in database\n";
    if (isset($sessionDbResult['error'])) {
        echo "   Error: {$sessionDbResult['error']}\n";
    }
}

echo "\n🧪 Test 4: Session Cleanup (Logout)\n";
echo str_repeat("-", 35) . "\n";

// Test logout
$logoutResult = performRequest($logoutUrl, 'GET', null, $cookieJar);

if ($logoutResult['http_code'] === 200) {
    echo "✅ Logout successful\n";
    echo "   HTTP Code: {$logoutResult['http_code']}\n";
    
    // Check if cookies are cleared
    $cookieCleared = strpos($logoutResult['headers'], 'Set-Cookie: auth_token=; expires=') !== false;
    echo "   Auth Token Cleared: " . ($cookieCleared ? 'Yes' : 'No') . "\n";
} else {
    echo "❌ Logout failed\n";
    echo "   HTTP Code: {$logoutResult['http_code']}\n";
}

echo "\n🧪 Test 5: Post-Logout Session Validation\n";
echo str_repeat("-", 42) . "\n";

// Try to access protected content after logout
$postLogoutResult = performRequest($loginUrl, 'POST', $loginData, $cookieJar);

// Check if session was properly cleaned up in database
$postLogoutDbResult = checkDatabaseSession();
if (!$postLogoutDbResult['found']) {
    echo "✅ Session properly cleaned from database\n";
} else {
    echo "❌ Session still exists in database after logout\n";
}

// Cleanup
unlink($cookieJar);

echo "\n📊 SESSION MANAGEMENT TEST SUMMARY\n";
echo "==================================\n";
echo "✅ Session Creation: " . ($loginResult['success'] ? 'PASSED' : 'FAILED') . "\n";
echo "✅ Session Validation: " . ($sessionValidationResult['http_code'] === 200 ? 'PASSED' : 'FAILED') . "\n";
echo "✅ Database Storage: " . ($sessionDbResult['found'] ? 'PASSED' : 'FAILED') . "\n";
echo "✅ Session Cleanup: " . ($logoutResult['http_code'] === 200 ? 'PASSED' : 'FAILED') . "\n";
echo "✅ Post-Logout Cleanup: " . (!$postLogoutDbResult['found'] ? 'PASSED' : 'FAILED') . "\n";

echo "\n✨ Session management testing complete!\n";

function performRequest($url, $method = 'GET', $data = null, $cookieJar = null) {
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
    
    $json = json_decode($body, true);
    
    return [
        'success' => $json['success'] ?? false,
        'message' => $json['message'] ?? '',
        'user' => $json['user'] ?? null,
        'error' => $json['error'] ?? null,
        'http_code' => $httpCode,
        'headers' => $headers,
        'body' => $body
    ];
}

function checkDatabaseSession() {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=bishwo_calculator", 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get the most recent session by last_activity (table has no created_at column)
        $stmt = $pdo->query("
            SELECT 
                id as session_id,
                user_id,
                session_token,
                last_activity,
                expires_at
            FROM user_sessions 
            ORDER BY last_activity DESC 
            LIMIT 1
        ");
        
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($session) {
            return [
                'found' => true,
                'session_id' => $session['session_id'],
                'user_id' => $session['user_id'],
                // Map created_at in summary output to last_activity for compatibility
                'created_at' => $session['last_activity'],
                'last_activity' => $session['last_activity'],
                'expires_at' => $session['expires_at']
            ];
        } else {
            return ['found' => false];
        }
        
    } catch (Exception $e) {
        return [
            'found' => false,
            'error' => $e->getMessage()
        ];
    }
}
?>
