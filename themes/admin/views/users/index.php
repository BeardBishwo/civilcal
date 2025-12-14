<?php

/**
 * OPTIMIZED USERS MANAGEMENT INTERFACE
 * Compact, User-Friendly Layout with Enhanced Functionality
 */

// Extract data for use in template
$page_title = $page_title ?? 'User Management - Admin Panel';
$users = $users ?? [];
$stats = $stats ?? [];

// Calculate stats if not passed (though controller passes them)
$totalUsers = $stats['total'] ?? count($users);
$activeUsers = $stats['active'] ?? count(array_filter($users, fn($u) => $u['is_active'] ?? true));
$adminUsers = $stats['admins'] ?? count(array_filter($users, fn($u) => ($u['role'] ?? '') === 'admin'));
$regularUsers = $stats['regular'] ?? ($totalUsers - $adminUsers);

?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-users"></i>
                    <h1>Users</h1>
                </div>
                <div class="header-subtitle"><?php echo $totalUsers; ?> users • <?php echo $activeUsers; ?> active</div>
            </div>
            <div class="header-actions">
                <a href="<?php echo app_base_url('admin/users/create'); ?>" class="btn btn-primary btn-compact">
                    <i class="fas fa-plus"></i>
                    <span>New User</span>
                </a>
            </div>
        </div>

        <!-- Compact Stats Cards -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $totalUsers; ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $activeUsers; ?></div>
                    <div class="stat-label">Active Users</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $adminUsers; ?></div>
                    <div class="stat-label">Administrators</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-user"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $regularUsers; ?></div>
                    <div class="stat-label">Regular Users</div>
                </div>
            </div>
        </div>

        <!-- Compact Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search users..." id="page-search">
                    <button class="search-clear" id="search-clear" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <select id="status-filter" class="filter-compact">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <select id="role-filter" class="filter-compact">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
            <div class="toolbar-right">
                <div class="view-controls">
                    <button class="view-btn active" data-view="table" title="Table View">
                        <i class="fas fa-table"></i>
                    </button>
                    <button class="view-btn" data-view="grid" title="Grid View">
                        <i class="fas fa-th-large"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Users Content Area -->
        <div class="pages-content">

            <!-- Table View -->
            <div id="table-view" class="view-section active">
                <div class="table-container">
                    <?php if (empty($users)): ?>
                        <div class="empty-state-compact">
                            <i class="fas fa-users"></i>
                            <h3>No users found</h3>
                            <p>Create your first user to get started</p>
                            <a href="<?php echo app_base_url('admin/users/create'); ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Create User
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="table-compact">
                                <thead>
                                    <tr>
                                        <th class="col-checkbox">
                                            <input type="checkbox" id="select-all">
                                        </th>
                                        <th class="col-title">User</th>
                                        <th class="col-title">Email</th>
                                        <th class="col-status">Status</th>
                                        <th class="col-status">Role</th>
                                        <th class="col-date">Joined</th>
                                        <th class="col-actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr data-page-id="<?php echo $user['id']; ?>" class="page-row">
                                            <td>
                                                <input type="checkbox" class="page-checkbox" value="<?php echo $user['id']; ?>">
                                            </td>
                                            <td>
                                                <div class="user-info-compact" style="display:flex; align-items:center; gap:0.75rem;">
                                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--admin-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight:600;">
                                                        <?php echo strtoupper(substr($user['username'] ?? $user['email'], 0, 1)); ?>
                                                    </div>
                                                    <div class="page-info">
                                                        <div class="page-title-compact"><?php echo htmlspecialchars($user['username'] ?? ''); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="author-compact">
                                                    <?php echo htmlspecialchars($user['email'] ?? ''); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?php echo ($user['is_active'] ?? 1) ? 'active' : 'inactive'; ?>">
                                                    <i class="fas fa-<?php echo ($user['is_active'] ?? 1) ? 'check-circle' : 'ban'; ?>"></i>
                                                    <?php echo ($user['is_active'] ?? 1) ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                             <td>
                                                <span class="status-badge status-<?php echo ($user['role'] ?? 'user'); ?>">
                                                    <?php echo ucfirst($user['role'] ?? 'user'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="date-compact">
                                                    <?php echo date('M j, Y', strtotime($user['created_at'] ?? 'now')); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="actions-compact">
                                                    <a href="<?php echo app_base_url('/admin/users/' . $user['id'] . '/edit'); ?>"
                                                        class="action-btn-icon edit-btn"
                                                        title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?php if (($user['role'] ?? '') !== 'admin'): ?>
                                                    <button class="action-btn-icon delete-btn"
                                                        onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username'] ?? 'User'); ?>')"
                                                        title="Delete">
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

            <!-- Grid View -->
            <div id="grid-view" class="view-section">
                <div class="grid-container">
                     <?php if (empty($users)): ?>
                         <div class="empty-state-compact">
                            <i class="fas fa-users"></i>
                            <h3>No users found</h3>
                        </div>
                     <?php else: ?>
                        <div class="pages-grid-compact">
                            <?php foreach ($users as $user): ?>
                                <div class="page-card-compact page-row" data-page-id="<?php echo $user['id']; ?>">
                                    <div class="card-header-compact">
                                        <div class="card-status">
                                            <span class="status-badge status-<?php echo ($user['is_active'] ?? 1) ? 'active' : 'inactive'; ?>">
                                                <i class="fas fa-<?php echo ($user['is_active'] ?? 1) ? 'check-circle' : 'ban'; ?>"></i>
                                            </span>
                                        </div>
                                        <div class="card-actions">
                                             <?php if (($user['role'] ?? '') !== 'admin'): ?>
                                            <button class="action-btn-icon" onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username'] ?? 'User'); ?>')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="card-content-compact">
                                        <div style="display:flex; flex-direction:column; align-items:center; padding: 1rem 0;">
                                             <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--admin-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight:600; margin-bottom: 1rem;">
                                                <?php echo strtoupper(substr($user['username'] ?? $user['email'], 0, 1)); ?>
                                            </div>
                                            <h3 class="card-title-compact"><?php echo htmlspecialchars($user['username'] ?? ''); ?></h3>
                                            <div class="page-slug-compact"><?php echo htmlspecialchars($user['email'] ?? ''); ?></div>
                                        </div>
                                        
                                        <div class="card-meta-compact">
                                            <span class="meta-item">
                                                <i class="fas fa-shield-alt"></i>
                                                <?php echo ucfirst($user['role'] ?? 'user'); ?>
                                            </span>
                                            <span class="meta-item">
                                                <i class="fas fa-calendar"></i>
                                                <?php echo date('M j, Y', strtotime($user['created_at'] ?? 'now')); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-footer-compact">
                                        <a href="<?php echo app_base_url('/admin/users/' . $user['id'] . '/edit'); ?>" class="btn btn-sm btn-primary" style="width:100%; text-align:center;">
                                            <i class="fas fa-edit"></i>
                                            Edit
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Server-Side Pagination -->
        <?php if (($filters['total_pages'] ?? 1) > 1): ?>
            <div class="pagination-compact">
                <div class="pagination-info">
                    Showing <?php echo count($users); ?> of <?php echo $filters['total_records']; ?> users
                </div>
                <div class="pagination-controls">
                    <?php 
                        $currentPage = $filters['page'] ?? 1;
                        $totalPages = $filters['total_pages'] ?? 1;
                        
                        // Helper to build URL with current filters
                        function buildUrl($page, $filters) {
                            $params = [
                                'page' => $page,
                                'search' => $filters['search'],
                                'status' => $filters['status'],
                                'role' => $filters['role']
                            ];
                            return '?' . http_build_query(array_filter($params)); // filter empty
                        }
                    ?>
                    
                    <a href="<?php echo buildUrl($currentPage - 1, $filters); ?>" 
                       class="page-btn <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>"
                       <?php echo $currentPage <= 1 ? 'onclick="return false;" style="opacity:0.5; pointer-events:none;"' : ''; ?>>
                        <i class="fas fa-chevron-left"></i>
                    </a>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="<?php echo buildUrl($i, $filters); ?>" 
                           class="page-btn <?php echo $currentPage == $i ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <a href="<?php echo buildUrl($currentPage + 1, $filters); ?>" 
                       class="page-btn <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>"
                       <?php echo $currentPage >= $totalPages ? 'onclick="return false;" style="opacity:0.5; pointer-events:none;"' : ''; ?>>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Floating Bulk Actions Bar -->
