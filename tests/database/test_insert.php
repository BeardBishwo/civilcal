<?php
require_once 'app/bootstrap.php';

try {
    $db = \App\Core\Database::getInstance();
    $result = $db->query("DESCRIBE user_sessions");
    $columns = $result->fetchAll(\PDO::FETCH_ASSOC);
    
    echo "Current user_sessions structure:\n";
    foreach ($columns as $col) {
        echo sprintf("%-20s %s\n", $col['Field'], $col['Type']);
    }
    
    // Try a test insert
    echo "\n\nTesting INSERT statement...\n";
    
    $pdo = $db->getPdo();
    $sessionToken = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
    
    $stmt = $pdo->prepare("
        INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, expires_at)
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        1,
        $sessionToken,
        '127.0.0.1',
        'Test Agent',
        $expiresAt,
    ]);
    
    echo "[SUCCESS] Test insert worked!\n";
    
} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
?>
