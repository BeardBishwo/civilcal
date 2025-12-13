<?php
// Notification Center View - Beautiful UI
$content = '
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-bell"></i>
            Notification Center
        </h1>
        <p class="page-description">Manage and view system notifications</p>
    </div>

    <!-- Notification Actions -->
    <div class="toolbar">
        <div class="toolbar-actions">
            <button id="mark-all-read" class="btn btn-secondary">
                <i class="fas fa-check-circle"></i>
                Mark All as Read
            </button>
            <button id="refresh-notifications" class="btn btn-outline-secondary">
                <i class="fas fa-sync"></i>
                Refresh
            </button>
        </div>
        <div class="notification-stats">
            <span class="stats-label">Total: <span class="stats-value">' . count($notifications ?? []) . '</span></span>
            <span class="stats-label">Unread: <span class="stats-value unread-count">' . ($unreadCount ?? 0) . '</span></span>
        </div>
    </div>

    <!-- Notification List -->
    <div class="notifications-container">
        ' . (empty($notifications ?? []) ? '<div class="empty-state">
            <i class="fas fa-bell-slash fa-3x"></i>
            <h3>No Notifications</h3>
            <p>You have no notifications at this time</p>
        </div>' : '') . '

        <div class="notification-list">
            ' . implode('', array_map(function($notification) {
                $typeClass = '';
                $icon = 'info-circle';
                switch ($notification['type']) {
                    case 'success':
                        $typeClass = 'notification-success';
                        $icon = 'check-circle';
                        break;
                    case 'warning':
                        $typeClass = 'notification-warning';
                        $icon = 'exclamation-triangle';
                        break;
                    case 'error':
                        $typeClass = 'notification-error';
                        $icon = 'exclamation-circle';
                        break;
                    case 'info':
                    default:
                        $typeClass = 'notification-info';
                        break;
                }

                $timeAgo = '';
                if (!empty($notification['created_at'])) {
                    $createdAt = new DateTime($notification['created_at']);
                    $now = new DateTime();
                    $diff = $now->diff($createdAt);

                    if ($diff->days > 0) {
                        $timeAgo = $diff->days . 'd ago';
                    } elseif ($diff->h > 0) {
                        $timeAgo = $diff->h . 'h ago';
                    } elseif ($diff->i > 0) {
                        $timeAgo = $diff->i . 'm ago';
                    } else {
                        $timeAgo = 'Just now';
                    }
                }

                return '<div class="notification-item ' . $typeClass . ' ' . ($notification['is_read'] ? '' : 'notification-unread') . '" data-id="' . $notification['id'] . '">
                    <div class="notification-icon">
                        <i class="fas fa-' . $icon . '"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-header">
                            <h4 class="notification-title">' . htmlspecialchars($notification['title']) . '</h4>
                            <div class="notification-meta">
                                <span class="notification-time">' . $timeAgo . '</span>
                                <button class="btn btn-sm btn-icon notification-dismiss" data-id="' . $notification['id'] . '" title="Delete">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="notification-message">
                            ' . htmlspecialchars($notification['message']) . '
                        </div>
                        <div class="notification-footer">
                            <span class="notification-date">' . date('M j, Y g:i A', strtotime($notification['created_at'])) . '</span>
                            ' . ($notification['is_read'] ? '<span class="read-badge"><i class="fas fa-check-circle"></i> Read</span>' : '<span class="unread-badge"><i class="fas fa-circle"></i> Unread</span>') . '
                        </div>
                    </div>
                </div>';
            }, $notifications ?? [])) . '
        </div>
    </div>

    <!-- Pagination -->
    ' . ((($page ?? 1) > 1 || count($notifications ?? []) === 20) ? '<div class="pagination">
        ' . (($page ?? 1) > 1 ? '<a href="?page=' . (($page ?? 1) - 1) . '" class="page-link">
            <i class="fas fa-chevron-left"></i> Previous
        </a>' : '') . '
        <span class="page-link active">' . ($page ?? 1) . '</span>
        ' . (count($notifications ?? []) === 20 ? '<a href="?page=' . (($page ?? 1) + 1) . '" class="page-link">
            Next <i class="fas fa-chevron-right"></i>
        </a>' : '') . '
    </div>' : '') . '
