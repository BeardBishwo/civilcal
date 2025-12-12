<?php
/**
 * PayPal Subscription Database Setup Script
 * 
 * This script creates all necessary database tables for PayPal subscription integration
 * Run this once to set up the database schema
 */

require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

echo "==============================================\n";
echo "PayPal Subscription Database Setup\n";
echo "==============================================\n\n";

try {
    // Get database connection
    $db = Database::getInstance()->getPdo();
    
    echo "✓ Database connection established\n\n";
    
    // Read the SQL schema file
    $schemaFile = __DIR__ . '/paypal_subscription_schema.sql';
    
    if (!file_exists($schemaFile)) {
        throw new Exception("Schema file not found: $schemaFile");
    }
    
    echo "Reading schema file...\n";
    $sql = file_get_contents($schemaFile);
    
    // Split SQL file into individual statements
    // Remove comments and split by delimiter
    $sql = preg_replace('/--.*$/m', '', $sql); // Remove single-line comments
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql); // Remove multi-line comments
    
    // Split by semicolon but respect DELIMITER changes
    $statements = [];
    $currentStatement = '';
    $delimiter = ';';
    $lines = explode("\n", $sql);
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        // Check for DELIMITER change
        if (preg_match('/^DELIMITER\s+(.+)$/i', $line, $matches)) {
            $delimiter = trim($matches[1]);
            continue;
        }
        
        if (empty($line)) {
            continue;
        }
        
        $currentStatement .= $line . "\n";
        
        // Check if statement ends with current delimiter
        if (substr(rtrim($line), -strlen($delimiter)) === $delimiter) {
            // Remove delimiter from statement
            $currentStatement = substr($currentStatement, 0, -strlen($delimiter));
            $currentStatement = trim($currentStatement);
            
            if (!empty($currentStatement)) {
                $statements[] = $currentStatement;
            }
            
            $currentStatement = '';
        }
    }
    
    // Add any remaining statement
    if (!empty(trim($currentStatement))) {
        $statements[] = trim($currentStatement);
    }
    
    echo "Found " . count($statements) . " SQL statements\n\n";
    echo "Executing schema...\n\n";
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $index => $statement) {
        try {
            // Skip empty statements
            if (empty(trim($statement))) {
                continue;
            }
            
            // Execute statement
            $db->exec($statement);
            
            // Get statement type for better logging
            $statementType = 'Unknown';
            if (preg_match('/^(CREATE|ALTER|INSERT|UPDATE|DELETE|DROP)\s+(\w+)/i', $statement, $matches)) {
                $statementType = strtoupper($matches[1]) . ' ' . strtoupper($matches[2]);
            }
            
            echo "✓ Executed: $statementType\n";
            $successCount++;
            
        } catch (PDOException $e) {
            // Check if error is about existing object (which is okay)
            if (strpos($e->getMessage(), 'already exists') !== false || 
                strpos($e->getMessage(), 'Duplicate') !== false) {
                echo "⚠ Skipped (already exists): " . substr($statement, 0, 50) . "...\n";
            } else {
                echo "✗ Error: " . $e->getMessage() . "\n";
                echo "  Statement: " . substr($statement, 0, 100) . "...\n";
                $errorCount++;
            }
        }
    }
    
    echo "\n==============================================\n";
    echo "Setup Complete!\n";
    echo "==============================================\n";
    echo "✓ Successful: $successCount\n";
    if ($errorCount > 0) {
        echo "✗ Errors: $errorCount\n";
    }
    echo "\n";
    
    // Verify tables were created
    echo "Verifying tables...\n\n";
    $tables = [
        'user_subscriptions',
        'payments',
        'invoices',
        'payment_methods',
        'webhook_events'
    ];
    
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✓ Table '$table' exists\n";
        } else {
            echo "✗ Table '$table' NOT FOUND\n";
        }
    }
    
    echo "\n==============================================\n";
    echo "Database schema is ready for PayPal integration!\n";
    echo "==============================================\n\n";
    
    echo "Next steps:\n";
    echo "1. Run: composer require paypal/rest-api-sdk-php\n";
    echo "2. Set up PayPal configuration\n";
    echo "3. Get PayPal sandbox credentials\n\n";
    
} catch (Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
