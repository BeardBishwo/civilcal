<?php
/**
 * Authentication API Testing: Login Endpoint
 * Tests the complete login flow from API request to session management
 */

echo "🔐 AUTHENTICATION API TESTING: LOGIN ENDPOINT\n";
echo "=============================================\n\n";

// Test the login API endpoint
$url = 'http://localhost/Bishwo_Calculator/api/login';

// Test cases
$testCases = [
    [
        'name' => 'Valid Admin Login',
        'data' => [
            'username_email' => 'admin',
            'password' => 'admin123',
            'remember_me' => false
        ],
        'expected_success' => true
    ],
    [
        'name' => 'Valid Admin Login with Remember Me',
        'data' => [
            'username_email' => 'admin',
            'password' => 'admin123',
            'remember_me' => true
        ],
        'expected_success' => true
    ],
    [
        'name' => 'Invalid Password',
        'data' => [
            'username_email' => 'admin',
            'password' => 'wrongpassword',
            'remember_me' => false
        ],
        'expected_success' => false
    ],
    [
        'name' => 'Non-existent User',
        'data' => [
            'username_email' => 'nonexistent@example.com',
            'password' => 'password123',
            'remember_me' => false
        ],
        'expected_success' => false
    ],
    [
        'name' => 'Empty Credentials',
        'data' => [
            'username_email' => '',
            'password' => '',
            'remember_me' => false
        ],
        'expected_success' => false
    ]
];

$results = [];
$cookieJar = tempnam(sys_get_temp_dir(), 'login_test_cookies');

foreach ($testCases as $index => $testCase) {
    echo "🧪 Test " . ($index + 1) . ": {$testCase['name']}\n";
    echo str_repeat("-", 50) . "\n";
    
    $result = testLoginEndpoint($url, $testCase['data'], $cookieJar);
    $results[] = [
        'test' => $testCase['name'],
        'expected' => $testCase['expected_success'],
        'actual' => $result['success'],
        'passed' => $result['success'] === $testCase['expected_success'],
        'response' => $result
    ];
    
    if ($result['success'] === $testCase['expected_success']) {
        echo "✅ PASSED\n";
    } else {
        echo "❌ FAILED\n";
        echo "   Expected: " . ($testCase['expected_success'] ? 'Success' : 'Failure') . "\n";
        echo "   Actual: " . ($result['success'] ? 'Success' : 'Failure') . "\n";
    }
    
    if (isset($result['error'])) {
        echo "   Error: {$result['error']}\n";
    }
    
    if (isset($result['user'])) {
        echo "   User: {$result['user']['username']} ({$result['user']['email']})\n";
    }
    
    if (isset($result['remember_token_set'])) {
        echo "   Remember Token: " . ($result['remember_token_set'] ? 'Set' : 'Not Set') . "\n";
    }
    
    echo "\n";
}

// Summary
echo "📊 TEST SUMMARY\n";
echo "===============\n";
$passed = array_filter($results, fn($r) => $r['passed']);
$failed = array_filter($results, fn($r) => !$r['passed']);

echo "✅ Passed: " . count($passed) . "/" . count($results) . "\n";
echo "❌ Failed: " . count($failed) . "/" . count($results) . "\n";

if (count($failed) > 0) {
    echo "\n🚨 Failed Tests:\n";
    foreach ($failed as $fail) {
        echo "   - {$fail['test']}\n";
    }
}

// Cleanup
unlink($cookieJar);

echo "\n✨ Authentication API testing complete!\n";

function testLoginEndpoint($url, $data, $cookieJar) {
    $ch = curl_init();
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_TIMEOUT => 10,
        CURLOPT_COOKIEJAR => $cookieJar,
        CURLOPT_COOKIEFILE => $cookieJar,
        CURLOPT_HEADER => true
    ]);
    
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
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'success' => false,
            'error' => 'Invalid JSON response: ' . json_last_error_msg(),
            'http_code' => $httpCode,
            'raw_response' => $body
        ];
    }
    
    return [
        'success' => $json['success'] ?? false,
        'message' => $json['message'] ?? '',
        'user' => $json['user'] ?? null,
        'redirect_url' => $json['redirect_url'] ?? null,
        'error' => $json['error'] ?? null,
        'http_code' => $httpCode,
        'remember_token_set' => $rememberTokenSet,
        'headers' => $headers
    ];
}
?>
