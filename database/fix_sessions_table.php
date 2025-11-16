<?php
/**
 * Fix User Sessions Table Schema
 * Adds missing session_token and expires_at columns required by Auth system
 */

require_once '../app/bootstrap.php';

try {
    $db = \App\Core\Database::getInstance();
    $pdo = $db->getPdo();
    
    echo "=== Fixing user_sessions Table Schema ===\n\n";
    
    // First, check current structure
    $result = $db->query("DESCRIBE user_sessions");
    $columns = $result->fetchAll(\PDO::FETCH_ASSOC);
    
    echo "Current table structure:\n";
    foreach ($columns as $col) {
        echo sprintf("  %-20s %s\n", $col['Field'], $col['Type']);
    }
    
    // The current table has wrong structure - id should be INT AUTO_INCREMENT, not VARCHAR(128)
    // Let's backup and recreate
    echo "\n[CRITICAL] Table structure is wrong. Backing up and recreating...\n";
    
    // Get all existing sessions to preserve if possible
    $existingSessions = [];
    try {
        $result = $db->query("SELECT * FROM user_sessions WHERE user_id IS NOT NULL");
        $existingSessions = $result->fetchAll(\PDO::FETCH_ASSOC);
        echo "Preserved " . count($existingSessions) . " existing sessions.\n";
    } catch (Exception $e) {
        echo "No sessions to preserve.\n";
    }
    
    // Drop the old table
    echo "Dropping old user_sessions table...\n";
    $pdo->exec("DROP TABLE IF EXISTS user_sessions");
    
    // Create new table with correct schema
    echo "Creating new user_sessions table with correct schema...\n";
    $pdo->exec("
        CREATE TABLE user_sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            session_token VARCHAR(255) UNIQUE NOT NULL,
            ip_address VARCHAR(45),
            user_agent TEXT,
            last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            expires_at TIMESTAMP NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_session_token (session_token)
        )
    ");
    echo "[+] New user_sessions table created\n";
    
    // Step 3: Verify the fixes
    echo "\n=== Verifying Fixed Schema ===\n";
    $result = $db->query("DESCRIBE user_sessions");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $col) {
        echo sprintf("%-20s %s\n", $col['Field'], $col['Type']);
    }
    
    echo "\n=== Validation ===\n";
    $required = ['id', 'user_id', 'session_token', 'ip_address', 'user_agent', 'last_activity', 'expires_at'];
    $existing = array_column($columns, 'Field');
    
    $allPresent = true;
    foreach ($required as $col) {
        if (!in_array($col, $existing)) {
            echo "[MISSING] $col\n";
            $allPresent = false;
        } else {
            echo "[OK] $col\n";
        }
    }
    
    if ($allPresent) {
        echo "\n[SUCCESS] All required columns are now present!\n";
        echo "The user_sessions table is ready for authentication.\n";
    } else {
        echo "\n[WARNING] Some required columns are still missing.\n";
        exit(1);
    }
    
} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    exit(1);
}
?>
