<?php
/**
 * Registration API Testing
 * Tests user registration with agreement tracking
 */

echo "📝 REGISTRATION API TESTING\n";
echo "===========================\n\n";

$url = 'http://localhost/Bishwo_Calculator/api/register';

// Test cases for registration
$testCases = [
    [
        'name' => 'Valid Registration with All Fields',
        'data' => [
            'username' => 'testuser_' . time(),
            'email' => 'test_' . time() . '@example.com',
            'password' => 'TestPassword123!',
            'full_name' => 'Test User',
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone_number' => '+1234567890',
            'engineer_roles' => ['civil'],
            'terms_agree' => true,
            'marketing_agree' => true
        ],
        'expected_success' => true
    ],
    [
        'name' => 'Valid Registration without Marketing Consent',
        'data' => [
            'username' => 'testuser2_' . time(),
            'email' => 'test2_' . time() . '@example.com',
            'password' => 'TestPassword123!',
            'full_name' => 'Test User 2',
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone_number' => '',
            'engineer_roles' => ['structural'],
            'terms_agree' => true,
            'marketing_agree' => false
        ],
        'expected_success' => true
    ],
    [
        'name' => 'Registration without Terms Agreement',
        'data' => [
            'username' => 'testuser3_' . time(),
            'email' => 'test3_' . time() . '@example.com',
            'password' => 'TestPassword123!',
            'full_name' => 'Test User 3',
            'first_name' => 'Test',
            'last_name' => 'User',
            'terms_agree' => false,
            'marketing_agree' => true
        ],
        'expected_success' => false
    ],
    [
        'name' => 'Registration with Duplicate Username',
        'data' => [
            'username' => 'admin',
            'email' => 'duplicate_' . time() . '@example.com',
            'password' => 'TestPassword123!',
            'full_name' => 'Duplicate User',
            'first_name' => 'Duplicate',
            'last_name' => 'User',
            'terms_agree' => true,
            'marketing_agree' => false
        ],
        'expected_success' => false
    ],
    [
        'name' => 'Registration with Missing Required Fields',
        'data' => [
            'username' => '',
            'email' => '',
            'password' => '',
            'terms_agree' => true
        ],
        'expected_success' => false
    ]
];

$results = [];
$successfulRegistrations = [];

foreach ($testCases as $index => $testCase) {
    echo "🧪 Test " . ($index + 1) . ": {$testCase['name']}\n";
    echo str_repeat("-", 50) . "\n";
    
    $result = testRegistrationEndpoint($url, $testCase['data']);
    $results[] = [
        'test' => $testCase['name'],
        'expected' => $testCase['expected_success'],
        'actual' => $result['success'],
        'passed' => $result['success'] === $testCase['expected_success'],
        'response' => $result
    ];
    
    if ($result['success'] === $testCase['expected_success']) {
        echo "✅ PASSED\n";
        
        if ($result['success'] && isset($result['user_id'])) {
            $successfulRegistrations[] = $result['user_id'];
            echo "   User ID: {$result['user_id']}\n";
            
            // Test agreement tracking for successful registrations
            $agreementData = testAgreementTracking($result['user_id']);
            if ($agreementData) {
                echo "   Terms Agreed: " . ($agreementData['terms_agreed'] ? 'Yes' : 'No') . "\n";
                echo "   Marketing Consent: " . ($agreementData['marketing_emails'] ? 'Yes' : 'No') . "\n";
                echo "   Agreement Timestamp: {$agreementData['terms_agreed_at']}\n";
            }
        }
    } else {
        echo "❌ FAILED\n";
        echo "   Expected: " . ($testCase['expected_success'] ? 'Success' : 'Failure') . "\n";
        echo "   Actual: " . ($result['success'] ? 'Success' : 'Failure') . "\n";
    }
    
    if (isset($result['error'])) {
        echo "   Error: {$result['error']}\n";
    }
    
    echo "\n";
}

// Test database schema
echo "🔍 Testing Database Schema...\n";
echo str_repeat("-", 30) . "\n";
testDatabaseSchema();

