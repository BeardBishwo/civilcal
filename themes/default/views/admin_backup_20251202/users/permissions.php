<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>User Permissions</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Manage and assign permissions to user roles</p>
        </div>
    </div>
</div>

<!-- Permissions Overview -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-key" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Permissions</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo count($permissions ?? []); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Defined</div>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-users" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">User Roles</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo count($permissions ?? []); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">With Permissions</div>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-shield-alt" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Security Level</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;">High</div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Protected</div>
    </div>
</div>

<!-- Permissions Matrix -->
<div class="admin-card">
    <h2 class="admin-card-title">Permission Matrix</h2>
    <div class="admin-card-content">
        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Permission</th>
                        <th style="text-align: center; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Admin</th>
                        <th style="text-align: center; padding: 0.75rem; color: #9ca3af; font-weight: 600;">User</th>
                        <th style="text-align: center; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Engineer</th>
                        <th style="text-align: center; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($permissions)): ?>
                        <?php foreach ($permissions as $permission => $roles): ?>
                            <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <td style="padding: 0.75rem; font-weight: 500;"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $permission))); ?></td>
                                
                                <td style="padding: 0.75rem; text-align: center;">
                                    <?php $hasAdmin = in_array('admin', $roles); ?>
                                    <i class="fas <?php echo $hasAdmin ? 'fa-check-circle' : 'fa-times-circle'; ?>" 
                                       style="color: <?php echo $hasAdmin ? '#34d399' : '#f87171'; ?>;"></i>
                                </td>
                                
                                <td style="padding: 0.75rem; text-align: center;">
                                    <?php $hasUser = in_array('user', $roles); ?>
                                    <i class="fas <?php echo $hasUser ? 'fa-check-circle' : 'fa-times-circle'; ?>" 
                                       style="color: <?php echo $hasUser ? '#34d399' : '#f87171'; ?>;"></i>
                                </td>
                                
                                <td style="padding: 0.75rem; text-align: center;">
                                    <?php $hasEngineer = in_array('engineer', $roles); ?>
                                    <i class="fas <?php echo $hasEngineer ? 'fa-check-circle' : 'fa-times-circle'; ?>" 
                                       style="color: <?php echo $hasEngineer ? '#34d399' : '#f87171'; ?>;"></i>
                                </td>
                                
                                <td style="padding: 0.75rem;">
                                    <a href="<?php echo app_base_url('/admin/users/permissions/'.($permission).'/edit'); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; text-decoration: none; color: #4cc9f0; font-size: 0.75rem;">
                                        <i class="fas fa-edit"></i>
                                        <span>Edit</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 1rem; color: #9ca3af;">No permissions defined</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Available Permissions -->
<div class="admin-card">
    <h2 class="admin-card-title">Available Permissions</h2>
    <div class="admin-card-content">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
                <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-user-shield" style="color: #4cc9f0;"></i>
                    User Management
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-check-circle" style="color: #34d399;"></i>
                        <span style="color: #f9fafb;">manage_users</span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-check-circle" style="color: #34d399;"></i>
                        <span style="color: #f9fafb;">view_users</span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-times-circle" style="color: #f87171;"></i>
                        <span style="color: #f9fafb;">delete_users</span>
                    </li>
                </ul>
            </div>
            
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
                <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-calculator" style="color: #34d399;"></i>
                    Calculator Access
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-check-circle" style="color: #34d399;"></i>
                        <span style="color: #f9fafb;">use_calculators</span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-check-circle" style="color: #34d399;"></i>
                        <span style="color: #f9fafb;">advanced_calculations</span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-times-circle" style="color: #f87171;"></i>
                        <span style="color: #f9fafb;">create_calculators</span>
                    </li>
                </ul>
            </div>
            
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
                <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-cogs" style="color: #fbbf24;"></i>
                    System Management
                </h3>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-check-circle" style="color: #34d399;"></i>
                        <span style="color: #f9fafb;">manage_system</span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-check-circle" style="color: #34d399;"></i>
                        <span style="color: #f9fafb;">view_analytics</span>
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-check-circle" style="color: #34d399;"></i>
                        <span style="color: #f9fafb;">manage_modules</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Permission Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Permission Management</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/users/permissions/create'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-plus-circle"></i>
            <span>Create Permission</span>
        </a>

        <a href="<?php echo app_base_url('/admin/users/roles'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-user-tag"></i>
            <span>Manage Roles</span>
        </a>

        <a href="<?php echo app_base_url('/admin/users/permissions/import'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-file-import"></i>
            <span>Import Permissions</span>
        </a>

        <a href="<?php echo app_base_url('/admin/users/permissions/export'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-file-export"></i>
            <span>Export Permissions</span>
        </a>
    </div>
</div>

<!-- Permission Assignment -->
<div class="admin-card">
    <h2 class="admin-card-title">Quick Role Assignment</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem;">Assign Permission to Role</h3>
            <form>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: #9ca3af; margin-bottom: 0.5rem;">Select Permission</label>
                    <select style="width: 100%; padding: 0.5rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                        <option value="">Select Permission</option>
                        <option value="manage_users">Manage Users</option>
                        <option value="view_analytics">View Analytics</option>
                        <option value="manage_calculators">Manage Calculators</option>
                        <option value="use_advanced_features">Use Advanced Features</option>
                    </select>
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: #9ca3af; margin-bottom: 0.5rem;">Select Role</label>
                    <select style="width: 100%; padding: 0.5rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                        <option value="">Select Role</option>
                        <option value="admin">Administrator</option>
                        <option value="user">Regular User</option>
                        <option value="engineer">Engineer</option>
                    </select>
                </div>
                <button type="submit" style="width: 100%; padding: 0.75rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer;">
                    <i class="fas fa-plus"></i>
                    <span>Assign Permission</span>
                </button>
            </form>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem;">Role Summary</h3>
            <div style="display: grid; gap: 0.75rem;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #9ca3af;">Admin Role:</span>
                    <span style="color: #f9fafb;"><?php echo count($permissions['admin'] ?? []); ?> permissions</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #9ca3af;">User Role:</span>
                    <span style="color: #f9fafb;"><?php echo count($permissions['user'] ?? []); ?> permissions</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #9ca3af;">Engineer Role:</span>
                    <span style="color: #f9fafb;"><?php echo count($permissions['engineer'] ?? []); ?> permissions</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #9ca3af;">Total Unique:</span>
                    <span style="color: #f9fafb;"><?php echo count(array_unique(array_merge($permissions['admin'] ?? [], $permissions['user'] ?? [], $permissions['engineer'] ?? []))); ?> permissions</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>