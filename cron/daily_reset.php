<?php
// cron/daily_reset.php
// Run this file via CRON job every day at 00:00
// Command: /usr/bin/php /path/to/cron/daily_reset.php

if (php_sapi_name() !== 'cli') {
    die('Access Denied: CLI only');
}

// Shared Hosting Optimization
ini_set('memory_limit', '64M');
set_time_limit(30);

require_once __DIR__ . '/../app/bootstrap.php';
use App\Core\Database;
use App\Services\SettingsService;

$db = Database::getInstance();
$pdo = $db->getPdo();

echo "Starting Daily Reset: " . date('Y-m-d H:i:s') . "\n";

try {
    // 1. Reset User Daily Limits
    $pdo->beginTransaction();
    
    // Reset Ads Watched
    $stmt = $pdo->query("UPDATE user_resources SET daily_ads_watched = 0");
    echo "- Reset daily_ads_watched. Rows: " . $stmt->rowCount() . "\n";
    
    // Reset Daily Login Claimed
    $stmt = $pdo->query("UPDATE user_resources SET daily_login_claimed = 0");
    echo "- Reset daily_login_claimed. Rows: " . $stmt->rowCount() . "\n";
    
    // Reset Daily Quest Progress (if stored in user_resources, usually in mission progress table)
    // Assuming we might have a specific column or just rely on mission logic.
    // Logic: If missions are daily, we might need to reset 'claimed' status in a tracking table.
    // For MVP gamification, we'll stick to resources table resets.
    
    $pdo->commit();
    echo "✅ User Limits Reset Complete.\n";

    // 2. Rotate Tool of the Day
    $tools = ['brickwork', 'concrete', 'earthwork', 'flooring', 'painting'];
    $newTool = $tools[array_rand($tools)];
    
    // Direct DB update for settings to ensure persistence without session context
    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('tool_of_the_day', :val) ON DUPLICATE KEY UPDATE setting_value = :val2");
    $stmt->execute(['val' => $newTool, 'val2' => $newTool]);
    
    echo "✅ Tool of the Day rotated to: $newTool\n";

    // 3. Cleanup Old Security Logs (Retention: 30 days)
    $stmt = $pdo->query("DELETE FROM security_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
    echo "✅ Cleaned " . $stmt->rowCount() . " old security logs.\n";
    
    // 4. Cleanup Banned IPs (Optional: Unban after 30 days if temporary?)
    // Keeping permanent bans permanent for now.

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Daily Reset Finished Successfully.\n";
