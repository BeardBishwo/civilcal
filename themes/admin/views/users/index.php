<?php
/**
 * PREMIUM USER MANAGEMENT
 * High-density layout for user administration.
 * Features: Server-side Search, Filtering, Pagination, Bulk Actions, and Grid/Table Views.
 */

// Helper to preserve filters in links
function get_filter_params($extras = []) {
    $params = $_GET;
    return http_build_query(array_merge($params, $extras));
}

$totalUsers = $stats['total'] ?? count($users);
$activeUsers = $stats['active'] ?? count(array_filter($users, fn($u) => $u['is_active'] ?? 1));
$adminUsers = $stats['admins'] ?? count(array_filter($users, fn($u) => ($u['role'] ?? '') === 'admin'));

// Pagination Data
$currentPage = $filters['page'] ?? 1;
$totalPages = $filters['total_pages'] ?? 1;
$totalRecords = $filters['total_records'] ?? count($users);
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-users"></i>
                    <h1>User Management</h1>
                </div>
                <div class="header-subtitle"><?php echo $totalRecords; ?> Total Users • <?php echo $activeUsers; ?> Active</div>
            </div>
            
            <div class="header-actions" style="display:flex; gap:10px; align-items:center;">
                <!-- Header Stats -->
                <div class="stat-pill">
                    <span class="label">ACTIVE</span>
                    <span class="value"><?php echo $activeUsers; ?></span>
                </div>
                <div class="stat-pill warning">
                    <span class="label">ADMINS</span>
                    <span class="value"><?php echo $adminUsers; ?></span>
                </div>
                
                <div style="width:1px; height:24px; background:rgba(255,255,255,0.2); margin:0 8px;"></div>

                <a href="<?php echo app_base_url('admin/users/create'); ?>" class="btn btn-primary btn-compact" style="background:white; color:var(--admin-primary); text-decoration:none;">
                    <i class="fas fa-plus"></i>
                    <span>New User</span>
                </a>
            </div>
        </div>

        <!-- Filter & Toolbar -->
        <div class="compact-toolbar">
            <form id="filterForm" method="GET" class="toolbar-left" style="display:flex; gap:10px; flex:1; width:100%;">
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="Search by name, email..." onchange="this.form.submit()">
                </div>
                <select name="role" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Roles</option>
                    <option value="admin" <?php echo ($_GET['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="user" <?php echo ($_GET['role'] ?? '') === 'user' ? 'selected' : ''; ?>>User</option>
                </select>
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" <?php echo ($_GET['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($_GET['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </form>
            
            <div class="toolbar-right" style="display:flex; gap:8px;">
                 <button id="bulkDeleteTrigger" class="btn-danger-compact" style="display:none;" onclick="confirmBulkDelete()">
                    <i class="fas fa-trash"></i> Delete (<span id="selectedCount">0</span>)
                </button>
                
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

        <!-- Content Area -->
        <div class="pages-content">
            
            <!-- Table View -->
            <div id="table-view" class="view-section active">
                <div class="table-container">
                    <div class="table-wrapper">
                        <table class="table-compact">
                            <thead>
                                <tr>
                                    <th class="col-checkbox" style="width: 40px; text-align:center;">
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th>User Profile</th>
                                    <th>Email</th>
                                    <th class="text-center" style="width: 120px;">Role</th>
                                    <th class="text-center" style="width: 120px;">Status</th>
                                    <th class="text-center" style="width: 150px;">Joined</th>
                                    <th class="text-center" style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr><td colspan="7" class="empty-cell">
                                        <div class="empty-state-compact">
                                            <i class="fas fa-users"></i>
                                            <h3>No users found</h3>
                                            <p>Try adjusting your search filters.</p>
                                        </div>
                                    </td></tr>
                                <?php else: ?>
                                    <?php foreach ($users as $user): 
                                        $isAdmin = ($user['role'] ?? '') === 'admin';
                                        $isActive = $user['is_active'] ?? 1;
                                        $initial = strtoupper(substr($user['username'] ?? $user['email'] ?? 'U', 0, 1));
                                    ?>
                                        <tr class="user-item group page-row">
                                            <td class="text-center">
                                                <input type="checkbox" class="user-checkbox" value="<?php echo $user['id']; ?>">
                                            </td>
                                            <td>
                                                <div class="item-info">
                                                    <div class="item-icon-circle" style="background: <?php echo $isAdmin ? '#f3e8ff' : '#eff6ff'; ?>; color: <?php echo $isAdmin ? '#9333ea' : '#3b82f6'; ?>;">
                                                        <?php echo $initial; ?>
                                                    </div>
                                                    <div class="item-text">
                                                        <div class="item-title">
                                                            <?php echo htmlspecialchars($user['username'] ?? 'Unknown'); ?>
                                                        </div>
                                                        <div class="item-slug" style="font-size:0.7rem;">ID: <?php echo $user['id']; ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-slate-600 font-medium" style="font-size:0.85rem;">
                                                <?php echo htmlspecialchars($user['email'] ?? ''); ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge-pill" style="<?php echo $isAdmin ? 'background:#f3e8ff; color:#9333ea; border-color:#e9d5ff;' : 'background:#f1f5f9; color:#64748b; border-color:#e2e8f0;'; ?>">
                                                    <?php echo ucfirst($user['role'] ?? 'user'); ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="status-dot <?php echo $isActive ? 'success' : 'secondary'; ?>">
                                                    <?php echo $isActive ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span style="font-size:0.75rem; color:#94a3b8; font-weight:500;">
                                                    <?php echo date('M j, Y', strtotime($user['created_at'] ?? 'now')); ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="actions-compact justify-center">
                                                    <a href="<?php echo app_base_url('/admin/users/' . $user['id'] . '/edit'); ?>" class="action-btn-icon" title="Edit Profile" style="color: #6366f1;">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?php if (!$isAdmin): ?>
                                                        <button class="action-btn-icon" onclick="openBanModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username'] ?? 'User'); ?>')" title="Ban User" style="color: #f59e0b;">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                        <button class="action-btn-icon" onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username'] ?? 'User'); ?>')" title="Delete" style="color: #ef4444;">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Grid View -->
            <div id="grid-view" class="view-section">
                <?php if (empty($users)): ?>
                    <div class="empty-state-compact">
                         <i class="fas fa-users"></i>
                         <h3>No users found</h3>
                    </div>
                <?php else: ?>
                    <div class="grid-container">
                        <?php foreach ($users as $user): 
                            $isAdmin = ($user['role'] ?? '') === 'admin';
                            $isActive = $user['is_active'] ?? 1;
                            $initial = strtoupper(substr($user['username'] ?? $user['email'] ?? 'U', 0, 1));
                        ?>
                            <div class="user-card">
                                <div class="card-header-user">
                                    <div class="user-avatar-lg" style="background: <?php echo $isAdmin ? '#f3e8ff' : '#eff6ff'; ?>; color: <?php echo $isAdmin ? '#9333ea' : '#3b82f6'; ?>;">
                                        <?php echo $initial; ?>
                                    </div>
                                    <div class="user-card-actions">
                                        <a href="<?php echo app_base_url('/admin/users/' . $user['id'] . '/edit'); ?>" class="card-action-btn" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if (!$isAdmin): ?>
                                            <button class="card-action-btn danger" onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username'] ?? 'User'); ?>')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-body-user">
                                    <h3 class="user-name"><?php echo htmlspecialchars($user['username'] ?? 'Unknown'); ?></h3>
                                    <p class="user-email"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
                                    
                                    <div class="user-meta-badges">
                                        <span class="badge-pill" style="font-size:0.7rem; <?php echo $isAdmin ? 'background:#f3e8ff; color:#9333ea;' : 'background:#f1f5f9; color:#64748b;'; ?>">
                                            <?php echo ucfirst($user['role'] ?? 'user'); ?>
                                        </span>
                                        <span class="status-dot <?php echo $isActive ? 'success' : 'secondary'; ?>">
                                            <?php echo $isActive ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </div>
                                    
                                    <div class="user-joined">
                                        Joined <?php echo date('M j, Y', strtotime($user['created_at'] ?? 'now')); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Server-Side Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination-compact">
                    <div class="pagination-info">
                        Showing <?php echo count($users); ?> of <?php echo $totalRecords; ?> entries
                    </div>
                    <div class="pagination-controls">
                        <a href="?<?php echo get_filter_params(['page' => max(1, $currentPage - 1)]); ?>" 
                           class="page-btn <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>

                        <?php for($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i == 1 || $i == $totalPages || ($i >= $currentPage - 2 && $i <= $currentPage + 2)): ?>
                                <a href="?<?php echo get_filter_params(['page' => $i]); ?>" 
                                   class="page-btn <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php elseif ($i == $currentPage - 3 || $i == $currentPage + 3): ?>
                                <span class="page-dots">...</span>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <a href="?<?php echo get_filter_params(['page' => min($totalPages, $currentPage + 1)]); ?>" 
                           class="page-btn <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- SweetAlert2 Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // View Toggle
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            
            // Toggle Buttons
            document.querySelectorAll('.view-btn').forEach(b => b.classList.toggle('active', b.dataset.view === view));
            
            // Toggle Content
            document.querySelectorAll('.view-section').forEach(s => s.classList.toggle('active', s.id === `${view}-view`));
            
            // Save Preference
            localStorage.setItem('users_view_preference', view);
        });
    });
    
    // Restore View
    const savedView = localStorage.getItem('users_view_preference') || 'table';
    document.querySelector(`.view-btn[data-view="${savedView}"]`)?.click();


    // Search Debounce
    let timeout = null;
    document.querySelector('input[name="search"]').addEventListener('keyup', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            this.form.submit();
        }, 800);
    });

    // Bulk Selection (Table Only)
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.user-checkbox');
    const bulkBtn = document.getElementById('bulkDeleteTrigger');
    const selectedCountSpan = document.getElementById('selectedCount');

    function updateBulkState() {
        const checked = document.querySelectorAll('.user-checkbox:checked');
        if(checked.length > 0) {
            bulkBtn.style.display = 'inline-flex';
            selectedCountSpan.innerText = checked.length;
        } else {
            bulkBtn.style.display = 'none';
        }
    }

    if(selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkState();
        });
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateBulkState));

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    // Actions
    function deleteUser(id, name) {
        Swal.fire({
            title: `Delete ${name}?`,
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, Delete',
        }).then((result) => {
            if (result.isConfirmed) {
                const token = getCsrfToken();
                fetch(`<?= app_base_url('/admin/users/') ?>${id}/delete`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': token },
                    body: JSON.stringify({ csrf_token: token })
                })
                .then(r => r.json())
                .then(d => {
                    if(d.success) Swal.fire({ icon:'success', title:'Deleted', timer:1000, showConfirmButton:false }).then(() => location.reload());
                    else Swal.fire('Error', d.message || 'Failed', 'error');
                });
            }
        });
    }

    function openBanModal(id, name) {
        Swal.fire({
            title: `Ban ${name}`,
            input: 'textarea',
            inputLabel: 'Reason',
            inputPlaceholder: 'Brief reason for ban...',
            showCancelButton: true,
            confirmButtonText: 'Ban User',
            confirmButtonColor: '#f59e0b',
            preConfirm: (reason) => {
                if(!reason) return Swal.showValidationMessage('Reason required');
                const token = getCsrfToken();
                return fetch(`<?= app_base_url('/admin/users/') ?>${id}/ban`, {
                     method: 'POST',
                     headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-Token': token },
                     body: `reason=${encodeURIComponent(reason)}&csrf_token=${token}`
                }).then(r => r.json())
            }
        }).then((result) => {
            if(result.isConfirmed && result.value.success) location.reload();
            else if(result.isConfirmed) Swal.fire('Error', 'Failed', 'error');
        });
    }

    function confirmBulkDelete() {
        const checked = document.querySelectorAll('.user-checkbox:checked');
        const ids = Array.from(checked).map(cb => parseInt(cb.value));
        if(ids.length === 0) return;

        Swal.fire({
            title: `Delete ${ids.length} Users?`,
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Delete All'
        }).then((result) => {
            if(result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Processing...',
                    text: `Deleting ${ids.length} users...`,
                    didOpen: () => { Swal.showLoading(); },
                    allowOutsideClick: false
                });

                const token = getCsrfToken();
                fetch('<?= app_base_url('/admin/users/bulk-delete') ?>', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-Token': token,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ ids: ids, csrf_token: token })
                })
                .then(r => {
                    if (!r.ok) {
                        throw new Error(`HTTP error! status: ${r.status}`);
                    }
                    return r.json();
                })
                .then(d => {
                    if(d.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: d.message || `Deleted ${ids.length} users successfully`,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('Error', d.message || 'Failed to delete users', 'error');
                    }
                })
                .catch(err => {
                    console.error('Bulk Delete Error:', err);
                    Swal.fire('System Error', 'Could not connect to the server. Please check console for details.', 'error');
                });
            }
        });
    }
