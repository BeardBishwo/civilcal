<?php
/**
 * Test Marketing Preferences Management
 */

echo "📬 TESTING MARKETING PREFERENCES SYSTEM\n";
echo "=======================================\n\n";

// Test getting marketing opt-in users
echo "🎯 Testing Marketing Opt-In Users API...\n";
$url = 'http://localhost/Bishwo_Calculator/api/marketing/opt-in-users';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
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
    echo "📝 Response: $response\n\n";
    
    $json = json_decode($response, true);
    if ($json && isset($json['success'])) {
        if ($json['success']) {
            echo "✅ Marketing opt-in users retrieved successfully\n";
            echo "📊 Total opt-in users: " . count($json['users']) . "\n";
            
            foreach ($json['users'] as $user) {
                echo "   👤 {$user['first_name']} {$user['last_name']} ({$user['email']})\n";
            }
        } else {
            echo "❌ API Error: " . ($json['error'] ?? 'Unknown error') . "\n";
        }
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🔍 Testing User Agreement Status Methods...\n";
echo str_repeat("=", 50) . "\n";

testUserAgreementMethods();

echo "\n✨ Marketing preferences test complete!\n";

function testUserAgreementMethods() {
    try {
        // Test the User model methods directly
        define('BISHWO_CALCULATOR', true);
        require_once __DIR__ . '/app/bootstrap.php';
        
        $userModel = new \App\Models\User();
        
        // Get the latest user for testing
        $pdo = \App\Core\Database::getInstance()->getPdo();
        $stmt = $pdo->query("SELECT id FROM users ORDER BY id DESC LIMIT 1");
        $latestUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($latestUser) {
            $userId = $latestUser['id'];
            echo "🔍 Testing with User ID: $userId\n\n";
            
            // Test hasAgreedToTerms method
            echo "📋 Testing hasAgreedToTerms()...\n";
            $hasAgreed = $userModel->hasAgreedToTerms($userId);
            echo "   Result: " . ($hasAgreed ? 'Yes' : 'No') . "\n\n";
            
            // Test getAgreementStatus method
            echo "📋 Testing getAgreementStatus()...\n";
            $agreementStatus = $userModel->getAgreementStatus($userId);
            if ($agreementStatus) {
                echo "   ✅ Terms Agreed: " . ($agreementStatus['terms_agreed'] ? 'Yes' : 'No') . "\n";
                echo "   📅 Terms Agreed At: " . ($agreementStatus['terms_agreed_at'] ?? 'Not set') . "\n";
                echo "   🔒 Privacy Agreed: " . ($agreementStatus['privacy_agreed'] ? 'Yes' : 'No') . "\n";
                echo "   📅 Privacy Agreed At: " . ($agreementStatus['privacy_agreed_at'] ?? 'Not set') . "\n";
                echo "   📬 Marketing Emails: " . ($agreementStatus['marketing_emails'] ? 'Yes' : 'No') . "\n";
            } else {
                echo "   ❌ No agreement status found\n";
            }
            
            // Test getMarketingOptInUsers method
            echo "\n📬 Testing getMarketingOptInUsers()...\n";
            $optInUsers = $userModel->getMarketingOptInUsers(5);
            echo "   📊 Marketing opt-in users (limit 5): " . count($optInUsers) . "\n";
            foreach ($optInUsers as $user) {
                echo "   👤 {$user['first_name']} {$user['last_name']} ({$user['email']})\n";
            }
            
            // Test updateMarketingPreferences method
            echo "\n🔄 Testing updateMarketingPreferences()...\n";
            $originalStatus = $agreementStatus['marketing_emails'] ?? false;
            $newStatus = !$originalStatus;
            
            echo "   Original status: " . ($originalStatus ? 'Opted in' : 'Opted out') . "\n";
            echo "   Changing to: " . ($newStatus ? 'Opted in' : 'Opted out') . "\n";
            
            $updateResult = $userModel->updateMarketingPreferences($userId, $newStatus);
            if ($updateResult) {
                echo "   ✅ Marketing preference updated successfully\n";
                
                // Verify the change
                $newAgreementStatus = $userModel->getAgreementStatus($userId);
                echo "   📊 New status: " . ($newAgreementStatus['marketing_emails'] ? 'Opted in' : 'Opted out') . "\n";
                
                // Restore original status
                $userModel->updateMarketingPreferences($userId, $originalStatus);
                echo "   🔄 Restored original status\n";
            } else {
                echo "   ❌ Marketing preference update failed\n";
            }
            
        } else {
            echo "❌ No users found in database\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error testing user agreement methods: " . $e->getMessage() . "\n";
    }
}
?>
