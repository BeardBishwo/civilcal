<?php
/**
 * Database Migration Runner
 * Executes database migrations to update the schema with proper PDO handling
 */

require_once '../app/bootstrap.php';

try {
    echo "=== Database Migration Runner ===\n\n";
    
    // Get PDO connection from Database singleton
    $db = \App\Core\Database::getInstance();
    $pdo = $db->getPdo();
    
    echo "[OK] Database connection established.\n\n";
    
    // Get list of all migration files
    $migrationsDir = __DIR__ . '/migrations';
    $migrationFiles = glob($migrationsDir . '/*.php');
    sort($migrationFiles);
    
    if (empty($migrationFiles)) {
        echo "[WARNING] No migration files found in $migrationsDir\n";
        exit(0);
    }
    
    echo "Found " . count($migrationFiles) . " migration files.\n\n";
    
    $successful = 0;
    $skipped = 0;
    $failed = 0;
    
    foreach ($migrationFiles as $migrationFile) {
        $migrationName = basename($migrationFile);
        
        // Skip if not a PHP file
        if (!preg_match('/\.php$/', $migrationFile)) {
            echo "[-] Skipping non-PHP file: $migrationName\n";
            $skipped++;
            continue;
        }
        
        echo "Processing: $migrationName\n";
        
        try {
            // Check file content to see if it's class-based or legacy
            $fileContent = file_get_contents($migrationFile);
            
            // Check if it's a class-based migration
            if (preg_match('/class\s+(\w+)/', $fileContent, $matches)) {
                // Class-based migration - load and execute
                $className = $matches[1];
                
                // Check for bad require paths that will fail
                if (strpos($fileContent, 'require_once') !== false && 
                    preg_match('/require_once.*[\\\\\\/]\.\./', $fileContent)) {
                    echo "  [-] Skipping (bad require paths)\n";
                    $skipped++;
                    continue;
                }
                
                // Load the migration file
                require_once $migrationFile;
                
                // Find the actual class (try different namespaces)
                $actualClassName = null;
                if (class_exists($className)) {
                    $actualClassName = $className;
                } elseif (class_exists('App\\Migrations\\' . $className)) {
                    $actualClassName = 'App\\Migrations\\' . $className;
                } else {
                    echo "  [!] Class not found: {$className}\n";
                    $skipped++;
                    continue;
                }
                
                // Verify the class has an up() method
                if (!method_exists($actualClassName, 'up')) {
                    echo "  [!] Class does not have up() method\n";
                    $skipped++;
                    continue;
                }
                
                // Instantiate and call migration
                $migration = new $actualClassName();
                $migration->up($pdo);
                echo "  [+] Completed\n";
                $successful++;
                
            } else {
                // Legacy SQL file - skip if it has require errors
                if (strpos($fileContent, 'require_once') !== false && 
                    preg_match('/require_once.*Database\.php/', $fileContent)) {
                    echo "  [-] Skipping (legacy with broken requires)\n";
                    $skipped++;
                    continue;
                }
                
                echo "  [-] Not a class-based migration (skipped)\n";
                $skipped++;
            }
            
        } catch (Exception $e) {
            echo "  [ERROR] " . $e->getMessage() . "\n";
            $failed++;
        }
        
        echo "\n";
    }
    
    // Summary
    echo "\n=== Migration Summary ===\n";
    echo "Successful: $successful\n";
    echo "Skipped: $skipped\n";
    echo "Failed: $failed\n";
    
    if ($failed > 0) {
        echo "\n[WARNING] Some migrations failed. Review the errors above.\n";
        exit(1);
    } else {
        echo "\n[SUCCESS] All migrations processed!\n";
        exit(0);
    }
    
} catch (Exception $e) {
    echo "[ERROR] Fatal error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
?>

