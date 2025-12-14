<?php
$notifications = $notifications ?? [];
$totalCount = $totalCount ?? 0;
$currentPage = $currentPage ?? 1;
$perPage = $perPage ?? 20;
$totalPages = ceil($totalCount / $perPage);
?>

<div class="admin-content">
    <div class="content-header">
        <div>
            <h1>Notification History</h1>
            <p>View and manage all your notifications</p>
        </div>
        <div class="header-actions">
            <button id="bulkMarkRead" class="btn btn-primary" disabled>
                <i class="fas fa-check-double"></i> Mark Selected as Read
            </button>
            <button id="bulkDelete" class="btn btn-danger" disabled>
                <i class="fas fa-trash"></i> Delete Selected
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-container">
        <div class="filter-group">
            <label>Type</label>
            <select id="filterType" class="form-select">
                <option value="">All Types</option>
                <option value="system">System</option>
                <option value="user_action">User Action</option>
                <option value="email">Email</option>
                <option value="alert">Alert</option>
                <option value="info">Info</option>
            </select>
        </div>

        <div class="filter-group">
            <label>Status</label>
            <select id="filterStatus" class="form-select">
                <option value="">All</option>
                <option value="unread">Unread</option>
                <option value="read">Read</option>
            </select>
        </div>

        <div class="filter-group">
            <label>Priority</label>
            <select id="filterPriority" class="form-select">
                <option value="">All Priorities</option>
                <option value="urgent">Urgent</option>
                <option value="high">High</option>
                <option value="normal">Normal</option>
                <option value="low">Low</option>
            </select>
        </div>

        <div class="filter-group">
            <label>Search</label>
            <input type="text" id="searchQuery" class="form-input" placeholder="Search notifications...">
        </div>

        <div class="filter-actions">
            <button id="applyFilters" class="btn btn-primary">
                <i class="fas fa-filter"></i> Apply
            </button>
            <button id="clearFilters" class="btn btn-outline-secondary">
                <i class="fas fa-times"></i> Clear
            </button>
        </div>
    </div>

    <!-- Notifications Table -->
    <div class="notifications-table-container">
        <table class="notifications-table">
            <thead>
                <tr>
                    <th width="40">
                        <input type="checkbox" id="selectAll">
                    </th>
                    <th>Notification</th>
                    <th width="120">Type</th>
                    <th width="100">Priority</th>
                    <th width="100">Status</th>
                    <th width="150">Date</th>
                    <th width="100">Actions</th>
                </tr>
            </thead>
            <tbody id="notificationsTableBody">
                <?php if (empty($notifications)): ?>
                <tr>
                    <td colspan="7" class="empty-state">
                        <i class="fas fa-bell-slash"></i>
                        <p>No notifications found</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($notifications as $notif): ?>
                <tr class="notification-row <?= $notif['is_read'] ? '' : 'unread' ?>" data-id="<?= $notif['id'] ?>">
                    <td>
                        <input type="checkbox" class="notification-checkbox" value="<?= $notif['id'] ?>">
                    </td>
                    <td>
                        <div class="notification-cell">
                            <div class="notification-icon-cell <?= $notif['type'] ?>">
                                <i class="fas <?= htmlspecialchars($notif['icon'] ?? 'fa-bell') ?>"></i>
                            </div>
                            <div class="notification-details">
                                <div class="notification-title-cell"><?= htmlspecialchars($notif['title']) ?></div>
                                <div class="notification-message-cell"><?= htmlspecialchars($notif['message']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-<?= $notif['type'] ?>">
                            <?= ucfirst(str_replace('_', ' ', $notif['type'])) ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-priority-<?= $notif['priority'] ?>">
                            <?= ucfirst($notif['priority']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($notif['is_read']): ?>
                        <span class="badge badge-read">Read</span>
                        <?php else: ?>
                        <span class="badge badge-unread">Unread</span>
                        <?php endif; ?>
                    </td>
                    <td class="date-cell">
                        <?= date('M d, Y H:i', strtotime($notif['created_at'])) ?>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <?php if (!$notif['is_read']): ?>
                            <button class="btn-icon" onclick="markAsRead(<?= $notif['id'] ?>)" title="Mark as read">
                                <i class="fas fa-check"></i>
                            </button>
                            <?php endif; ?>
                            <button class="btn-icon btn-danger" onclick="deleteNotification(<?= $notif['id'] ?>)" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <button class="btn btn-sm" <?= $currentPage <= 1 ? 'disabled' : '' ?> onclick="goToPage(<?= $currentPage - 1 ?>)">
            <i class="fas fa-chevron-left"></i> Previous
        </button>
        
        <div class="page-numbers">
            <?php for ($i = 1; $i <= min($totalPages, 10); $i++): ?>
            <button class="btn btn-sm <?= $i === $currentPage ? 'active' : '' ?>" onclick="goToPage(<?= $i ?>)">
                <?= $i ?>
            </button>
            <?php endfor; ?>
        </div>

        <button class="btn btn-sm" <?= $currentPage >= $totalPages ? 'disabled' : '' ?> onclick="goToPage(<?= $currentPage + 1 ?>)">
            Next <i class="fas fa-chevron-right"></i>
        </button>
    </div>
    <?php endif; ?>
</div>

<style>
.filters-container {
    background: white;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 24px;
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    align-items: flex-end;
}

.filter-group {
    flex: 1;
    min-width: 150px;
}

.filter-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #374151;
    font-size: 14px;
}

.form-select, .form-input {
    width: 100%;
    padding: 10px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
}

.filter-actions {
    display: flex;
    gap: 8px;
}

.notifications-table-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.notifications-table {
    width: 100%;
    border-collapse: collapse;
}

.notifications-table thead {
    background: #f9fafb;
    border-bottom: 2px solid #e5e7eb;
}

.notifications-table th {
    padding: 16px;
    text-align: left;
    font-weight: 600;
    color: #374151;
    font-size: 14px;
}

.notifications-table td {
    padding: 16px;
    border-bottom: 1px solid #e5e7eb;
}

.notification-row.unread {
    background: #eff6ff;
}

.notification-cell {
    display: flex;
    gap: 12px;
    align-items: flex-start;
}

.notification-icon-cell {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.notification-icon-cell.system { background: #dbeafe; color: #3b82f6; }
.notification-icon-cell.user_action { background: #d1fae5; color: #10b981; }
.notification-icon-cell.email { background: #fce7f3; color: #ec4899; }
.notification-icon-cell.alert { background: #fee2e2; color: #ef4444; }
.notification-icon-cell.info { background: #e0e7ff; color: #6366f1; }

.notification-title-cell {
    font-weight: 600;
    color: #111827;
    margin-bottom: 4px;
}

.notification-message-cell {
    font-size: 14px;
    color: #6b7280;
}

.badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.badge-system { background: #dbeafe; color: #1e40af; }
.badge-user_action { background: #d1fae5; color: #065f46; }
.badge-email { background: #fce7f3; color: #9f1239; }
.badge-alert { background: #fee2e2; color: #991b1b; }
.badge-info { background: #e0e7ff; color: #3730a3; }

.badge-priority-urgent { background: #fee2e2; color: #991b1b; }
.badge-priority-high { background: #fed7aa; color: #92400e; }
.badge-priority-normal { background: #dbeafe; color: #1e40af; }
.badge-priority-low { background: #e5e7eb; color: #374151; }

.badge-read { background: #d1fae5; color: #065f46; }
.badge-unread { background: #dbeafe; color: #1e40af; }

.action-buttons {
    display: flex;
    gap: 8px;
}

.btn-icon {
    width: 32px;
    height: 32px;
    border: none;
    background: #f3f4f6;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.btn-icon:hover {
    background: #e5e7eb;
}

.btn-icon.btn-danger:hover {
    background: #fee2e2;
    color: #dc2626;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin-top: 24px;
}

.page-numbers {
    display: flex;
    gap: 4px;
}

.btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #9ca3af;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    display: block;
}

.header-actions {
    display: flex;
    gap: 12px;
}
</style>

<script>
// Bulk actions
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.notification-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateBulkButtons();
});

document.querySelectorAll('.notification-checkbox').forEach(cb => {
    cb.addEventListener('change', updateBulkButtons);
});

function updateBulkButtons() {
    const selected = document.querySelectorAll('.notification-checkbox:checked').length;
    document.getElementById('bulkMarkRead').disabled = selected === 0;
    document.getElementById('bulkDelete').disabled = selected === 0;
}

document.getElementById('bulkMarkRead').addEventListener('click', async function() {
    const ids = Array.from(document.querySelectorAll('.notification-checkbox:checked')).map(cb => cb.value);
    // Implement bulk mark as read
    for (const id of ids) {
        await markAsRead(id);
    }
    location.reload();
});

document.getElementById('bulkDelete').addEventListener('click', function() {
    showConfirmModal('Bulk Delete', 'Delete selected notifications?', async () => {
        const ids = Array.from(document.querySelectorAll('.notification-checkbox:checked')).map(cb => cb.value);
        // Implement bulk delete
        for (const id of ids) {
            await deleteNotification(id, true); // Pass true to skip inner confirmation
        }
        location.reload();
    });
});

// Filters
document.getElementById('applyFilters').addEventListener('click', function() {
    const params = new URLSearchParams();
    const type = document.getElementById('filterType').value;
    const status = document.getElementById('filterStatus').value;
    const priority = document.getElementById('filterPriority').value;
    const search = document.getElementById('searchQuery').value;
    
    if (type) params.append('type', type);
    if (status) params.append('status', status);
    if (priority) params.append('priority', priority);
    if (search) params.append('search', search);
    
    window.location.href = '<?= app_base_url('/notifications/history') ?>?' + params.toString();
});

document.getElementById('clearFilters').addEventListener('click', function() {
    window.location.href = '<?= app_base_url('/notifications/history') ?>';
});

async function markAsRead(id) {
    await fetch(`<?= app_base_url('/notifications/') ?>${id}/read`, { method: 'POST' });
}

function deleteNotification(id, skipConfirm = false) {
    if (skipConfirm) {
        return fetch(`<?= app_base_url('/notifications/') ?>${id}`, { method: 'DELETE' });
    }
    showConfirmModal('Delete Notification', 'Delete this notification?', async () => {
        await fetch(`<?= app_base_url('/notifications/') ?>${id}`, { method: 'DELETE' });
        location.reload();
    });
}

function goToPage(page) {
    const params = new URLSearchParams(window.location.search);
    params.set('page', page);
    window.location.href = '<?= app_base_url('/notifications/history') ?>?' + params.toString();
}
</script>
