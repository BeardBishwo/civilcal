<?php
// Extract data for use in template
$page_title = $page_title ?? 'User Management';
$users = $users ?? [];
$stats = $stats ?? [];
?>

<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-users"></i>
            <?php echo htmlspecialchars($page_title); ?>
        </h1>
        <p class="page-description">Manage all system users and their access levels</p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon primary">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-value"><?php echo $stats['total'] ?? 0; ?></div>
            <div class="stat-label">Total Users</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon success">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
            <div class="stat-value"><?php echo $stats['active'] ?? 0; ?></div>
            <div class="stat-label">Active Users</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon warning">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
            <div class="stat-value"><?php echo $stats['admins'] ?? 0; ?></div>
            <div class="stat-label">Administrators</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon info">
                    <i class="fas fa-user"></i>
                </div>
            </div>
            <div class="stat-value"><?php echo $stats['regular'] ?? 0; ?></div>
            <div class="stat-label">Regular Users</div>
        </div>
    </div>

    <!-- Actions Bar -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-cog"></i>
                Quick Actions
            </div>
        </div>
        <div class="card-content">
            <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                <a href="/admin/users/create" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Add New User
                </a>
                <a href="/admin/users/bulk" class="btn btn-secondary">
                    <i class="fas fa-users-cog"></i>
                    Bulk Operations
                </a>
                <a href="/admin/users/roles" class="btn btn-secondary">
                    <i class="fas fa-user-tag"></i>
                    Manage Roles
                </a>
                <a href="/admin/users/permissions" class="btn btn-secondary">
                    <i class="fas fa-key"></i>
                    Permissions
                </a>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="table-container">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i>
                All Users (<?php echo count($users); ?>)
            </h3>
        </div>
        <div class="card-content">
            <?php if (empty($users)): ?>
                <div style="text-align: center; padding: 40px; color: var(--admin-gray-500);">
                    <i class="fas fa-users" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                    <p>No users found in the system</p>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--admin-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                                <?php echo strtoupper(substr($user['username'] ?? $user['email'], 0, 1)); ?>
                                            </div>
                                            <strong><?php echo htmlspecialchars($user['username'] ?? ''); ?></strong>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))); ?></td>
                                    <td>
                                        <span class="badge <?php echo ($user['role'] ?? '') === 'admin' ? 'badge-primary' : 'badge-secondary'; ?>">
                                            <?php echo htmlspecialchars($user['role'] ?? 'user'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (($user['is_active'] ?? 1)): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($user['created_at'] ?? 'now')); ?></td>
                                    <td>
                                        <div style="display: flex; gap: 8px;">
                                            <a href="/admin/users/<?php echo $user['id']; ?>/edit" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if (($user['role'] ?? '') !== 'admin'): ?>
                                                <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
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

<style>
.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
}

.badge-primary { background: var(--admin-primary); color: white; }
.badge-secondary { background: var(--admin-gray-200); color: var(--admin-gray-700); }
.badge-success { background: var(--admin-success); color: white; }
.badge-danger { background: var(--admin-danger); color: white; }
</style>

<script>
function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        // Implement delete functionality
        fetch(`/admin/users/${userId}/delete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?php echo $this->csrfToken(); ?>'
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('Error deleting user');
            }
        });
    }
}
</script>