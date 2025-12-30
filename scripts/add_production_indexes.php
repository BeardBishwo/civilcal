<?php
// scripts/add_production_indexes.php
// Run ONCE to optimize database for production

if (php_sapi_name() !== 'cli') die('CLI only');

require_once __DIR__ . '/../app/bootstrap.php';
use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getPdo();

echo "Applying Production Database Indexes...\n";

$indexes = [
    // Users
    "ALTER TABLE users ADD INDEX idx_email (email)",
    "ALTER TABLE users ADD INDEX idx_username (username)",
    "ALTER TABLE users ADD INDEX idx_status (status)",
    "ALTER TABLE users ADD INDEX idx_created_at (created_at)",
    
    // Quiz Attempts
    "ALTER TABLE quiz_attempts ADD INDEX idx_user_score (user_id, score)",
    "ALTER TABLE quiz_attempts ADD INDEX idx_created_at (created_at)",
    // Note: category_id might not exist in attempts directly if linked via exams? 
    // Checking previous code: attempt links to exam_id. exam links to category?
    // Let's stick to safe indexes or check existence.
    // "ALTER TABLE quiz_attempts ADD INDEX idx_exam_id (exam_id)", // Good practice
    
    // User Resources
    "ALTER TABLE user_resources ADD INDEX idx_user_id (user_id)",
    "ALTER TABLE user_resources ADD INDEX idx_coins (coins)",
    // idx_net_worth is an expression index, supported in MySQL 8.0+. 
    // Shared hosting might use MariaDB or older MySQL. Skipping expression index for safety.
    
    // Security Logs (audit_logs/security_logs)
    "ALTER TABLE security_logs ADD INDEX idx_user_time (user_id, created_at)",
    "ALTER TABLE security_logs ADD INDEX idx_action_time (action_type, created_at)", // Check col name 'action_type' or 'action'
    
    // Settings
    "ALTER TABLE settings ADD INDEX idx_key (setting_key)"
];

foreach ($indexes as $sql) {
    try {
        echo "Executing: $sql ... ";
        $pdo->exec($sql);
        echo "✅ OK\n";
    } catch (PDOException $e) {
        // Code 42000 = Syntax error or access violation
        // Code HY000 = Generic error
        // Often 'Duplicate key name' means index exists.
        if (strpos($e->getMessage(), 'Duplicate key name') !== false || strpos($e->getMessage(), 'already exists') !== false) {
             echo "⚠️ Exists (Skipping)\n";
        } else {
             echo "❌ Error: " . $e->getMessage() . "\n";
        }
    }
}

echo "Database Index Optimization Complete.\n";
