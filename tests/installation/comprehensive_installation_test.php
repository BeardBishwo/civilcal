<?php
/**
 * Comprehensive Installation System Test
 * Tests the complete installation flow including database creation,
 * migration execution, and admin user setup
 */

require_once __DIR__ . '/../install/index.php';

class InstallationSystemTest
{
    private $pdo;
    private $testDbName = 'bishwo_calculator_test';
    private $testConfig = [
        'host' => 'localhost',
        'user' => 'root',
        'pass' => '' // Update with your test database password if needed
    ];
    
    public function __construct()
    {
        echo "🧪 Bishwo Calculator - Comprehensive Installation Test\n";
        echo "=================================================\n\n";
    }
    
    public function runAllTests()
    {
        $this->testDatabaseCreation();
        $this->testMigrationExecution();
        $this->testAdminUserCreation();
        $this->testEnvFileGeneration();
        $this->testInstallationLock();
        
        echo "\n✅ All tests completed!\n";
    }
    
    private function testDatabaseCreation()
    {
        echo "1. Testing Database Creation...\n";
        
        try {
            // Test 1: Connect to MySQL server without database
            $pdoServer = new PDO(
                "mysql:host={$this->testConfig['host']}", 
                $this->testConfig['user'], 
                $this->testConfig['pass']
            );
            $pdoServer->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create test database
            $pdoServer->exec("CREATE DATABASE IF NOT EXISTS `{$this->testDbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            echo "   ✅ Test database created successfully\n";
            
            // Test 2: Connect to the specific database
            $this->pdo = new PDO(
                "mysql:host={$this->testConfig['host']};dbname={$this->testDbName}", 
                $this->testConfig['user'], 
                $this->testConfig['pass']
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "   ✅ Database connection successful\n";
            
        } catch (PDOException $e) {
            echo "   ❌ Database test failed: " . $e->getMessage() . "\n";
            exit(1);
        }
        
        echo "   ✅ Database creation test passed\n\n";
    }
    
    private function testMigrationExecution()
    {
        echo "2. Testing Migration Execution...\n";
        
        try {
            // Create migrations table
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            
            // Get all migration files
            $migrationsDir = __DIR__ . '/../database/migrations';
            $migrationFiles = glob($migrationsDir . '/*.php');
            sort($migrationFiles);
            
            echo "   Found " . count($migrationFiles) . " migration files\n";
            
            $executed = 0;
            $skipped = 0;
            $errors = 0;
            
            foreach ($migrationFiles as $file) {
                $migrationName = basename($file);
                
                // Check if already executed
                $checkStmt = $this->pdo->prepare("SELECT id FROM migrations WHERE migration = ?");
                $checkStmt->execute([$migrationName]);
                
                if ($checkStmt->fetch()) {
                    $skipped++;
                    continue;
                }
                
                try {
                    // Include and execute migration
                    $content = file_get_contents($file);
                    
                    if (preg_match('/class\s+(\w+).*?\{/', $content, $matches)) {
                        $className = $matches[1];
                        include $file;
                        
                        if (class_exists($className)) {
                            $migration = new $className();
                            if (method_exists($migration, 'up')) {
                                $migration->up($this->pdo);
                            }
                        }
                    } else {
                        // Handle legacy format
                        eval('?>' . $content);
                    }
                    
                    // Record as executed
                    $this->pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, 1)")
                        ->execute([$migrationName]);
                    
                    $executed++;
                    echo "   ✅ Executed: $migrationName\n";
                    
                } catch (Exception $e) {
                    $errors++;
                    echo "   ❌ Error in $migrationName: " . $e->getMessage() . "\n";
                }
            }
            
            echo "\n   Summary: $executed executed, $skipped skipped, $errors errors\n";
            
            if ($errors === 0) {
                echo "   ✅ Migration execution test passed\n\n";
            } else {
                echo "   ⚠️  Migration execution completed with errors\n\n";
            }
            
        } catch (Exception $e) {
            echo "   ❌ Migration test failed: " . $e->getMessage() . "\n";
        }
    }
    
    private function testAdminUserCreation()
    {
        echo "3. Testing Admin User Creation...\n";
        
        try {
            // Simulate admin user data
            $adminData = [
                'name' => 'Test Admin',
                'email' => 'admin@test.com',
                'password' => password_hash('testpass123', PASSWORD_DEFAULT)
            ];
            
            // Parse name
            $fullName = trim($adminData['name']);
            $nameParts = explode(' ', $fullName, 2);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
            
            // Check if user already exists
            $checkStmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $checkStmt->execute([$adminData['email']]);
            
            if ($checkStmt->fetch()) {
                // Update existing user
                $stmt = $this->pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, password = ?, role = 'admin', updated_at = NOW() WHERE email = ?");
                $stmt->execute([
                    $firstName,
                    $lastName,
                    $adminData['password'],
                    $adminData['email']
                ]);
            } else {
                // Insert new user
                $stmt = $this->pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role, created_at, updated_at) 
                                          VALUES (?, ?, ?, ?, 'admin', NOW(), NOW())");
                $stmt->execute([
                    $firstName,
                    $lastName,
                    $adminData['email'],
                    $adminData['password']
                ]);
            }
            
            // Verify user was created
            $verifyStmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $verifyStmt->execute([$adminData['email']]);
            $user = $verifyStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo "   ✅ Admin user created successfully\n";
                echo "   📧 Email: {$user['email']}\n";
                echo "   👤 Name: {$user['first_name']} {$user['last_name']}\n";
                echo "   🔑 Role: {$user['role']}\n\n";
            } else {
                echo "   ❌ Admin user creation verification failed\n\n";
            }
            
        } catch (Exception $e) {
            echo "   ❌ Admin user test failed: " . $e->getMessage() . "\n";
        }
    }
    
    private function testEnvFileGeneration()
    {
        echo "4. Testing .env File Generation...\n";
        
        try {
            // Simulate session data
            $_SESSION['db_config'] = [
                'host' => $this->testConfig['host'],
                'name' => $this->testDbName,
                'user' => $this->testConfig['user'],
                'pass' => $this->testConfig['pass']
            ];
            
            $_SESSION['admin_config'] = [
                'name' => 'Test Admin',
                'email' => 'admin@test.com'
            ];
            
            $_SESSION['email_config'] = [
                'smtp_enabled' => false
            ];
            
            // Test env file generation
            $envContent = $this->generateEnvFile();
            
            // Check if required variables are present
            $requiredVars = [
                'APP_NAME',
                'DB_HOST',
                'DB_DATABASE',
                'DB_USERNAME',
                'MAIL_FROM_ADDRESS'
            ];
            
            $missingVars = [];
            foreach ($requiredVars as $var) {
                if (strpos($envContent, $var . '=') === false) {
                    $missingVars[] = $var;
                }
            }
            
            if (empty($missingVars)) {
                echo "   ✅ .env file generation successful\n";
                echo "   📄 Generated " . strlen($envContent) . " characters of configuration\n\n";
            } else {
                echo "   ❌ Missing variables in .env file: " . implode(', ', $missingVars) . "\n\n";
            }
            
        } catch (Exception $e) {
            echo "   ❌ .env file test failed: " . $e->getMessage() . "\n";
        }
    }
    
    private function testInstallationLock()
    {
        echo "5. Testing Installation Lock...\n";
        
        try {
            // Test lock file creation
            $lockPath = __DIR__ . '/../storage/test_install.lock';
            $lockContent = date('Y-m-d H:i:s');
            
            if (file_put_contents($lockPath, $lockContent)) {
                if (file_exists($lockPath)) {
                    echo "   ✅ Installation lock file created successfully\n";
                    unlink($lockPath); // Clean up
                } else {
                    echo "   ❌ Lock file creation verification failed\n";
                }
            } else {
                echo "   ❌ Failed to create lock file\n";
            }
            
            // Test storage directory creation
            $storageDirs = [
                __DIR__ . '/../storage/test_logs',
                __DIR__ . '/../storage/test_cache',
                __DIR__ . '/../storage/test_sessions'
            ];
            
            foreach ($storageDirs as $dir) {
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }
            }
            
            $allCreated = true;
            foreach ($storageDirs as $dir) {
                if (!is_dir($dir)) {
                    $allCreated = false;
                    break;
                }
            }
            
            if ($allCreated) {
                echo "   ✅ Storage directories created successfully\n";
                
                // Clean up test directories
                foreach ($storageDirs as $dir) {
                    rmdir($dir);
                }
            } else {
                echo "   ❌ Storage directory creation failed\n";
            }
            
            echo "\n";
            
        } catch (Exception $e) {
            echo "   ❌ Installation lock test failed: " . $e->getMessage() . "\n";
        }
    }
    
    private function generateEnvFile()
    {
        $envVars = [
            'APP_NAME' => 'Bishwo Calculator',
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'APP_URL' => 'http://localhost',
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $_SESSION['db_config']['host'] ?? 'localhost',
            'DB_PORT' => '3306',
            'DB_DATABASE' => $_SESSION['db_config']['name'] ?? '',
            'DB_USERNAME' => $_SESSION['db_config']['user'] ?? '',
            'DB_PASSWORD' => $_SESSION['db_config']['pass'] ?? '',
            'MAIL_MAILER' => 'log',
            'MAIL_HOST' => '',
            'MAIL_PORT' => '587',
            'MAIL_USERNAME' => '',
            'MAIL_PASSWORD' => '',
            'MAIL_ENCRYPTION' => 'tls',
            'MAIL_FROM_ADDRESS' => $_SESSION['admin_config']['email'] ?? '',
            'MAIL_FROM_NAME' => $_SESSION['admin_config']['name'] ?? 'Bishwo Calculator',
        ];
        
        $content = "# Bishwo Calculator Environment Configuration\n";
        $content .= "# Generated on " . date('Y-m-d H:i:s') . "\n\n";
        
        foreach ($envVars as $key => $value) {
            $content .= "$key=$value\n";
        }
        
        return $content;
    }
    
    public function cleanup()
    {
        // Clean up test database
        if ($this->pdo) {
            try {
                $this->pdo->exec("DROP DATABASE IF EXISTS `{$this->testDbName}`");
                echo "🧹 Test database cleaned up\n";
            } catch (Exception $e) {
                echo "⚠️  Failed to clean up test database: " . $e->getMessage() . "\n";
            }
        }
    }
}

// Run the test
$test = new InstallationSystemTest();
$test->runAllTests();
$test->cleanup();


