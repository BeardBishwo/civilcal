<?php
/**
 * Test the login controller manually to see what errors occur
 */
require_once 'app/bootstrap.php';

// Set up test environment
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/api/login';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_USER_AGENT'] = 'TestScript/1.0';
$_SERVER['CONTENT_TYPE'] = 'application/json';

echo "=== Testing API Login Controller ===\n\n";

try {
    // Test payload
    $payload = [
        'username_email' => 'admin',
        'password' => 'admin123'
    ];
    
    $php_input = json_encode($payload);
    
    echo "1. Testing User lookup...\n";
    $user = \App\Models\User::findByUsername('admin');
    if (!$user) {
        echo "   ERROR: User not found!\n";
        exit(1);
    }
    echo "   OK: User found: {$user->username}\n";
    
    echo "\n2. Testing password verification...\n";
    if (!password_verify('admin123', $user->password)) {
        echo "   ERROR: Password mismatch!\n";
        echo "   Stored hash: " . substr($user->password, 0, 30) . "...\n";
        exit(1);
    }
    echo "   OK: Password verified\n";
    
    echo "\n3. Testing session insertion...\n";
    $db = \App\Core\Database::getInstance();
    $pdo = $db->getPdo();
    $sessionToken = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
    
    $stmt = $pdo->prepare("
        INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, expires_at)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $user->id,
        $sessionToken,
        '127.0.0.1',
        'TestScript',
        $expiresAt,
    ]);
    echo "   OK: Session inserted\n";
    
    echo "\n4. Testing controller method directly...\n";
    $controller = new \App\Controllers\Api\AuthController();
    
    // Mock the file_get_contents  
    $mockInput = function($file, $useIncludePath = false, $context = null) use ($php_input) {
        if ($file === 'php://input') {
            return $php_input;
        }
        return file_get_contents($file, $useIncludePath, $context);
    };
    
    // Simulate JSON input
    $_POST = [];
    
    // Call login method
    echo "   Calling controller->login()...\n";
    
    // Capture output
    ob_start();
    try {
        $controller->login();
        $output = ob_get_clean();
        echo "   Output: $output\n";
        
        // Parse JSON response
        $response = json_decode($output, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            if ($response['success'] ?? false) {
                echo "\n[SUCCESS] Login controller works!\n";
            } else {
                echo "\n[ERROR] Response indicates failure: " . ($response['error'] ?? 'unknown') . "\n";
            }
        } else {
            echo "\n[ERROR] Invalid JSON response: $output\n";
        }
    } catch (Exception $e) {
        ob_end_clean();
        echo "   EXCEPTION: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "[FATAL ERROR] " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
?>
