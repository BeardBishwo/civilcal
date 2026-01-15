<?php
// Migration: Efficiency Leaderboard
require_once __DIR__ . '/app/bootstrap.php';
$db = \App\Core\Database::getInstance();

try {
    $db->query("ALTER TABLE firm_biweekly_stats 
                ADD COLUMN efficiency_score DECIMAL(10,2) DEFAULT 0, 
                ADD COLUMN active_member_count INT DEFAULT 0");
    echo "Migration Successful: Added efficiency columns.\n";
} catch (Exception $e) {
    echo "Migration Error: " . $e->getMessage() . "\n";
}
