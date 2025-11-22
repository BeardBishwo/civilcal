<?php

/**
 * Email Manager Database Migration Runner
 * Runs all email-related table migrations in the correct order
 */

require_once __DIR__ . '/../app/Config/config.php';
require_once __DIR__ . '/../app/Config/db.php';
require_once __DIR__ . '/../app/Core/Database.php';

echo "=============================================\n";
echo "Email Manager Database Migration Runner\n";
echo "=============================================\n\n";

try {
    $db = App\Core\Database::getInstance();
    $pdo = $db->getPdo();

    echo "Connected to database successfully.\n";
    $stmt = $pdo->query("SELECT DATABASE()");
    echo "Connected to DB: " . $stmt->fetchColumn() . "\n\n";

    // Migration files in order
    $migrations = [
        '014_create_email_threads_table.php',
        '015_create_email_responses_table.php',
        '016_create_email_templates_table.php',
        '017_create_site_settings_table.php'
    ];

    foreach ($migrations as $migrationFile) {
        $filePath = __DIR__ . '/migrations/' . $migrationFile;

        echo "Running migration: $migrationFile\n";
        echo "-------------------------------------------\n";

        if (!file_exists($filePath)) {
            echo "❌ ERROR: Migration file not found: $filePath\n\n";
            continue;
        }

        try {
            require_once $filePath;

            // Get class name from file name (remove extension and convert to class name)
            $baseName = str_replace('.php', '', $migrationFile);
            // Remove numeric prefix (e.g., 014_)
            $baseName = preg_replace('/^\d+_/', '', $baseName);
            $className = implode('', array_map('ucfirst', explode('_', $baseName)));

            if (class_exists($className)) {
                $migration = new $className();
                $migration->up($pdo);
                echo "✅ SUCCESS: $migrationFile executed successfully\n\n";
            } else {
                echo "❌ ERROR: Class $className not found in $migrationFile\n\n";
            }
        } catch (Exception $e) {
            // Check if table already exists error
            if (strpos($e->getMessage(), 'already exists') !== false) {
                echo "⚠️  INFO: Table already exists, skipping...\n\n";
            } else {
                echo "❌ ERROR: " . $e->getMessage() . "\n\n";
            }
        }
    }

    // Verify tables were created
    echo "=============================================\n";
    echo "Verification\n";
    echo "=============================================\n\n";

    $tables = ['email_threads', 'email_responses', 'email_templates'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Table '$table' exists\n";

            // Show column count
            $stmt = $pdo->query("SHOW COLUMNS FROM $table");
            $columnCount = $stmt->rowCount();
            echo "   ($columnCount columns)\n";
        } else {
            echo "❌ Table '$table' does NOT exist\n";
        }
    }

    echo "\n=============================================\n";
    echo "Email Manager migrations completed!\n";
    echo "=============================================\n";
} catch (Exception $e) {
    echo "❌ FATAL ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
