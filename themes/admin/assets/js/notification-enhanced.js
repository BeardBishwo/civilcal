/**
 * Enhanced Notification System with Better Error Handling
 * Improved real-time polling and user experience
 */

class EnhancedNotificationSystem extends NotificationSystem {
    constructor() {
        super();
        
        // Enhanced features
        this.enhancedConfig = {
            soundEnabled: true,
            animationDuration: 300,
            retryDelays: [2000, 5000, 10000, 30000],
            maxRetries: 4
        };
    }

    /**
     * Enhanced error handling with better retry logic
     */
    handlePollingError(error) {
        this.state.retryCount++;
        this.error('Enhanced polling error:', error);

        if (this.state.retryCount <= this.enhancedConfig.maxRetries) {
            const delay = this.enhancedConfig.retryDelays[this.state.retryCount - 1] || 30000;
            
            this.log(`Enhanced retry ${this.state.retryCount}/${this.enhancedConfig.maxRetries} in ${delay}ms...`);
            
            setTimeout(() => {
                this.fetchUnreadCount();
            }, delay);
        } else {
            this.error('Max enhanced retries reached');
            this.showEnhancedErrorState();
        }
    }

    /**
     * Show enhanced error state
     */
    showEnhancedErrorState() {
        if (this.elements.list) {
            this.elements.list.innerHTML = `
                <div class="error-state enhanced-error">
                    <div class="error-icon">
                        <i class="fas fa-wifi"></i>
                    </div>
                    <div class="error-message">
                        <h4>Connection Lost</h4>
                        <p>Unable to fetch notifications. Please check your connection.</p>
                        <button onclick="window.notificationSystem.retryConnection()" class="btn btn-sm btn-primary">
                            <i class="fas fa-redo"></i> Retry Connection
                        </button>
                    </div>
                </div>
            `;
        }
        
        this.showNotificationToast('Connection lost. Trying to reconnect...', 'error');
    }

    /**
     * Retry connection with enhanced logic
     */
    async retryConnection() {
        this.state.retryCount = 0;
        this.showLoadingState();
        
        try {
            await this.fetchUnreadCount();
            await this.fetchNotifications();
            this.showNotificationToast('Connection restored!', 'success');
        } catch (error) {
            this.error('Retry connection failed:', error);
            this.showEnhancedErrorState();
        }
    }

    /**
     * Enhanced loading state
     */
    showLoadingState() {
        if (this.elements.list) {
            this.elements.list.innerHTML = `
                <div class="loading-state enhanced-loading">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <div class="loading-text">
                        <h4>Loading Notifications</h4>
                        <p>Please wait while we fetch your notifications...</p>
                    </div>
                </div>
            `;
        }
    }

    /**
     * Enhanced notification rendering
     */
    renderNotifications() {
        if (!this.elements.list) return;

        if (this.state.notifications.length === 0) {
            this.elements.list.innerHTML = `
                <div class="empty-state enhanced-empty">
                    <div class="empty-icon">
                        <i class="fas fa-bell-slash"></i>
                    </div>
                    <div class="empty-message">
                        <h4>All Caught Up!</h4>
                        <p>You have no new notifications at this time.</p>
                    </div>
                </div>
            `;
            return;
        }

        const notificationHTML = this.state.notifications.map((notification, index) => `
            <div class="notification-item enhanced-notification ${notification.is_read ? '' : 'unread'}" 
                 data-id="${notification.id}" 
                 style="animation-delay: ${index * 0.1}s">
                <div class="notification-icon enhanced-icon">
                    <i class="fas ${this.getNotificationIcon(notification.type)}"></i>
                </div>
                <div class="notification-content enhanced-content">
                    <div class="notification-header enhanced-header">
                        <h4 class="notification-title enhanced-title">${this.escapeHtml(notification.title)}</h4>
                        <span class="notification-time enhanced-time">${this.formatTimeAgo(notification.created_at)}</span>
                    </div>
                    <div class="notification-message enhanced-message">${this.escapeHtml(notification.message)}</div>
                </div>
            </div>
        `).join('');

        this.elements.list.innerHTML = notificationHTML;

        // Enhanced click handlers
        this.elements.list.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', async (e) => {
                e.preventDefault();
                const notificationId = item.getAttribute('data-id');
                if (!item.classList.contains('unread')) return;

                // Enhanced mark as read with visual feedback
                item.style.opacity = '0.6';
                const success = await this.markAsRead(notificationId);
                
                if (success) {
                    item.classList.remove('unread');
                    item.style.opacity = '1';
                    this.updateBadge(this.state.unreadCount - 1);
                    
                    // Visual feedback
                    item.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        item.style.transform = 'scale(1)';
                    }, 150);
                }
            });
        });
    }
}

// Replace the existing notification system with enhanced version
if (window.notificationSystem) {
    window.notificationSystem = new EnhancedNotificationSystem();
} else {
    window.notificationSystem = new EnhancedNotificationSystem();
}

console.log('✅ Enhanced notification system loaded');
