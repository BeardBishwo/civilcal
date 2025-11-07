<?php
/**
 * Database Migration Runner
 * Executes database migrations to update the schema
 */

require_once '../includes/config.php';
require_once '../includes/Database.php';

try {
    echo "Starting database migration...\n";
    
    // Create database instance
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception("Failed to connect to database");
    }
    
    echo "Database connection established.\n";
    
    // Load and execute the migration
    require_once 'migrations/010_add_profile_fields_to_users.php';
    
    $migration = new AddProfileFieldsToUsers();
    $sql = $migration->up();
    
    // First, check what columns already exist
    $stmt = $conn->query("DESCRIBE users");
    $existingColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $newFields = [
        'avatar' => "avatar VARCHAR(255) NULL",
        'professional_title' => "professional_title VARCHAR(255) NULL",
        'company' => "company VARCHAR(255) NULL",
        'phone' => "phone VARCHAR(20) NULL",
        'timezone' => "timezone VARCHAR(100) NULL DEFAULT 'UTC'",
        'measurement_system' => "measurement_system ENUM('metric', 'imperial') DEFAULT 'metric'",
        'notification_preferences' => "notification_preferences JSON NULL",
        'email_notifications' => "email_notifications BOOLEAN DEFAULT TRUE",
        'calculation_privacy' => "calculation_privacy ENUM('public', 'private', 'team') DEFAULT 'private'",
        'profile_completed' => "profile_completed BOOLEAN DEFAULT FALSE",
        'last_login' => "last_login DATETIME NULL",
        'login_count' => "login_count INT DEFAULT 0",
        'bio' => "bio TEXT NULL",
        'website' => "website VARCHAR(255) NULL",
        'location' => "location VARCHAR(255) NULL",
        'social_links' => "social_links JSON NULL",
        'email_verified_at' => "email_verified_at DATETIME NULL",
        'two_factor_enabled' => "two_factor_enabled BOOLEAN DEFAULT FALSE",
        'two_factor_secret' => "two_factor_secret VARCHAR(255) NULL",
        'updated_at' => "updated_at DATETIME NULL"
    ];
    
    echo "Checking existing columns in users table...\n";
    $fieldsToAdd = [];
    foreach ($newFields as $fieldName => $fieldDefinition) {
        if (!in_array($fieldName, $existingColumns)) {
            $fieldsToAdd[] = "ADD COLUMN $fieldDefinition";
            echo "  ➕ Will add: $fieldName\n";
        } else {
            echo "  ✅ Already exists: $fieldName\n";
        }
    }
    
    if (empty($fieldsToAdd)) {
        echo "ℹ️  All profile fields already exist in the users table.\n";
    } else {
        echo "Executing migration: Add profile fields to users table\n";
        
        $sql = "ALTER TABLE users " . implode(",\n        ", $fieldsToAdd);
        
        // Execute the migration
        $conn->exec($sql);
        
        echo "✅ Migration completed successfully!\n";
        echo "Added " . count($fieldsToAdd) . " new profile fields to users table.\n";
    }
    
    // Verify the migration by checking the table structure
    $stmt = $conn->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "\nVerifying migration results:\n";
    $found = 0;
    foreach (array_keys($newFields) as $field) {
        if (in_array($field, $columns)) {
            echo "  ✅ $field\n";
            $found++;
        } else {
            echo "  ❌ $field (NOT FOUND)\n";
        }
    }
    
    echo "\nMigration Summary:\n";
    echo "Total new fields: " . count($newFields) . "\n";
    echo "Fields present: $found\n";
    
    if ($found === count($newFields)) {
        echo "🎉 All profile fields are now present in the users table!\n";
    } else {
        echo "⚠️  Some fields may not have been created properly.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
