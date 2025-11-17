<?php
require_once __DIR__ . '/../app/bootstrap.php';

// Change directory to project root to ensure proper path resolution
chdir(__DIR__ . '/..');

try {
    $db = \App\Core\Database::getInstance();
    
    // Check if user_sessions table exists and what columns it has
    $result = $db->query("DESCRIBE user_sessions");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    
    echo "=== Current user_sessions Table Structure ===\n";
    foreach ($columns as $col) {
        echo sprintf("%-20s %s\n", $col['Field'], $col['Type']);
    }
    
    echo "\n=== Missing columns needed for session_token functionality ===\n";
    
    $required = ['id', 'user_id', 'session_token', 'ip_address', 'user_agent', 'last_activity', 'expires_at'];
    $existing = array_column($columns, 'Field');
    
    foreach ($required as $col) {
        if (!in_array($col, $existing)) {
            echo "❌ MISSING: $col\n";
        } else {
            echo "✅ EXISTS: $col\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
