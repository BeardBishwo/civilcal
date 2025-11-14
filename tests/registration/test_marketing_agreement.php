<?php
/**
 * Test Marketing Agreement Storage
 * This script tests if marketing preferences are being saved correctly
 */

define('BISHWO_CALCULATOR', true);
require_once __DIR__ . '/app/bootstrap.php';

echo "📧 MARKETING AGREEMENT TEST\n";
echo "===========================\n\n";

try {
    $db = \App\Core\Database::getInstance();
    $pdo = $db->getPdo();
    
    // Check if marketing columns exist
    echo "🔍 Checking database structure...\n";
    
    $columns = ['terms_agreed', 'terms_agreed_at', 'marketing_emails'];
    foreach ($columns as $column) {
        $checkSql = "SHOW COLUMNS FROM users LIKE '{$column}'";
        $result = $pdo->query($checkSql);
        
        if ($result->rowCount() > 0) {
            echo "   ✅ Column '{$column}' exists\n";
        } else {
            echo "   ❌ Column '{$column}' missing\n";
        }
    }
    
    echo "\n📊 CURRENT USER MARKETING PREFERENCES:\n";
    echo "=====================================\n";
    
    // Query users and their marketing preferences
    $stmt = $pdo->prepare("
        SELECT 
            id,
            username,
            email,
            first_name,
            last_name,
            terms_agreed,
            terms_agreed_at,
            marketing_emails,
            created_at
        FROM users 
        ORDER BY created_at DESC
    ");
    
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "No users found in database.\n";
    } else {
        echo "Total users: " . count($users) . "\n\n";
        
        $marketingOptIn = 0;
        $marketingOptOut = 0;
        
        foreach ($users as $user) {
            $name = trim($user['first_name'] . ' ' . $user['last_name']);
            if (empty($name)) {
                $name = $user['username'];
            }
            
            $termsStatus = $user['terms_agreed'] ? '✅ Agreed' : '❌ Not Agreed';
            $marketingStatus = $user['marketing_emails'] ? '📧 Opted In' : '🚫 Opted Out';
            
            if ($user['marketing_emails']) {
                $marketingOptIn++;
            } else {
                $marketingOptOut++;
            }
            
            echo "👤 {$name} ({$user['email']})\n";
            echo "   Terms: {$termsStatus}";
            if ($user['terms_agreed_at']) {
                echo " on " . date('M j, Y', strtotime($user['terms_agreed_at']));
            }
            echo "\n";
            echo "   Marketing: {$marketingStatus}\n";
            echo "   Registered: " . date('M j, Y H:i', strtotime($user['created_at'])) . "\n\n";
        }
        
        echo "📈 MARKETING STATISTICS:\n";
        echo "========================\n";
        echo "📧 Opted In for Marketing: {$marketingOptIn} users\n";
        echo "🚫 Opted Out of Marketing: {$marketingOptOut} users\n";
        echo "📊 Opt-in Rate: " . ($marketingOptIn > 0 ? round(($marketingOptIn / count($users)) * 100, 1) : 0) . "%\n\n";
        
        if ($marketingOptIn > 0) {
            echo "🎯 MARKETING LIST (Users who opted in):\n";
            echo "======================================\n";
            
            $marketingStmt = $pdo->prepare("
                SELECT username, email, first_name, last_name, created_at
                FROM users 
                WHERE marketing_emails = 1 
                ORDER BY created_at DESC
            ");
            
            $marketingStmt->execute();
            $marketingUsers = $marketingStmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($marketingUsers as $user) {
                $name = trim($user['first_name'] . ' ' . $user['last_name']);
                if (empty($name)) {
                    $name = $user['username'];
                }
                
                echo "📧 {$user['email']} ({$name}) - Joined " . date('M j, Y', strtotime($user['created_at'])) . "\n";
            }
        }
    }
    
    echo "\n✨ TEST COMPLETE!\n";
    echo "\n🔧 USAGE FOR MARKETING:\n";
    echo "======================\n";
    echo "To get marketing list, use this SQL query:\n";
    echo "SELECT email, first_name, last_name FROM users WHERE marketing_emails = 1;\n\n";
    echo "To update marketing preferences:\n";
    echo "UPDATE users SET marketing_emails = 0 WHERE email = 'user@example.com';\n\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
?>
