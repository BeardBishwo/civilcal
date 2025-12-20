<?php
/**
 * Professional Engineering Calculator Installer
 * Modern Installation Experience with Auto-Deletion
 */

// Start session
session_start();

// Check if already installed
if (file_exists(__DIR__ . '/../storage/install.lock')) {
    header('Location: ../');
    exit('Installation already completed!');
}

// Installation steps
$steps = [
    'welcome' => ['title' => 'Welcome', 'icon' => 'fas fa-heart', 'desc' => 'Welcome to the Professional Engineering Calculator'],
    'requirements' => ['title' => 'Requirements', 'icon' => 'fas fa-server', 'desc' => 'System compatibility check'],
    'database' => ['title' => 'Database', 'icon' => 'fas fa-database', 'desc' => 'Configure database connection'],
    'admin' => ['title' => 'Admin User', 'icon' => 'fas fa-user-shield', 'desc' => 'Create administrator account'],
    'settings' => ['title' => 'Settings', 'icon' => 'fas fa-cogs', 'desc' => 'Configure site settings'],
    'complete' => ['title' => 'Complete', 'icon' => 'fas fa-check-circle', 'desc' => 'Installation finished']
];

$currentStep = $_GET['step'] ?? 'welcome';
if (!isset($steps[$currentStep])) {
    $currentStep = 'welcome';
}

$stepKeys = array_keys($steps);
$currentIndex = array_search($currentStep, $stepKeys);

// Handle form submissions
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'check_requirements':
            $errors = checkSystemRequirements();
            if (empty($errors)) {
                $_SESSION['requirements_passed'] = true;
                header('Location: ?step=database');
                exit;
            }
            break;
            
        case 'test_database':
            $errors = testDatabaseConnection($_POST);
            if (empty($errors)) {
                $_SESSION['db_config'] = $_POST;
                $success = 'Database connection successful!';
            }
            break;
            
        case 'save_database':
            $errors = saveDatabaseConfig($_POST);
            if (empty($errors)) {
                header('Location: ?step=admin');
                exit;
            }
            break;
            
        case 'create_admin':
            $errors = createAdminUser($_POST);
            if (empty($errors)) {
                header('Location: ?step=settings');
                exit;
            }
            break;
            
        case 'save_settings':
            $errors = saveSettings($_POST);
            if (empty($errors)) {
                header('Location: ?step=complete');
                exit;
            }
            break;
            
        case 'finish_install':
            $result = finishInstallation();
            if ($result['success']) {
                // Check auto-delete setting
                if (getAutoDeleteSetting()) {
                    deleteInstallFolder();
                }
                $baseUrl = getAppBaseUrl();
                header('Location: ' . $baseUrl . '/admin/dashboard');
                exit;
            } else {
                $errors[] = $result['error'];
            }
            break;
    }
}

// Helper functions

/**
 * Get the base URL for the application
 */
function getAppBaseUrl() {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/install/installer.php';
    $scriptDir = dirname($scriptName);
    
    // If we are in /install or /Bishwo_Calculator/install, go up one level
    $basePath = dirname($scriptDir);
    
    // Normalize root path to empty string
    if ($basePath === '/' || $basePath === '\\' || $basePath === '.') {
        $basePath = '';
    }
    
    // Fallback detection for common subdirectory setups if basePath is still empty
    if (empty($basePath) && isset($_SERVER['REQUEST_URI'])) {
        $uri = $_SERVER['REQUEST_URI'];
        if (strpos($uri, '/' . basename(dirname(__DIR__)) . '/') === 0) {
            $basePath = '/' . basename(dirname(__DIR__));
        }
    }
    
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    return $protocol . '://' . $host . $basePath;
}

function checkSystemRequirements() {
    $errors = [];
    
    // PHP version check
    if (version_compare(PHP_VERSION, '7.4.0', '<')) {
        $errors[] = 'PHP 7.4.0 or higher required. Current: ' . PHP_VERSION;
    }
    
    // Required extensions
    $required = ['pdo', 'pdo_mysql', 'mbstring', 'curl', 'openssl'];
    foreach ($required as $ext) {
        if (!extension_loaded($ext)) {
            $errors[] = "PHP extension '{$ext}' is required but not installed";
        }
    }
    
    // Directory permissions
    $dirs = ['../storage', '../storage/logs', '../storage/cache', '../config'];
    foreach ($dirs as $dir) {
        if (!is_writable($dir)) {
            $errors[] = "Directory '{$dir}' is not writable";
        }
    }
    
    return $errors;
}

