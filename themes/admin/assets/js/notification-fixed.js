/**
 * Bishwo Calculator - Fixed Notification System
 * Single-click functionality with improved error handling
 * Fixed initialization conflicts and real-time polling
 * Consistently uses window.appConfig for dynamic base URLs
 */

class NotificationSystem {
    constructor() {
        // System state
        this.state = {
            isInitialized: false,
            isDropdownOpen: false,
            isLoading: false,
            notifications: [],
            unreadCount: 0,
            lastUnreadCount: 0,
            retryCount: 0,
            maxRetries: 3,
            pollingInterval: 30000, // 30 seconds
            pollingTimer: null
        };

        // DOM elements cache
        this.elements = {
            toggle: null,
            dropdown: null,
            badge: null,
            list: null,
            markAllReadBtn: null,
            toast: null
        };

        // Configuration
        this.config = {
            apiBase: (window.appConfig ? window.appConfig.baseUrl : '') + '/api/notifications',
            debugMode: true
        };

        // Initialize the system
        this.init();
    }

    /**
     * Initialize the notification system
     */
    init() {
        if (this.state.isInitialized) {
            this.log('Notification system already initialized');
            return;
        }

        this.log('🚀 Initializing fixed notification system...');

        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }

        this.state.isInitialized = true;
        this.log('✅ Fixed notification system initialized');
    }

    /**
     * Setup notification system
     */
    setup() {
        this.log('🔧 Setting up notification system...');

        // Cache DOM elements
        this.cacheElements();

        // Check if we have required elements
        if (!this.elements.toggle) {
            this.error('Notification toggle button not found');
            return;
        }

        // Setup event handlers
        this.setupClickHandlers();
        this.setupOutsideClickHandler();
        this.setupMarkAllReadHandler();

        // Load initial notification count
        this.fetchUnreadCount();

        // Start real-time polling
        this.startPolling();

        this.log('✅ Notification system setup complete');
    }

    /**
     * Cache DOM elements for better performance
     */
    cacheElements() {
        this.elements = {
            toggle: document.getElementById('notificationToggle'),
            dropdown: document.getElementById('notificationDropdown'),
            badge: document.getElementById('notificationBadge'),
            list: document.querySelector('.notification-list'),
            markAllReadBtn: document.getElementById('markAllRead'),
            toast: document.getElementById('notification-toast')
        };

        this.log('📋 Cached DOM elements:', {
            toggle: !!this.elements.toggle,
            dropdown: !!this.elements.dropdown,
            badge: !!this.elements.badge,
            list: !!this.elements.list,
            markAllReadBtn: !!this.elements.markAllReadBtn,
            toast: !!this.elements.toast
        });
    }

    /**
     * Setup click handlers for notification button
     */
    setupClickHandlers() {
        if (!this.elements.toggle) return;

        // Remove any existing event listeners to prevent conflicts
        const clone = this.elements.toggle.cloneNode(true);
        this.elements.toggle.parentNode.replaceChild(clone, this.elements.toggle);
        this.elements.toggle = clone;

        // Add click handler
        this.elements.toggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.log('Bell clicked - Toggling dropdown');
            this.toggleDropdown();
        });

        // Ensure button is visible and clickable
        this.elements.toggle.style.display = 'inline-block';
        this.elements.toggle.style.visibility = 'visible';
        this.elements.toggle.style.opacity = '1';
        this.elements.toggle.style.cursor = 'pointer';

        this.log('✅ Notification button click handler attached');
    }

    /**
     * Setup outside click handler to close dropdown
     */
    setupOutsideClickHandler() {
        document.addEventListener('click', (e) => {
            if (this.state.isDropdownOpen &&
                this.elements.dropdown &&
                !this.elements.dropdown.contains(e.target) &&
                !this.elements.toggle.contains(e.target)) {
                this.closeDropdown();
            }
        });
    }

    /**
     * Setup mark all read button handler
     */
    setupMarkAllReadHandler() {
        if (this.elements.markAllReadBtn) {
            this.elements.markAllReadBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.markAllAsRead();
            });
            this.log('✅ Mark all read button handler attached');
        }
    }

    /**
     * Toggle notification dropdown
     */
    toggleDropdown() {
        if (this.state.isDropdownOpen) {
            this.closeDropdown();
        } else {
            this.openDropdown();
        }
    }

    /**
     * Open notification dropdown and load notifications
     */
    openDropdown() {
        if (!this.elements.dropdown) {
            this.error('Notification dropdown not found');
            return;
        }

        this.state.isDropdownOpen = true;
        this.elements.dropdown.classList.add('show');
        this.elements.dropdown.style.display = 'block';

        // Load notifications every time it's opened to ensure fresh data
        this.fetchNotifications();

        this.log('🔔 Notification dropdown opened');
    }

    /**
     * Close notification dropdown
     */
    closeDropdown() {
        if (this.elements.dropdown) {
            this.state.isDropdownOpen = false;
            this.elements.dropdown.classList.remove('show');
            this.elements.dropdown.style.display = 'none';
            this.log('🔔 Notification dropdown closed');
        }
    }

    /**
     * Start real-time polling for notifications
     */
    startPolling() {
        if (this.state.pollingTimer) {
            clearInterval(this.state.pollingTimer);
        }

        this.log('🕒 Starting notification polling...');

        // Regular polling
        this.state.pollingTimer = setInterval(() => {
            this.fetchUnreadCount();
        }, this.state.pollingInterval);
    }

    /**
     * Fetch unread notification count from API
     */
    async fetchUnreadCount() {
        try {
            this.log('📊 Fetching unread notification count...');

            const response = await fetch(`${this.config.apiBase}/unread-count`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            if (!data.success || typeof data.unread_count !== 'number') {
                throw new Error('Invalid API response format');
            }

            const newCount = data.unread_count;
            this.updateBadge(newCount);

            // Check if we have new notifications
            if (newCount > this.state.lastUnreadCount) {
                const newNotificationsCount = newCount - this.state.lastUnreadCount;
                this.showNotificationToast(
                    `${newNotificationsCount} new notification${newNotificationsCount > 1 ? 's' : ''}`,
                    'info'
                );
            }

            this.state.lastUnreadCount = newCount;
            this.state.retryCount = 0; // Reset retry count on success

            this.log(`📊 Unread count: ${newCount}`);

        } catch (error) {
            this.handlePollingError(error);
        }
    }

    /**
     * Handle polling errors with retry logic
     */
    handlePollingError(error) {
        this.state.retryCount++;
        this.error('Polling error:', error);

        if (this.state.retryCount <= this.state.maxRetries) {
            this.log(`Retrying notification fetch (${this.state.retryCount}/${this.state.maxRetries})...`);

            const delay = Math.min(5000 * this.state.retryCount, 30000); // Progressive delay up to 30 seconds

            setTimeout(() => {
                this.fetchUnreadCount();
            }, delay);
        } else {
            this.error('Max retries reached for notification polling');
            // Only show toast if it's a persistent failure
            if (this.state.retryCount === this.state.maxRetries + 1) {
                this.showNotificationToast('Notification server connection issues. Retrying...', 'warning');
            }
        }
    }

    /**
     * Fetch notifications list from API
     */
    async fetchNotifications() {
        if (this.state.isLoading) {
            this.log('Already loading notifications, skipping...');
            return;
        }

        this.state.isLoading = true;
        this.showLoadingState();

        try {
            this.log('📋 Fetching notification list...');

            const url = new URL(`${this.config.apiBase}/list`, window.location.origin);
            url.searchParams.append('unread_only', 'true');
            url.searchParams.append('limit', '10');

            const response = await fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();

            if (!data.success || !Array.isArray(data.notifications)) {
                throw new Error('Invalid API response format');
            }

            this.state.notifications = data.notifications;
            this.renderNotifications();

            this.log(`✅ Loaded ${data.notifications.length} notifications`);

        } catch (error) {
            this.error('Failed to load notifications:', error);
            this.showErrorState(`
                Failed to load notifications.
                <button onclick="notificationSystem.fetchNotifications()" class="btn btn-sm btn-primary">
                    <i class="fas fa-refresh"></i> Retry
                </button>
            `);
        } finally {
            this.state.isLoading = false;
        }
    }

    /**
     * Mark individual notification as read
     */
    async markAsRead(notificationId) {
        try {
            this.log(`📝 Marking notification ${notificationId} as read...`);

            const response = await fetch(`${this.config.apiBase}/mark-read/${notificationId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Failed to mark notification as read');
            }

            this.log(`✅ Notification ${notificationId} marked as read`);
            return true;

        } catch (error) {
            this.error('Error marking notification as read:', error);
            return false;
        }
    }

    /**
     * Mark all notifications as read
     */
    async markAllAsRead() {
        try {
            this.log('📝 Marking all notifications as read...');

            const response = await fetch(`${this.config.apiBase}/mark-all-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            const data = await response.json();

            if (data.success) {
                // Update local state
                this.state.notifications.forEach(n => n.is_read = 1);
                this.updateBadge(0);
                this.renderNotifications();

                this.showNotificationToast('All notifications marked as read.', 'success');
                setTimeout(() => this.closeDropdown(), 1500);
            } else {
                throw new Error(data.message || 'Failed to mark all as read');
            }

        } catch (error) {
            this.error('Error marking all as read:', error);
            this.showNotificationToast('Failed to mark all notifications as read.', 'error');
        }
    }

    /**
     * Update notification badge with current count
     */
    updateBadge(count) {
        if (!this.elements.badge) return;

        this.state.unreadCount = count;

        if (count > 0) {
            this.elements.badge.textContent = count;
            this.elements.badge.style.display = 'inline-flex';
        } else {
            this.elements.badge.textContent = '';
            this.elements.badge.style.display = 'none';
        }

        this.log(`🔔 Badge updated: ${count} unread notifications`);
    }

    /**
     * Show loading state in notification list
     */
    showLoadingState() {
        if (this.elements.list) {
            this.elements.list.innerHTML = `
                <div class="loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Loading notifications...</span>
                </div>
            `;
        }
    }

    /**
     * Show error state in notification list
     */
    showErrorState(message) {
        if (this.elements.list) {
            this.elements.list.innerHTML = `
                <div class="error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>${message}</span>
                </div>
            `;
        }
    }

    /**
     * Render notifications in dropdown
     */
    renderNotifications() {
        if (!this.elements.list) return;

        if (this.state.notifications.length === 0) {
            this.elements.list.innerHTML = `
                <div class="empty-state" style="text-align: center; padding: 20px; color: var(--admin-gray-500);">
                    <i class="fas fa-bell-slash" style="font-size: 24px; display: block; margin-bottom: 10px;"></i>
                    <span>No new notifications</span>
                </div>
            `;
            return;
        }

        const notificationHTML = this.state.notifications.map(notification => `
            <div class="notification-item ${notification.is_read ? '' : 'unread'}" data-id="${notification.id}" style="cursor: pointer; padding: 12px 16px; border-bottom: 1px solid var(--admin-border); transition: background 0.2s;">
                <div class="notification-icon" style="margin-right: 12px; color: var(--admin-primary);">
                    <i class="fas ${this.getNotificationIcon(notification.type)}"></i>
                </div>
                <div class="notification-content" style="flex: 1;">
                    <div class="notification-header" style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <h4 class="notification-title" style="margin: 0; font-size: 14px; font-weight: 600;">${this.escapeHtml(notification.title)}</h4>
                        <span class="notification-time" style="font-size: 11px; color: var(--admin-gray-500);">${this.formatTimeAgo(notification.created_at)}</span>
                    </div>
                    <div class="notification-message" style="font-size: 13px; color: var(--admin-gray-700); line-height: 1.4;">${this.escapeHtml(notification.message)}</div>
                </div>
            </div>
        `).join('');

        this.elements.list.innerHTML = notificationHTML;

        // Add click handlers to notification items
        this.elements.list.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', async () => {
                const notificationId = item.getAttribute('data-id');
                
                // If it's a link notification, it might have an action URL
                // For now just mark as read
                
                if (item.classList.contains('unread')) {
                    const success = await this.markAsRead(notificationId);
                    if (success) {
                        item.classList.remove('unread');
                        this.updateBadge(Math.max(0, this.state.unreadCount - 1));
                    }
                }
            });
        });
    }

    /**
     * Show notification toast message
     */
    showNotificationToast(message, type = 'info') {
        if (!this.elements.toast) return;

        this.elements.toast.className = `notification-toast notification-${type} show`;
        this.elements.toast.innerHTML = `
            <div class="toast-content" style="display: flex; align-items: center; padding: 12px 16px;">
                <i class="toast-icon fas ${this.getNotificationIcon(type)}" style="margin-right: 10px;"></i>
                <span class="toast-message" style="flex: 1;">${this.escapeHtml(message)}</span>
                <button class="toast-close" onclick="this.parentElement.parentElement.classList.remove('show')" style="background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
            </div>
        `;

        setTimeout(() => {
            if (this.elements.toast) {
                this.elements.toast.classList.remove('show');
            }
        }, 5000);
    }

    /**
     * Get notification icon based on type
     */
    getNotificationIcon(type) {
        const icons = {
            'success': 'fa-check-circle',
            'error': 'fa-exclamation-circle',
            'warning': 'fa-exclamation-triangle',
            'info': 'fa-info-circle',
            'system': 'fa-cog'
        };
        return icons[type] || icons['info'];
    }

    /**
     * Format time ago
     */
    formatTimeAgo(dateString) {
        if (!dateString) return '';
        const now = new Date();
        const date = new Date(dateString);
        const diffInSeconds = Math.floor((now - date) / 1000);

        if (diffInSeconds < 60) return 'Just now';
        if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + 'm ago';
        if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + 'h ago';
        return Math.floor(diffInSeconds / 86400) + 'd ago';
    }

    /**
     * Escape HTML
     */
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Log messages if debug mode is enabled
     */
    log(...args) {
        if (this.config.debugMode) {
            console.log('[NotificationSystem]', ...args);
        }
    }

    /**
     * Log errors
     */
    error(...args) {
        console.error('[NotificationSystem]', ...args);
    }
}

// Initialize the notification system once DOM is ready
if (typeof window.notificationSystem === 'undefined') {
    window.notificationSystem = new NotificationSystem();
}

console.log('✅ Notification system loaded successfully');
