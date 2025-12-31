<?php
// cron/cleanup.php
// Run WEEKLY via Cron
// Command: /usr/bin/php /path/to/cron/cleanup.php

if (php_sapi_name() !== 'cli') die('CLI only');

require_once __DIR__ . '/../app/bootstrap.php';
use App\Core\Database;

$db = Database::getInstance();
$pdo = $db->getPdo();

echo "Starting System Cleanup: " . date('Y-m-d H:i:s') . "\n";

try {
    $pdo->beginTransaction();

    // 1. Purge Expired Nonces (Older than 24 hours)
    // Assuming nonces table has created_at
    $stmt = $pdo->query("DELETE FROM security_nonces WHERE created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    echo "✅ Deleted " . $stmt->rowCount() . " expired nonces.\n";

    // 2. Archive/Delete Old User Resource Logs (Older than 90 days)
    // We keep them longer than security logs for economic audit trails
    $stmt = $pdo->query("DELETE FROM user_resource_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)");
    echo "✅ Purged " . $stmt->rowCount() . " old resource logs (90+ days).\n";

    // 3. Cleanup Old Notifications (Optional, if table exists)
    // $stmt = $pdo->query("DELETE FROM notifications WHERE is_read = 1 AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
    
    // 4. Cleanup Old Login Sessions (Older than 1 year)
    // Using login_time as the timestamp column
    $stmt = $pdo->query("DELETE FROM login_sessions WHERE login_time < DATE_SUB(NOW(), INTERVAL 1 YEAR)");
    echo "✅ Purged " . $stmt->rowCount() . " old login sessions (1 year+).\n";
    
    $pdo->commit();
    echo "System Cleanup Finished Successfully.\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "❌ Cleanup Failed: " . $e->getMessage() . "\n";
    exit(1);
}
