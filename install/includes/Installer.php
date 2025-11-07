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
        <div class="install-welcome">
            <div class="card">
                <div class="card-body text-center">
                    <h2 class="mb-4"><i class="fas fa-rocket text-primary"></i> Welcome to Bishwo Calculator</h2>
                    <p class="lead mb-4">You are about to install a comprehensive professional calculator suite designed for engineering, construction, and technical professionals.</p>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="feature-card">
                                <i class="fas fa-calculator fa-2x text-primary mb-3"></i>
                                <h5>Multiple Calculator Types</h5>
                                <p class="text-muted">Civil, Electrical, HVAC, Plumbing, and more specialized calculation tools.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-card">
                                <i class="fas fa-chart-line fa-2x text-success mb-3"></i>
                                <h5>Professional Reports</h5>
                                <p class="text-muted">Generate detailed calculation reports and export to various formats.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-card">
                                <i class="fas fa-shield-alt fa-2x text-info mb-3"></i>
                                <h5>Secure & Reliable</h5>
                                <p class="text-muted">Enterprise-grade security with user management and data protection.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> Before You Begin</h5>
                        <ul class="text-start mb-0">
                            <li>Ensure you have database credentials ready</li>
                            <li>Have administrator account details prepared</li>
                            <li>Make sure your server meets the system requirements</li>
                            <li>Backup any existing data if upgrading</li>
                        </ul>
                    </div>
                    
                    <form method="post" class="mt-4">
                        <input type="hidden" name="action" value="save_requirements">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-arrow-right"></i> Get Started
                        </button>
                    </form>
                </div>
            </div>
        </div>';
        
        return $html;
    }
    
    private function renderRequirementsStep() {
        $requirements = new Requirements();
        $results = $requirements->check();
        
        $allPassed = $results['passed'];
        $totalChecks = count($results['checks']);
        $passedChecks = count(array_filter($results['checks'], function($check) { return $check['status'] === 'pass'; }));
        
        $html = '
        <div class="install-requirements">
            <div class="card">
                <div class="card-body">
                    <h2 class="mb-4"><i class="fas fa-check-circle text-primary"></i> System Requirements</h2>
                    <p class="text-muted">Verifying your server meets the minimum requirements for Bishwo Calculator.</p>
                    
                    <div class="requirements-results mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stat-card">
                                    <h3 class="text-primary">' . $passedChecks . '/' . $totalChecks . '</h3>
                                    <p class="text-muted">Requirements Met</p>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="progress mb-2">
                                    <div class="progress-bar ' . ($allPassed ? 'bg-success' : 'bg-warning') . '" 
                                         style="width: ' . ($passedChecks / $totalChecks * 100) . '%"></div>
                                </div>
                                <small class="text-muted">' . ($allPassed ? 'All requirements are satisfied' : 'Some requirements need attention') . '</small>
                            </div>
                        </div>
                    </div>';
        
        // Display each requirement check
        foreach ($results['checks'] as $check) {
            $iconClass = $check['status'] === 'pass' ? 'text-success' : ($check['status'] === 'warning' ? 'text-warning' : 'text-danger');
            $icon = $check['status'] === 'pass' ? 'fa-check-circle' : ($check['status'] === 'warning' ? 'fa-exclamation-triangle' : 'fa-times-circle');
            
            $html .= '
            <div class="requirement-item d-flex align-items-center mb-3">
                <div class="requirement-icon me-3">
                    <i class="fas ' . $icon . ' ' . $iconClass . '"></i>
                </div>
                <div class="requirement-details flex-grow-1">
                    <h6 class="mb-1">' . htmlspecialchars($check['name']) . '</h6>
                    <p class="text-muted mb-0">' . htmlspecialchars($check['description']) . '</p>
                    <small class="text-muted">Current: ' . htmlspecialchars($check['current']) . '</small>';
            
            if (!empty($check['required'])) {
                $html .= ' <small class="text-muted">| Required: ' . htmlspecialchars($check['required']) . '</small>';
            }
            
            $html .= '
                </div>
            </div>';
        }
        
        $html .= '
                    <div class="alert ' . ($allPassed ? 'alert-success' : 'alert-danger') . ' mt-4">
                        <h5><i class="fas ' . ($allPassed ? 'fa-thumbs-up' : 'fa-exclamation-triangle') . '"></i> 
                        ' . ($allPassed ? 'System Ready' : 'Requirements Not Met') . '</h5>
                        <p class="mb-0">' . ($allPassed ? 
                            'Your system meets all requirements. You can proceed with the installation.' : 
                            'Please address the failed requirements before proceeding with the installation.') . '</p>
                    </div>';
        
        if ($allPassed) {
            $html .= '
                    <form method="post" class="mt-4">
                        <input type="hidden" name="action" value="save_requirements">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-arrow-right"></i> Continue to Database Setup
                        </button>
                    </form>';
        }
        
        $html .= '
                </div>
            </div>
        </div>';
        
        return $html;
    }
    
    private function renderDatabaseStep() {
        $dbConfig = $_SESSION['db_config'] ?? [];
        
        $html = '
        <div class="install-database">
            <div class="card">
                <div class="card-body">
                    <h2 class="mb-4"><i class="fas fa-database text-primary"></i> Database Configuration</h2>
                    <p class="text-muted">Configure your database connection to store application data.</p>
                    
                    <form method="post" id="databaseForm">
                        <input type="hidden" name="action" value="save_database">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="db_host" class="form-label">Database Host <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="db_host" name="db_host" 
                                           value="' . htmlspecialchars($dbConfig['host'] ?? 'localhost') . '" required>
                                    <div class="form-text">Usually "localhost" or your database server IP</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="db_port" class="form-label">Database Port</label>
                                    <input type="text" class="form-control" id="db_port" name="db_port" 
                                           value="' . htmlspecialchars($dbConfig['port'] ?? '3306') . '">
                                    <div class="form-text">Default MySQL port is 3306</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="db_name" class="form-label">Database Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="db_name" name="db_name" 
                                   value="' . htmlspecialchars($dbConfig['name'] ?? '') . '" required>
                            <div class="form-text">The database must already exist</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="db_user" class="form-label">Database Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="db_user" name="db_user" 
                                           value="' . htmlspecialchars($dbConfig['user'] ?? '') . '" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="db_pass" class="form-label">Database Password</label>
                                    <input type="password" class="form-control" id="db_pass" name="db_pass" 
                                           value="' . htmlspecialchars($dbConfig['pass'] ?? '') . '">
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Database Setup Instructions</h6>
                            <ol class="mb-0">
                                <li>Create a MySQL database using your hosting control panel or phpMyAdmin</li>
                                <li>Create a database user and grant all privileges to the database</li>
                                <li>Enter the database credentials above</li>
                            </ol>
                        </div>
                        
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-database"></i> Test & Save Database Configuration
                            </button>
                            <a href="index.php?step=welcome" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>';
        
        return $html;
    }
    
    private function renderAdminStep() {
        $adminConfig = $_SESSION['admin_config'] ?? [];
        
        $html = '
        <div class="install-admin">
            <div class="card">
                <div class="card-body">
                    <h2 class="mb-4"><i class="fas fa-user-shield text-primary"></i> Administrator Account</h2>
                    <p class="text-muted">Create the administrator account that will have full access to the system.</p>
                    
                    <form method="post" id="adminForm">
                        <input type="hidden" name="action" value="save_admin">
                        
                        <div class="mb-3">
                            <label for="admin_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="admin_name" name="admin_name" 
                                   value="' . htmlspecialchars($adminConfig['name'] ?? '') . '" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="admin_email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="admin_email" name="admin_email" 
                                   value="' . htmlspecialchars($adminConfig['email'] ?? '') . '" required>
                            <div class="form-text">This will be used for login and system notifications</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="admin_pass" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="admin_pass" name="admin_pass" 
                                           minlength="6" required>
                                    <div class="form-text">Minimum 6 characters</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="admin_pass_confirm" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="admin_pass_confirm" name="admin_pass_confirm" 
                                           minlength="6" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="password-strength mb-3">
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar" id="passwordStrength" role="progressbar" style="width: 0%"></div>
                            </div>
                            <small class="text-muted" id="passwordStrengthText">Password strength indicator</small>
                        </div>
                        
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle"></i> Security Notice</h6>
                            <ul class="mb-0">
                                <li>Choose a strong, unique password</li>
                                <li>Store your credentials securely</li>
                                <li>Change the default admin email after installation</li>
                            </ul>
                        </div>
                        
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i> Create Administrator
                            </button>
                            <a href="index.php?step=database" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>';
        
        return $html;
    }
    
    private function renderEmailStep() {
        $emailConfig = $_SESSION['email_config'] ?? [];
        
        $html = '
        <div class="install-email">
            <div class="card">
                <div class="card-body">
                    <h2 class="mb-4"><i class="fas fa-envelope text-primary"></i> Email Configuration</h2>
                    <p class="text-muted">Configure email settings for system notifications and user communications.</p>
                    
                    <form method="post" id="emailForm">
                        <input type="hidden" name="action" value="save_email">
                        
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="smtp_enabled" name="smtp_enabled" 
                                       ' . (isset($emailConfig['smtp_enabled']) && $emailConfig['smtp_enabled'] ? 'checked' : '') . '>
                                <label class="form-check-label" for="smtp_enabled">
                                    <strong>Enable SMTP Email</strong>
                                    <div class="form-text">Use SMTP for sending emails (recommended for production)</div>
                                </label>
                            </div>
                        </div>
                        
                        <div id="smtp_config" class="' . (isset($emailConfig['smtp_enabled']) && $emailConfig['smtp_enabled'] ? '' : 'd-none') . '">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="smtp_host" class="form-label">SMTP Host <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                                               value="' . htmlspecialchars($emailConfig['host'] ?? '') . '">
                                        <div class="form-text">e.g., smtp.gmail.com, smtp.sendgrid.net</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="smtp_port" class="form-label">SMTP Port <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="smtp_port" name="smtp_port" 
                                               value="' . htmlspecialchars($emailConfig['port'] ?? '587') . '">
                                        <div class="form-text">Usually 587 (TLS) or 465 (SSL)</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="smtp_user" class="form-label">SMTP Username <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="smtp_user" name="smtp_user" 
                                               value="' . htmlspecialchars($emailConfig['user'] ?? '') . '">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="smtp_pass" class="form-label">SMTP Password</label>
                                        <input type="password" class="form-control" id="smtp_pass" name="smtp_pass" 
                                               value="' . htmlspecialchars($emailConfig['pass'] ?? '') . '">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <h6><i class="fas fa-lightbulb"></i> SMTP Configuration Tips</h6>
                                <ul class="mb-0">
                                    <li><strong>Gmail:</strong> Use app-specific passwords or allow less secure apps</li>
                                    <li><strong>SendGrid:</strong> Use your API key as the password</li>
                                    <li><strong>Custom SMTP:</strong> Contact your hosting provider for details</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="alert alert-secondary">
                            <h6><i class="fas fa-info-circle"></i> Email Settings</h6>
                            <p class="mb-0">If SMTP is disabled, emails will be logged to the system log files instead of being sent.</p>
                        </div>
                        
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Email Configuration
                            </button>
                            <a href="index.php?step=admin" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>';
        
        return $html;
    }
    
    private function renderFinishStep() {
        $html = '
        <div class="install-finish">
            <div class="card">
                <div class="card-body text-center">
                    <div class="success-animation mb-4">
                        <i class="fas fa-check-circle text-success fa-5x"></i>
                    </div>
                    
                    <h2 class="mb-4 text-success">Installation Complete!</h2>
                    <p class="lead mb-4">Bishwo Calculator has been successfully installed and configured.</p>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="completion-stat">
                                <i class="fas fa-database text-primary fa-2x mb-2"></i>
                                <h5>Database</h5>
                                <p class="text-muted">Configured & Migrated</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="completion-stat">
                                <i class="fas fa-user-shield text-success fa-2x mb-2"></i>
                                <h5>Administrator</h5>
                                <p class="text-muted">Account Created</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="completion-stat">
                                <i class="fas fa-envelope text-info fa-2x mb-2"></i>
                                <h5>Email System</h5>
                                <p class="text-muted">Configured</p>
                            </div>
                        </div>
                    </div>
                    
                    <form method="post" class="mb-4">
                        <input type="hidden" name="action" value="complete_installation">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-rocket"></i> Complete Installation
                        </button>
                    </form>
                    
                    <div class="alert alert-info">
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
                        <div class="d-flex gap-2 justify-content-center flex-wrap">
                            <a href="../" class="btn btn-primary">
                                <i class="fas fa-home"></i> Go to Application
                            </a>
                            <a href="../admin" class="btn btn-outline-primary">
                                <i class="fas fa-cog"></i> Admin Dashboard
                            </a>
                            <a href="../login" class="btn btn-outline-secondary">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
        
        return $html;
    }
}
?>
