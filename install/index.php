<?php
/**
 * Bishwo Calculator - Installation Wizard
 * Main Installation Entry Point
 * 
 * @package BishwoCalculator
 * @version 1.0.0
 */

// Start session for installation data persistence
session_start();

// Define installation steps
define('INSTALL_STEPS', [
    'welcome' => 'Welcome',
    'requirements' => 'System Requirements',
    'permissions' => 'File Permissions',
    'database' => 'Database Configuration',
    'admin' => 'Administrator Account',
    'email' => 'Email Configuration',
    'finish' => 'Installation Complete'
]);

// Get current step from URL parameter
$currentStep = isset($_GET['step']) ? $_GET['step'] : 'welcome';
$stepIndex = array_search($currentStep, array_keys(INSTALL_STEPS));

// Redirect to welcome if invalid step
if ($stepIndex === false) {
    $currentStep = 'welcome';
    $stepIndex = 0;
}

// Check if installation is already completed
if (file_exists(__DIR__ . '/../storage/install.lock') && $currentStep !== 'finish') {
    header('Location: ../');
    exit;
}

// Handle form submissions
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'save_requirements':
            // Requirements step completed
            $_SESSION['install_requirements_ok'] = true;
            redirectToNextStep();
            break;
            
        case 'save_permissions':
            // Permissions step completed
            $_SESSION['install_permissions_ok'] = true;
            redirectToNextStep();
            break;
            
        case 'save_database':
            $errors = handleDatabaseStep();
            if (empty($errors)) {
                $_SESSION['install_database_ok'] = true;
                redirectToNextStep();
            }
            break;
            
        case 'save_admin':
            $errors = handleAdminStep();
            if (empty($errors)) {
                $_SESSION['install_admin_ok'] = true;
                redirectToNextStep();
            }
            break;
            
        case 'save_email':
            $errors = handleEmailStep();
            if (empty($errors)) {
                $_SESSION['install_email_ok'] = true;
                redirectToNextStep();
            }
            break;
            
        case 'complete_installation':
            $errors = handleInstallationCompletion();
            if (empty($errors)) {
                $success = true;
            }
            break;
    }
}

// Helper Functions
function redirectToNextStep() {
    $currentStep = $_GET['step'] ?? 'welcome';
    $steps = array_keys(INSTALL_STEPS);
    $currentIndex = array_search($currentStep, $steps);
    
    if ($currentIndex < count($steps) - 1) {
        $nextStep = $steps[$currentIndex + 1];
        header("Location: index.php?step=$nextStep");
        exit;
    }
}

function handleDatabaseStep() {
    $errors = [];
    
    // Validate database configuration
    $dbHost = trim($_POST['db_host'] ?? '');
    $dbName = trim($_POST['db_name'] ?? '');
    $dbUser = trim($_POST['db_user'] ?? '');
    $dbPass = $_POST['db_pass'] ?? '';
    
    if (empty($dbHost)) $errors[] = 'Database host is required';
    if (empty($dbName)) $errors[] = 'Database name is required';
    if (empty($dbUser)) $errors[] = 'Database username is required';
    
    if (empty($errors)) {
        try {
            // First try to connect to MySQL server without specifying database
            $pdoServer = new PDO("mysql:host=$dbHost", $dbUser, $dbPass);
            $pdoServer->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create database if it doesn't exist
            $pdoServer->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Now connect to the specific database
            $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Save database configuration to session
            $_SESSION['db_config'] = [
                'host' => $dbHost,
                'name' => $dbName,
                'user' => $dbUser,
                'pass' => $dbPass
            ];
        } catch (PDOException $e) {
            $errors[] = 'Database connection failed: ' . $e->getMessage();
        }
    }
    
    return $errors;
}

function handleAdminStep() {
    $errors = [];
    
    $adminName = trim($_POST['admin_name'] ?? '');
    $adminEmail = trim($_POST['admin_email'] ?? '');
    $adminPass = $_POST['admin_pass'] ?? '';
    $adminPassConfirm = $_POST['admin_pass_confirm'] ?? '';
    
    if (empty($adminName)) $errors[] = 'Administrator name is required';
    if (empty($adminEmail)) $errors[] = 'Administrator email is required';
    if (empty($adminPass)) $errors[] = 'Administrator password is required';
    if ($adminPass !== $adminPassConfirm) $errors[] = 'Passwords do not match';
    if (strlen($adminPass) < 6) $errors[] = 'Password must be at least 6 characters long';
    
    if (empty($errors)) {
        // Save admin configuration
        $_SESSION['admin_config'] = [
            'name' => $adminName,
            'email' => $adminEmail,
            'password' => password_hash($adminPass, PASSWORD_DEFAULT)
        ];
    }
    
    return $errors;
}