</script>

<style>
/* ========================================
   PREMIUM CORE STYLES (Restored)
   ======================================== */
:root {
    --admin-primary: #667eea;
    --admin-secondary: #764ba2;
    --admin-gray-50: #f8f9fa;
}

.admin-wrapper-container { padding: 1rem; background: var(--admin-gray-50); min-height: calc(100vh - 70px); }
.admin-content-wrapper { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; /* padding-bottom: 2rem; REMOVED FOR CLEANER UI */ }

/* Header */
.compact-header { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%); color: white; }
.header-title { display: flex; align-items: center; gap: 0.75rem; }
.header-title h1 { margin: 0; font-size: 1.5rem; font-weight: 700; color: white; }
.header-title i { font-size: 1.25rem; opacity: 0.9; }
.header-subtitle { font-size: 0.85rem; opacity: 0.8; margin-top: 4px; font-weight: 500; }

.stat-pill { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; padding: 0.5rem 1rem; display: flex; flex-direction: column; align-items: center; min-width: 80px; }
.stat-pill.warning { background: rgba(252, 211, 77, 0.15); border-color: rgba(252, 211, 77, 0.3); }
.stat-pill .label { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px; opacity: 0.9; }
.stat-pill .value { font-size: 1.1rem; font-weight: 800; line-height: 1.1; }

