<?php
require_once __DIR__ . '/app/bootstrap.php';

echo "=======================================================\n";
echo "     Bishwo Calculator - Update Test User Password    \n";
echo "=======================================================\n\n";

try {
    $pdo = \App\Core\Database::getInstance()->getPdo();
    
    // Update the test user password
    $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $newPassword = password_hash('testpassword123', PASSWORD_DEFAULT);
    $result = $updateStmt->execute([$newPassword, 'uniquebishwo@gmail.com']);
    
    if ($result) {
        echo "✅ Password updated successfully for uniquebishwo@gmail.com\n";
        echo "   New password: testpassword123\n\n";
        
        // Verify the update
        $user = \App\Models\User::findByUsername('uniquebishwo@gmail.com');
        if ($user) {
            $passwordCorrect = password_verify('testpassword123', $user->password);
            echo "✅ Password verification: " . ($passwordCorrect ? 'SUCCESS' : 'FAILED') . "\n";
        }
    } else {
        echo "❌ Failed to update password\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "📁 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
}

echo "\n=======================================================\n";
echo "              PASSWORD UPDATE COMPLETE                 \n";
echo "=======================================================\n";
?>