function handleEmailStep() {
    $errors = [];
    
    // Check if email setup is being skipped
    if (isset($_POST['skip_email'])) {
        $_SESSION['email_config'] = [
            'smtp_enabled' => false,
            'skipped' => true
        ];
        $_SESSION['install_email_ok'] = true;
        redirectToNextStep();
        return [];
    }
    
    // Handle SMTP configuration
    $smtpEnabled = isset($_POST['smtp_enabled']) && $_POST['smtp_enabled'] === '1';
    $_SESSION['email_config'] = [
        'smtp_enabled' => $smtpEnabled
    ];
    
    if ($smtpEnabled) {
        $smtpHost = trim($_POST['smtp_host'] ?? '');
        $smtpPort = trim($_POST['smtp_port'] ?? '');
        $smtpUser = trim($_POST['smtp_user'] ?? '');
        $smtpPass = $_POST['smtp_pass'] ?? '';
        
        // Only validate if SMTP is enabled
        if (empty($smtpHost)) $errors[] = 'SMTP host is required when SMTP is enabled';
        if (empty($smtpPort)) $errors[] = 'SMTP port is required when SMTP is enabled';
        if (empty($smtpUser)) $errors[] = 'SMTP username is required when SMTP is enabled';
        
        if (empty($errors)) {
            $_SESSION['email_config'] = array_merge($_SESSION['email_config'], [
                'host' => $smtpHost,
                'port' => $smtpPort,
                'user' => $smtpUser,
                'pass' => $smtpPass
            ]);
        }
    } else {
        // Store empty values when SMTP is disabled
        $_SESSION['email_config'] = array_merge($_SESSION['email_config'], [
            'host' => '',
            'port' => '',
            'user' => '',
            'pass' => ''
        ]);
    }
    
    return $errors;
}

function handleInstallationCompletion() {
    $errors = [];
    
    try {
        // Create .env file
        $envContent = generateEnvFile();
        $envPath = __DIR__ . '/../.env';
        
        if (file_put_contents($envPath, $envContent) === false) {
            $errors[] = 'Failed to create .env file';
        }
        
        // Create installation lock files (new + legacy)
        $legacyLockPath = __DIR__ . '/../storage/install.lock';
        $installedLockPath = __DIR__ . '/../storage/installed.lock';
        if (file_put_contents($legacyLockPath, date('Y-m-d H:i:s')) === false) {
            $errors[] = 'Failed to create legacy installation lock file';
        }
        if (file_put_contents($installedLockPath, date('Y-m-d H:i:s')) === false) {
            $errors[] = 'Failed to create installation lock file';
        }
        
        // Create storage directories if they don't exist
        $storageDirs = [
            __DIR__ . '/../storage/logs',
            __DIR__ . '/../storage/cache',
            __DIR__ . '/../storage/sessions'
        ];
        
        foreach ($storageDirs as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
        }
        
        // Run database migrations
        if (!empty($_SESSION['db_config'])) {
            runDatabaseMigrations();
        }
        
    } catch (Exception $e) {
        $errors[] = 'Installation completion failed: ' . $e->getMessage();
    }
    
    return $errors;
}

