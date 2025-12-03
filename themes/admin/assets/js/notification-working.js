/**
 * Bishwo Calculator - Enhanced Notification System
 * Handles real-time notifications in the admin panel.
 */
class NotificationSystem {
    constructor() {
        this.elements = {
            toggle: document.getElementById('notificationToggle'),
            dropdown: document.getElementById('notificationDropdown'),
            badge: document.getElementById('notificationBadge'),
            list: document.querySelector('.notification-list'),
            markAllReadBtn: document.getElementById('markAllRead'),
        };
        this.state = {
            isDropdownOpen: false,
            isLoading: false,
            notifications: [],
            unreadCount: 0,
        };
        // Correct API base path as defined in routes.php
        this.apiBase = '/api/notifications'; 
        this.pollingInterval = 30000; // 30 seconds
        this.pollingTimer = null;

        // Ensure this script doesn't run if the necessary elements aren't on the page
        if (!this.elements.toggle) {
            console.warn('Notification toggle button not found. System will not initialize.');
            return;
        }

        this.init();
    }

    init() {
        console.log('NotificationSystem: Initializing...');
        // DOM is likely already loaded as this script is at the end of the body, 
        // but this is a safe way to ensure it.
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
    }

    setup() {
        console.log('NotificationSystem: Setting up event listeners and starting polling.');
        this.elements.toggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.toggleDropdown();
        });
        
        if (this.elements.markAllReadBtn) {
            this.elements.markAllReadBtn.addEventListener('click', () => this.markAllAsRead());
        }

        document.addEventListener('click', (e) => {
            if (this.state.isDropdownOpen && !this.elements.dropdown.contains(e.target) && !this.elements.toggle.contains(e.target)) {
                this.closeDropdown();
            }
        });
        
        this.fetchUnreadCount();
        this.pollingTimer = setInterval(() => this.fetchUnreadCount(), this.pollingInterval);
    }

    toggleDropdown() {
        if (this.state.isDropdownOpen) {
            this.closeDropdown();
        } else {
            this.openDropdown();
        }
    }

    openDropdown() {
        console.log('NotificationSystem: Opening dropdown.');
        this.state.isDropdownOpen = true;
        this.elements.dropdown.classList.add('show');
        this.fetchNotifications();
    }

    closeDropdown() {
        console.log('NotificationSystem: Closing dropdown.');
        this.state.isDropdownOpen = false;
        this.elements.dropdown.classList.remove('show');
    }

    async fetchUnreadCount() {
        console.log('NotificationSystem: Fetching unread count.');
        try {
            const response = await fetch(`${this.apiBase}/unread-count`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status} ${response.statusText}`);
            }
            const data = await response.json();

            if (data.success) {
                console.log(`NotificationSystem: Unread count is ${data.unread_count}.`);
                if (data.unread_count > this.state.unreadCount) {
                   this.showToast('You have new notifications!', 'info');
                }
                this.state.unreadCount = data.unread_count;
                this.updateBadge(this.state.unreadCount);
            } else {
                 console.error('NotificationSystem: API returned success:false for unread count.', data);
            }
        } catch (error) {
            console.error("NotificationSystem: Error fetching unread count:", error);
        }
    }

    async fetchNotifications() {
        if (this.state.isLoading) return;
        this.state.isLoading = true;
        this.renderLoading();
        console.log('NotificationSystem: Fetching notification list.');

        try {
            const response = await fetch(`${this.apiBase}/list?unread_only=true&limit=10`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status} ${response.statusText}`);
            }
            const data = await response.json();

            if (data.success) {
                console.log('NotificationSystem: Successfully fetched notifications.', data.notifications);
                this.state.notifications = data.notifications;
                this.renderNotifications();
            } else {
                console.error('NotificationSystem: API returned success:false for notification list.', data);
                this.renderError('Failed to load notifications.');
            }
        } catch (error) {
            console.error("NotificationSystem: Error fetching notifications:", error);
            this.renderError('Could not connect to the server.');
        } finally {
            this.state.isLoading = false;
        }
    }
    
    async markAllAsRead() {
        console.log('NotificationSystem: Marking all notifications as read.');
        try {
            const response = await fetch(`${this.apiBase}/mark-all-read`, { method: 'POST' });
            if (!response.ok) throw new Error('Failed to mark all as read');
            const data = await response.json();

            if (data.success) {
                this.state.notifications.forEach(n => n.is_read = 1);
                this.updateBadge(0);
                this.renderNotifications();
                this.showToast('All notifications marked as read.', 'success');
                this.closeDropdown();
            }
        } catch (error) {
            console.error('NotificationSystem: Error marking all as read:', error);
            this.showToast('Failed to mark all notifications as read.', 'error');
        }
    }

    updateBadge(count) {
        if (!this.elements.badge) return;
        
        if (count > 0) {
            this.elements.badge.textContent = count;
            this.elements.badge.style.display = 'inline-flex';
        } else {
            this.elements.badge.style.display = 'none';
        }
    }

    renderLoading() {
        this.elements.list.innerHTML = '<div class="loading">Loading...</div>';
    }

    renderError(message) {
        this.elements.list.innerHTML = `<div class="error">${message} <button class="btn btn-sm btn-outline-primary" onclick="window.notificationSystem.fetchNotifications()">Retry</button></div>`;
    }

    renderNotifications() {
        if (this.state.notifications.length === 0) {
            this.elements.list.innerHTML = '<div class="empty">You have no unread notifications.</div>';
            return;
        }

        this.elements.list.innerHTML = this.state.notifications.map(n => `
            <div class="notification-item ${!n.is_read ? 'unread' : ''}" data-id="${n.id}">
                <div class="notification-icon">
                    <i class="${this.getIcon(n.type)}"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header">
                       <h5 class="notification-title">${n.title}</h5>
                       <span class="notification-time">${this.timeAgo(n.created_at)}</span>
                    </div>
                    <p class="notification-message">${n.message}</p>
                </div>
            </div>
        `).join('');
    }
    
    getIcon(type) {
        switch (type) {
            case 'success': return 'fas fa-check-circle text-success';
            case 'error': return 'fas fa-times-circle text-danger';
            case 'warning': return 'fas fa-exclamation-triangle text-warning';
            case 'info':
            default: return 'fas fa-info-circle text-info';
        }
    }

    timeAgo(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);
        let interval = seconds / 31536000;
        if (interval > 1) return Math.floor(interval) + "y ago";
        interval = seconds / 2592000;
        if (interval > 1) return Math.floor(interval) + "mo ago";
        interval = seconds / 86400;
        if (interval > 1) return Math.floor(interval) + "d ago";
        interval = seconds / 3600;
        if (interval > 1) return Math.floor(interval) + "h ago";
        interval = seconds / 60;
        if (interval > 1) return Math.floor(interval) + "m ago";
        return Math.floor(seconds) + "s ago";
    }

    showToast(message, type = 'info') {
        // Use the existing AdminApp's notification system if available for consistency
        if (window.AdminApp && typeof window.AdminApp.showNotification === 'function') {
            window.AdminApp.showNotification(message, type);
        } else {
            // Fallback for standalone use
            console.log(`[${type.toUpperCase()}] Notification: ${message}`);
        }
    }
}

// Initialize the system only if it hasn't been initialized already
if (typeof window.notificationSystem === 'undefined') {
    window.notificationSystem = new NotificationSystem();
}