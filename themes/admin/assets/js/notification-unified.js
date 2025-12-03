/**
 * Bishwo Calculator - Unified Notification System
 * Enhanced real-time notification system combining best features from both implementations
 * with comprehensive error handling, debugging, and user feedback
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
            maxRetries: 5,
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
            apiBase: '/api/notifications',
            notificationSounds: {
                enabled: true,
                volume: 0.3
            },
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

        this.log('🚀 Initializing unified notification system...');

        // Cache DOM elements
        this.cacheElements();

        // Check if we have required elements
        if (!this.elements.toggle) {
            this.error('Notification toggle button not found. System will not initialize.');
            return;
        }

        // Set up event listeners when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }

        this.state.isInitialized = true;
        this.log('✅ Unified notification system initialized successfully');
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
     * Set up event listeners and start polling
     */
    setup() {
        this.log('🔧 Setting up notification system...');

        // Set up click handler for notification button
        this.setupClickHandler();

        // Set up click outside handler
        this.setupOutsideClickHandler();

        // Set up mark all read button
        this.setupMarkAllReadHandler();

        // Load initial notification count
        this.fetchUnreadCount();

        // Start real-time polling
        this.startPolling();

        this.log('✅ Notification system setup complete');
    }

    /**
     * Set up notification button click handler
     */
    setupClickHandler() {
        if (!this.elements.toggle) return;

        // Remove any existing event listeners to prevent conflicts
        const clone = this.elements.toggle.cloneNode(true);
        this.elements.toggle.parentNode.replaceChild(clone, this.elements.toggle);
        this.elements.toggle = clone;

        this.elements.toggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.toggleDropdown();
        });

        this.log('✅ Notification button click handler attached');
    }

    /**
     * Set up outside click handler to close dropdown
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
     * Set up mark all read button handler
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

        // Load notifications if not already loaded
        if (!this.elements.dropdown.dataset.loaded) {
            this.fetchNotifications();
            this.elements.dropdown.dataset.loaded = 'true';
        }

        this.log('🔔 Notification dropdown opened');
    }

    /**
     * Close notification dropdown
     */
    closeDropdown() {
        if (this.elements.dropdown) {
            this.state.isDropdownOpen = false;
            this.elements.dropdown.classList.remove('show');
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

        this.log('🕒 Starting notification polling every 30 seconds...');

        // Initial fetch with small delay
        setTimeout(() => {
            this.fetchUnreadCount();
        }, 2000);

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
                this.playNotificationSound();
            }

            this.state.lastUnreadCount = newCount;
            this.state.retryCount = 0; // Reset retry count on success

            this.log(`📊 Unread count: ${newCount}`);

        } catch (error) {
            this.handlePollingError(error);
        }
    }

    /**
     * Handle polling errors with retry logic using exponential backoff strategy
     */
    handlePollingError(error) {
        this.state.retryCount++;
        this.error('Polling error:', error);

        if (this.state.retryCount <= this.state.maxRetries) {
            this.log(`Retrying notification fetch (${this.state.retryCount}/${this.state.maxRetries})...`);

            // Exponential backoff strategy - multiply delay by 2^(retryCount-1) with maximum cap
            const baseDelay = 5000;
            const exponentialFactor = Math.pow(2, this.state.retryCount - 1);
            const delay = Math.min(baseDelay * exponentialFactor, 60000); // Cap at 60 seconds for reasonable wait times

            this.log(`Using exponential backoff: ${delay}ms delay (factor: ${exponentialFactor})`);

            setTimeout(() => {
                this.fetchUnreadCount();
            }, delay);
        } else {
            this.error('Max retries reached for notification polling');
            this.showErrorState('Unable to fetch notifications. Please refresh the page.');

            // Show error toast to user
            this.showNotificationToast('Connection lost. Retrying...', 'error');
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

            const url = new URL(`${this.config.apiBase}/list`);
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
                <button onclick="window.notificationSystem.fetchNotifications()" class="btn btn-sm btn-primary">
                    <i class="fas fa-refresh"></i> Retry
                </button>
            `);
        } finally {
            this.state.isLoading = false;
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
                this.closeDropdown();
            } else {
                throw new Error(data.message || 'Failed to mark all as read');
            }

        } catch (error) {
            this.error('Error marking all as read:', error);
            this.showNotificationToast('Failed to mark all notifications as read.', 'error');
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

            return true;

        } catch (error) {
            this.error('Error marking notification as read:', error);
            return false;
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
                <div class="empty">
                    <i class="fas fa-bell-slash"></i>
                    <span>No new notifications</span>
                </div>
            `;
            return;
        }

        const notificationHTML = this.state.notifications.map(notification => `
            <div class="notification-item ${notification.is_read ? '' : 'unread'}" data-id="${notification.id}">
                <div class="notification-icon">
                    <i class="fas ${this.getNotificationIcon(notification.type)}"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header">
                        <h4 class="notification-title">${this.escapeHtml(notification.title)}</h4>
                        <span class="notification-time">${this.formatTimeAgo(notification.created_at)}</span>
                    </div>
                    <div class="notification-message">${this.escapeHtml(notification.message)}</div>
                </div>
            </div>
        `).join('');

        this.elements.list.innerHTML = notificationHTML;

        // Add click handlers to notification items
        this.elements.list.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', async () => {
                const notificationId = item.getAttribute('data-id');
                if (!item.classList.contains('unread')) return;

                // Mark as read
                const success = await this.markAsRead(notificationId);
                if (success) {
                    item.classList.remove('unread');
                    this.updateBadge(this.state.unreadCount - 1);
                }
            });
        });
    }

    /**
     * Show notification toast message
     */
    showNotificationToast(message, type = 'info') {
        if (!this.elements.toast) return;

        const iconMap = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };

        this.elements.toast.className = `notification-toast notification-${type} show`;
        this.elements.toast.innerHTML = `
            <div class="toast-content">
                <div class="toast-icon">
                    <i class="fas ${iconMap[type] || iconMap.info}"></i>
                </div>
                <div class="toast-message">${this.escapeHtml(message)}</div>
                <button class="toast-close" onclick="this.parentElement.parentElement.classList.remove('show')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (this.elements.toast.classList.contains('show')) {
                this.elements.toast.classList.remove('show');
            }
        }, 5000);

        this.log(`🔔 Toast shown: ${message}`);
    }

    /**
     * Play notification sound
     */
    playNotificationSound() {
        if (!this.config.notificationSounds.enabled) return;

        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.value = 800;
            oscillator.type = 'sine';

            gainNode.gain.setValueAtTime(0, audioContext.currentTime);
            gainNode.gain.linearRampToValueAtTime(this.config.notificationSounds.volume, audioContext.currentTime + 0.01);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.3);

        } catch (error) {
            this.log('Could not play notification sound:', error);
        }
    }

    /**
     * Get notification icon based on type
     */
    getNotificationIcon(type) {
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle',
            system: 'fa-cog',
            user: 'fa-user',
            security: 'fa-shield-alt',
            backup: 'fa-database',
            update: 'fa-sync-alt'
        };
        return icons[type] || icons.info;
    }

    /**
     * Format time as "X minutes ago"
     */
    formatTimeAgo(dateString) {
        if (!dateString) return '';

        const date = new Date(dateString);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);

        if (diffInSeconds < 60) {
            return `${diffInSeconds}s ago`;
        } else if (diffInSeconds < 3600) {
            return `${Math.floor(diffInSeconds / 60)}m ago`;
        } else if (diffInSeconds < 86400) {
            return `${Math.floor(diffInSeconds / 3600)}h ago`;
        } else {
            return `${Math.floor(diffInSeconds / 86400)}d ago`;
        }
    }

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Log messages with debug info
     */
    log(...args) {
        if (this.config.debugMode) {
            console.log('🔔 NotificationSystem:', ...args);
        }
    }

    /**
     * Log error messages
     */
    error(...args) {
        console.error('❌ NotificationSystem Error:', ...args);
    }
}

// Initialize the unified notification system
const notificationSystem = new NotificationSystem();

// Make it globally available for debugging and external access
window.notificationSystem = notificationSystem;

// Export for module systems if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NotificationSystem;
}