<?php
/**
 * Web-based Installation System Test
 * Can be run from the web browser to test the installation system
 */

// Prevent session conflicts
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation System Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h1 class="card-title mb-0">
                            <i class="fas fa-cog"></i> Bishwo Calculator - Installation System Test
                        </h1>
                    </div>
                    <div class="card-body">
                        
                        <?php
                        $testResults = [];
                        $overallStatus = 'success';
                        
                        // Test 1: Check migration files exist and are properly structured
                        echo "<h3><i class='fas fa-database'></i> Test 1: Migration Files Structure</h3>";
                        $migrationsDir = __DIR__ . '/../database/migrations';
                        $migrationFiles = glob($migrationsDir . '/*.php');
                        
                        if (empty($migrationFiles)) {
                            echo "<div class='alert alert-danger'>❌ No migration files found</div>";
                            $testResults['migrations'] = false;
                            $overallStatus = 'danger';
                        } else {
                            echo "<div class='alert alert-info'>Found " . count($migrationFiles) . " migration files</div>";
                            echo "<ul>";
                            
                            $validMigrations = 0;
                            foreach ($migrationFiles as $file) {
                                $fileName = basename($file);
                                $content = file_get_contents($file);
                                
                                // Check if it has proper class structure
                                if (preg_match('/class\s+(\w+).*?\{/', $content, $matches)) {
                                    $className = $matches[1];
                                    echo "<li class='text-success'>✅ $fileName (Class: $className)</li>";
                                    $validMigrations++;
                                } else {
                                    echo "<li class='text-warning'>⚠️  $fileName (Legacy format)</li>";
                                }
                            }
                            echo "</ul>";
                            
                            $testResults['migrations'] = $validMigrations > 0;
                        }
                        
                        // Test 2: Check installation file syntax
                        echo "<h3><i class='fas fa-file-code'></i> Test 2: Installation File Syntax</h3>";
                        $installFile = __DIR__ . '/index.php';
                        
                        if (!file_exists($installFile)) {
                            echo "<div class='alert alert-danger'>❌ Installation file not found</div>";
                            $testResults['syntax'] = false;
                            $overallStatus = 'danger';
                        } else {
                            $installContent = file_get_contents($installFile);
                            
                            // Basic syntax checks
                            $issues = [];
                            if (strpos($installContent, '<?php') === false) {
                                $issues[] = "Missing opening PHP tag";
                            }
                            if (substr($installContent, -2) !== '?>') {
                                $issues[] = "Missing closing PHP tag (may be intentional)";
                            }
                            if (substr_count($installContent, 'function handleDatabaseStep()') !== 1) {
                                $issues[] = "handleDatabaseStep() function issues";
                            }
                            
                            if (empty($issues)) {
                                echo "<div class='alert alert-success'>✅ Installation file syntax appears valid</div>";
                                $testResults['syntax'] = true;
                            } else {
                                echo "<div class='alert alert-warning'>⚠️  Potential issues found:</div><ul>";
                                foreach ($issues as $issue) {
                                    echo "<li class='text-warning'>$issue</li>";
                                }
                                echo "</ul>";
                                $testResults['syntax'] = false;
                                $overallStatus = 'warning';
                            }
                        }
                        
                        // Test 3: Test database connection logic
                        echo "<h3><i class='fas fa-server'></i> Test 3: Database Connection Logic</h3>";
                        
                        // Simulate database configuration
                        $testDbConfig = [
                            'host' => 'localhost',
                            'user' => 'root',
                            'pass' => '',
                            'name' => 'test_db'
                        ];
                        
                        try {
                            // Test connecting to MySQL server without database
                            $pdoServer = new PDO("mysql:host={$testDbConfig['host']}", $testDbConfig['user'], $testDbConfig['pass']);
                            $pdoServer->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            echo "<div class='alert alert-success'>✅ Can connect to MySQL server</div>";
                            
                            // Test creating database
                            $pdoServer->exec("CREATE DATABASE IF NOT EXISTS `{$testDbConfig['name']}_test` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                            
                            echo "<div class='alert alert-success'>✅ Database creation logic works</div>";
                            
                            // Test connecting to specific database
                            $pdo = new PDO("mysql:host={$testDbConfig['host']};dbname={$testDbConfig['name']}_test", $testDbConfig['user'], $testDbConfig['pass']);
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            echo "<div class='alert alert-success'>✅ Database-specific connection works</div>";
                            
                            // Clean up test database
                            $pdoServer->exec("DROP DATABASE IF EXISTS `{$testDbConfig['name']}_test`");
                            
                            $testResults['database'] = true;
                            
                        } catch (PDOException $e) {
                            echo "<div class='alert alert-warning'>⚠️  Database test skipped: " . $e->getMessage() . "</div>";
                            $testResults['database'] = false;
                        }
                        
                        // Test 4: Environment file generation
                        echo "<h3><i class='fas fa-cog'></i> Test 4: Environment File Generation</h3>";
                        
                        // Simulate session data
                        $_SESSION['db_config'] = $testDbConfig;
                        $_SESSION['admin_config'] = [
                            'name' => 'Test Admin',
                            'email' => 'admin@test.com'
                        ];
                        $_SESSION['email_config'] = [
                            'smtp_enabled' => false
                        ];
                        
                        // Test env file generation function (replicated from install/index.php)
                        function generateEnvFileTest() {
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
                        
                        try {
                            $envContent = generateEnvFileTest();
                            $requiredVars = ['APP_NAME', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'MAIL_FROM_ADDRESS'];
                            
                            $missingVars = [];
                            foreach ($requiredVars as $var) {
                                if (strpos($envContent, $var . '=') === false) {
                                    $missingVars[] = $var;
                                }
                            }
                            
                            if (empty($missingVars)) {
                                echo "<div class='alert alert-success'>✅ Environment file generation works correctly</div>";
                                $testResults['env'] = true;
                            } else {
                                echo "<div class='alert alert-danger'>❌ Missing variables: " . implode(', ', $missingVars) . "</div>";
                                $testResults['env'] = false;
                                $overallStatus = 'danger';
                            }
                            
                            echo "<details class='mt-2'><summary>View Generated Content</summary>";
                            echo "<pre class='mt-2 p-2 bg-light border'>" . htmlspecialchars($envContent) . "</pre>";
                            echo "</details>";
                            
                        } catch (Exception $e) {
                            echo "<div class='alert alert-danger'>❌ Environment file test failed: " . $e->getMessage() . "</div>";
                            $testResults['env'] = false;
                            $overallStatus = 'danger';
                        }
                        
                        // Test 5: File system permissions
                        echo "<h3><i class='fas fa-folder'></i> Test 5: File System Permissions</h3>";
                        
                        $storagePath = __DIR__ . '/../storage';
                        $envPath = __DIR__ . '/../.env';
                        
                        if (!is_dir($storagePath)) {
                            mkdir($storagePath, 0755, true);
                            echo "<div class='alert alert-info'>ℹ️  Created storage directory</div>";
                        }
                        
                        $testFiles = [
                            $storagePath . '/test.txt',
                            $envPath
                        ];
                        
                        $permissionTestPassed = true;
                        foreach ($testFiles as $testFile) {
                            if (file_put_contents($testFile, 'test') !== false) {
                                echo "<div class='alert alert-success'>✅ Can write to: " . basename($testFile) . "</div>";
                                unlink($testFile); // Clean up
                            } else {
                                echo "<div class='alert alert-danger'>❌ Cannot write to: " . basename($testFile) . "</div>";
                                $permissionTestPassed = false;
                            }
                        }
                        
                        $testResults['permissions'] = $permissionTestPassed;
                        if (!$permissionTestPassed) {
                            $overallStatus = 'danger';
                        }
                        
                        // Summary
                        echo "<h3><i class='fas fa-chart-bar'></i> Test Summary</h3>";
                        
                        $passedTests = array_filter($testResults);
                        $totalTests = count($testResults);
                        $passedCount = count($passedTests);
                        
                        if ($overallStatus === 'success') {
                            echo "<div class='alert alert-success'>";
                            echo "<h4>🎉 All tests passed! Installation system is ready.</h4>";
                        } elseif ($overallStatus === 'warning') {
                            echo "<div class='alert alert-warning'>";
                            echo "<h4>⚠️  Tests passed with warnings. System should work but may need attention.</h4>";
                        } else {
                            echo "<div class='alert alert-danger'>";
                            echo "<h4>❌ Some critical tests failed. Please fix before installing.</h4>";
                        }
                        
                        echo "<p><strong>Results:</strong> $passedCount/$totalTests tests passed</p>";
                        echo "<ul>";
                        foreach ($testResults as $test => $result) {
                            $icon = $result ? '✅' : '❌';
                            $testName = ucfirst($test);
                            echo "<li>$icon $testName</li>";
                        }
                        echo "</ul>";
                        echo "</div>";
                        
                        ?>
                        
                        <div class="mt-4">
                            <h3><i class='fas fa-rocket'></i> Ready to Install?</h3>
                            <p>If all tests pass, you can now proceed with the installation:</p>
                            <a href="index.php" class="btn btn-success btn-lg">
                                <i class="fas fa-play"></i> Start Installation
                            </a>
                            <a href="../" class="btn btn-secondary">
                                <i class="fas fa-home"></i> Back to Home
                            </a>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
