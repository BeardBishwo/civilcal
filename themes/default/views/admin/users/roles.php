<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>User Roles Management</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Manage user roles and their capabilities</p>
        </div>
    </div>
</div>

<!-- Roles Overview -->
<div class="admin-grid">
    <?php if (!empty($roles)): ?>
        <?php foreach ($roles as $role_key => $role): ?>
            <div class="admin-card" style="text-align: center; padding: 1.5rem;">
                <i class="fas fa-user-tag" style="font-size: 1.5rem; color: <?php echo $role_key === 'admin' ? '#4cc9f0' : ($role_key === 'engineer' ? '#34d399' : '#fbbf24'); ?>; margin-bottom: 1rem;"></i>
                <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($role['name']); ?></h3>
                <div style="font-size: 2rem; font-weight: 700; color: <?php echo $role_key === 'admin' ? '#4cc9f0' : ($role_key === 'engineer' ? '#34d399' : '#fbbf24'); ?>; margin-bottom: 0.5rem;"><?php echo number_format($role_stats[$role_key] ?? 0); ?></div>
                <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Users</div>
                <p style="color: #9ca3af; font-size: 0.75rem; margin: 0;"><?php echo htmlspecialchars($role['description']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="admin-card" style="text-align: center; padding: 1.5rem; grid-column: 1 / -1;">
            <i class="fas fa-user-tag" style="font-size: 1.5rem; color: #9ca3af; margin-bottom: 1rem;"></i>
            <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">No Roles Defined</h3>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Create user roles to manage permissions</p>
        </div>
    <?php endif; ?>
</div>

<!-- Role Definitions -->
<div class="admin-card">
    <h2 class="admin-card-title">Role Definitions</h2>
    <div class="admin-card-content">
        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Role</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Description</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Users</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($roles)): ?>
                        <?php foreach ($roles as $role_key => $role): ?>
                            <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <td style="padding: 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fas fa-user-tag" style="color: <?php echo $role_key === 'admin' ? '#4cc9f0' : ($role_key === 'engineer' ? '#34d399' : '#fbbf24'); ?>;"></i>
                                        <span style="font-weight: 600; color: <?php echo $role_key === 'admin' ? '#4cc9f0' : ($role_key === 'engineer' ? '#34d399' : '#fbbf24'); ?>;"><?php echo htmlspecialchars($role['name']); ?></span>
                                    </div>
                                </td>
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars($role['description']); ?></td>
                                <td style="padding: 0.75rem;"><?php echo number_format($role_stats[$role_key] ?? 0); ?> users</td>
                                <td style="padding: 0.75rem;">
                                    <a href="<?php echo app_base_url('/admin/users/roles/'.($role_key).'/edit'); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem; margin-right: 0.5rem;">
                                        <i class="fas fa-edit"></i>
                                        <span>Edit</span>
                                    </a>
                                    <a href="<?php echo app_base_url('/admin/users/roles/'.($role_key).'/permissions'); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 4px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                                        <i class="fas fa-lock"></i>
                                        <span>Permissions</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 1rem; color: #9ca3af;">No roles defined</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Role Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Role Management</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/users/roles/create'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-plus-circle"></i>
            <span>Create Role</span>
        </a>

        <a href="<?php echo app_base_url('/admin/users/permissions'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-key"></i>
            <span>Manage Permissions</span>
        </a>

        <a href="<?php echo app_base_url('/admin/users/roles/import'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-file-import"></i>
            <span>Import Roles</span>
        </a>

        <a href="<?php echo app_base_url('/admin/users/roles/export'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-file-export"></i>
            <span>Export Roles</span>
        </a>
    </div>
</div>

<!-- Role Permissions Matrix -->
<div class="admin-card">
    <h2 class="admin-card-title">Role Permissions Matrix</h2>
    <div class="admin-card-content">
        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Permission</th>
                        <?php foreach ($roles as $role_key => $role): ?>
                            <th style="text-align: center; padding: 0.75rem; color: #9ca3af; font-weight: 600;"><?php echo htmlspecialchars($role['name']); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                        <td style="padding: 0.75rem; font-weight: 500;">Manage Users</td>
                        <?php foreach ($roles as $role_key => $role): ?>
                            <td style="padding: 0.75rem; text-align: center;">
                                <?php 
                                $hasPermission = ($role_key === 'admin' || $role_key === 'engineer') ? true : false;
                                ?>
                                <i class="fas <?php echo $hasPermission ? 'fa-check-circle' : 'fa-times-circle'; ?>" 
                                   style="color: <?php echo $hasPermission ? '#34d399' : '#f87171'; ?>;"></i>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                        <td style="padding: 0.75rem; font-weight: 500;">View Analytics</td>
                        <?php foreach ($roles as $role_key => $role): ?>
                            <td style="padding: 0.75rem; text-align: center;">
                                <?php 
                                $hasPermission = ($role_key === 'admin' || $role_key === 'engineer') ? true : false;
                                ?>
                                <i class="fas <?php echo $hasPermission ? 'fa-check-circle' : 'fa-times-circle'; ?>" 
                                   style="color: <?php echo $hasPermission ? '#34d399' : '#f87171'; ?>;"></i>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                        <td style="padding: 0.75rem; font-weight: 500;">Manage Calculators</td>
                        <?php foreach ($roles as $role_key => $role): ?>
                            <td style="padding: 0.75rem; text-align: center;">
                                <?php 
                                $hasPermission = ($role_key === 'admin') ? true : false;
                                ?>
                                <i class="fas <?php echo $hasPermission ? 'fa-check-circle' : 'fa-times-circle'; ?>" 
                                   style="color: <?php echo $hasPermission ? '#34d399' : '#f87171'; ?>;"></i>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td style="padding: 0.75rem; font-weight: 500;">Use Calculators</td>
                        <?php foreach ($roles as $role_key => $role): ?>
                            <td style="padding: 0.75rem; text-align: center;">
                                <?php 
                                $hasPermission = true; // All roles should have this
                                ?>
                                <i class="fas <?php echo $hasPermission ? 'fa-check-circle' : 'fa-times-circle'; ?>" 
                                   style="color: <?php echo $hasPermission ? '#34d399' : '#f87171'; ?>;"></i>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>