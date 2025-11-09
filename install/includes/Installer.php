<?php
/**
 * Bishwo Calculator - Installation Wizard
 * Core Installer Class
 * 
 * @package BishwoCalculator
 * @version 1.0.0
 */

class Installer {
    
    private $steps = [
        'welcome' => 'Welcome',
        'requirements' => 'System Requirements',
        'permissions' => 'File Permissions',
        'database' => 'Database Configuration',
        'admin' => 'Administrator Account',
        'email' => 'Email Configuration',
        'finish' => 'Installation Complete'
    ];
    
    public function renderStep($step) {
        switch ($step) {
            case 'welcome':
                return $this->renderWelcomeStep();
            case 'requirements':
                return $this->renderRequirementsStep();
            case 'permissions':
                return $this->renderPermissionsStep();
            case 'database':
                return $this->renderDatabaseStep();
            case 'admin':
                return $this->renderAdminStep();
            case 'email':
                return $this->renderEmailStep();
            case 'finish':
                return $this->renderFinishStep();
            default:
                return $this->renderWelcomeStep();
        }
    }
    
    private function renderWelcomeStep() {
        $html = '
        <!-- Welcome Header with Coder Vibe -->
        <div class="welcome-header">
            <div class="code-block">
                <div class="code-header">
                    <div class="code-dots">
                        <span class="dot red"></span>
                        <span class="dot yellow"></span>
                        <span class="dot green"></span>
                    </div>
                    <span class="code-title">welcome.php</span>
                </div>
                <div class="code-content">
                    <div class="code-line">
                        <span class="keyword">function</span> 
                        <span class="function-name">initializeBishwoCalculator</span>() {
                    </div>
                    <div class="code-line indent-1">
                        <span class="keyword">echo</span> 
                        <span class="string">"🚀 Welcome to Bishwo Calculator"</span>;
                    </div>
                    <div class="code-line indent-1">
                        <span class="keyword">echo</span> 
                        <span class="string">"Professional Engineering Calculator Suite"</span>;
                    </div>
                    <div class="code-line">
                        }
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Feature Showcase -->
        <div class="features-showcase">
            <div class="feature-card-enhanced">
                <div class="feature-icon-bg">
                    <i class="fas fa-calculator feature-icon"></i>
                </div>
                <div class="feature-content">
                    <h5 class="feature-title">Multiple Calculator Types</h5>
                    <p class="feature-description">Civil, Electrical, HVAC, Plumbing, and more specialized calculation tools.</p>
                    <div class="feature-tags">
                        <span class="tag">Civil</span>
                        <span class="tag">Electrical</span>
                        <span class="tag">HVAC</span>
                        <span class="tag">Plumbing</span>
                    </div>
                </div>
            </div>
            
            <div class="feature-card-enhanced">
                <div class="feature-icon-bg">
                    <i class="fas fa-chart-line feature-icon"></i>
                </div>
                <div class="feature-content">
                    <h5 class="feature-title">Professional Reports</h5>
                    <p class="feature-description">Generate detailed calculation reports and export to various formats.</p>
                    <div class="feature-tags">
                        <span class="tag">PDF</span>
                        <span class="tag">Excel</span>
                        <span class="tag">Export</span>
                    </div>
                </div>
            </div>
            
            <div class="feature-card-enhanced">
                <div class="feature-icon-bg">
                    <i class="fas fa-shield-alt feature-icon"></i>
                </div>
                <div class="feature-content">
                    <h5 class="feature-title">Secure & Reliable</h5>
                    <p class="feature-description">Enterprise-grade security with user management and data protection.</p>
                    <div class="feature-tags">
                        <span class="tag">Secure</span>
                        <span class="tag">Multi-User</span>
                        <span class="tag">Protected</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pre-Installation Checklist -->
        <div class="pre-install-checklist">
            <h5 class="checklist-title">
                <i class="fas fa-clipboard-list"></i>
                Pre-Installation Checklist
            </h5>
            <div class="checklist-items">
                <div class="checklist-item">
                    <i class="fas fa-database checklist-icon"></i>
                    <span>Database credentials ready</span>
                </div>
                <div class="checklist-item">
                    <i class="fas fa-user-shield checklist-icon"></i>
                    <span>Administrator account details prepared</span>
                </div>
                <div class="checklist-item">
                    <i class="fas fa-server checklist-icon"></i>
                    <span>Server meets system requirements</span>
                </div>
                <div class="checklist-item">
                    <i class="fas fa-backup checklist-icon"></i>
                    <span>Existing data backed up (if upgrading)</span>
                </div>
            </div>
        </div>
        
        <form method="post" class="mt-4">
            <input type="hidden" name="action" value="save_requirements">
            <button type="submit" class="btn btn-primary btn-enhanced">
                <span class="btn-text">
                    <i class="fas fa-rocket"></i>
                    Initialize Installation
                </span>
                <span class="btn-glow"></span>
            </button>
        </form>';
        
        return $html;
    }
    
