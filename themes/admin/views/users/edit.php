<?php
$page_title = $page_title ?? 'Edit User';
$user = $user ?? [];
?>

<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-user-edit"></i>
            <?php echo htmlspecialchars($page_title); ?>
        </h1>
        <p class="page-description">Edit user account information and settings</p>
    </div>

    <!-- Breadcrumb -->
    <nav class="breadcrumb" style="margin-bottom: 24px;">
        <a href="/admin/users">Users</a>
        <span class="breadcrumb-divider">/</span>
        <span class="breadcrumb-current">Edit User</span>
    </nav>

    <!-- User Info Card -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i>
                User Information
            </h3>
        </div>
        <div class="card-content">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--admin-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold;">
                    <?php echo strtoupper(substr($user['username'] ?? $user['email'], 0, 1)); ?>
                </div>
                <div>
                    <h3 style="margin: 0; color: var(--admin-gray-800);">
                        <?php echo htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))); ?>
                    </h3>
                    <p style="margin: 4px 0; color: var(--admin-gray-600);">
                        @<?php echo htmlspecialchars($user['username'] ?? ''); ?> • <?php echo htmlspecialchars($user['email'] ?? ''); ?>
                    </p>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <span class="badge <?php echo ($user['role'] ?? '') === 'admin' ? 'badge-warning' : 'badge-primary'; ?>">
                            <?php echo htmlspecialchars($user['role'] ?? 'user'); ?>
                        </span>
                        <?php if (($user['is_active'] ?? 1)): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Inactive</span>
                        <?php endif; ?>
                        <span style="font-size: 12px; color: var(--admin-gray-500);">
                            Joined: <?php echo date('M j, Y', strtotime($user['created_at'] ?? 'now')); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Form -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-edit"></i>
                Edit User Information
            </h3>
        </div>
        <div class="card-content">
            <form id="editUserForm" method="POST" action="/admin/users/<?php echo $user['id']; ?>/update">
                <?php $this->csrfField(); ?>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                    <!-- Personal Information -->
                    <div>
                        <h4 style="margin-bottom: 16px; color: var(--admin-gray-700); border-bottom: 1px solid var(--admin-gray-200); padding-bottom: 8px;">
                            <i class="fas fa-user"></i>
                            Personal Information
                        </h4>
                        
                        <div class="form-group">
                            <label class="form-label" for="first_name">First Name *</label>
                            <input type="text" id="first_name" name="first_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="last_name">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" class="form-control" 
                                   value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="username">Username *</label>
                            <input type="text" id="username" name="username" class="form-control" 
                                   value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                            <small style="color: var(--admin-gray-500); font-size: 12px;">Choose a unique username</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="email">Email Address *</label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                            <small style="color: var(--admin-gray-500); font-size: 12px;">A valid email address is required</small>
                        </div>
                    </div>
                    
                    <!-- Account Settings -->
                    <div>
                        <h4 style="margin-bottom: 16px; color: var(--admin-gray-700); border-bottom: 1px solid var(--admin-gray-200); padding-bottom: 8px;">
                            <i class="fas fa-cog"></i>
                            Account Settings
                        </h4>
                        
                        <div class="form-group">
                            <label class="form-label" for="role">Role *</label>
                            <select id="role" name="role" class="form-control" required>
                                <option value="user" <?php echo ($user['role'] ?? '') === 'user' ? 'selected' : ''; ?>>Regular User</option>
                                <option value="engineer" <?php echo ($user['role'] ?? '') === 'engineer' ? 'selected' : ''; ?>>Engineer</option>
                                <option value="admin" <?php echo ($user['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Account Status</label>
                            <div style="display: flex; gap: 16px;">
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="radio" name="is_active" value="1" <?php echo ($user['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                    <span>Active</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="radio" name="is_active" value="0" <?php echo !($user['is_active'] ?? 1) ? 'checked' : ''; ?>>
                                    <span>Inactive</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Email Verification</label>
                            <div style="display: flex; gap: 16px;">
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="radio" name="email_verified" value="1" <?php echo ($user['email_verified'] ?? 1) ? 'checked' : ''; ?>>
                                    <span>Verified</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 8px;">
                                    <input type="radio" name="email_verified" value="0" <?php echo !($user['email_verified'] ?? 1) ? 'checked' : ''; ?>>
                                    <span>Unverified</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Account Actions -->
                <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--admin-gray-200);">
                    <h4 style="margin-bottom: 16px; color: var(--admin-gray-700); border-bottom: 1px solid var(--admin-gray-200); padding-bottom: 8px;">
                        <i class="fas fa-key"></i>
                        Account Actions
                    </h4>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="send_password_reset" value="1">
                            <span>Send password reset email</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="reset_sessions" value="1">
                            <span>Reset all active sessions</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="marketing_emails" value="1" <?php echo ($user['marketing_emails'] ?? 0) ? 'checked' : ''; ?>>
                            <span>Allow marketing emails</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="checkbox" name="force_password_change" value="1">
                            <span>Force password change on next login</span>
                        </label>
                    </div>
                </div>
                
                <!-- Danger Zone -->
                <?php if (($user['role'] ?? '') !== 'admin'): ?>
                <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--admin-danger);">
                    <h4 style="margin-bottom: 16px; color: var(--admin-danger); border-bottom: 1px solid var(--admin-gray-200); padding-bottom: 8px;">
                        <i class="fas fa-exclamation-triangle"></i>
                        Danger Zone
                    </h4>
                    
                    <div style="background: var(--admin-gray-50); padding: 16px; border-radius: 8px; border-left: 4px solid var(--admin-danger);">
                        <h5 style="margin: 0 0 8px 0; color: var(--admin-danger);">Delete User Account</h5>
                        <p style="margin: 0 0 16px 0; color: var(--admin-gray-600); font-size: 14px;">
                            Once you delete this user account, there is no going back. Please be certain.
                        </p>
                        <button type="button" class="btn btn-danger" onclick="deleteUser(<?php echo $user['id']; ?>)">
                            <i class="fas fa-trash"></i>
                            Permanently Delete User
                        </button>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Form Actions -->
                <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--admin-gray-200); display: flex; gap: 16px; justify-content: flex-end;">
                    <a href="/admin/users" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Cancel
                    </a>
                    <button type="button" class="btn btn-secondary" onclick="previewChanges()">
                        <i class="fas fa-eye"></i>
                        Preview Changes
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
}

