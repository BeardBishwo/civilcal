/**
 * Enhanced Real-time Notification System for Admin Panel
 * Standalone implementation with improved error handling and real-time features
 */

// Notification System Class
class NotificationSystem {
    constructor() {
        this.lastUnreadCount = 0;
        this.notificationInterval = null;
        this.isInitialized = false;
        this.retryCount = 0;
        this.maxRetries = 3;
        this.notificationSounds = {
            enabled: true,
            volume: 0.3
        };
    }

    // Initialize the notification system
    init() {
        if (this.isInitialized) return;

        console.log('🔔 Initializing notification system...');

        // Check if we're on an admin page
        if (!window.location.pathname.includes('/admin')) {
            console.log('📍 Not on admin page, skipping notification initialization');
            return;
        }

        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initializeElements());
        } else {
            this.initializeElements();
        }

        this.isInitialized = true;
        console.log('✅ Enhanced notification system initialized');
    }

    // Initialize DOM elements and event listeners
    initializeElements() {
        // Add click handler to notification button with better selector
        const notificationBtn = document.querySelector('#notificationToggle') || 
                               document.querySelector('button[title="Notifications"]');
        
        if (notificationBtn) {
            notificationBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleNotificationDropdown();
            });
            console.log('✅ Notification button click handler attached');
        } else {
            console.warn('⚠️ Notification button not found');
        }

        // Add click outside handler to close dropdown
        document.addEventListener('click', (e) => {
            const dropdown = document.getElementById('notificationDropdown');
            const button = document.querySelector('#notificationToggle');
            
            if (dropdown && !dropdown.contains(e.target) && !button?.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });

        // Initialize polling for real-time updates
        this.startNotificationPolling();

        // Load initial notification count
        this.loadInitialNotificationCount();
    }

    // Start notification polling with enhanced reliability
    startNotificationPolling() {
        // Poll every 30 seconds for new notifications
        this.notificationInterval = setInterval(() => {
            this.fetchUnreadCount();
        }, 30000);

        // Initial fetch with delay to ensure page is fully loaded
        setTimeout(() => {
            this.fetchUnreadCount();
        }, 2000);
    }

    // Fetch unread notification count with retry logic
    async fetchUnreadCount() {
        try {
            const response = await fetch('/api/notifications/unread-count', {
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

            if (data.success && typeof data.unread_count === 'number') {
                this.updateNotificationBadge(data.unread_count);

                // Show toast if new notifications arrived
                if (data.unread_count > (this.lastUnreadCount || 0)) {
                    const newCount = data.unread_count - (this.lastUnreadCount || 0);
                    this.showNotification(
                        `${newCount} new ${newCount === 1 ? 'notification' : 'notifications'}`,
                        'info'
                    );
                    this.playNotificationSound();
                }

                this.lastUnreadCount = data.unread_count;
                this.retryCount = 0; // Reset retry count on success
            } else {
                throw new Error('Invalid response format');
            }
        } catch (error) {
            console.error('Notification polling error:', error);
            this.handlePollingError(error);
        }
    }

    // Handle polling errors with retry logic
    handlePollingError(error) {
        this.retryCount++;
        
        if (this.retryCount <= this.maxRetries) {
            console.log(`Retrying notification fetch (${this.retryCount}/${this.maxRetries})...`);
            setTimeout(() => {
                this.fetchUnreadCount();
            }, 5000 * this.retryCount); // Exponential backoff
        } else {
            console.warn('Max retries reached for notification polling');
            this.showNotification('Unable to fetch notifications. Please refresh the page.', 'error');
        }
    }

    // Update notification badge
    updateNotificationBadge(count) {
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'inline-block';
            } else {
                badge.textContent = '';
                badge.style.display = 'none';
            }
        }
    }

    // Load initial notification count
    async loadInitialNotificationCount() {
        try {
            const response = await fetch('/api/notifications/unread-count');
            const data = await response.json();

            if (data.success) {
                this.updateNotificationBadge(data.unread_count);
                this.lastUnreadCount = data.unread_count;
            }
        } catch (error) {
            console.error('Failed to load initial notification count:', error);
        }
    }

    // Toggle notification dropdown with enhanced functionality
    toggleNotificationDropdown() {
        const dropdown = document.getElementById('notificationDropdown');
        if (!dropdown) {
            console.warn('Notification dropdown not found');
            return;
        }

        const isVisible = dropdown.classList.contains('show');
        
        // Close dropdown if it's currently open
        if (isVisible) {
            dropdown.classList.remove('show');
            return;
        }

        // Open dropdown and load notifications
        dropdown.classList.add('show');
        
        // Load notifications if not already loaded or if we want to refresh
        if (!dropdown.dataset.loaded || dropdown.dataset.needsRefresh === 'true') {
            this.loadNotifications();
            dropdown.dataset.loaded = 'true';
            dropdown.dataset.needsRefresh = 'false';
        }

        console.log('🔔 Notification dropdown opened');
    }

    // Load notifications for dropdown with enhanced error handling
    async loadNotifications() {
        const dropdown = document.getElementById('notificationDropdown');
        const listContainer = dropdown?.querySelector('.notification-list');
        
        if (!listContainer) {
            console.error('Notification list container not found');
            return;
        }

        // Show loading state
        listContainer.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Loading notifications...</div>';

        try {
            const response = await fetch('/api/notifications/list?limit=10&unread_only=true', {
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

            if (data.success && Array.isArray(data.notifications)) {
                this.renderNotificationDropdown(data.notifications);
                console.log(`✅ Loaded ${data.notifications.length} notifications`);
            } else {
                throw new Error('Invalid response format');
            }
        } catch (error) {
            console.error('Failed to load notifications:', error);
            listContainer.innerHTML = `
                <div class="error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Failed to load notifications</span>
                    <button onclick="window.notificationSystem.loadNotifications()" class="btn btn-sm btn-primary">
                        <i class="fas fa-refresh"></i> Retry
                    </button>
                </div>
            `;
        }
    }

    // Render notification dropdown
    renderNotificationDropdown(notifications) {
        const dropdown = document.getElementById('notificationDropdown');
        if (!dropdown) return;

        const listContainer = dropdown.querySelector('.notification-list');
        if (!listContainer) return;

        if (notifications.length === 0) {
            listContainer.innerHTML = `
                <div class="notification-item empty">
                    <i class="fas fa-bell-slash"></i>
                    <span>No new notifications</span>
                </div>
            `;
            return;
        }

        listContainer.innerHTML = notifications.map(notification => `
            <div class="notification-item ${notification.is_read ? '' : 'unread'}" data-id="${notification.id}">
                <div class="notification-icon">
                    <i class="fas ${this.getNotificationIcon(notification.type)}"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-header">
                        <h4 class="notification-title">${notification.title}</h4>
                        <span class="notification-time">${this.formatTimeAgo(notification.created_at)}</span>
                    </div>
                    <div class="notification-message">${notification.message}</div>
                </div>
            </div>
        `).join('');

        // Add click handlers to notification items
        listContainer.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', () => {
                const notificationId = item.getAttribute('data-id');
                if (!item.classList.contains('unread')) return;

                // Mark as read
                this.markNotificationAsRead(notificationId);

                // Remove unread class
                item.classList.remove('unread');

                // Update badge count
                const currentCount = parseInt(document.querySelector('.notification-badge')?.textContent || '0');
                this.updateNotificationBadge(currentCount - 1);
            });
        });
    }

    // Mark notification as read
    async markNotificationAsRead(notificationId) {
        try {
            const response = await fetch(`/admin/notifications/mark-read/${notificationId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();
            if (!data.success) {
                console.error('Failed to mark notification as read');
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    // Play notification sound
    playNotificationSound() {
        if (!this.notificationSounds.enabled) return;

        try {
            // Create a simple beep sound using Web Audio API
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.value = 800;
            oscillator.type = 'sine';

            gainNode.gain.setValueAtTime(0, audioContext.currentTime);
            gainNode.gain.linearRampToValueAtTime(this.notificationSounds.volume, audioContext.currentTime + 0.01);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.3);
        } catch (error) {
            console.warn('Could not play notification sound:', error);
        }
    }

    // Show notification toast with enhanced styling
    showNotification(message, type = 'info', duration = 5000) {
        const toast = document.getElementById('notification-toast');
        if (!toast) {
            console.warn('Notification toast element not found');
            return;
        }

        const iconMap = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle',
            system: 'fa-cog'
        };

        toast.className = `notification-toast notification-${type} show`;
        toast.innerHTML = `
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

        // Auto-hide after specified duration
        setTimeout(() => {
            if (toast.classList.contains('show')) {
                toast.classList.remove('show');
            }
        }, duration);

        console.log(`🔔 Notification shown: ${message}`);
    }

    // Escape HTML to prevent XSS
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Get notification icon based on type
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

    // Format time as "X minutes ago"
    formatTimeAgo(dateString) {
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
}

// Initialize notification system when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const notificationSystem = new NotificationSystem();
    notificationSystem.init();

    // Make it globally available for debugging
    window.notificationSystem = notificationSystem;
});