// Test marketing preferences
if (!empty($successfulRegistrations)) {
    echo "\n📬 Testing Marketing Preferences...\n";
    echo str_repeat("-", 35) . "\n";
    testMarketingPreferences($successfulRegistrations[0]);
}

// Summary
echo "\n📊 REGISTRATION TEST SUMMARY\n";
echo "============================\n";
$passed = array_filter($results, fn($r) => $r['passed']);
$failed = array_filter($results, fn($r) => !$r['passed']);

echo "✅ Passed: " . count($passed) . "/" . count($results) . "\n";
echo "❌ Failed: " . count($failed) . "/" . count($results) . "\n";
echo "👥 Successful Registrations: " . count($successfulRegistrations) . "\n";

if (count($failed) > 0) {
    echo "\n🚨 Failed Tests:\n";
    foreach ($failed as $fail) {
        echo "   - {$fail['test']}\n";
    }
}

echo "\n✨ Registration API testing complete!\n";

function testRegistrationEndpoint($url, $data) {
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
        CURLOPT_TIMEOUT => 10
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return [
            'success' => false,
            'error' => $error,
            'http_code' => $httpCode
        ];
    }
    
    $json = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'success' => false,
            'error' => 'Invalid JSON response: ' . json_last_error_msg(),
            'http_code' => $httpCode,
            'raw_response' => $response
        ];
    }
    
    return [
        'success' => $json['success'] ?? false,
        'message' => $json['message'] ?? '',
        'user_id' => $json['user_id'] ?? null,
        'error' => $json['error'] ?? null,
        'http_code' => $httpCode
    ];
}

function testAgreementTracking($userId) {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=bishwo_calculator", 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("
            SELECT 
                terms_agreed, 
                terms_agreed_at, 
                marketing_emails,
                privacy_agreed,
                privacy_agreed_at
            FROM users 
            WHERE id = ?
        ");
        $stmt->execute([$userId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        echo "   ❌ Agreement tracking error: " . $e->getMessage() . "\n";
        return null;
    }
}

function testDatabaseSchema() {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=bishwo_calculator", 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->query("SHOW COLUMNS FROM users");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $requiredColumns = [
            'terms_agreed' => 'Agreement tracking',
            'terms_agreed_at' => 'Agreement timestamp',
            'marketing_emails' => 'Marketing consent',
            'privacy_agreed' => 'Privacy agreement',
            'privacy_agreed_at' => 'Privacy timestamp'
        ];
        
        foreach ($requiredColumns as $column => $description) {
            $found = false;
            foreach ($columns as $dbColumn) {
                if ($dbColumn['Field'] === $column) {
                    echo "   ✅ $column: {$dbColumn['Type']}\n";
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                echo "   ❌ $column: Missing\n";
            }
        }
        
    } catch (Exception $e) {
        echo "   ❌ Database schema check failed: " . $e->getMessage() . "\n";
    }
}

function testMarketingPreferences($userId) {
    try {
        define('BISHWO_CALCULATOR', true);
        require_once __DIR__ . '/../../app/bootstrap.php';
        
        $userModel = new \App\Models\User();
        
        // Test getting marketing opt-in users
        $optInUsers = $userModel->getMarketingOptInUsers(3);
        echo "   📊 Marketing opt-in users: " . count($optInUsers) . "\n";
        
        // Test updating marketing preferences
        $originalStatus = $userModel->getAgreementStatus($userId);
        if ($originalStatus) {
            $currentMarketing = $originalStatus['marketing_emails'];
            echo "   📬 Current marketing status: " . ($currentMarketing ? 'Opted in' : 'Opted out') . "\n";
            
            // Toggle preference
            $newStatus = !$currentMarketing;
            $updateResult = $userModel->updateMarketingPreferences($userId, $newStatus);
            
            if ($updateResult) {
                echo "   ✅ Marketing preference update: Success\n";
                
                // Restore original
                $userModel->updateMarketingPreferences($userId, $currentMarketing);
                echo "   🔄 Restored original preference\n";
            } else {
                echo "   ❌ Marketing preference update: Failed\n";
            }
        }
        
    } catch (Exception $e) {
        echo "   ❌ Marketing preferences test error: " . $e->getMessage() . "\n";
    }
}
?>