.compact-toolbar { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 2rem; background: #eff6ff; border-bottom: 1px solid #bfdbfe; }
.toolbar-left { display: flex; gap: 10px; align-items: center; flex: 1; }

.search-compact { position: relative; width: 100%; max-width: 300px; }
.search-compact i { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 0.85rem; }
.search-compact input { width: 100%; height: 36px; padding: 0 0.75rem 0 2.25rem; font-size: 0.85rem; border: 1px solid #bfdbfe; border-radius: 6px; outline: none; background: white; color: #1e40af; }

.filter-select { height: 36px; border: 1px solid #bfdbfe; border-radius: 6px; padding: 0 1rem; color: #1e40af; outline: none; background: white; font-size: 0.85rem; font-weight: 600; cursor: pointer; }

.btn-danger-compact { height: 36px; padding: 0 1rem; background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; border-radius: 6px; font-weight: 600; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: 0.2s; white-space: nowrap;}
.btn-danger-compact:hover { background: #fee2e2; border-color: #ef4444; transform: translateY(-1px); }

/* View Controls */
.view-controls { display: flex; border: 1px solid #bfdbfe; border-radius: 6px; overflow: hidden; }
.view-btn { padding: 0 12px; height: 36px; border: none; background: white; color: #1e40af; cursor: pointer; transition: 0.2s; font-size: 0.9rem; }
.view-btn:hover { background: #eff6ff; }
.view-btn.active { background: #667eea; color: white; }
.view-section { display: none; }
.view-section.active { display: block; }

.table-compact { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
.table-compact th { background: white; padding: 0.75rem 1.5rem; text-align: left; font-weight: 600; color: #94a3b8; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; }
.table-compact td { padding: 0.6rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.user-item:hover { background: #f8fafc; }

.item-info { display: flex; align-items: center; gap: 0.75rem; }
.item-icon-circle { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; }
.item-title { font-weight: 600; color: #334155; line-height: 1.2; }

.badge-pill { background: #e0e7ff; color: #4338ca; padding: 2px 10px; border-radius: 12px; font-size: 0.7rem; font-weight: 700; border: 1px solid #c7d2fe; white-space: nowrap; margin-right: 4px; }
.status-dot { font-size: 0.7rem; font-weight: 700; padding: 2px 8px; border-radius: 10px; display: inline-flex; align-items: center; gap: 4px; }
.status-dot.success { background: #dcfce7; color: #166534; }
.status-dot.secondary { background: #f1f5f9; color: #64748b; }

.action-btn-icon { width: 32px; height: 32px; border: 1px solid #e2e8f0; border-radius: 6px; background: white; color: #94a3b8; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; margin: 0 2px; }
.action-btn-icon:hover { transform: translateY(-1px); border-color: #cbd5e1; }

.empty-state-compact { padding: 3rem; text-align: center; color: #94a3b8; }
.empty-state-compact i { font-size: 2.5rem; margin-bottom: 0.5rem; opacity: 0.5; }
.empty-state-compact h3 { font-size: 1rem; font-weight: 600; color: #64748b; margin: 0; }

.pagination-compact { display: flex; justify-content: space-between; align-items: center; padding: 1rem 2rem; border-top: 1px solid #e2e8f0; }
.pagination-info { font-size: 0.8rem; color: #64748b; }
.pagination-controls { display: flex; gap: 4px; }
.page-btn { min-width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: 1px solid #e2e8f0; border-radius: 6px; background: white; color: #64748b; font-size: 0.8rem; cursor: pointer; text-decoration: none; }
.page-btn.active { background: #4f46e5; color: white; border-color: #4f46e5; }
.page-btn.disabled { opacity: 0.5; pointer-events: none; }
.page-dots { display: flex; align-items: center; padding: 0 8px; color: #94a3b8; }

/* Grid View Styles */
.grid-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; padding: 1.5rem 2rem; }
.user-card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; transition: 0.2s; }
.user-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
.card-header-user { display: flex; justify-content: space-between; align-items: flex-start; padding: 1.25rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; }
.user-avatar-lg { width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
.user-card-actions { display: flex; gap: 4px; }
.card-action-btn { width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 6px; color: #94a3b8; background: white; border: 1px solid #e2e8f0; transition: 0.2s; cursor: pointer; }
.card-action-btn:hover { color: #6366f1; border-color: #c7d2fe; }
.card-action-btn.danger:hover { color: #ef4444; border-color: #fecaca; }

.card-body-user { padding: 1.25rem; text-align: center; }
.user-name { font-size: 1.1rem; font-weight: 700; color: #1e293b; margin: 0 0 4px 0; }
.user-email { font-size: 0.85rem; color: #64748b; margin-bottom: 1rem; word-break: break-all; }
.user-meta-badges { display: flex; justify-content: center; gap: 8px; margin-bottom: 1rem; }
.user-joined { font-size: 0.75rem; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 0.75rem; margin-top: 0.5rem; }
</style>