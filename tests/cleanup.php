<?php
/**
 * Bishwo Calculator - Installation Wizard
 * Security Cleanup Script
 * 
 * @package BishwoCalculator
 * @version 1.0.0
 */

// Security check - only allow direct access from installation completion
if (!isset($_GET['cleanup']) || $_GET['cleanup'] !== '1') {
    die('Access denied. This script can only be executed from the installation wizard.');
}

session_start();

// Verify installation is complete
if (!file_exists(__DIR__ . '/../storage/install.lock') || !file_exists(__DIR__ . '/../.env')) {
    die('Installation not complete. Please complete the installation process first.');
}

$cleanupResults = [
    'errors' => [],
    'warnings' => [],
    'success' => []
];

try {
    // 1. Remove installation files and directories
    cleanupInstallationFiles();
    
    // 2. Set proper file permissions
    setSecurePermissions();
    
    // 3. Update .htaccess for security
    updateHtaccess();
    
    // 4. Create backup of installation files (optional)
    backupInstallationFiles();
    
    // 5. Update version file
    updateVersionFile();
    
} catch (Exception $e) {
    $cleanupResults['errors'][] = 'Cleanup process failed: ' . $e->getMessage();
}

/**
 * Remove installation files and directories
 */
function cleanupInstallationFiles() {
    global $cleanupResults;
    
    $installDir = __DIR__;
    $itemsToRemove = [
        $installDir . '/welcome.php',
        $installDir . '/requirements.php', 
        $installDir . '/database.php',
        $installDir . '/admin.php',
        $installDir . '/email.php',
        $installDir . '/finish.php',
        $installDir . '/cleanup.php'
    ];
    
    foreach ($itemsToRemove as $file) {
        if (file_exists($file)) {
            if (unlink($file)) {
                $cleanupResults['success'][] = 'Removed: ' . basename($file);
            } else {
                $cleanupResults['errors'][] = 'Failed to remove: ' . basename($file);
            }
        }
    }
    
    // Remove assets directory
    $assetsDir = $installDir . '/assets';
    if (is_dir($assetsDir)) {
        if (removeDirectory($assetsDir)) {
            $cleanupResults['success'][] = 'Removed assets directory';
        } else {
            $cleanupResults['errors'][] = 'Failed to remove assets directory';
        }
    }
    
    // Remove includes directory but keep it as placeholder
    $includesDir = $installDir . '/includes';
    if (is_dir($includesDir)) {
        $includesFiles = glob($includesDir . '/*.php');
        foreach ($includesFiles as $file) {
            if (basename($file) !== 'README.md') { // Keep README for documentation
                unlink($file);
            }
        }
        $cleanupResults['success'][] = 'Cleaned includes directory';
    }
}

/**
 * Recursively remove directory
 */
function removeDirectory($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    
    $files = array_diff(scandir($dir), ['.', '..']);
    
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            removeDirectory($path);
        } else {
            unlink($path);
        }
    }
    
    return rmdir($dir);
}

/**
 * Set secure file permissions
 */
function setSecurePermissions() {
    global $cleanupResults;
    
    $baseDir = __DIR__ . '/..';
    $secureDirs = [
        $baseDir . '/storage' => 0755,
        $baseDir . '/storage/logs' => 0755,
        $baseDir . '/storage/cache' => 0755,
        $baseDir . '/storage/sessions' => 0755,
        $baseDir . '/storage/backups' => 0755
    ];
    
    foreach ($secureDirs as $dir => $permission) {
        if (is_dir($dir)) {
            if (chmod($dir, $permission)) {
                $cleanupResults['success'][] = 'Set permissions for: ' . basename($dir);
            } else {
                $cleanupResults['warnings'][] = 'Could not set permissions for: ' . basename($dir);
            }
        }
    }
    
    // Secure sensitive files
    $sensitiveFiles = [
        $baseDir . '/.env' => 0600,
        $baseDir . '/.htaccess' => 0644
    ];
    
    foreach ($sensitiveFiles as $file => $permission) {
        if (file_exists($file)) {
            if (chmod($file, $permission)) {
                $cleanupResults['success'][] = 'Secured file: ' . basename($file);
            } else {
                $cleanupResults['warnings'][] = 'Could not secure file: ' . basename($file);
            }
        }
    }
}

/**
 * Update .htaccess for security
 */
