<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Create User</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Add a new user to the system</p>
        </div>
    </div>
</div>

<!-- Create User Form -->
<div class="admin-card">
    <h2 class="admin-card-title">User Information</h2>
    <form method="POST" action="<?php echo app_base_url('/admin/users/store'); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <div>
                <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Username</label>
                <input type="text" name="username" required
                       style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
            </div>
            
            <div>
                <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Email</label>
                <input type="email" name="email" required
                       style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
            <div>
                <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Full Name</label>
                <input type="text" name="full_name"
                       style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
            </div>
            
            <div>
                <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Role</label>
                <select name="role"
                        style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                    <option value="user">Regular User</option>
                    <option value="admin">Administrator</option>
                    <option value="engineer">Engineer</option>
                </select>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
            <div>
                <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Password</label>
                <input type="password" name="password" required
                       style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
            </div>
            
            <div>
                <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                       style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
            </div>
        </div>
        
        <div style="margin-top: 1.5rem; display: flex; gap: 1rem;">
            <button type="submit" 
                    style="padding: 0.75rem 1.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer;">
                <i class="fas fa-plus-circle"></i>
                <span>Create User</span>
            </button>
            <a href="<?php echo app_base_url('/admin/users'); ?>" 
               style="padding: 0.75rem 1.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #22d3ee; text-decoration: none;">
                <i class="fas fa-arrow-left"></i>
                <span>Cancel</span>
            </a>
        </div>
    </form>
</div>

<!-- Security Tips -->
<div class="admin-card">
    <h2 class="admin-card-title">Security Tips</h2>
    <div class="admin-card-content">
        <ul style="list-style: none; padding: 0; margin: 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
            <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                <i class="fas fa-shield-alt" style="color: #34d399; margin-top: 0.125rem;"></i>
                <div>
                    <h4 style="color: #f9fafb; margin: 0 0 0.25rem 0;">Strong Passwords</h4>
                    <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Use complex passwords with at least 8 characters, numbers, and special characters</p>
                </div>
            </li>
            <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                <i class="fas fa-user-shield" style="color: #fbbf24; margin-top: 0.125rem;"></i>
                <div>
                    <h4 style="color: #f9fafb; margin: 0 0 0.25rem 0;">Role Assignment</h4>
                    <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Assign appropriate roles with minimal necessary permissions for security</p>
                </div>
            </li>
            <li style="display: flex; align-items: flex-start; gap: 0.75rem;">
                <i class="fas fa-lock" style="color: #22d3ee; margin-top: 0.125rem;"></i>
                <div>
                    <h4 style="color: #f9fafb; margin: 0 0 0.25rem 0;">Account Verification</h4>
                    <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Verify user identity before creating accounts with elevated privileges</p>
                </div>
            </li>
        </ul>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>