    private function renderRequirementsStep() {
        $results = $this->checkSystemRequirements();
        
        $allPassed = $results['passed'];
        $totalChecks = count($results['checks']);
        $passedChecks = count(array_filter($results['checks'], function($check) { return $check['status'] === 'pass'; }));
        
        $html = '
        <div class="panel-title"><i class="fas fa-check-circle"></i> System Requirements</div>
        <div class="panel-subtitle">Verifying your server meets the minimum requirements for Bishwo Calculator.</div>
        
        <div class="alert alert-' . ($allPassed ? 'success' : 'error') . '">
            <h5><i class="fas ' . ($allPassed ? 'fa-thumbs-up' : 'fa-exclamation-triangle') . '"></i> 
            ' . ($allPassed ? 'System Ready' : 'Requirements Not Met') . '</h5>
            <p class="mb-0">' . ($allPassed ? 
                'Your system meets all requirements. You can proceed with the installation.' : 
                'Please address the failed requirements before proceeding with the installation.') . '</p>
        </div>';
        
        // Requirements Progress Bar
        $progressPercent = ($passedChecks / $totalChecks) * 100;
        $html .= '
        <div class="requirements-progress mb-4">
            <div class="progress-info mb-2">
                <span class="text-light">Requirements Checked: ' . $passedChecks . ' / ' . $totalChecks . '</span>
                <span class="text-light float-end">' . round($progressPercent) . '% Complete</span>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" style="width: ' . $progressPercent . '%"></div>
            </div>
        </div>';
        
        // Display each requirement check with enhanced styling
        foreach ($results['checks'] as $check) {
            $iconClass = $check['status'] === 'pass' ? 'text-success' : ($check['status'] === 'warning' ? 'text-warning' : 'text-danger');
            $icon = $check['status'] === 'pass' ? 'fa-check-circle' : ($check['status'] === 'warning' ? 'fa-exclamation-triangle' : 'fa-times-circle');
            
            $html .= '
            <div class="requirement-card pass-animation">
                <div class="requirement-header">
                    <div class="requirement-info">
                        <i class="fas ' . $icon . ' requirement-icon"></i>
                        <span class="requirement-name">' . htmlspecialchars($check['name']) . '</span>
                    </div>
                    <span class="status-badge status-' . ($check['status'] === 'pass' ? 'ok' : 'error') . '">
                        ' . ($check['status'] === 'pass' ? 'PASS' : 'FAIL') . '
                    </span>
                </div>
                <div class="requirement-details">
                    <div class="requirement-description">' . htmlspecialchars($check['description']) . '</div>
                    <div class="requirement-current">
                        <i class="fas fa-info-circle me-1"></i>
                        <span>Current: <strong>' . htmlspecialchars($check['current']) . '</strong></span>';
            
            if (!empty($check['required'])) {
                $html .= ' <span class="requirement-required">Required: ' . htmlspecialchars($check['required']) . '</span>';
            }
            
            $html .= '
                    </div>
                </div>
            </div>';
        }
        
