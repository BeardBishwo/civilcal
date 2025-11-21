<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Edit User</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Update user information and settings</p>
        </div>
    </div>
</div>

<!-- User Information -->
<div class="admin-card">
    <h2 class="admin-card-title">User Information</h2>
    <form method="POST" action="<?php echo app_base_url('/admin/users/'.($user['id'] ?? 0).'/update'); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <div>
                <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required
                       style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
            </div>
            
            <div>
                <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required
                       style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
            <div>
                <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Full Name</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>"
                       style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
            </div>
            
            <div>
                <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Role</label>
                <select name="role"
                        style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                    <option value="user" <?php echo ($user['role'] ?? 'user') === 'user' ? 'selected' : ''; ?>>Regular User</option>
                    <option value="admin" <?php echo ($user['role'] ?? 'user') === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                    <option value="engineer" <?php echo ($user['role'] ?? 'user') === 'engineer' ? 'selected' : ''; ?>>Engineer</option>
                </select>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
            <div>
                <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Status</label>
                <select name="is_active"
                        style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                    <option value="1" <?php echo ($user['is_active'] ?? 1) ? 'selected' : ''; ?>>Active</option>
                    <option value="0" <?php echo !($user['is_active'] ?? 1) ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            
            <div>
                <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Registration Date</label>
                <input type="text" value="<?php echo $user['created_at'] ?? 'Unknown'; ?>" readonly
                       style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #9ca3af;">
            </div>
        </div>
        
        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
            <button type="submit" 
                    style="padding: 0.75rem 1.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer;">
                <i class="fas fa-save"></i>
                <span>Update User</span>
            </button>
            <a href="<?php echo app_base_url('/admin/users'); ?>" 
               style="padding: 0.75rem 1.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #22d3ee; text-decoration: none;">
                <i class="fas fa-arrow-left"></i>
                <span>Cancel</span>
            </a>
            <a href="<?php echo app_base_url('/admin/users/'.($user['id'] ?? 0).'/delete'); ?>" 
               style="padding: 0.75rem 1.5rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; color: #f87171; text-decoration: none; margin-left: auto;">
                <i class="fas fa-trash"></i>
                <span>Delete User</span>
            </a>
        </div>
    </form>
</div>

<!-- Change Password Form -->
<div class="admin-card">
    <h2 class="admin-card-title">Change Password</h2>
    <form method="POST" action="<?php echo app_base_url('/admin/users/'.($user['id'] ?? 0).'/change-password'); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <div>
                <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">New Password</label>
                <input type="password" name="new_password"
                       style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
            </div>
            
            <div>
                <label style="display: block; color: #f9fafb; margin-bottom: 0.5rem;">Confirm New Password</label>
                <input type="password" name="confirm_password"
                       style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
            </div>
        </div>
        
        <div style="margin-top: 1.5rem;">
            <button type="submit" 
                    style="padding: 0.75rem 1.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; color: #34d399; cursor: pointer;">
                <i class="fas fa-key"></i>
                <span>Change Password</span>
            </button>
        </div>
    </form>
</div>

<!-- User Activity -->
<div class="admin-card">
    <h2 class="admin-card-title">User Activity</h2>
    <div class="admin-card-content">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; text-align: center;">
            <div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.25rem;"><?php echo $user['calculations_count'] ?? 0; ?></div>
                <div style="color: #9ca3af; font-size: 0.875rem;">Calculations</div>
            </div>
            <div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #34d399; margin-bottom: 0.25rem;"><?php echo $user['last_login'] ?? 'Never'; ?></div>
                <div style="color: #9ca3af; font-size: 0.875rem;">Last Login</div>
            </div>
            <div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.25rem;"><?php echo $user['login_count'] ?? 0; ?></div>
                <div style="color: #9ca3af; font-size: 0.875rem;">Login Count</div>
            </div>
            <div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.25rem;"><?php echo $user['account_age'] ?? '0 days'; ?></div>
                <div style="color: #9ca3af; font-size: 0.875rem;">Account Age</div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>