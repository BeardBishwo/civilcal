<?php
/**
 * BANNED USERS INTERFACE
 */

$users = $users ?? [];
$filters = $filters ?? [];
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">
        <!-- Compact Page Header -->
        <div class="compact-header" style="background: linear-gradient(135deg, #ef4444 0%, #991b1b 100%);">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-user-slash"></i>
                    <h1>Banned Users</h1>
                </div>
                <div class="header-subtitle"><?php echo count($users); ?> users currently restricted from access</div>
            </div>
            <div class="header-actions">
                <a href="<?php echo app_base_url('admin/users'); ?>" class="btn btn-compact" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                    <i class="fas fa-users"></i>
                    <span>All Users</span>
                </a>
            </div>
        </div>

        <!-- Compact Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <form action="" method="GET" class="search-compact" style="display: flex; gap: 0.5rem; max-width: 400px;">
                    <div style="position: relative; flex: 1;">
                        <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>" 
                               placeholder="Search banned users..." 
                               style="width: 100%; padding: 0.625rem 1rem 0.625rem 2.5rem; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.875rem;">
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Content Area -->
        <div class="pages-content">
            <div class="table-container">
                <?php if (empty($users)): ?>
                    <div class="empty-state-compact" style="padding: 4rem 2rem; text-align: center; color: #6b7280;">
                        <i class="fas fa-user-check" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                        <h3>No banned users</h3>
                        <p>Currently no users are restricted from the platform.</p>
                    </div>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="table-compact" style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">User</th>
                                    <th style="padding: 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Ban Reason</th>
                                    <th style="padding: 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Banned On</th>
                                    <th style="padding: 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr style="border-bottom: 1px solid #f3f4f6;">
                                        <td style="padding: 1.25rem 1rem;">
                                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                <div style="width: 36px; height: 36px; border-radius: 50%; background: #fee2e2; color: #b91c1c; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                                    <?php echo strtoupper(substr($user['username'] ?? '?', 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div style="font-weight: 600; color: #111827;"><?php echo htmlspecialchars($user['username'] ?? ''); ?></div>
                                                    <div style="font-size: 0.75rem; color: #6b7280;"><?php echo htmlspecialchars($user['email'] ?? ''); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding: 1.25rem 1rem;">
                                            <div style="font-size: 0.875rem; color: #374151; max-width: 300px;"><?php echo htmlspecialchars($user['ban_reason'] ?? 'No reason provided'); ?></div>
                                        </td>
                                        <td style="padding: 1.25rem 1rem;">
                                            <div style="font-size: 0.875rem; color: #374151;"><?php echo $user['banned_at'] ? date('M j, Y H:i', strtotime($user['banned_at'])) : 'Unknown'; ?></div>
                                        </td>
                                        <td style="padding: 1.25rem 1rem; text-align: center;">
                                            <button onclick="unbanUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username'] ?? ''); ?>')" 
                                                    class="btn" style="padding: 0.5rem 1rem; background: #10b981; color: white; border-radius: 6px; border: none; cursor: pointer; font-size: 0.875rem;">
                                                <i class="fas fa-undo"></i> Unban
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function unbanUser(userId, username) {
        if (!confirm(`Are you sure you want to unban user "${username}"?`)) return;
        
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        fetch(`<?php echo app_base_url('admin/users/'); ?>${userId}/unban`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ csrf_token: token })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('User unbanned successfully');
                location.reload();
            } else {
                alert(data.message || 'Failed to unban user');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while unbanning the user');
        });
    }
</script>

<style>
    .admin-wrapper-container { max-width: 1400px; margin: 0 auto; padding: 1rem; }
    .admin-content-wrapper { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden; }
    .compact-header { padding: 2rem; color: white; }
    .header-title { display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem; }
    .header-title h1 { margin: 0; font-size: 1.5rem; font-weight: 700; }
    .compact-toolbar { padding: 1rem 2rem; border-bottom: 1px solid #e5e7eb; background: #fdfdfd; }
    .btn { cursor: pointer; font-family: inherit; }
</style>
