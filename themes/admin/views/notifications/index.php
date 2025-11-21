<?php
// Notification Center View
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
                
                return '<div class="notification-item ' . $typeClass . ' ' . ($notification['is_read'] ? '' : 'notification-unread') . '" data-id="' . $notification['id'] . '">
                    <div class="notification-icon">
                        <i class="fas fa-' . $icon . '"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-header">
                            <h4 class="notification-title">' . htmlspecialchars($notification['title']) . '</h4>
                            <div class="notification-meta">
                                <span class="notification-time">' . date('M j, Y g:i A', strtotime($notification['created_at'])) . '</span>
                                <button class="btn btn-sm btn-icon notification-dismiss" data-id="' . $notification['id'] . '">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="notification-message">
                            ' . htmlspecialchars($notification['message']) . '
                        </div>
                    </div>
                </div>';
            }, $notifications ?? [])) . '
        </div>
    </div>

    <!-- Pagination -->
    ' . ((($page ?? 1) > 1 || count($notifications ?? []) === 20) ? '<div class="pagination">
        ' . (($page ?? 1) > 1 ? '<a href="?page=' . (($page ?? 1) - 1) . '" class="page-link">Previous</a>' : '') . '
        <span class="page-link active">' . ($page ?? 1) . '</span>
        ' . (count($notifications ?? []) === 20 ? '<a href="?page=' . (($page ?? 1) + 1) . '" class="page-link">Next</a>' : '') . '
    </div>' : '') . '
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Mark all as read button
    document.getElementById("mark-all-read").addEventListener("click", markAllAsRead);
    
    // Refresh button
    document.getElementById("refresh-notifications").addEventListener("click", refreshNotifications);
    
    // Add event listeners to dismiss buttons
    document.querySelectorAll(".notification-dismiss").forEach(button => {
        button.addEventListener("click", function() {
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
.notifications-container {
    margin-top: 24px;
}

.notification-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.notification-item {
    display: flex;
    gap: 12px;
    padding: 16px;
    border: 1px solid var(--admin-border);
    border-radius: 8px;
    background: white;
    transition: var(--transition);
    cursor: pointer;
}

.notification-item:hover {
    box-shadow: var(--admin-shadow);
    border-color: var(--admin-primary);
}

.notification-item.notification-unread {
    border-left: 4px solid var(--admin-primary);
    background-color: var(--admin-gray-50);
}

.notification-icon {
    font-size: 20px;
    padding-top: 2px;
}

.notification-info .notification-icon {
    color: var(--admin-info);
}

.notification-success .notification-icon {
    color: var(--admin-success);
}

.notification-warning .notification-icon {
    color: var(--admin-warning);
}

.notification-error .notification-icon {
    color: var(--admin-danger);
}

.notification-content {
    flex: 1;
}

.notification-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 4px;
}

.notification-title {
    margin: 0;
    font-weight: 600;
    color: var(--admin-gray-800);
    font-size: 16px;
}

.notification-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: var(--admin-gray-600);
}

.notification-time {
    white-space: nowrap;
}

.notification-dismiss {
    color: var(--admin-gray-400);
}

.notification-dismiss:hover {
    color: var(--admin-danger);
}

.notification-message {
    color: var(--admin-gray-700);
    line-height: 1.5;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: var(--admin-gray-500);
}

.empty-state i {
    margin-bottom: 16px;
    opacity: 0.5;
}

.toolbar-actions {
    display: flex;
    gap: 8px;
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
include __DIR__ . '/../layouts/main.php';
?>