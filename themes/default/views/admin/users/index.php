<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>User Management</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Manage user accounts, roles, and permissions</p>
    </div>
</div>

<!-- Users Statistics -->
<div class="admin-grid">
    <div class="admin-card">
        <div style="text-align: center;">
            <i class="fas fa-users" style="font-size: 2rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Total Users</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> Active Users</div>
    </div>
    
    <div class="admin-card">
        <div style="text-align: center;">
            <i class="fas fa-user-shield" style="font-size: 1.5rem; color: #34d399;"></i>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['active'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;"><?php echo number_format($stats['active'] ?? 0); ?></div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Online</small>
    </div>
    
    <div class="admin-card">
        <div style="text-align: center;">
            <i class="fas fa-user-cog" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($stats['admins'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;"><?php echo number_format($stats['regular'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Administrators</div>
        <small style="color: #9ca3af; font-size: 0.75rem;">Regular Users</div>
    </div>
</div>

<!-- Users Table -->
<div class="admin-card">
    <h2 class="admin-card-title">All Users</h2>
    <div class="admin-card-content">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['username'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($user['role'] ?? 'user'); ?></td>
                    <td>
                        <span class="status-<?php echo ($user['is_active'] ?? true) ? 'success' : 'error'; ?>">
                    <?php echo ucfirst($user['role'] ?? 'user'); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>

<!-- Quick Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">User Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/users/create'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; text-decoration: none; border-radius: 6px;">
            <i class="fas fa-user-plus"></i>
            <span>Add New User</span>
        </a>
        
        <a href="<?php echo app_base_url('/admin/users/roles'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; text-decoration: none;">
            <i class="fas fa-cog"></i>
            <span>Manage Roles</span>
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>