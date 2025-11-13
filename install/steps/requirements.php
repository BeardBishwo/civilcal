<?php
$requirements = [
    'PHP Version ≥ 7.4' => version_compare(PHP_VERSION, '7.4.0', '>='),
    'PDO Extension' => extension_loaded('pdo'),
    'PDO MySQL' => extension_loaded('pdo_mysql'),
    'Mbstring Extension' => extension_loaded('mbstring'),
    'cURL Extension' => extension_loaded('curl'),
    'OpenSSL Extension' => extension_loaded('openssl'),
    'GD Extension' => extension_loaded('gd'),
    'ZIP Extension' => extension_loaded('zip'),
];

$permissions = [
    'storage/ directory' => is_writable('../storage'),
    'storage/logs/ directory' => is_writable('../storage/logs'),
    'storage/cache/ directory' => is_writable('../storage/cache'),
    'config/ directory' => is_writable('../config'),
    '.env file' => is_writable('../.env') || !file_exists('../.env'),
];

$allPassed = true;
foreach (array_merge($requirements, $permissions) as $check) {
    if (!$check) {
        $allPassed = false;
        break;
    }
}
?>

<div class="step-content">
    <div class="step-icon">
        <i class="fas fa-server"></i>
    </div>
    <h2 class="step-heading">System Requirements</h2>
    <p class="step-description">
        Checking if your server meets the minimum requirements for Bishwo Calculator.
    </p>
    
    <div class="requirements-list">
        <h3 style="margin-bottom: 16px;">PHP Extensions & Version</h3>
        <?php foreach ($requirements as $name => $passed): ?>
        <div class="requirement-item">
            <span><?php echo $name; ?></span>
            <span class="requirement-status <?php echo $passed ? 'status-pass' : 'status-fail'; ?>">
                <i class="fas <?php echo $passed ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
            </span>
        </div>
        <?php endforeach; ?>
        
        <h3 style="margin: 24px 0 16px;">File Permissions</h3>
        <?php foreach ($permissions as $name => $passed): ?>
        <div class="requirement-item">
            <span><?php echo $name; ?></span>
            <span class="requirement-status <?php echo $passed ? 'status-pass' : 'status-fail'; ?>">
                <i class="fas <?php echo $passed ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
            </span>
        </div>
        <?php endforeach; ?>
    </div>
    
    <?php if (!$allPassed): ?>
    <div class="alert alert-error" style="margin-top: 24px;">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Requirements Not Met:</strong> Please fix the failed requirements before continuing.
        Contact your hosting provider if you need help with server configuration.
    </div>
    <?php endif; ?>
    
    <div class="btn-actions">
        <a href="?step=welcome" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back
        </a>
        
        <?php if ($allPassed): ?>
        <a href="?step=database" class="btn btn-primary">
            <i class="fas fa-arrow-right"></i>
            Continue
        </a>
        <?php else: ?>
        <button onclick="window.location.reload()" class="btn btn-primary">
            <i class="fas fa-sync-alt"></i>
            Re-check Requirements
        </button>
        <?php endif; ?>
    </div>
</div>