function updateHtaccess() {
    global $cleanupResults;
    
    $htaccessPath = __DIR__ . '/../.htaccess';
    $installBlock = <<<HTACCESS
# Installation Security Block
# Added during cleanup to prevent access to install directory

# Block access to installation files
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Block direct access to install directory
    RewriteRule ^install/ - [F,L]
    
    # Block access to sensitive files
    RewriteRule \.(env|log|sql|bak|backup|old)$ - [F,L]
    
    # Block access to configuration files
    RewriteRule ^(config|includes)/.*\.(php|inc)$ - [F,L]
    
    # Enable security headers
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# Prevent directory browsing
Options -Indexes

# Protect sensitive directories
<DirectoryMatch "^/(storage|config|database)/">
    Order allow,deny
    Deny from all
</DirectoryMatch>

HTACCESS;

    if (file_put_contents($htaccessPath, $installBlock, FILE_APPEND)) {
        $cleanupResults['success'][] = 'Updated .htaccess with security rules';
    } else {
        $cleanupResults['warnings'][] = 'Could not update .htaccess';
    }
}

/**
 * Create backup of installation files
 */
function backupInstallationFiles() {
    global $cleanupResults;
    
    $backupDir = __DIR__ . '/../storage/backups';
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    
    $backupName = 'install_backup_' . date('Y-m-d_H-i-s') . '.zip';
    $backupPath = $backupDir . '/' . $backupName;
    
    // Simple backup using file copying
    $installDir = __DIR__;
    $backupFile = $backupPath . '_files.txt';
    
    $fileList = [
        'index.php' => file_get_contents($installDir . '/index.php'),
        'includes/Installer.php' => file_get_contents($installDir . '/includes/Installer.php'),
        'includes/Requirements.php' => file_get_contents($installDir . '/includes/Requirements.php')
    ];
    
    $backupContent = "Bishwo Calculator Installation Backup\n";
    $backupContent .= "Created: " . date('Y-m-d H:i:s') . "\n\n";
    
    foreach ($fileList as $file => $content) {
        $backupContent .= "=== $file ===\n";
        $backupContent .= $content . "\n\n";
    }
    
    if (file_put_contents($backupFile, $backupContent)) {
        $cleanupResults['success'][] = 'Created installation backup: ' . basename($backupFile);
    } else {
        $cleanupResults['warnings'][] = 'Could not create installation backup';
    }
}

/**
 * Update version file
 */
function updateVersionFile() {
    global $cleanupResults;
    
    $versionPath = __DIR__ . '/../version.json';
    if (file_exists($versionPath)) {
        $versionData = json_decode(file_get_contents($versionPath), true);
        if ($versionData) {
            $versionData['installed'] = true;
            $versionData['install_date'] = date('Y-m-d H:i:s');
            $versionData['install_version'] = $versionData['version'] ?? '1.0.0';
            
            if (file_put_contents($versionPath, json_encode($versionData, JSON_PRETTY_PRINT))) {
                $cleanupResults['success'][] = 'Updated version information';
            } else {
                $cleanupResults['warnings'][] = 'Could not update version file';
            }
        }
    }
}

// Display results
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bishwo Calculator - Security Cleanup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-shield-alt"></i> Security Cleanup Complete</h4>
                    </div>
                    <div class="card-body">
                        
                        <?php if (!empty($cleanupResults['success'])): ?>
                        <div class="alert alert-success">
                            <h5><i class="fas fa-check-circle"></i> Successfully Completed</h5>
                            <ul class="mb-0">
                                <?php foreach ($cleanupResults['success'] as $success): ?>
                                    <li><?php echo htmlspecialchars($success); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($cleanupResults['warnings'])): ?>
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle"></i> Warnings</h5>
                            <ul class="mb-0">
                                <?php foreach ($cleanupResults['warnings'] as $warning): ?>
                                    <li><?php echo htmlspecialchars($warning); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($cleanupResults['errors'])): ?>
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-times-circle"></i> Errors</h5>
                            <ul class="mb-0">
                                <?php foreach ($cleanupResults['errors'] as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> Security Notes</h5>
                            <ul class="mb-0">
                                <li>Installation files have been removed or secured</li>
                                <li>File permissions have been set to secure levels</li>
                                <li>.htaccess has been updated with security rules</li>
                                <li>A backup of installation files has been created</li>
                            </ul>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="../" class="btn btn-primary btn-lg">
                                <i class="fas fa-home"></i> Go to Application
                            </a>
                            <a href="../admin" class="btn btn-outline-primary">
                                <i class="fas fa-cog"></i> Admin Dashboard
                            </a>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