.badge-primary { background: var(--admin-primary); color: white; }
.badge-warning { background: var(--admin-warning); color: white; }
.badge-success { background: var(--admin-success); color: white; }
.badge-danger { background: var(--admin-danger); color: white; }
</style>

<script>
// Form validation and interactions
document.getElementById('editUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    updateUser();
});

function previewChanges() {
    const form = document.getElementById('editUserForm');
    const formData = new FormData(form);
    
    const changes = {
        firstName: formData.get('first_name'),
        lastName: formData.get('last_name'),
        username: formData.get('username'),
        email: formData.get('email'),
        role: formData.get('role'),
        status: formData.get('is_active') === '1' ? 'Active' : 'Inactive',
        emailVerified: formData.get('email_verified') === '1' ? 'Verified' : 'Unverified',
        marketingEmails: formData.get('marketing_emails') === '1' ? 'Yes' : 'No'
    };
    
    let changesHTML = '<h3>Changes Preview</h3>';
    changesHTML += '<div style="text-align: left;">';
    changesHTML += '<p><strong>Name:</strong> ' + changes.firstName + ' ' + changes.lastName + '</p>';
    changesHTML += '<p><strong>Username:</strong> ' + changes.username + '</p>';
    changesHTML += '<p><strong>Email:</strong> ' + changes.email + '</p>';
    changesHTML += '<p><strong>Role:</strong> ' + changes.role + '</p>';
    changesHTML += '<p><strong>Status:</strong> ' + changes.status + '</p>';
    changesHTML += '<p><strong>Email Verification:</strong> ' + changes.emailVerified + '</p>';
    changesHTML += '<p><strong>Marketing Emails:</strong> ' + changes.marketingEmails + '</p>';
    changesHTML += '</div>';
    
    showPreviewModal(changesHTML);
}

function updateUser() {
    const form = document.getElementById('editUserForm');
    const formData = new FormData(form);
    const userId = <?php echo $user['id']; ?>;
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    submitBtn.disabled = true;
    
    fetch(`/admin/users/${userId}/update`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-Token': '<?php echo $this->csrfToken(); ?>'
        }
    })
    .then(response => {
        if (response.ok) {
            alert('User updated successfully!');
            window.location.href = '/admin/users';
        } else {
            throw new Error('Failed to update user');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating user: ' + error.message);
    })
    .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function deleteUser(userId) {
    if (confirm('Are you absolutely sure you want to delete this user? This action cannot be undone.')) {
        if (confirm('This will permanently delete the user account and all associated data. Are you sure?')) {
            fetch(`/admin/users/${userId}/delete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?php echo $this->csrfToken(); ?>'
                }
            })
            .then(response => {
                if (response.ok) {
                    alert('User deleted successfully!');
                    window.location.href = '/admin/users';
                } else {
                    throw new Error('Failed to delete user');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting user: ' + error.message);
            });
        }
    }
}

function showPreviewModal(content) {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center;
        z-index: 9999;
    `;
    
    modal.innerHTML = `
        <div style="background: white; padding: 32px; border-radius: 12px; max-width: 500px; width: 90%;">
            ${content}
            <div style="margin-top: 24px; text-align: right;">
                <button onclick="this.closest('[style*=fixed]').remove()" class="btn btn-secondary">Close</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}
</script>