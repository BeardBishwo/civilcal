
<?php
/**
 * Admin User Setup Script
 * Creates an admin user for testing the admin panel
 */

// Define application constant
define('BISHWO_CALCULATOR', true);

// Load application bootstrap
require_once dirname(dirname(dirname(__DIR__))) . '/app/bootstrap.php';

// Load database configuration
require_once dirname(dirname(dirname(__DIR__))) . '/app/Config/db.php';

// Get database connection
$pdo = get_db();

if (!$pdo) {
    die("Database connection failed. Please check your database configuration.");
}

echo "=== Admin User Setup ===\n";

// Check if users table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    $tableExists = $stmt->rowCount() > 0;
    
    if (!$tableExists) {
        echo "❌ Users table does not exist. Please run the installation first.\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ Error checking users table: " . $e->getMessage() . "\n";
    exit(1);
}

// Check if admin user already exists
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'admin' OR email = 'admin@bishwocalculator.com'");
    $stmt->execute();
    $existingAdmin = $stmt->fetch();
    
    if ($existingAdmin) {
        echo "✅ Admin user already exists (ID: {$existingAdmin['id']})\n";
        
        // Update the existing admin user to ensure it has admin privileges
        $updateStmt = $pdo->prepare("UPDATE users SET role = 'admin', is_admin = 1 WHERE id = ?");
        $updateStmt->execute([$existingAdmin['id']]);
        echo "✅ Updated existing user with admin privileges\n";
        exit(0);
    }
} catch (Exception $e) {
    echo "❌ Error checking for existing admin: " . $e->getMessage() . "\n";
    exit(1);
}

// Create admin user
try {
    $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $currentTime = date('Y-m-d H:i:s');
    
    $stmt = $pdo->prepare("
        INSERT INTO users (
            username, email, password, first_name, last_name, role, is_admin,
           
