<?php
/**
 * Test User Registration and Agreement Tracking System
 */

echo "📝 TESTING USER REGISTRATION SYSTEM\n";
echo "===================================\n\n";

// Test the registration API endpoint
$url = 'http://localhost/Bishwo_Calculator/api/register';

// Test data with all agreement tracking fields
$testData = [
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
];

echo "📡 Testing Registration API: $url\n";
echo "📝 Test Data:\n";
foreach ($testData as $key => $value) {
    if ($key === 'password') {
        echo "   $key: [HIDDEN]\n";
    } else {
        echo "   $key: " . (is_array($value) ? json_encode($value) : ($value === true ? 'true' : ($value === false ? 'false' : $value))) . "\n";
    }
}
echo "\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ cURL Error: $error\n";
} else {
    echo "📊 HTTP Code: $code\n";
    echo "📝 Raw Response:\n";
    echo $response . "\n\n";
    
    $json = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "❌ JSON Decode Error: " . json_last_error_msg() . "\n";
    } else {
        echo "📦 Decoded Response:\n";
        print_r($json);
        
        if (isset($json['success']) && $json['success']) {
            echo "\n✅ Registration successful!\n";
            
            // Test agreement tracking
            if (isset($json['user_id'])) {
                echo "\n🔍 Testing Agreement Tracking...\n";
                testAgreementTracking($json['user_id']);
            }
        } else {
            echo "\n❌ Registration failed: " . ($json['error'] ?? 'Unknown error') . "\n";
        }
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🔍 Testing Database Schema...\n";
echo str_repeat("=", 50) . "\n";

testDatabaseSchema();

echo "\n✨ Registration system test complete!\n";

function testAgreementTracking($userId) {
    echo "📋 Checking agreement data for user ID: $userId\n";
    
    try {
        // Connect to database
        $pdo = new PDO("mysql:host=localhost;dbname=bishwo_calculator", 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Query user agreement data
        $stmt = $pdo->prepare("
            SELECT 
                username, 
                email, 
                terms_agreed, 
                terms_agreed_at, 
                marketing_emails,
                privacy_agreed,
                privacy_agreed_at,
                created_at
            FROM users 
            WHERE id = ?
        ");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "✅ User found in database:\n";
            echo "   👤 Username: {$user['username']}\n";
            echo "   📧 Email: {$user['email']}\n";
            echo "   ✅ Terms Agreed: " . ($user['terms_agreed'] ? 'Yes' : 'No') . "\n";
            echo "   📅 Terms Agreed At: " . ($user['terms_agreed_at'] ?? 'Not set') . "\n";
            echo "   📬 Marketing Emails: " . ($user['marketing_emails'] ? 'Yes' : 'No') . "\n";
            echo "   🔒 Privacy Agreed: " . ($user['privacy_agreed'] ? 'Yes' : 'No') . "\n";
            echo "   📅 Privacy Agreed At: " . ($user['privacy_agreed_at'] ?? 'Not set') . "\n";
            echo "   🕐 Created At: {$user['created_at']}\n";
        } else {
            echo "❌ User not found in database\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Database error: " . $e->getMessage() . "\n";
    }
}

function testDatabaseSchema() {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=bishwo_calculator", 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "🔍 Checking users table schema...\n";
        
        $stmt = $pdo->query("SHOW COLUMNS FROM users");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $requiredColumns = [
            'terms_agreed' => 'Agreement tracking',
            'terms_agreed_at' => 'Agreement timestamp',
            'marketing_emails' => 'Marketing consent',
            'privacy_agreed' => 'Privacy agreement',
            'privacy_agreed_at' => 'Privacy timestamp'
        ];
        
        echo "📊 Database columns status:\n";
        foreach ($requiredColumns as $column => $description) {
            $found = false;
            foreach ($columns as $dbColumn) {
                if ($dbColumn['Field'] === $column) {
                    echo "   ✅ $column: {$dbColumn['Type']} - $description\n";
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                echo "   ❌ $column: Missing - $description\n";
            }
        }
        
        // Test marketing opt-in users query
        echo "\n🎯 Testing marketing opt-in query...\n";
        $stmt = $pdo->query("
            SELECT COUNT(*) as count 
            FROM users 
            WHERE marketing_emails = 1 AND is_active = 1
        ");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "   📬 Users opted in for marketing: {$result['count']}\n";
        
    } catch (Exception $e) {
        echo "❌ Database schema check failed: " . $e->getMessage() . "\n";
    }
}
?>
