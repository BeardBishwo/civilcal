<?php
/**
 * Username Availability Testing
 * Tests username checking API and suggestions
 */

echo "👤 USERNAME AVAILABILITY TESTING\n";
echo "================================\n\n";

$url = 'http://localhost/Bishwo_Calculator/direct_check_username.php';

// Test cases for username availability
$testCases = [
    [
        'name' => 'Available Username',
        'username' => 'newuser_' . time(),
        'expected_available' => true
    ],
    [
        'name' => 'Taken Username (admin)',
        'username' => 'admin',
        'expected_available' => false
    ],
    [
        'name' => 'Empty Username',
        'username' => '',
        'expected_available' => false
    ],
    [
        'name' => 'Username with Special Characters',
        'username' => 'user@#$%',
        'expected_available' => false
    ],
    [
        'name' => 'Very Short Username',
        'username' => 'ab',
        'expected_available' => false
    ],
    [
        'name' => 'Very Long Username',
        'username' => str_repeat('a', 51),
        'expected_available' => false
    ],
    [
        'name' => 'Username with Numbers',
        'username' => 'user123_' . time(),
        'expected_available' => true
    ],
    [
        'name' => 'Username with Underscores',
        'username' => 'test_user_' . time(),
        'expected_available' => true
    ]
];

$results = [];

foreach ($testCases as $index => $testCase) {
    echo "🧪 Test " . ($index + 1) . ": {$testCase['name']}\n";
    echo str_repeat("-", 50) . "\n";
    echo "   Username: '{$testCase['username']}'\n";
    
    $result = testUsernameAvailability($url, $testCase['username']);
    $results[] = [
        'test' => $testCase['name'],
        'username' => $testCase['username'],
        'expected' => $testCase['expected_available'],
        'actual' => $result['available'],
        'passed' => $result['available'] === $testCase['expected_available'],
        'response' => $result
    ];
    
    if ($result['available'] === $testCase['expected_available']) {
        echo "   ✅ PASSED\n";
    } else {
        echo "   ❌ FAILED\n";
        echo "   Expected: " . ($testCase['expected_available'] ? 'Available' : 'Not Available') . "\n";
        echo "   Actual: " . ($result['available'] ? 'Available' : 'Not Available') . "\n";
    }
    
    if (isset($result['error'])) {
        echo "   Error: {$result['error']}\n";
    }
    
    if (isset($result['suggestions']) && !empty($result['suggestions'])) {
        echo "   Suggestions: " . implode(', ', $result['suggestions']) . "\n";
    }
    
    if (isset($result['message'])) {
        echo "   Message: {$result['message']}\n";
    }
    
    echo "\n";
}

// Test suggestion quality
echo "🔍 Testing Username Suggestions Quality...\n";
echo str_repeat("-", 42) . "\n";

$takenUsername = 'admin';
$suggestionResult = testUsernameAvailability($url, $takenUsername);

if (isset($suggestionResult['suggestions']) && !empty($suggestionResult['suggestions'])) {
    echo "✅ Suggestions provided for taken username '$takenUsername':\n";
    
    foreach ($suggestionResult['suggestions'] as $i => $suggestion) {
        echo "   " . ($i + 1) . ". $suggestion\n";
        
        // Test if suggestions are actually available
        $suggestionCheck = testUsernameAvailability($url, $suggestion);
        if ($suggestionCheck['available']) {
            echo "      ✅ Available\n";
        } else {
            echo "      ❌ Not available (bad suggestion)\n";
        }
    }
} else {
    echo "❌ No suggestions provided for taken username\n";
}

// Test API response format
echo "\n📋 Testing API Response Format...\n";
echo str_repeat("-", 34) . "\n";

$formatTest = testUsernameAvailability($url, 'testuser_' . time());
$requiredFields = ['available', 'message'];
$optionalFields = ['suggestions', 'error'];

echo "Required fields:\n";
foreach ($requiredFields as $field) {
    $present = isset($formatTest[$field]);
    echo "   $field: " . ($present ? '✅ Present' : '❌ Missing') . "\n";
}

echo "Optional fields:\n";
foreach ($optionalFields as $field) {
    $present = isset($formatTest[$field]);
    echo "   $field: " . ($present ? '✅ Present' : 'ℹ️ Not present') . "\n";
}

// Performance test
echo "\n⚡ Performance Testing...\n";
echo str_repeat("-", 25) . "\n";

$performanceTests = 5;
$totalTime = 0;

for ($i = 1; $i <= $performanceTests; $i++) {
    $startTime = microtime(true);
    testUsernameAvailability($url, 'perftest_' . time() . '_' . $i);
    $endTime = microtime(true);
    
    $requestTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
    $totalTime += $requestTime;
    
    echo "   Request $i: " . number_format($requestTime, 2) . "ms\n";
}

$averageTime = $totalTime / $performanceTests;
echo "   Average: " . number_format($averageTime, 2) . "ms\n";

if ($averageTime < 100) {
    echo "   ✅ Performance: Excellent (<100ms)\n";
} elseif ($averageTime < 500) {
    echo "   ✅ Performance: Good (<500ms)\n";
} else {
    echo "   ⚠️ Performance: Slow (≥500ms)\n";
}

// Summary
echo "\n📊 USERNAME AVAILABILITY TEST SUMMARY\n";
echo "=====================================\n";
$passed = array_filter($results, fn($r) => $r['passed']);
$failed = array_filter($results, fn($r) => !$r['passed']);

echo "✅ Passed: " . count($passed) . "/" . count($results) . "\n";
echo "❌ Failed: " . count($failed) . "/" . count($results) . "\n";
echo "⚡ Average Response Time: " . number_format($averageTime, 2) . "ms\n";

if (count($failed) > 0) {
    echo "\n🚨 Failed Tests:\n";
    foreach ($failed as $fail) {
        echo "   - {$fail['test']} ('{$fail['username']}')\n";
    }
}

echo "\n✨ Username availability testing complete!\n";

function testUsernameAvailability($url, $username) {
    $ch = curl_init();
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(['username' => $username]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_TIMEOUT => 10
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return [
            'available' => false,
            'error' => $error,
            'http_code' => $httpCode
        ];
    }
    
    $json = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'available' => false,
            'error' => 'Invalid JSON response: ' . json_last_error_msg(),
            'http_code' => $httpCode,
            'raw_response' => $response
        ];
    }
    
    return [
        'available' => $json['available'] ?? false,
        'message' => $json['message'] ?? '',
        'suggestions' => $json['suggestions'] ?? [],
        'error' => $json['error'] ?? null,
        'http_code' => $httpCode
    ];
}
?>