<div id="bulk-actions-float" class="bulk-actions-float" style="display: none;">
    <div class="bulk-actions-content">
        <span class="selected-count"><span id="bulk-count">0</span> selected</span>
        <div class="bulk-buttons">
            <button class="btn btn-sm btn-danger" id="bulk-delete">
                <i class="fas fa-trash"></i>
                Delete
            </button>
        </div>
        <button class="bulk-close" onclick="clearBulkSelection()">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializePageManager();
        
        // Restore values from URL params (already set via PHP but JS fallback is good)
        const params = new URLSearchParams(window.location.search);
        if(params.has('search')) document.getElementById('page-search').value = params.get('search');
        if(params.has('status')) document.getElementById('status-filter').value = params.get('status');
        if(params.has('role')) document.getElementById('role-filter').value = params.get('role');
        
        const searchClear = document.getElementById('search-clear');
        if(document.getElementById('page-search').value) {
            searchClear.style.display = 'block';
        }
    });

    function initializePageManager() {
        // Search functionality
        const searchInput = document.getElementById('page-search');
        const searchClear = document.getElementById('search-clear');

        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchClear.style.display = this.value ? 'block' : 'none';
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 600); // 600ms Debounce for server request
        });
        
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                clearTimeout(searchTimeout);
                applyFilters();
            }
        });

        searchClear.addEventListener('click', function() {
            searchInput.value = '';
            applyFilters();
        });

        // Filter functionality
        document.getElementById('status-filter').addEventListener('change', function() {
            applyFilters();
        });
        
        document.getElementById('role-filter').addEventListener('change', function() {
            applyFilters();
        });

        // View toggle
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                switchView(this.dataset.view);
            });
        });
        
        // Restore View State
        const savedView = localStorage.getItem('users_view_preference') || 'table';
        switchView(savedView);

        // Bulk selection
        document.getElementById('select-all').addEventListener('change', function() {
            toggleAllSelection(this.checked);
        });

        document.getElementById('bulk-delete').addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.page-checkbox:checked');
            if (checkedBoxes.length === 0) return;

            showConfirmModal(
                'Bulk Delete Users',
                `Are you sure you want to delete <strong>${checkedBoxes.length} users</strong>? This action cannot be undone.`,
                () => {
                    const ids = Array.from(checkedBoxes).map(cb => cb.value);
                    
                    const token = getCsrfToken();
                    fetch('<?= app_base_url('/admin/users/bulk-delete') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': token,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ 
                            ids: ids,
                            csrf_token: token
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(`${ids.length} users deleted successfully`, 'success');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showNotification(data.message || 'Error deleting users', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('An error occurred while deleting users', 'error');
                    });
                }
            );
        });

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('page-checkbox')) {
                updateBulkActions();
            }
        });
    }

    function applyFilters() {
        const search = document.getElementById('page-search').value.trim();
        const status = document.getElementById('status-filter').value;
        const role = document.getElementById('role-filter').value;
        
        const params = new URLSearchParams();
        if (search) params.set('search', search);
        if (status) params.set('status', status);
        if (role) params.set('role', role);
        
        // Always reset to page 1 on new filter
        params.set('page', 1);

        window.location.search = params.toString();
    }

    function switchView(view) {
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.view === view);
        });

        document.querySelectorAll('.view-section').forEach(section => {
            section.classList.toggle('active', section.id === `${view}-view`);
        });
        
        localStorage.setItem('users_view_preference', view);
    }

    function toggleAllSelection(checked) {
        const checkboxes = document.querySelectorAll('.page-checkbox');
        checkboxes.forEach(cb => cb.checked = checked);
        updateBulkActions();
    }

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.page-checkbox:checked');
        const bulkBar = document.getElementById('bulk-actions-float');
        const bulkCount = document.getElementById('bulk-count');

        if (checkedBoxes.length > 0) {
            bulkBar.style.display = 'block';
            bulkCount.textContent = checkedBoxes.length;
            setTimeout(() => bulkBar.classList.add('visible'), 10);
        } else {
            bulkBar.classList.remove('visible');
            setTimeout(() => bulkBar.style.display = 'none', 300);
        }
    }

    function clearBulkSelection() {
        document.getElementById('select-all').checked = false;
        document.querySelectorAll('.page-checkbox').forEach(cb => cb.checked = false);
        updateBulkActions();
    }

    function updateResultsCount(count) {
        const infoElement = document.querySelector('.pagination-info');
        if (infoElement) {
            infoElement.textContent = `Showing ${count} users`;
        }
    }

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }



    function deleteUser(userId, userName) {
        showConfirmModal(
            'Delete User',
            `Are you sure you want to delete user "<strong>${userName}</strong>"? This action cannot be undone.`,
            () => {
                const token = getCsrfToken();
                fetch(`<?= app_base_url('/admin/users/') ?>${userId}/delete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        csrf_token: token
                    })
                })
                .then(async response => {
                    const data = await response.json().catch(() => ({}));
                    if (response.ok && data.success) {
                        showNotification('User deleted successfully', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        console.error('Delete failed:', data, response);
                        showNotification(data.message || 'Error deleting user', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while deleting the user', 'error');
                });
            }
        );
    }

    // Bulk delete functionality
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select_all');
        const userCheckboxes = document.querySelectorAll('.user-checkbox');
        const bulkDeleteBtn = document.getElementById('bulk_delete_btn');
        const selectedCount = document.querySelector('.selected-count');
        const bulkActions = document.querySelector('.bulk-actions');

        function updateBulkUI() {
            const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
            if (checkedCount > 0) {
                selectedCount.textContent = `${checkedCount} selected`;
                bulkActions.classList.add('show');
            } else {
                bulkActions.classList.remove('show');
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                userCheckboxes.forEach(cb => cb.checked = this.checked);
                updateBulkUI();
            });
        }

        userCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateBulkUI();
                if (!this.checked && selectAll) selectAll.checked = false;
            });
        });

        if (bulkDeleteBtn) {
            bulkDeleteBtn.addEventListener('click', function() {
                const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
                const ids = Array.from(checkedBoxes).map(cb => cb.value);

                if (ids.length === 0) return;

                showConfirmModal(
                    'Bulk Delete Users',
                    `Are you sure you want to delete <strong>${ids.length} users</strong>? This action cannot be undone.`,
                    () => {
                        const token = getCsrfToken();
                        fetch('<?= app_base_url('/admin/users/bulk-delete') ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-Token': token,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ 
                                ids: ids,
                                csrf_token: token
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showNotification(`${ids.length} users deleted successfully`, 'success');
                                setTimeout(() => location.reload(), 1000);
                            } else {
                                showNotification(data.message || 'Error deleting users', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('An error occurred while deleting users', 'error');
                        });
                    }
                );
            });
        }
    });
</script>

<style>


    /* ========================================
   OPTIMIZED ADMIN WRAPPER CONTAINER
   ======================================== */

    .admin-wrapper-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 1rem;
        background: var(--admin-gray-50, #f8f9fa);
        min-height: calc(100vh - 70px);
    }

    .admin-content-wrapper {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    /* ========================================
   COMPACT HEADER
   ======================================== */

    .compact-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .header-left {
        flex: 1;
    }

    .header-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.25rem;
    }

    .header-title h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
    }

    .header-title i {
        font-size: 1.5rem;
        opacity: 0.9;
    }

    .header-subtitle {
        font-size: 0.875rem;
        opacity: 0.8;
        margin: 0;
    }

    .header-actions {
        flex-shrink: 0;
    }

    .btn-compact {
        padding: 0.625rem 1.25rem;
        font-size: 0.875rem;
        border-radius: 8px;
        font-weight: 500;
    }

    /* ========================================
   COMPACT STATS
   ======================================== */

    .compact-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: var(--admin-gray-50, #f8f9fa);
        border-radius: 8px;
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        transition: all 0.2s ease;
    }

    .stat-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
    }

    .stat-icon.primary {
        background: #667eea;
    }

    .stat-icon.success {
        background: #48bb78;
    }

    .stat-icon.warning {
        background: #ed8936;
    }

    .stat-icon.info {
        background: #4299e1;
    }

    .stat-info {
        flex: 1;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--admin-gray-900, #1f2937);
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.75rem;
        color: var(--admin-gray-600, #6b7280);
        font-weight: 500;
    }

    /* ========================================
   COMPACT TOOLBAR
   ======================================== */

    .compact-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        gap: 1rem;
    }

    .toolbar-left {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex: 1;
    }

    .search-compact {
        position: relative;
        min-width: 250px;
        flex: 1;
        max-width: 350px;
    }

    .search-compact i {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--admin-gray-400, #9ca3af);
        font-size: 0.875rem;
    }

    .search-compact input {
        width: 100%;
        padding: 0.625rem 0.75rem 0.625rem 2.5rem;
        border: 1px solid var(--admin-gray-300, #d1d5db);
        border-radius: 6px;
        font-size: 0.875rem;
        background: white;
        transition: all 0.2s ease;
    }

    .search-compact input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .search-clear {
        position: absolute;
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--admin-gray-400, #9ca3af);
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .search-clear:hover {
        background: var(--admin-gray-100, #f3f4f6);
        color: var(--admin-gray-600, #6b7280);
    }

    .filter-compact {
        padding: 0.625rem 0.75rem;
        border: 1px solid var(--admin-gray-300, #d1d5db);
        border-radius: 6px;
        font-size: 0.875rem;
        background: white;
        min-width: 120px;
    }

    .toolbar-right {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .view-controls {
        display: flex;
        border: 1px solid var(--admin-gray-300, #d1d5db);
        border-radius: 6px;
        overflow: hidden;
    }

    .view-btn {
        padding: 0.625rem;
        border: none;
        background: white;
        color: var(--admin-gray-600, #6b7280);
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }

    .view-btn:hover {
        background: var(--admin-gray-50, #f8f9fa);
    }

    .view-btn.active {
        background: #667eea;
        color: white;
    }

    /* ========================================
   PAGES CONTENT
   ======================================== */

    .pages-content {
        min-height: 400px;
    }

    .view-section {
        display: none;
    }

    .view-section.active {
        display: block;
    }

    .table-container {
        padding: 0;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .table-compact {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .table-compact th {
        background: var(--admin-gray-50, #f8f9fa);
        padding: 0.75rem 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--admin-gray-700, #374151);
        border-bottom: 2px solid var(--admin-gray-200, #e5e7eb);
        white-space: nowrap;
    }

    .table-compact td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        vertical-align: middle;
    }

    .table-compact tbody tr:hover {
        background: var(--admin-gray-50, #f8f9fa);
    }

    .col-checkbox {
        width: 40px;
    }

    .col-title {
        min-width: 200px;
    }

    .col-status {
        width: 100px;
    }

    .col-author {
        width: 120px;
    }

    .col-date {
        width: 100px;
    }

    .col-views {
        width: 80px;
    }

    .col-actions {
        width: 180px;
    }

    /* Page Info */
    .page-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .page-title-compact {
        font-weight: 600;
        color: var(--admin-gray-900, #1f2937);
        line-height: 1.2;
    }

    .page-slug-compact {
        font-size: 0.75rem;
        color: var(--admin-gray-500, #6b7280);
        font-family: 'Monaco', 'Menlo', monospace;
    }

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .status-active, .status-published {
        background: rgba(72, 187, 120, 0.1);
        color: #48bb78;
    }

    .status-inactive, .status-draft {
        background: rgba(237, 137, 54, 0.1);
        color: #ed8936;
    }
    
    .status-admin {
         background: rgba(66, 153, 225, 0.1);
        color: #4299e1;
    }
    
    .status-user {
        background: rgba(160, 174, 192, 0.1);
        color: #718096;
    }

    /* Author */
    .author-compact {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        color: var(--admin-gray-700, #374151);
        font-size: 0.875rem;
    }

    /* Date */
    .date-compact {
        display: flex;
        flex-direction: column;
        gap: 0.125rem;
        font-size: 0.875rem;
    }

    .time-compact {
        font-size: 0.75rem;
        color: var(--admin-gray-500, #6b7280);
    }

    /* Views */
    .views-compact {
        font-weight: 600;
        color: var(--admin-gray-900, #1f2937);
        text-align: right;
    }

    /* Actions */
    .actions-compact {
        display: flex;
        gap: 0.25rem;
        justify-content: flex-end;
    }

    .action-btn-icon {
        width: 2rem;
        height: 2rem;
        border: 1px solid var(--admin-gray-300, #d1d5db);
        border-radius: 6px;
        background: white;
        color: var(--admin-gray-600, #6b7280);
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
    }

    .action-btn-icon:hover {
        transform: translateY(-1px);
    }

    .preview-btn:hover {
        background: #4299e1;
        color: white;
        border-color: #4299e1;
    }

    .edit-btn:hover {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .toggle-btn:hover {
        background: #ed8936;
        color: white;
        border-color: #ed8936;
    }

    .delete-btn:hover {
        background: #f56565;
        color: white;
        border-color: #f56565;
    }

    /* Grid View */
    .grid-container {
        padding: 1.5rem 2rem;
    }

    .pages-grid-compact {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
    }

    .page-card-compact {
        background: white;
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .page-card-compact:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .card-header-compact {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: var(--admin-gray-50, #f8f9fa);
    }

    .card-actions {
        display: flex;
        gap: 0.25rem;
    }

    .card-content-compact {
        padding: 1rem;
    }

    .card-title-compact {
        font-size: 1rem;
        font-weight: 600;
        color: var(--admin-gray-900, #1f2937);
        margin: 0 0 0.25rem 0;
        line-height: 1.3;
    }

    .card-meta-compact {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.75rem;
        color: var(--admin-gray-600, #6b7280);
    }

    .card-footer-compact {
        padding: 0.75rem 1rem;
        border-top: 1px solid var(--admin-gray-200, #e5e7eb);
        background: var(--admin-gray-50, #f8f9fa);
        display: flex;
        gap: 0.5rem;
    }

    /* Empty State */
    .empty-state-compact {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-state-compact i {
        font-size: 3rem;
        color: var(--admin-gray-400, #9ca3af);
        margin-bottom: 1rem;
    }

    .empty-state-compact h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--admin-gray-900, #1f2937);
        margin: 0 0 0.5rem 0;
    }

    .empty-state-compact p {
        color: var(--admin-gray-600, #6b7280);
        margin: 0 0 1.5rem 0;
    }

    /* Pagination */
    .pagination-compact {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem;
        border-top: 1px solid var(--admin-gray-200, #e5e7eb);
    }

    .pagination-info {
        font-size: 0.875rem;
        color: var(--admin-gray-600, #6b7280);
    }

    .pagination-controls {
        display: flex;
        gap: 0.25rem;
    }

    .page-btn {
        padding: 0.5rem 0.75rem;
        border: 1px solid var(--admin-gray-300, #d1d5db);
        background: white;
        color: var(--admin-gray-600, #6b7280);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }

    .page-btn:hover:not(:disabled) {
        background: var(--admin-gray-50, #f8f9fa);
        border-color: #667eea;
        color: #667eea;
    }

    .page-btn.active {
        background: #667eea;
        border-color: #667eea;
        color: white;
    }

    .page-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Bulk Actions Float */
    .bulk-actions-float {
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%);
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .bulk-actions-float.visible {
        opacity: 1;
        visibility: visible;
    }

    .bulk-actions-content {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.5rem;
    }

    .selected-count {
        font-weight: 500;
        color: var(--admin-gray-700, #374151);
    }

    .bulk-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .bulk-close {
        width: 2rem;
        height: 2rem;
        border: 1px solid var(--admin-gray-300, #d1d5db);
        border-radius: 6px;
        background: white;
        color: var(--admin-gray-600, #6b7280);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .bulk-close:hover {
        background: var(--admin-gray-50, #f8f9fa);
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .admin-wrapper-container { padding: 0.5rem; }
        .compact-header { padding: 1rem 1.5rem; flex-direction: column; align-items: stretch; gap: 1rem; }
        .toolbar-left { flex-direction: column; align-items: stretch; gap: 0.75rem; }
        .search-compact { min-width: auto; max-width: none; }
    }
</style>