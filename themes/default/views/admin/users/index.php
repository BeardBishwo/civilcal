<?php
ob_start();
?>

<div class="page-header" style="margin-bottom: 2rem;">
    <h1 style="font-size: 1.75rem; font-weight: 600; color: #f9fafb; margin: 0 0 0.5rem 0;">User Management</h1>
    <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Manage all registered users and their permissions</p>
</div>

<!-- Statistics Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.5rem; text-align: center;">
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Total Users</div>
    </div>
    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.5rem; text-align: center;">
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['active'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Active Users</div>
    </div>
    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.5rem; text-align: center;">
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($stats['admins'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Administrators</div>
    </div>
</div>

<!-- Users Table -->
<div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem;">
    <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0;">All Users</h5>
    
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                    <th style="padding: 0.75rem; text-align: left; color: #9ca3af; font-size: 0.875rem; font-weight: 600;">ID</th>
                    <th style="padding: 0.75rem; text-align: left; color: #9ca3af; font-size: 0.875rem; font-weight: 600;">Username</th>
                    <th style="padding: 0.75rem; text-align: left; color: #9ca3af; font-size: 0.875rem; font-weight: 600;">Email</th>
                    <th style="padding: 0.75rem; text-align: left; color: #9ca3af; font-size: 0.875rem; font-weight: 600;">Role</th>
                    <th style="padding: 0.75rem; text-align: left; color: #9ca3af; font-size: 0.875rem; font-weight: 600;">Registered</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                            <td style="padding: 0.75rem; color: #e5e7eb; font-size: 0.875rem;"><?php echo htmlspecialchars($user['id'] ?? ''); ?></td>
                            <td style="padding: 0.75rem; color: #e5e7eb; font-size: 0.875rem;"><?php echo htmlspecialchars($user['username'] ?? ''); ?></td>
                            <td style="padding: 0.75rem; color: #e5e7eb; font-size: 0.875rem;"><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
                            <td style="padding: 0.75rem; color: #e5e7eb; font-size: 0.875rem;">
                                <span style="padding: 0.25rem 0.75rem; background: <?php echo ($user['role'] ?? '') === 'admin' ? 'rgba(67, 97, 238, 0.2)' : 'rgba(156, 163, 175, 0.2)'; ?>; border-radius: 9999px; font-size: 0.75rem; color: <?php echo ($user['role'] ?? '') === 'admin' ? '#4cc9f0' : '#9ca3af'; ?>;">
                                    <?php echo htmlspecialchars(ucfirst($user['role'] ?? 'user')); ?>
                                </span>
                            </td>
                            <td style="padding: 0.75rem; color: #9ca3af; font-size: 0.875rem;"><?php echo isset($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : ''; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="padding: 2rem; text-align: center; color: #9ca3af;">No users found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
