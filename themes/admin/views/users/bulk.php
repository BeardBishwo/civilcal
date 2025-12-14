<?php
$page_title = $page_title ?? 'Bulk User Operations';
$users = $users ?? [];
$stats = $stats ?? [];
?>

<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-users-cog"></i>
            <?php echo htmlspecialchars($page_title); ?>
        </h1>
        <p class="page-description">Perform bulk operations on multiple users</p>
    </div>

    <!-- Stats Overview -->
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
                <div class="stat-icon danger">
                    <i class="fas fa-user-times"></i>
                </div>
            </div>
            <div class="stat-value"><?php echo $stats['inactive'] ?? 0; ?></div>
            <div class="stat-label">Inactive Users</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon info">
                    <i class="fas fa-tasks"></i>
                </div>
            </div>
            <div class="stat-value">0</div>
            <div class="stat-label">Selected</div>
        </div>
    </div>

    <!-- Bulk Operations -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-cogs"></i>
                Bulk Operations
            </h3>
        </div>
        <div class="card-content">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                <!-- Activate Users -->
                <div class="operation-panel">
                    <h4 style="margin-bottom: 16px; color: var(--admin-success);">
                        <i class="fas fa-play"></i>
                        Activate Users
                    </h4>
                    <p style="margin-bottom: 16px; color: var(--admin-gray-600);">
                        Activate selected inactive users
                    </p>
                    <button class="btn btn-success" onclick="bulkOperation('activate')">
                        <i class="fas fa-user-check"></i>
                        Activate Selected
                    </button>
                </div>
                
                <!-- Deactivate Users -->
                <div class="operation-panel">
                    <h4 style="margin-bottom: 16px; color: var(--admin-warning);">
                        <i class="fas fa-pause"></i>
                        Deactivate Users
                    </h4>
                    <p style="margin-bottom: 16px; color: var(--admin-gray-600);">
                        Deactivate selected users (they can log back in)
                    </p>
                    <button class="btn btn-warning" onclick="bulkOperation('deactivate')">
                        <i class="fas fa-user-times"></i>
                        Deactivate Selected
                    </button>
                </div>
                
                <!-- Delete Users -->
                <div class="operation-panel">
                    <h4 style="margin-bottom: 16px; color: var(--admin-danger);">
                        <i class="fas fa-trash"></i>
                        Delete Users
                    </h4>
                    <p style="margin-bottom: 16px; color: var(--admin-gray-600);">
                        Permanently delete selected users (cannot be undone)
                    </p>
                    <button class="btn btn-danger" onclick="bulkOperation('delete')">
                        <i class="fas fa-trash"></i>
                        Delete Selected
                    </button>
                </div>
                
                <!-- Export Users -->
                <div class="operation-panel">
                    <h4 style="margin-bottom: 16px; color: var(--admin-info);">
                        <i class="fas fa-download"></i>
                        Export Users
                    </h4>
                    <p style="margin-bottom: 16px; color: var(--admin-gray-600);">
                        Export selected user data to CSV
                    </p>
                    <button class="btn btn-primary" onclick="bulkOperation('export')">
                        <i class="fas fa-file-export"></i>
                        Export Selected
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Selection Table -->
    <div class="table-container">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-table"></i>
                Select Users for Bulk Operations
            </h3>
            <div class="card-actions">
                <button class="btn btn-sm btn-secondary" onclick="toggleSelectAll()">
                    <i class="fas fa-check-square"></i>
                    Select All
                </button>
                <button class="btn btn-sm btn-secondary" onclick="clearSelection()">
                    <i class="fas fa-square"></i>
                    Clear Selection
                </button>
            </div>
        </div>
        <div class="card-content">
            <?php if (empty($users)): ?>
                <div style="text-align: center; padding: 40px; color: var(--admin-gray-500);">
                    <i class="fas fa-users" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                    <p>No users found for bulk operations</p>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Last Activity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="user-checkbox" value="<?php echo $user['id']; ?>" onchange="updateSelectionCount()">
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--admin-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                                <?php echo strtoupper(substr($user['username'] ?? $user['email'], 0, 1)); ?>
                                            </div>
                                            <strong><?php echo htmlspecialchars($user['username'] ?? ''); ?></strong>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
                                    <td>
                                        <span class="badge <?php echo ($user['role'] ?? '') === 'admin' ? 'badge-warning' : 'badge-primary'; ?>">
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
                                        <span style="color: var(--admin-gray-500); font-size: 12px;">
                                            Recently
                                        </span>
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
.operation-panel {
    padding: 20px;
    border: 2px solid var(--admin-gray-200);
    border-radius: 8px;
    background: var(--admin-gray-50);
}

.operation-panel:hover {
    border-color: var(--admin-primary);
    background: white;
}

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
let selectedCount = 0;

function updateSelectionCount() {
    const checkboxes = document.querySelectorAll('.user-checkbox:checked');
    selectedCount = checkboxes.length;
    document.querySelector('.stats-grid .stat-card:last-child .stat-value').textContent = selectedCount;
}

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    
    userCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateSelectionCount();
}

function clearSelection() {
    document.getElementById('selectAll').checked = false;
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    updateSelectionCount();
}

function bulkOperation(operation) {
    const selectedUsers = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
    
    if (selectedUsers.length === 0) {
        showNotification('Please select at least one user for bulk operations.', 'warning');
        return;
    }
    
    const confirmMessages = {
        'activate': `Are you sure you want to activate ${selectedUsers.length} users?`,
        'deactivate': `Are you sure you want to deactivate ${selectedUsers.length} users?`,
        'delete': `Are you sure you want to permanently delete ${selectedUsers.length} users? This action cannot be undone!`,
        'export': `Are you sure you want to export data for ${selectedUsers.length} users?`
    };
    
    showConfirmModal('Bulk Operation', confirmMessages[operation], () => {
        // Implement bulk operation logic here
        console.log(`Bulk ${operation} operation for users:`, selectedUsers);
        showNotification(`${operation.charAt(0).toUpperCase() + operation.slice(1)} operation simulated on ${selectedUsers.length} users.`, 'info');
    });
}
</script>