function testDatabaseConnection($config) {
    $errors = [];
    
    try {
        $pdo = new PDO(
            "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4",
            $config['db_user'],
            $config['db_pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    } catch (Exception $e) {
        $errors[] = 'Database connection failed: ' . $e->getMessage();
    }
    
    return $errors;
}

function saveDatabaseConfig($config) {
    $envContent = "DB_HOST={$config['db_host']}\n";
    $envContent .= "DB_DATABASE={$config['db_name']}\n";
    $envContent .= "DB_USERNAME={$config['db_user']}\n";
    $envContent .= "DB_PASSWORD={$config['db_pass']}\n";
    $envContent .= "AUTO_DELETE_INSTALLER=" . (isset($config['auto_delete']) ? 'true' : 'false') . "\n";
    
    if (file_put_contents(__DIR__ . '/../.env', $envContent)) {
        $_SESSION['db_saved'] = true;
        return [];
    } else {
        return ['Failed to save database configuration'];
    }
}

function createAdminUser($data) {
    $errors = [];
    
    if (empty($data['admin_email']) || !filter_var($data['admin_email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email address is required';
    }
    
    if (empty($data['admin_password']) || strlen($data['admin_password']) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }
    
    if ($data['admin_password'] !== $data['admin_password_confirm']) {
        $errors[] = 'Passwords do not match';
    }
    
    if (empty($errors)) {
        $_SESSION['admin_user'] = $data;
    }
    
    return $errors;
}

function saveSettings($data) {
    $_SESSION['site_settings'] = $data;
    return [];
}

function finishInstallation() {
    try {
        // Create database connection
        $dbConfig = $_SESSION['db_config'] ?? [];
        $pdo = new PDO(
            "mysql:host={$dbConfig['db_host']};dbname={$dbConfig['db_name']};charset=utf8mb4",
            $dbConfig['db_user'],
            $dbConfig['db_pass']
        );
        
        // Create tables (Base Schema)
        createDatabaseTables($pdo);
        
        // Run migrations (Feature Tables & Updates)
        runDatabaseMigrations($pdo);
        
        // Insert admin user
        insertAdminUser($pdo);
        
        // Create install lock file
        file_put_contents(__DIR__ . '/../storage/install.lock', date('Y-m-d H:i:s'));
        
        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function createDatabaseTables($pdo) {
    $sql = file_get_contents(__DIR__ . '/database.sql');
    $pdo->exec($sql);
}

function runDatabaseMigrations($pdo) {
    $migrationsDir = __DIR__ . '/../database/migrations';
    if (!is_dir($migrationsDir)) {
        return;
    }
    
    // Get all migration files (.php and .sql)
    $migrationFiles = glob($migrationsDir . '/*.{php,sql}', GLOB_BRACE);
    sort($migrationFiles);
    
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
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        
        if (!in_array($migrationName, $executed)) {
            try {
                if ($extension === 'sql') {
                    $sql = file_get_contents($file);
                    $pdo->exec($sql);
                } else {
                    // Include the file and check for class structure
                    $content = file_get_contents($file);
                    
                    // Check if it's a class-based migration
                    if (preg_match('/class\s+(\w+).*?\{/', $content, $matches)) {
                        $className = $matches[1];
                        include_once $file;
                        
                        if (class_exists($className)) {
                            $migration = new $className();
                            if (method_exists($migration, 'up')) {
                                $migration->up($pdo);
                            }
                        }
                    } else {
                        // Handle legacy migration format (just execute SQL)
                        eval('?>' . $content);
                    }
                }
                
                // Record migration as executed
                $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, 1)")
                    ->execute([$migrationName]);
                    
            } catch (Exception $e) {
                error_log("Migration error for $migrationName: " . $e->getMessage());
            }
        }
    }
}

function insertAdminUser($pdo) {
    $adminData = $_SESSION['admin_user'];
    
    $stmt = $pdo->prepare("
        INSERT INTO users (username, email, password, first_name, last_name, role, is_active, email_verified, created_at)
        VALUES (?, ?, ?, ?, ?, 'admin', 1, 1, NOW())
    ");
    
    $stmt->execute([
        $adminData['admin_username'] ?? 'admin',
        $adminData['admin_email'],
        password_hash($adminData['admin_password'], PASSWORD_DEFAULT),
        $adminData['admin_first_name'] ?? 'Administrator',
        $adminData['admin_last_name'] ?? 'User'
    ]);
}

function getAutoDeleteSetting() {
    // Check .env file for AUTO_DELETE_INSTALLER setting
    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        $content = file_get_contents($envFile);
        if (preg_match('/AUTO_DELETE_INSTALLER=true/', $content)) {
            return true;
        }
    }
    return false;
}

function deleteInstallFolder() {
    function deleteDirectory($dir) {
        if (!is_dir($dir)) return false;
        
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                deleteDirectory($path);
            } else {
                unlink($path);
            }
        }
        return rmdir($dir);
    }
    
    deleteDirectory(__DIR__);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Engineering Calculator Pro - Installation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --secondary: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --success: #10b981;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .installer-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 900px;
            overflow: hidden;
        }
        
        .installer-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            text-align: center;
            padding: 40px 30px;
        }
        
        .installer-logo {
            font-size: 48px;
            margin-bottom: 16px;
        }
        
        .installer-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .installer-subtitle {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .progress-container {
            background: var(--gray-50);
            padding: 30px;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .progress-steps {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            flex: 1;
        }

        .step-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }
        
        .step.completed .step-circle {
            background: var(--success);
            color: white;
        }
        
        .step.active .step-circle {
            background: var(--primary);
            color: white;
        }
        
        .step.pending .step-circle {
            background: var(--gray-200);
            color: var(--gray-600);
        }
        
        .step-title {
            font-size: 12px;
            font-weight: 500;
            text-align: center;
            color: var(--gray-700);
        }
        
        .step-connector {
            position: absolute;
            top: 24px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: var(--gray-200);
            z-index: -1;
        }
        
        .step.completed .step-connector {
            background: var(--success);
        }
        
        .progress-bar {
            width: 100%;
            height: 6px;
            background: var(--gray-200);
            border-radius: 3px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-radius: 3px;
            transition: width 0.5s ease;
        }
        
        .installer-content {
            padding: 40px;
            min-height: 400px;
        }
        
        .step-content {
            text-align: center;
        }
        
        .step-icon {
            font-size: 64px;
            color: var(--primary);
            margin-bottom: 24px;
        }
        
        .step-heading {
            font-size: 24px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 12px;
        }
        
        .step-description {
            font-size: 16px;
            color: var(--gray-600);
            margin-bottom: 32px;
        }
        
        .form-group {
            margin-bottom: 24px;
            text-align: left;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 8px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 16px;
        }
        
        .btn {
            padding: 12px 32px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.3);
        }
        
        .btn-secondary {
            background: var(--gray-100);
            color: var(--gray-700);
            border: 1px solid var(--gray-200);
        }
        
        .btn-secondary:hover {
            background: var(--gray-200);
        }
        
        .btn-success {
            background: var(--success);
            color: white;
        }
        
        .btn-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        
        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-weight: 500;
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        
        .requirements-list {
            text-align: left;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .requirement-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .requirement-item:last-child {
            border-bottom: none;
        }
        
        .requirement-status {
            font-size: 20px;
        }
        
        .status-pass {
            color: var(--success);
        }
        
        .status-fail {
            color: var(--danger);
        }
        
        .auto-delete-warning {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: #d97706;
            padding: 16px;
            border-radius: 8px;
            margin-top: 16px;
            font-size: 14px;
        }
        
        .completion-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 32px 0;
        }
        
        .stat-card {
            background: var(--gray-50);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 4px;
        }
        
        .stat-label {
            font-size: 14px;
            color: var(--gray-600);
        }
        
        @media (max-width: 768px) {
            .installer-container {
                margin: 10px;
                border-radius: 16px;
            }
            
            .progress-steps {
                flex-wrap: wrap;
                gap: 12px;
            }
            
            .step {
                flex-basis: calc(50% - 6px);
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .btn-actions {
                flex-direction: column;
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="installer-container">
        <!-- Header -->
        <div class="installer-header">
            <div class="installer-logo">
                <i class="fas fa-calculator"></i>
            </div>
            <h1 class="installer-title">Engineering Calculator Pro</h1>
            <p class="installer-subtitle">Professional Engineering Calculator Installation</p>
        </div>
        
        <!-- Progress -->
        <div class="progress-container">
            <div class="progress-steps">
                <?php foreach ($steps as $key => $step): ?>
                <div class="step <?php 
                    if (array_search($key, $stepKeys) < $currentIndex) echo 'completed';
                    elseif ($key === $currentStep) echo 'active';
                    else echo 'pending';
                ?>">
                    <div class="step-circle">
                        <?php if (array_search($key, $stepKeys) < $currentIndex): ?>
                            <i class="fas fa-check"></i>
                        <?php else: ?>
                            <i class="<?php echo $step['icon']; ?>"></i>
                        <?php endif; ?>
                    </div>
                    <div class="step-title"><?php echo $step['title']; ?></div>
                    <?php if ($key !== array_key_last($steps)): ?>
                    <div class="step-connector"></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo (($currentIndex + 1) / count($steps)) * 100; ?>%"></div>
            </div>
        </div>
        
        <!-- Content -->
        <div class="installer-content">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php foreach ($errors as $error): ?>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <?php include __DIR__ . "/steps/{$currentStep}.php"; ?>
        </div>
    </div>
</body>
</html>
