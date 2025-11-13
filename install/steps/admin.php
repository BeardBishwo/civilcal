<div class="step-content">
    <div class="step-icon">
        <i class="fas fa-user-shield"></i>
    </div>
    <h2 class="step-heading">Create Administrator Account</h2>
    <p class="step-description">
        Create your administrator account. This will be the main admin account with full access to the system.
    </p>
    
    <form method="POST" style="text-align: left;">
        <input type="hidden" name="action" value="create_admin">
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">First Name</label>
                <input type="text" name="admin_first_name" class="form-control" 
                       value="<?php echo htmlspecialchars($_SESSION['admin_user']['admin_first_name'] ?? ''); ?>" 
                       placeholder="Administrator" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Last Name</label>
                <input type="text" name="admin_last_name" class="form-control" 
                       value="<?php echo htmlspecialchars($_SESSION['admin_user']['admin_last_name'] ?? ''); ?>" 
                       placeholder="User" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="admin_username" class="form-control" 
                       value="<?php echo htmlspecialchars($_SESSION['admin_user']['admin_username'] ?? 'admin'); ?>" 
                       placeholder="admin" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="admin_email" class="form-control" 
                       value="<?php echo htmlspecialchars($_SESSION['admin_user']['admin_email'] ?? ''); ?>" 
                       placeholder="admin@example.com" required>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="admin_password" class="form-control" 
                       placeholder="Minimum 6 characters" required minlength="6">
            </div>
            
            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="admin_password_confirm" class="form-control" 
                       placeholder="Confirm password" required>
            </div>
        </div>
        
        <div style="background: var(--gray-50); padding: 20px; border-radius: 8px; margin: 24px 0;">
            <h4 style="margin-bottom: 12px; color: var(--gray-800);">
                <i class="fas fa-key"></i>
                Administrator Privileges
            </h4>
            <div style="font-size: 14px; color: var(--gray-600); line-height: 1.6;">
                This account will have complete access to:
                <ul style="margin: 8px 0 0 20px;">
                    <li>User management and permissions</li>
                    <li>System settings and configuration</li>
                    <li>Module management and installation</li>
                    <li>Analytics and reporting</li>
                    <li>Database backup and maintenance</li>
                </ul>
            </div>
        </div>
        
        <div class="btn-actions">
            <a href="?step=database" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back
            </a>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-arrow-right"></i>
                Create Account
            </button>
        </div>
    </form>
</div>

<script>
// Password confirmation validation
document.addEventListener('DOMContentLoaded', function() {
    const password = document.querySelector('input[name="admin_password"]');
    const confirmPassword = document.querySelector('input[name="admin_password_confirm"]');
    
    function validatePassword() {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity("Passwords don't match");
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    password.addEventListener('input', validatePassword);
    confirmPassword.addEventListener('input', validatePassword);
});
</script>