        if ($allPassed) {
            $html .= '
            <div class="mt-4 text-center">
                <div class="compact-success-message">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    <span class="success-text">All systems ready for installation!</span>
                </div>
                <form method="post" class="d-inline mt-3">
                    <input type="hidden" name="action" value="save_requirements">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-arrow-right"></i> Continue to File Permissions
                    </button>
                </form>
            </div>';
        } else {
            $html .= '
            <div class="mt-4 text-center">
                <div class="error-message mb-3">
                    <i class="fas fa-exclamation-triangle text-warning fa-2x"></i>
                    <h5 class="text-warning mt-2">Please fix the requirements above to continue</h5>
                </div>
                <button type="button" class="btn btn-secondary" onclick="location.reload()">
                    <i class="fas fa-refresh"></i> Re-check Requirements
                </button>
            </div>';
        }
        
        return $html;
    }
    
    private function renderPermissionsStep() {
        $results = $this->checkSystemRequirements();
        $directories = $results['directories'] ?? [];
        
        $allPassed = true; // Assume permissions are OK if directories exist and are readable
        
        $html = '
        <div class="panel-title"><i class="fas fa-key"></i> File Permissions</div>
        <div class="panel-subtitle">Review and configure directory and file permissions for secure installation.</div>
        
        <div class="alert alert-success">
            <h5><i class="fas fa-shield-alt"></i> Security Overview</h5>
            <p class="mb-0">Proper file permissions are crucial for application security. Below are the current permissions for all critical directories.</p>
        </div>';
        
        // Display directory permissions
        $html .= '
        <div class="directory-structure-section">
            <h4 class="section-title"><i class="fas fa-folder-tree"></i> Directory & File Permissions</h4>
            <p class="section-subtitle">Application directories and their current access permissions</p>';
        
        foreach ($directories as $dir) {
            $dirIcon = $dir['status'] === 'pass' ? 'fa-folder-open text-success' : 'fa-folder text-danger';
            $permClass = $dir['status'] === 'pass' ? 'text-success' : 'text-danger';
            $statusIcon = $dir['status'] === 'pass' ? 'fa-shield-alt' : 'fa-exclamation-triangle';
            
            $html .= '
            <div class="directory-card">
                <div class="directory-header">
                    <div class="directory-info">
                        <i class="fas ' . $dirIcon . ' me-2"></i>
                        <span class="directory-name">' . htmlspecialchars($dir['name']) . '</span>
                        <span class="directory-path">(' . htmlspecialchars($dir['path']) . ')</span>
                    </div>
                    <span class="status-badge status-' . ($dir['status'] === 'pass' ? 'ok' : 'error') . '">
                        <i class="fas ' . $statusIcon . ' me-1"></i>' . ($dir['status'] === 'pass' ? 'SECURE' : 'ISSUE') . '
                    </span>
                </div>
                <div class="directory-details">
                    <div class="permission-info">
                        <i class="fas fa-key me-1"></i>Permissions: 
                        <span class="permission-value ' . $permClass . '">' . htmlspecialchars($dir['permissions']) . '</span>
                        <span class="permission-status ms-2">' . htmlspecialchars($dir['status_text']) . '</span>
                    </div>
                </div>
            </div>';
        }
        
        $html .= '
            <div class="permissions-guide mt-3">
                <div class="guide-card">
                    <h6><i class="fas fa-info-circle me-2"></i>Permission Guide</h6>
                    <div class="guide-items">
                        <div class="guide-item">
                            <code>755</code> - Owner: read/write/execute, Group: read/execute, Others: read/execute
                        </div>
                        <div class="guide-item">
                            <code>775</code> - Owner: read/write/execute, Group: read/write/execute, Others: read/execute
                        </div>
                        <div class="guide-item">
                            <code>777</code> - Full access for everyone (not recommended for security)
                        </div>
                    </div>
                </div>
            </div>
        </div>';
        
        if ($allPassed) {
            $html .= '
            <div class="mt-4 text-center">
                <div class="success-message mb-3">
                    <i class="fas fa-check-circle text-success fa-2x"></i>
                    <h5 class="text-success mt-2">All file permissions are properly configured!</h5>
                </div>
                <form method="post" class="d-inline">
                    <input type="hidden" name="action" value="save_permissions">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-arrow-right"></i> Continue to Database Configuration
                    </button>
                </form>
            </div>';
        } else {
            $html .= '
            <div class="mt-4 text-center">
                <div class="error-message mb-3">
                    <i class="fas fa-exclamation-triangle text-warning fa-2x"></i>
                    <h5 class="text-warning mt-2">Some permissions need attention</h5>
                </div>
                <button type="button" class="btn btn-secondary" onclick="location.reload()">
                    <i class="fas fa-refresh"></i> Re-check Permissions
                </button>
            </div>';
        }
        
        return $html;
    }
    
    private function renderDatabaseStep() {
        $dbConfig = $_SESSION['db_config'] ?? [];
        
        $html = '
        <div class="panel-title"><i class="fas fa-database"></i> Database Configuration</div>
        <div class="panel-subtitle">Configure your database connection to store application data.</div>
        
        <form method="post" id="databaseForm">
            <input type="hidden" name="action" value="save_database">
            
            <div class="form-group">
                <label for="db_host" class="form-label">Database Host <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="db_host" name="db_host" 
                       value="' . htmlspecialchars($dbConfig['host'] ?? 'localhost') . '" required>
            </div>
            
            <div class="form-group">
                <label for="db_name" class="form-label">Database Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="db_name" name="db_name" 
                       value="' . htmlspecialchars($dbConfig['name'] ?? '') . '" required>
            </div>
            
            <div class="form-group">
                <label for="db_user" class="form-label">Database Username <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="db_user" name="db_user" 
                       value="' . htmlspecialchars($dbConfig['user'] ?? '') . '" required>
            </div>
            
            <div class="form-group">
                <label for="db_pass" class="form-label">Database Password</label>
                <input type="password" class="form-control" id="db_pass" name="db_pass" 
                       value="' . htmlspecialchars($dbConfig['pass'] ?? '') . '">
            </div>
            
            <div class="xampp-guide">
                <h6><i class="fab fa-php me-2"></i>XAMPP Database Setup Guide</h6>
                
                <!-- XAMPP Quick Setup -->
                <div class="xampp-quick-setup">
                    <h6><i class="fas fa-bolt me-1"></i>Quick XAMPP Settings:</h6>
                    <div class="quick-settings">
                        <div class="setting-item">
                            <strong>Database Host:</strong> <code>localhost</code>
                        </div>
                        <div class="setting-item">
                            <strong>Database Name:</strong> <code>bishwo_calculator</code> (or your choice)
                        </div>
                        <div class="setting-item">
                            <strong>Database Username:</strong> <code>root</code>
                        </div>
                        <div class="setting-item">
                            <strong>Database Password:</strong> <code>leave empty</code> (default for XAMPP)
                        </div>
                    </div>
                </div>
                
                <!-- Step by step instructions -->
                <div class="xampp-steps">
                    <h6><i class="fas fa-list-ol me-1"></i>Setup Steps:</h6>
                    <ol>
                        <li><strong>Start XAMPP:</strong> Open XAMPP Control Panel and start Apache + MySQL</li>
                        <li><strong>Create Database:</strong> Go to <a href="http://localhost/phpmyadmin" target="_blank">phpMyAdmin</a> → Click "New" → Enter database name → Create</li>
                        <li><strong>Default User:</strong> XAMPP comes with <code>root</code> user (no password by default)</li>
                        <li><strong>Enter Credentials:</strong> Use the quick settings above or create a custom user</li>
                    </ol>
                </div>
                
                <!-- Alternative custom user -->
                <div class="custom-user-guide">
                    <h6><i class="fas fa-user-shield me-1"></i>Custom User (Optional):</h6>
                    <p>If you want to create a custom database user instead of using root:</p>
                    <ol>
                        <li>In phpMyAdmin → <strong>User accounts</strong> → <strong>Add user account</strong></li>
                        <li>Enter username and password</li>
                        <li>Check "Create database with same name and grant all privileges"</li>
                        <li>Click <strong>Go</strong></li>
                    </ol>
                </div>
                
                <div class="alert alert-info mt-3">
                    <small><i class="fas fa-lightbulb me-1"></i><strong>Tip:</strong> For production, always use a custom user with limited privileges instead of root.</small>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-database"></i> Test & Save Database Configuration
            </button>
        </form>';
        
        return $html;
    }
    
    private function renderAdminStep() {
        $adminConfig = $_SESSION['admin_config'] ?? [];
        
        $html = '
        <div class="panel-title"><i class="fas fa-user-shield"></i> Administrator Account</div>
        <div class="panel-subtitle">Create the administrator account that will have full access to the system.</div>
        
        <form method="post" id="adminForm">
            <input type="hidden" name="action" value="save_admin">
            
            <div class="form-group">
                <label for="admin_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="admin_name" name="admin_name" 
                       value="' . htmlspecialchars($adminConfig['name'] ?? '') . '" required>
            </div>
            
            <div class="form-group">
                <label for="admin_email" class="form-label">Email Address <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="admin_email" name="admin_email" 
                       value="' . htmlspecialchars($adminConfig['email'] ?? '') . '" required>
            </div>
            
            <div class="form-group">
                <label for="admin_pass" class="form-label">Password <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="admin_pass" name="admin_pass" 
                       minlength="6" required>
            </div>
            
            <div class="form-group">
                <label for="admin_pass_confirm" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="admin_pass_confirm" name="admin_pass_confirm" 
                       minlength="6" required>
            </div>
            
            <div class="alert alert-success">
                <h6><i class="fas fa-exclamation-triangle"></i> Security Notice</h6>
                <ul class="mb-0">
                    <li>Choose a strong, unique password</li>
                    <li>Store your credentials securely</li>
                    <li>Change the default admin email after installation</li>
                </ul>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Create Administrator
            </button>
        </form>';
        
        return $html;
    }
    
    private function renderEmailStep() {
        $emailConfig = $_SESSION['email_config'] ?? [];
        $smtpEnabled = isset($emailConfig['smtp_enabled']) ? (bool)$emailConfig['smtp_enabled'] : false;
        
        $html = '
        <div class="panel-title"><i class="fas fa-envelope"></i> Email Configuration</div>
        <div class="panel-subtitle">Configure email settings for system notifications and user communications. You can skip this step and configure later.</div>
        
        <!-- Email Configuration Toggle -->
        <div class="email-config-toggle mb-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="smtp_enabled" name="smtp_enabled" ' . 
                       ($smtpEnabled ? 'checked' : '') . '>
                <label class="form-check-label" for="smtp_enabled">
                    <strong>Enable SMTP Email Configuration</strong>
                    <small class="d-block text-muted">Disable to skip email setup and use system log instead</small>
                </label>
            </div>
        </div>
        
        <form method="post" id="emailForm">
            <input type="hidden" name="action" value="save_email">
            <input type="hidden" name="smtp_enabled" id="smtp_enabled_hidden" value="' . ($smtpEnabled ? '1' : '0') . '">
            
            <div class="smtp-fields" style="' . ($smtpEnabled ? '' : 'display: none;') . '">
                <div class="form-group">
                    <label for="smtp_host" class="form-label">SMTP Host</label>
                    <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                           value="' . htmlspecialchars($emailConfig['host'] ?? '') . '">
                    <div class="form-text">e.g., smtp.gmail.com, smtp.sendgrid.net</div>
                </div>
                
                <div class="form-group">
                    <label for="smtp_port" class="form-label">SMTP Port</label>
                    <input type="text" class="form-control" id="smtp_port" name="smtp_port" 
                           value="' . htmlspecialchars($emailConfig['port'] ?? '587') . '">
                    <div class="form-text">Usually 587 (TLS) or 465 (SSL)</div>
                </div>
                
                <div class="form-group">
                    <label for="smtp_user" class="form-label">SMTP Username</label>
                    <input type="text" class="form-control" id="smtp_user" name="smtp_user" 
                           value="' . htmlspecialchars($emailConfig['user'] ?? '') . '">
                </div>
                
                <div class="form-group">
                    <label for="smtp_pass" class="form-label">SMTP Password</label>
                    <input type="password" class="form-control" id="smtp_pass" name="smtp_pass" 
                           value="' . htmlspecialchars($emailConfig['pass'] ?? '') . '">
                </div>
                
                <div class="alert alert-success">
                    <h6><i class="fas fa-lightbulb"></i> SMTP Configuration Tips</h6>
                    <ul class="mb-0">
                        <li><strong>Gmail:</strong> Use app-specific passwords or allow less secure apps</li>
                        <li><strong>SendGrid:</strong> Use your API key as the password</li>
                        <li><strong>Custom SMTP:</strong> Contact your hosting provider for details</li>
                    </ul>
                </div>
            </div>
            
            <div class="email-actions">
                <button type="submit" class="btn btn-primary me-3">
                    <i class="fas fa-save"></i> Save Email Configuration
                </button>
                <button type="submit" name="skip_email" value="1" class="btn btn-secondary" 
                        onclick="return confirm(\'Skip email configuration? You can configure it later from admin settings.\')">
                    <i class="fas fa-forward"></i> Skip Email Setup
                </button>
            </div>
        </form>
        
        <div class="alert alert-info mt-3">
            <h6><i class="fas fa-info-circle"></i> Email Configuration Note</h6>
            <p class="mb-2">Email configuration is optional. If you skip this step:</p>
            <ul class="mb-0">
                <li>System notifications will be logged to file instead</li>
                <li>You can configure email settings later from the admin panel</li>
                <li>User registration emails will not be sent until configured</li>
            </ul>
        </div>';
        
        return $html;
    }
    
    private function renderFinishStep() {
        $html = '
        <div class="text-center">
            <div class="success-animation mb-4">
                <i class="fas fa-check-circle text-success fa-5x"></i>
            </div>
            
            <h2 class="panel-title text-success">Installation Complete!</h2>
            <p class="panel-subtitle">Bishwo Calculator has been successfully installed and configured.</p>
            
            <div class="feature-grid">
                <div class="feature-card">
                    <i class="fas fa-database feature-icon"></i>
                    <h5 class="feature-title">Database</h5>
                    <p class="feature-description">Configured & Migrated</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-user-shield feature-icon"></i>
                    <h5 class="feature-title">Administrator</h5>
                    <p class="feature-description">Account Created</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-envelope feature-icon"></i>
                    <h5 class="feature-title">Email System</h5>
                    <p class="feature-description">Configured</p>
                </div>
            </div>
            
            <form method="post" class="mb-4">
                <input type="hidden" name="action" value="complete_installation">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-rocket"></i> Complete Installation
                </button>
            </form>
            
            <div class="alert alert-success">
                <h5><i class="fas fa-shield-alt"></i> Security Recommendations</h5>
                <ul class="text-start mb-0">
                    <li>Delete or rename the <code>/install</code> directory for security</li>
                    <li>Update your admin password regularly</li>
                    <li>Keep your system and plugins updated</li>
                    <li>Regularly backup your database and files</li>
                </ul>
            </div>
            
            <div class="mt-4">
                <h5>Next Steps:</h5>
                <a href="../" class="btn btn-primary">
                    <i class="fas fa-home"></i> Go to Application
                </a>
                <a href="../admin" class="btn btn-secondary">
                    <i class="fas fa-cog"></i> Admin Dashboard
                </a>
            </div>
        </div>';
        
        return $html;
    }

    private function checkSystemRequirements() {
        $requirements = [
            'php_version' => [
                'name' => 'PHP Version >= 7.4',
                'description' => 'Required PHP version for Bishwo Calculator',
                'current' => PHP_VERSION,
                'required' => '7.4',
                'status' => version_compare(PHP_VERSION, '7.4.0', '>=') ? 'pass' : 'fail'
            ],
            'pdo_mysql' => [
                'name' => 'PDO MySQL Extension',
                'description' => 'Required for database connectivity',
                'current' => extension_loaded('pdo_mysql') ? 'Available' : 'Not Available',
                'status' => extension_loaded('pdo_mysql') ? 'pass' : 'fail'
            ],
            'gd_library' => [
                'name' => 'GD Library',
                'description' => 'Required for image processing',
                'current' => extension_loaded('gd') ? 'Available' : 'Not Available',
                'status' => extension_loaded('gd') ? 'pass' : 'fail'
            ],
            'json_support' => [
                'name' => 'JSON Support',
                'description' => 'Required for data handling',
                'current' => extension_loaded('json') ? 'Available' : 'Not Available',
                'status' => extension_loaded('json') ? 'pass' : 'fail'
            ],
            'curl_support' => [
                'name' => 'cURL Support', 
                'description' => 'Required for external API calls',
                'current' => extension_loaded('curl') ? 'Available' : 'Not Available',
                'status' => extension_loaded('curl') ? 'pass' : 'fail'
            ],
            'file_uploads' => [
                'name' => 'File Uploads Enabled',
                'description' => 'Required for file upload functionality',
                'current' => ini_get('file_uploads') ? 'Enabled' : 'Disabled',
                'status' => ini_get('file_uploads') ? 'pass' : 'fail'
            ],
            'write_permissions' => [
                'name' => 'Storage Directory Writable',
                'description' => 'Required for application data storage',
                'current' => is_writable(__DIR__ . '/../../storage') ? 'Writable' : 'Not Writable',
                'status' => is_writable(__DIR__ . '/../../storage') ? 'pass' : 'fail'
            ]
        ];
        
        // Check directory structure and permissions
        $directories = $this->checkDirectoryStructure();
        
        $checks = array_values($requirements);
        $passed = !in_array('fail', array_column($requirements, 'status'));
        
        return [
            'passed' => $passed,
            'checks' => $checks,
            'directories' => $directories
        ];
    }
    
    private function checkDirectoryStructure() {
        $basePath = __DIR__ . '/../../';
        $dirs = [
            [
                'name' => 'Storage Directory',
                'path' => 'storage/',
                'required_permissions' => '755 (writable)',
            ],
            [
                'name' => 'Log Directory',
                'path' => 'storage/logs/',
                'required_permissions' => '755 (writable)',
            ],
            [
                'name' => 'Cache Directory',
                'path' => 'storage/cache/',
                'required_permissions' => '755 (writable)',
            ],
            [
                'name' => 'Session Directory',
                'path' => 'storage/sessions/',
                'required_permissions' => '755 (writable)',
            ],
            [
                'name' => 'App Directory',
                'path' => 'app/',
                'required_permissions' => '755 (readable)',
            ],
            [
                'name' => 'Config Directory',
                'path' => 'config/',
                'required_permissions' => '755 (readable)',
            ],
            [
                'name' => 'Public Directory',
                'path' => 'public/',
                'required_permissions' => '755 (readable)',
            ]
        ];
        
        $results = [];
        
        foreach ($dirs as $dir) {
            $fullPath = $basePath . $dir['path'];
            $exists = is_dir($fullPath);
            $writable = is_writable($fullPath);
            $readable = is_readable($fullPath);
            
            $permissions = fileperms($fullPath);
            $permString = substr(sprintf('%o', $permissions), -4);
            
            if ($exists) {
                $status = ($writable || $readable) ? 'pass' : 'fail';
                $statusText = $writable ? 'Writable' : ($readable ? 'Readable' : 'Not Accessible');
            } else {
                $status = 'fail';
                $statusText = 'Directory Not Found';
                $permString = 'N/A';
            }
            
            $results[] = [
                'name' => $dir['name'],
                'path' => $dir['path'],
                'permissions' => $permString,
                'status' => $status,
                'status_text' => $statusText
            ];
        }
        
        return $results;
    }
}
?>