</div>

<script>
// Enhanced JavaScript with better UX
document.addEventListener("DOMContentLoaded", function() {
    // Mark all as read button
    document.getElementById("mark-all-read").addEventListener("click", markAllAsRead);

    // Refresh button
    document.getElementById("refresh-notifications").addEventListener("click", refreshNotifications);

    // Create test notification button (if empty state)
    const createTestBtn = document.getElementById("create-test-notification");
    if (createTestBtn) {
        createTestBtn.addEventListener("click", createTestNotification);
    }

    // Add event listeners to dismiss buttons
    document.querySelectorAll(".notification-dismiss").forEach(button => {
        button.addEventListener("click", function(e) {
            e.stopPropagation();
            const notificationId = this.getAttribute("data-id");
            dismissNotification(notificationId);
        });
    });

    // Add event listeners to notification items to mark as read when clicked
    document.querySelectorAll(".notification-item").forEach(item => {
        item.addEventListener("click", function() {
            const notificationId = this.getAttribute("data-id");
            if (!this.classList.contains("notification-unread")) return;

            markAsRead(notificationId);
        });
    });

    // Add smooth animations on page load
    setTimeout(() => {
        document.querySelectorAll(".notification-item").forEach((item, index) => {
            item.style.animation = "fadeIn 0.5s ease-out " + (index * 0.1) + "s forwards";
            item.style.opacity = "0";
            setTimeout(() => {
                item.style.opacity = "1";
            }, 10);
        });
    }, 100);
});