function generateEnvFile() {
    $envVars = [
        'APP_NAME' => 'Bishwo Calculator',
        'APP_ENV' => 'production',
        'APP_DEBUG' => 'false',
        'APP_URL' => getBaseUrl(),
        'DB_CONNECTION' => 'mysql',
        'DB_HOST' => $_SESSION['db_config']['host'] ?? 'localhost',
        'DB_PORT' => '3306',
        'DB_DATABASE' => $_SESSION['db_config']['name'] ?? '',
        'DB_USERNAME' => $_SESSION['db_config']['user'] ?? '',
        'DB_PASSWORD' => $_SESSION['db_config']['pass'] ?? '',
        'MAIL_MAILER' => isset($_SESSION['email_config']['smtp_enabled']) && $_SESSION['email_config']['smtp_enabled'] ? 'smtp' : 'log',
        'MAIL_HOST' => $_SESSION['email_config']['host'] ?? '',
        'MAIL_PORT' => $_SESSION['email_config']['port'] ?? '587',
        'MAIL_USERNAME' => $_SESSION['email_config']['user'] ?? '',
        'MAIL_PASSWORD' => $_SESSION['email_config']['pass'] ?? '',
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

function runDatabaseMigrations() {
    $migrationsDir = __DIR__ . '/../database/migrations';
    if (!is_dir($migrationsDir)) {
        return;
    }
    
    // Get all migration files
    $migrationFiles = glob($migrationsDir . '/*.php');
    sort($migrationFiles);
    
    $pdo = new PDO(
        "mysql:host={$_SESSION['db_config']['host']};dbname={$_SESSION['db_config']['name']}", 
        $_SESSION['db_config']['user'], 
        $_SESSION['db_config']['pass']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Load migration compatibility layer
    require_once __DIR__ . '/includes/migration_compat.php';
    MigrationCompat::setPdo($pdo);
    
    // Create migrations table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL,
        batch INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Get executed migrations
    $executed = $pdo->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);
    
    // Run new migrations
    foreach ($migrationFiles as $file) {
        $migrationName = basename($file);
        if (!in_array($migrationName, $executed)) {
            try {
                // Include the file and check for class structure
                $content = file_get_contents($file);
                
                // Check if it's a class-based migration
                if (preg_match('/class\s+(\w+).*?\{/', $content, $matches)) {
                    $className = $matches[1];
                    include $file;
                    
                    if (class_exists($className)) {
                        $migration = new $className();
                        if (method_exists($migration, 'up')) {
                            // For modern migrations, pass PDO directly
                            if ($className === 'CreateCompleteSystemTables') {
                                $migration->up($pdo);
                            } else {
                                // For other migrations, they can use the Database class
                                $migration->up($pdo);
                            }
                        }
                    }
                } else {
                    // Handle legacy migration format (just execute SQL)
                    eval('?>' . $content);
                }
                
                // Record migration as executed
                $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, 1)")
                    ->execute([$migrationName]);
                    
            } catch (Exception $e) {
                // Log error but continue with other migrations
                error_log("Migration error for $migrationName: " . $e->getMessage());
            }
        }
    }
    
    // Create admin user
    if (!empty($_SESSION['admin_config'])) {
        try {
            // Parse the full name into first and last name
            $fullName = trim($_SESSION['admin_config']['name']);
            $nameParts = explode(' ', $fullName, 2);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
            
            // Check if admin user already exists
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $checkStmt->execute([$_SESSION['admin_config']['email']]);
            
            if ($checkStmt->fetch()) {
                // Update existing user
                $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, password = ?, role = 'admin', updated_at = NOW() WHERE email = ?");
                $stmt->execute([
                    $firstName,
                    $lastName,
                    $_SESSION['admin_config']['password'],
                    $_SESSION['admin_config']['email']
                ]);
            } else {
                // Insert new user
                $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role, created_at, updated_at) 
                                      VALUES (?, ?, ?, ?, 'admin', NOW(), NOW())");
                $stmt->execute([
                    $firstName,
                    $lastName,
                    $_SESSION['admin_config']['email'],
                    $_SESSION['admin_config']['password']
                ]);
            }
        } catch (Exception $e) {
            error_log("Admin user creation error: " . $e->getMessage());
        }
    }
}

function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $basePath = dirname($scriptName);
    if ($basePath === '/') $basePath = '';
    
    return $protocol . '://' . $host . $basePath;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bishwo Calculator - Installation Wizard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom Installation CSS -->
    <link href="assets/css/install.css" rel="stylesheet">
</head>
<body class="install-body">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <!-- Header -->
                <div class="install-header text-center mb-4">
                    <div class="install-logo">
                        <img src="assets/images/banner.jpg" alt="Bishwo Calculator Logo" class="logo-image">
                    </div>
                    <h1 class="install-title">🚀 Bishwo Calculator</h1>
                    <p class="install-subtitle">Professional Engineering Calculator Suite - Installation Wizard</p>
                </div>
                
                <!-- Professional Arrow-Style Progress Navigation -->
                <div class="arrow-progress-container">
                    <div class="arrow-progress">
                        <div class="progress-line"></div>
                        <div class="progress-line-fill" style="width: <?= ($stepIndex / (count(INSTALL_STEPS) - 1)) * 100 ?>%;"></div>
                        <?php foreach (INSTALL_STEPS as $step => $label): ?>
                            <?php $stepIndexNum = array_search($step, array_keys(INSTALL_STEPS)); ?>
                            <?php $isActive = ($step === $currentStep); ?>
                            <?php $isCompleted = ($stepIndexNum < $stepIndex); ?>
                            <div class="arrow-step <?php echo $isActive ? 'active' : ''; ?> <?php echo $isCompleted ? 'completed' : ''; ?>">
                                <div class="arrow-step-content">
                                    <div class="arrow-step-label"><?php echo $label; ?></div>
                                </div>
                                <?php if ($stepIndexNum < count(INSTALL_STEPS) - 1): ?>
                                    <div class="arrow-connector">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Main Content -->
                <div class="install-content">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle"></i> Installation Errors</h5>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <h5><i class="fas fa-check-circle"></i> Installation Complete!</h5>
                            <p>Bishwo Calculator has been successfully installed and is ready to use.</p>
                            <div class="d-flex gap-3 mt-3">
                                <a href="../index.php" class="btn btn-success">
                                    <i class="fas fa-home"></i> Go to Application
                                </a>
                                <a href="../admin" class="btn btn-primary">
                                    <i class="fas fa-cog"></i> Admin Dashboard
                                </a>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    If you see 404 errors, make sure your web server is configured to serve the parent directory.
                                </small>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php include "includes/Installer.php"; ?>
                        <?php
                        $installer = new Installer();
                        echo $installer->renderStep($currentStep);
                        ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Installation JS -->
    <script src="assets/js/install.js"></script>
</body>
</html>
