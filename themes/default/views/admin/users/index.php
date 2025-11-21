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
</div>

<!-- Users Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-users" style="font-size: 2rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Total Users</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> Active Users</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-user-shield" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['active'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Active Users</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Online</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-user-cog" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($stats['admins'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Administrators</div>
        <small style="color: #9ca3af; font-size: 0.75rem;"><?php echo number_format($stats['regular'] ?? 0); ?> Regular Users</small>
    </div>
</div>

<!-- Users Table -->
<div class="admin-card">
    <h2 class="admin-card-title">All Users</h2>
    <div class="admin-card-content">
        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Username</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Email</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Role</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Status</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                            <td style="padding: 0.75rem;"><?php echo htmlspecialchars($user['username'] ?? ''); ?></td>
                            <td style="padding: 0.75rem;"><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
                            <td style="padding: 0.75rem;"><?php echo htmlspecialchars(ucfirst($user['role'] ?? 'user')); ?></td>
                            <td style="padding: 0.75rem;">
                                <span class="status-<?php echo ($user['is_active'] ?? true) ? 'success' : 'error'; ?>">
                                    <?php echo ($user['is_active'] ?? true) ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td style="padding: 0.75rem;">
                                <a href="<?php echo app_base_url('/admin/users/'.($user['id'] ?? 0).'/edit'); ?>"
                                   style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem; margin-right: 0.5rem;">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit</span>
                                </a>
                                <a href="<?php echo app_base_url('/admin/users/'.($user['id'] ?? 0).'/delete'); ?>"
                                   style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 4px; text-decoration: none; color: #f87171; font-size: 0.875rem;">
                                    <i class="fas fa-trash"></i>
                                    <span>Delete</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">User Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/users/create'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-user-plus"></i>
            <span>Add New User</span>
        </a>

        <a href="<?php echo app_base_url('/admin/users/roles'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-cog"></i>
            <span>Manage Roles</span>
        </a>

        <a href="<?php echo app_base_url('/admin/users/permissions'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-user-lock"></i>
            <span>Manage Permissions</span>
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>