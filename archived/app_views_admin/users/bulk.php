<?php

/**
 * Admin Bulk User Operations View
 * Path: app/Views/admin/users/bulk.php
 */
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">Bulk User Operations</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?= app_base_url('/admin/dashboard') ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?= app_base_url('/admin/users') ?>">Users</a></li>
                            <li class="breadcrumb-item active">Bulk Operations</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="<?= app_base_url('/admin/users') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stat-card border-start border-primary border-4">
                <div class="card-body">
                    <p class="stat-label mb-1">Total Users</p>
                    <h3 class="stat-number text-primary"><?= $stats['total'] ?? 0 ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card border-start border-success border-4">
                <div class="card-body">
                    <p class="stat-label mb-1">Active Users</p>
                    <h3 class="stat-number text-success"><?= $stats['active'] ?? 0 ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card border-start border-warning border-4">
                <div class="card-body">
                    <p class="stat-label mb-1">Inactive Users</p>
                    <h3 class="stat-number text-warning"><?= $stats['inactive'] ?? 0 ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions Panel -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Bulk Actions</h5>
        </div>
        <div class="card-body">
            <form id="bulkActionForm" method="POST" action="<?= app_base_url('/admin/users/bulk-action') ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <input type="hidden" name="selected_users" id="selectedUsersInput">

                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label for="bulkAction" class="form-label">Select Action</label>
                        <select class="form-select" id="bulkAction" name="action" required>
                            <option value="">Choose an action...</option>
                            <option value="activate">Activate Users</option>
                            <option value="deactivate">Deactivate Users</option>
                            <option value="change_role">Change Role</option>
                            <option value="delete">Delete Users</option>
                            <option value="export">Export Selected</option>
                        </select>
                    </div>
                    <div class="col-md-4" id="roleSelectContainer" style="display: none;">
                        <label for="newRole" class="form-label">New Role</label>
                        <select class="form-select" id="newRole" name="new_role">
                            <option value="user">Regular User</option>
                            <option value="engineer">Engineer</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary" id="applyBulkAction" disabled>
                            <i class="bi bi-check-circle me-2"></i>Apply to Selected (<span id="selectedCount">0</span>)
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">
                        <input type="checkbox" id="selectAll" class="form-check-input me-2">
                        Select All Users
                    </h5>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchUsers" placeholder="Search users...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="50">
                                <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                            </th>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                                    <p class="text-muted mb-0">No users found</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input user-checkbox"
                                            value="<?= $user['id'] ?>" data-user-id="<?= $user['id'] ?>">
                                    </td>
                                    <td>#<?= htmlspecialchars($user['id']) ?></td>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <?php
                                        $role = $user['role'] ?? 'user';
                                        $badgeClass = match ($role) {
                                            'admin' => 'bg-danger',
                                            'engineer' => 'bg-warning',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= ucfirst(htmlspecialchars($role)) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($user['is_active'] ?? true): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($user['created_at'] ?? 'now')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Select All functionality
    document.getElementById('selectAllCheckbox')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateSelectedCount();
    });

    // Update selected count
    function updateSelectedCount() {
        const selected = document.querySelectorAll('.user-checkbox:checked');
        const count = selected.length;
        document.getElementById('selectedCount').textContent = count;
        document.getElementById('applyBulkAction').disabled = count === 0;

        // Update hidden input with selected user IDs
        const userIds = Array.from(selected).map(cb => cb.value);
        document.getElementById('selectedUsersInput').value = JSON.stringify(userIds);
    }

    // Listen to checkbox changes
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    // Show/hide role select based on action
    document.getElementById('bulkAction')?.addEventListener('change', function() {
        const roleContainer = document.getElementById('roleSelectContainer');
        if (this.value === 'change_role') {
            roleContainer.style.display = 'block';
        } else {
            roleContainer.style.display = 'none';
        }
    });

    // Form submission with confirmation
    document.getElementById('bulkActionForm')?.addEventListener('submit', function(e) {
        const action = document.getElementById('bulkAction').value;
        const count = document.querySelectorAll('.user-checkbox:checked').length;

        if (action === 'delete') {
            if (!confirm(`Are you sure you want to delete ${count} user(s)? This action cannot be undone.`)) {
                e.preventDefault();
                return false;
            }
        } else {
            if (!confirm(`Apply this action to ${count} user(s)?`)) {
                e.preventDefault();
                return false;
            }
        }
    });

    // Search functionality
    document.getElementById('searchUsers')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
</script>