<?php
/**
 * Bishwo Calculator - Installation Debugger
 * 
 * This script helps debug installation issues and provides status checks
 */

// Define base path FIRST
define('BASE_PATH', __DIR__);

// Start output buffering to catch any errors
ob_start();

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Bishwo Calculator - Installation Debug</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css' rel='stylesheet'>
</head>
<body class='bg-light'>

<div class='container mt-5'>
    <div class='row justify-content-center'>
        <div class='col-md-10'>
            <div class='card shadow'>
                <div class='card-header bg-primary text-white'>
                    <h3><i class='fas fa-tools'></i> Bishwo Calculator Installation Debug</h3>
                </div>
                <div class='card-body'>

";

try {
    // 1. Check PHP version
    echo "<h5><i class='fas fa-check-circle text-success'></i> System Check</h5>";
    echo "<ul class='list-group mb-4'>";
    
    $phpVersion = PHP_VERSION;
    echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
    echo "PHP Version: <span class='badge bg-info'>$phpVersion</span>";
    if (version_compare($phpVersion, '7.4.0', '>=')) {
        echo " <span class='badge bg-success'>✓ Compatible</span>";
    } else {
        echo " <span class='badge bg-danger'>✗ Requires PHP 7.4+</span>";
    }
    echo "</li>";
    
    // 2. Check required extensions
    $extensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'fileinfo'];
    foreach ($extensions as $ext) {
        $loaded = extension_loaded($ext);
        echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
        echo "PHP Extension '$ext': <span class='badge bg-info'>$ext</span>";
        if ($loaded) {
            echo " <span class='badge bg-success'>✓ Loaded</span>";
        } else {
            echo " <span class='badge bg-danger'>✗ Missing</span>";
        }
        echo "</li>";
    }
    echo "</ul>";
    
    // 3. Check file permissions
    echo "<h5><i class='fas fa-folder text-primary'></i> File System Check</h5>";
    echo "<ul class='list-group mb-4'>";
    
    $dirsToCheck = [
        BASE_PATH . '/storage' => 'Storage Directory',
        BASE_PATH . '/storage/logs' => 'Logs Directory',
        BASE_PATH . '/storage/cache' => 'Cache Directory',
        BASE_PATH . '/storage/sessions' => 'Sessions Directory',
        BASE_PATH . '/.env' => 'Environment File'
    ];
    
    foreach ($dirsToCheck as $path => $name) {
        echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
        echo "$name: <span class='text-muted'>$path</span>";
        if (file_exists($path)) {
            if (is_writable($path) || is_dir($path)) {
                echo " <span class='badge bg-success'>✓ Exists" . (is_writable($path) ? " & Writable" : "") . "</span>";
            } else {
                echo " <span class='badge bg-warning'>⚠ Exists but not writable</span>";
            }
        } else {
            echo " <span class='badge bg-danger'>✗ Missing</span>";
        }
        echo "</li>";
    }
    echo "</ul>";
    
    // 4. Check current installation status
    echo "<h5><i class='fas fa-install text-info'></i> Installation Status</h5>";
    echo "<ul class='list-group mb-4'>";
    
    $installLock = BASE_PATH . '/storage/install.lock';
    $envFile = BASE_PATH . '/.env';
    
    echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
    echo "Installation Lock File:";
    if (file_exists($installLock)) {
        echo " <span class='badge bg-success'>✓ Installation Completed</span>";
        $lockTime = file_get_contents($installLock);
        echo " <small class='text-muted'>($lockTime)</small>";
    } else {
        echo " <span class='badge bg-warning'>⚠ Not Installed</span>";
    }
    echo "</li>";
    
    echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
    echo "Environment File:";
    if (file_exists($envFile)) {
        echo " <span class='badge bg-success'>✓ .env file exists</span>";
        // Show first few lines of .env (without sensitive data)
        $envContent = file_get_contents($envFile);
        $lines = explode("\n", $envContent);
        $safeLines = array_filter($lines, function($line) {
            return !empty($line) && !strpos($line, 'PASSWORD') && !strpos($line, 'SECRET');
        });
        echo " <small class='text-muted'>" . count($safeLines) . " configuration lines</small>";
    } else {
        echo " <span class='badge bg-danger'>✗ .env file missing</span>";
    }
    echo "</li>";
    echo "</ul>";
    
    // 5. Database connection test (if .env exists)
    if (file_exists($envFile)) {
        echo "<h5><i class='fas fa-database text-primary'></i> Database Test</h5>";
        echo "<div class='alert ";
        
        try {
            $envVars = parse_ini_file($envFile);
            if ($envVars && isset($envVars['DB_HOST'], $envVars['DB_DATABASE'], $envVars['DB_USERNAME'])) {
                $dsn = "mysql:host={$envVars['DB_HOST']};dbname={$envVars['DB_DATABASE']}";
                $pdo = new PDO($dsn, $envVars['DB_USERNAME'], $envVars['DB_PASSWORD'] ?? '');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Test a simple query
                $stmt = $pdo->query("SELECT 1 as test");
                $result = $stmt->fetch();
                
                if ($result['test'] == 1) {
                    echo "alert-success'>✓ Database connection successful!";
                    echo "<br><small>Connected to: {$envVars['DB_DATABASE']}@{$envVars['DB_HOST']}</small>";
                } else {
                    echo "alert-warning'>⚠ Database query test failed";
                }
            } else {
                echo "alert-warning'>⚠ Database configuration incomplete in .env file";
            }
        } catch (Exception $e) {
            echo "alert-danger'>✗ Database connection failed: " . htmlspecialchars($e->getMessage());
        }
        echo "</div>";
    }
    
    // 6. Provide next steps
    echo "<h5><i class='fas fa-play text-success'></i> Next Steps</h5>";
    echo "<div class='d-grid gap-2'>";
    
    if (!file_exists($installLock)) {
        echo "<a href='install/index.php' class='btn btn-primary btn-lg'>";
        echo "<i class='fas fa-play'></i> Start Installation Wizard";
        echo "</a>";
        echo "<p class='text-muted mt-2'>The installation wizard will guide you through setting up the database and application.</p>";
    } else {
        echo "<a href='public/index.php' class='btn btn-success btn-lg'>";
        echo "<i class='fas fa-home'></i> Go to Application";
        echo "</a>";
        echo "<a href='public/index.php?url=admin' class='btn btn-info btn-lg'>";
        echo "<i class='fas fa-cog'></i> Admin Dashboard";
        echo "</a>";
        echo "<p class='text-muted mt-2'>Application is installed! You can access the main application and admin dashboard.</p>";
    }
    
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='alert alert-danger'>";
    echo "<h5><i class='fas fa-exclamation-triangle'></i> Debug Error</h5>";
    echo "<p>An error occurred while running the debug script:</p>";
    echo "<code>" . htmlspecialchars($e->getMessage()) . "</code>";
    echo "</div>";
}

// Clear output buffer
$content = ob_get_clean();
echo $content;

?>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
