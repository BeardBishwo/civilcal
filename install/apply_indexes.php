<?php
/**
 * Apply Performance Indexes
 * Run this script to add database indexes for better query performance
 */

require_once __DIR__ . '/../app/bootstrap.php';

$config = require CONFIG_PATH . '/database.php';
$db = new PDO(
    "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4",
    $config['username'],
    $config['password']
);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Applying performance indexes...\n";
echo str_repeat("=", 50) . "\n";

$sql = file_get_contents(__DIR__ . '/performance_indexes.sql');
$statements = array_filter(array_map('trim', explode(';', $sql)));

$success = 0;
$skipped = 0;

foreach ($statements as $statement) {
    if (empty($statement) || strpos($statement, '--') === 0) {
        continue;
    }
    
    try {
        $db->exec($statement);
        // Extract index name from statement
        if (preg_match('/idx_\w+/', $statement, $matches)) {
            echo "✓ Created index: {$matches[0]}\n";
            $success++;
        }
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            $skipped++;
        } else {
            echo "✗ Error: " . $e->getMessage() . "\n";
        }
    }
}

echo str_repeat("=", 50) . "\n";
echo "Indexes created: $success\n";
echo "Already existed: $skipped\n";
echo "Performance indexes applied successfully!\n";