async function markAsRead(notificationId) {
    try {
        const response = await fetch("' . app_base_url('/admin/notifications/mark-read/') . '" + notificationId, {
            method: "POST"
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Remove the unread class from the notification
            const notification = document.querySelector(`[data-id="${notificationId}"]`);
            if (notification) {
                notification.classList.remove("notification-unread");
                
                // Update the unread count in the header
                updateUnreadCount(-1);
            }
        }
    } catch (error) {
        console.error("Error marking notification as read:", error);
    }
}

async function markAllAsRead() {
    if (!confirm("Are you sure you want to mark all notifications as read?")) {
        return;
    }
    
    try {
        const response = await fetch("' . app_base_url('/admin/notifications/mark-all-read') . '", {
            method: "POST"
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Remove unread class from all notifications
            document.querySelectorAll(".notification-item.notification-unread").forEach(item => {
                item.classList.remove("notification-unread");
            });
            
            // Update the unread count in the header
            updateUnreadCount(-999); // Reset to 0
        }
    } catch (error) {
        console.error("Error marking all notifications as read:", error);
    }
}

async function dismissNotification(notificationId) {
    if (!confirm("Are you sure you want to delete this notification?")) {
        return;
    }
    
    try {
        const response = await fetch("' . app_base_url('/admin/notifications/delete/') . '" + notificationId, {
            method: "DELETE"
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Remove the notification from the DOM
            const notification = document.querySelector(`[data-id="${notificationId}"]`);
            if (notification) {
                notification.remove();
                
                // Update the unread count in the header
                if (notification.classList.contains("notification-unread")) {
                    updateUnreadCount(-1);
                }
                
                // Show empty state if no notifications remain
                if (document.querySelectorAll(".notification-item").length === 0) {
                    showEmptyState();
                }
            }
        }
    } catch (error) {
        console.error("Error dismissing notification:", error);
    }
}

async function refreshNotifications() {
    try {
        window.location.reload();
    } catch (error) {
        console.error("Error refreshing notifications:", error);
    }
}

function updateUnreadCount(change) {
    // This function would update the unread count displayed in the admin header
    // In a real implementation, this would modify a badge counter in the top navigation
    console.log("Unread count change:", change);
}

function showEmptyState() {
    // Show the empty state if no notifications remain
    if (document.querySelectorAll(".notification-item").length === 0) {
        document.querySelector(".notifications-container").innerHTML = \'
            <div class="empty-state">
                <i class="fas fa-bell-slash fa-3x"></i>
                <h3>No Notifications</h3>
                <p>You have no notifications at this time</p>
            </div>
        \';
    }
}
</script>


<style>

/* Additional inline styles for specific elements */
.notification-stats {
    display: flex;
    gap: 20px;
    align-items: center;
    font-size: 14px;
    color: var(--admin-gray-600);
}

.stats-label {
    display: flex;
    align-items: center;
    gap: 4px;
}

.stats-value {
    font-weight: 600;
    color: var(--admin-primary);
    font-size: 16px;
}

.stats-value.unread-count {
    color: var(--admin-danger);
    font-size: 16px;
}

.read-badge {
    background: var(--admin-success);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-left: 12px;
}

.unread-badge {
    background: var(--admin-danger);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-left: 12px;
}

.notification-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid var(--admin-gray-100);
    font-size: 12px;
    color: var(--admin-gray-500);
}

.notification-date {
    color: var(--admin-gray-400);
    font-size: 11px;
}

.mt-3 {
    margin-top: 16px;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 3000;
}

.loading-spinner {
    background: white;
    padding: 24px 32px;
    border-radius: 12px;
    text-align: center;
    color: var(--admin-gray-700);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.loading-spinner i {
    font-size: 24px;
    margin-bottom: 12px;
    color: var(--admin-primary);
    animation: spin 1s linear infinite;
}

.loading-state {
    padding: 16px;
    text-align: center;
    color: var(--admin-gray-500);
    font-size: 14px;
}

.loading-state i {
    font-size: 18px;
    margin-right: 8px;
    color: var(--admin-primary);
    animation: spin 1s linear infinite;
}

/* Toast notifications */
.notification-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 280px;
    max-width: 400px;
    padding: 0;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    z-index: 10000;
    transform: translateX(400px);
    transition: transform 0.3s ease-in-out, opacity 0.3s ease;
    opacity: 0;
    font-family: Inter, sans-serif;
}

.notification-toast.show {
    transform: translateX(0);
    opacity: 1;
}

.notification-toast.notification-success {
    border-left: 4px solid var(--admin-success);
}

.notification-toast.notification-error {
    border-left: 4px solid var(--admin-danger);
}

.notification-toast.notification-warning {
    border-left: 4px solid var(--admin-warning);
}

.notification-toast.notification-info {
    border-left: 4px solid var(--admin-info);
}

.toast-content {
    display: flex;
    align-items: center;
    padding: 16px 20px;
    background: white;
    border-radius: 12px;
}

.toast-icon {
    font-size: 20px;
    margin-right: 12px;
    width: 24px;
    text-align: center;
}

.toast-icon.fa-check-circle {
    color: var(--admin-success);
}

.toast-icon.fa-exclamation-circle {
    color: var(--admin-danger);
}

.toast-icon.fa-exclamation-triangle {
    color: var(--admin-warning);
}

.toast-icon.fa-info-circle {
    color: var(--admin-info);
}

.toast-message {
    flex: 1;
    font-size: 14px;
    line-height: 1.4;
    color: var(--admin-gray-800);
}

.toast-close {
    background: none;
    border: none;
    color: var(--admin-gray-400);
    cursor: pointer;
    padding: 4px;
    margin-left: 8px;
    border-radius: 4px;
    transition: all 0.2s ease;
    font-size: 16px;
}

.toast-close:hover {
    background: var(--admin-gray-100);
    color: var(--admin-gray-600);
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideIn {
    from {
        transform: translateX(20px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .toolbar {
        flex-direction: column;
        gap: 16px;
        align-items: stretch;
    }

    .toolbar-actions {
        justify-content: space-between;
    }

    .notification-stats {
        flex-direction: column;
        gap: 8px;
        align-items: flex-start;
    }
}
</style>
';

// Set breadcrumbs
$breadcrumbs = [
    ['title' => 'Notifications']
];

$page_title = $page_title ?? 'Notifications - Admin Panel';
$currentPage = $currentPage ?? 'notifications';

// Include the layout
// Include the layout
include BASE_PATH . '/themes/admin/layouts/main.php';

// Add the beautiful CSS link after the layout
echo '<link rel="stylesheet" href="' . app_base_url('themes/admin/assets/css/notifications-beautiful.css') . '">';