<?php
/**
 * Test script to verify agreement and marketing preference tracking
 */

require_once 'app/bootstrap.php';
use App\Models\User;

try {
    $userModel = new User();
    
    echo "🧪 Testing Agreement & Marketing Tracking System\n\n";
    
    // Test 1: Create a demo user with agreements
    echo "1️⃣ Testing user creation with agreements...\n";
    
    $testUserData = [
        'username' => 'test_user_' . time(),
        'email' => 'test' . time() . '@example.com',
        'password' => password_hash('TestPassword123!', PASSWORD_DEFAULT),
        'first_name' => 'Test',
        'last_name' => 'User',
        'company' => 'Test Company',
        'role' => 'engineer',
        'terms_agree' => true,
        'marketing_agree' => true,
        'email_verified' => 1,
        'is_active' => 1
    ];
    
    $userId = $userModel->create($testUserData);
    
    if ($userId) {
        echo "✅ User created successfully with ID: {$userId}\n";
        
        // Test 2: Check agreement status
        echo "\n2️⃣ Testing agreement status retrieval...\n";
        $agreementStatus = $userModel->getAgreementStatus($userId);
        
        if ($agreementStatus) {
            echo "✅ Agreement Status Retrieved:\n";
            echo "   - Terms Agreed: " . ($agreementStatus['terms_agreed'] ? 'YES' : 'NO') . "\n";
            echo "   - Marketing Emails: " . ($agreementStatus['marketing_emails'] ? 'OPT-IN' : 'OPT-OUT') . "\n";
            echo "   - Terms Agreed At: " . ($agreementStatus['terms_agreed_at'] ?? 'N/A') . "\n";
        }
        
        // Test 3: Test marketing preferences update
        echo "\n3️⃣ Testing marketing preference update...\n";
        $updateResult = $userModel->updateMarketingPreferences($userId, false);
        
        if ($updateResult) {
            echo "✅ Marketing preference updated to OPT-OUT\n";
            
            // Verify the update
            $updatedStatus = $userModel->getAgreementStatus($userId);
            echo "   - Updated Marketing Status: " . ($updatedStatus['marketing_emails'] ? 'OPT-IN' : 'OPT-OUT') . "\n";
        }
        
        // Test 4: Get marketing opt-in users
        echo "\n4️⃣ Testing marketing opt-in users retrieval...\n";
        $optInUsers = $userModel->getMarketingOptInUsers(5);
        echo "✅ Found " . count($optInUsers) . " users opted-in for marketing\n";
        
        foreach ($optInUsers as $user) {
            echo "   - {$user['email']} ({$user['first_name']} {$user['last_name']})\n";
        }
        
        // Test 5: Check terms agreement
        echo "\n5️⃣ Testing terms agreement check...\n";
        $hasAgreed = $userModel->hasAgreedToTerms($userId);
        echo "✅ User has agreed to terms: " . ($hasAgreed ? 'YES' : 'NO') . "\n";
        
        echo "\n🎉 All tests completed successfully!\n";
        
        // Cleanup (optional - remove test user)
        // $userModel->deleteAccount($userId);
        // echo "\n🧹 Test user cleaned up.\n";
        
    } else {
        echo "❌ Failed to create test user\n";
    }
    
} catch (Exception $e) {
    echo "❌ Test Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
