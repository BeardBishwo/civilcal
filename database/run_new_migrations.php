<?php
/**
 * Run new migrations for admin panel enhancement
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

echo "🚀 Running Admin Panel Migrations...\n\n";

try {
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    
    // Migration files
    $migrations = [
        '019_enhance_settings_table.php',
        '020_create_content_tables.php',
        '021_create_gdpr_tables.php'
    ];
    
    foreach ($migrations as $migrationFile) {
        echo "Running: $migrationFile\n";
        echo str_repeat('-', 50) . "\n";
        
        require_once __DIR__ . '/migrations/' . $migrationFile;
        
        // Extract class name from file name
        $className = str_replace('.php', '', $migrationFile);
        $className = implode('', array_map('ucfirst', explode('_', substr($className, strpos($className, '_') + 1))));
        
        if (class_exists($className)) {
            $migration = new $className();
            $migration->up($pdo);
            echo "✓ $migrationFile completed successfully\n\n";
        } else {
            echo "⚠ Class $className not found in $migrationFile\n\n";
        }
    }
    
    echo "\n✅ All migrations completed successfully!\n";
    echo "\n📊 Database Summary:\n";
    echo str_repeat('=', 50) . "\n";
    
    // Show tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Total tables: " . count($tables) . "\n";
    echo "Tables: " . implode(', ', $tables) . "\n";
    
    // Show settings count
    $settingsCount = $pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn();
    echo "\nSettings entries: " . $settingsCount . "\n";
    
    // Show settings groups
    $groups = $pdo->query("SELECT DISTINCT setting_group FROM settings ORDER BY setting_group")->fetchAll(PDO::FETCH_COLUMN);
    echo "Settings groups: " . implode(', ', $groups) . "\n";
    
    echo "\n✅ Admin panel database is ready!\n";
    
} catch (Exception $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
