<?php
$page_title = $page_title ?? 'User Permissions';
$permissions = $permissions ?? [];
?>

<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-key"></i>
            <?php echo htmlspecialchars($page_title); ?>
        </h1>
        <p class="page-description">Manage user permissions and access control</p>
    </div>

    <!-- Permissions Matrix -->
    <div class="table-container">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-table"></i>
                Permissions Matrix
            </h3>
        </div>
        <div class="card-content">
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Permission</th>
                            <th>Admin</th>
                            <th>Engineer</th>
                            <th>User</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $all_permissions = [
                            'manage_users' => 'User Management',
                            'manage_system' => 'System Management',
                            'view_analytics' => 'View Analytics',
                            'manage_modules' => 'Manage Modules',
                            'use_calculators' => 'Use Calculators',
                            'view_profile' => 'View Profile',
                            'advanced_tools' => 'Advanced Tools'
                        ];
                        
                        foreach ($all_permissions as $permission_key => $permission_name): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($permission_name); ?></strong></td>
                                <td style="text-align: center;">
                                    <?php if (in_array($permission_key, $permissions['admin'] ?? [])): ?>
                                        <i class="fas fa-check" style="color: var(--admin-success);"></i>
                                    <?php else: ?>
                                        <i class="fas fa-times" style="color: var(--admin-gray-300);"></i>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if (in_array($permission_key, $permissions['engineer'] ?? [])): ?>
                                        <i class="fas fa-check" style="color: var(--admin-success);"></i>
                                    <?php else: ?>
                                        <i class="fas fa-times" style="color: var(--admin-gray-300);"></i>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if (in_array($permission_key, $permissions['user'] ?? [])): ?>
                                        <i class="fas fa-check" style="color: var(--admin-success);"></i>
                                    <?php else: ?>
                                        <i class="fas fa-times" style="color: var(--admin-gray-300);"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($permission_name); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card" style="margin-top: 24px;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-cogs"></i>
                Permission Management
            </h3>
        </div>
        <div class="card-content">
            <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                <button class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Add Custom Permission
                </button>
                <button class="btn btn-secondary">
                    <i class="fas fa-save"></i>
                    Save Changes
                </button>
                <a href="/admin/users/roles" class="btn btn-secondary">
                    <i class="fas fa-user-tag"></i>
                    View Roles
                </a>
                <a href="/admin/users" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Users
                </a>
            </div>
        </div>
    </div>
</div>