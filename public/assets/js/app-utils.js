/**
 * Bishwo Calculator - Application Utilities
 * Helper functions for URL management and common operations
 */

/**
 * Build a URL with the application base path
 * @param {string} path - The path to append to base URL
 * @returns {string} Complete URL with base path
 */
function appUrl(path = '') {
    const baseUrl = window.APP_BASE_URL || '/';
    const cleanPath = path.replace(/^\/+/, ''); // Remove leading slashes
    return baseUrl + cleanPath;
}

/**
 * Build an API URL with the application base path
 * @param {string} endpoint - The API endpoint
 * @returns {string} Complete API URL
 */
function apiUrl(endpoint = '') {
    return appUrl('api/' + endpoint.replace(/^\/+/, ''));
}

/**
 * Build an admin URL with the application base path
 * @param {string} path - The admin path
 * @returns {string} Complete admin URL
 */
function adminUrl(path = '') {
    return appUrl('admin/' + path.replace(/^\/+/, ''));
}

/**
 * Build an asset URL
 * @param {string} path - The asset path
 * @returns {string} Complete asset URL
 */
function assetUrl(path = '') {
    return appUrl('public/assets/' + path.replace(/^\/+/, ''));
}

/**
 * Navigate to a URL within the application
 * @param {string} path - The path to navigate to
 */
function navigateTo(path) {
    window.location.href = appUrl(path);
}

/**
 * Navigate to an admin URL
 * @param {string} path - The admin path to navigate to
 */
function navigateToAdmin(path) {
    window.location.href = adminUrl(path);
}

/**
 * Make a fetch request to an API endpoint
 * @param {string} endpoint - The API endpoint
 * @param {object} options - Fetch options
 * @returns {Promise} Fetch promise
 */
function apiFetch(endpoint, options = {}) {
    return fetch(apiUrl(endpoint), {
        ...options,
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...options.headers
        }
    });
}

/**
 * Show a toast notification
 * @param {string} message - The message to display
 * @param {string} type - The type of notification (success, error, warning, info)
 * @param {number} duration - Duration in milliseconds
 */
function showToast(message, type = 'info', duration = 5000) {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fas fa-${getToastIcon(type)}"></i>
            <span>${message}</span>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

/**
 * Get the appropriate icon for a toast type
 * @param {string} type - The toast type
 * @returns {string} Font Awesome icon name
 */
function getToastIcon(type) {
    const icons = {
        success: 'check-circle',
        error: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    return icons[type] || 'info-circle';
}

/**
 * Format a number with commas
 * @param {number} num - The number to format
 * @returns {string} Formatted number
 */
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

/**
 * Debounce function
 * @param {function} func - The function to debounce
 * @param {number} wait - Wait time in milliseconds
 * @returns {function} Debounced function
 */
function debounce(func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Copy text to clipboard
 * @param {string} text - Text to copy
 * @returns {Promise<boolean>} Success status
 */
async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        showToast('Copied to clipboard!', 'success', 2000);
        return true;
    } catch (err) {
        showToast('Failed to copy to clipboard', 'error');
        return false;
    }
}

// Export functions for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        appUrl,
        apiUrl,
        adminUrl,
        assetUrl,
        navigateTo,
        navigateToAdmin,
        apiFetch,
        showToast,
        formatNumber,
        debounce,
        copyToClipboard
    };
}

// Add toast styles if not already present
if (!document.getElementById('toast-styles')) {
    const style = document.createElement('style');
    style.id = 'toast-styles';
    style.textContent = `
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-radius: 8px;
            padding: 1rem 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 1rem;
            transform: translateX(400px);
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 10000;
            max-width: 400px;
        }
        
        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }
        
        .toast-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex: 1;
        }
        
        .toast-success {
            border-left: 4px solid #10b981;
            color: #065f46;
        }
        
        .toast-error {
            border-left: 4px solid #ef4444;
            color: #991b1b;
        }
        
        .toast-warning {
            border-left: 4px solid #f59e0b;
            color: #92400e;
        }
        
        .toast-info {
            border-left: 4px solid #3b82f6;
            color: #1e40af;
        }
        
        .toast-close {
            background: none;
            border: none;
            font-size: 1.25rem;
            cursor: pointer;
            opacity: 0.5;
            padding: 0;
            line-height: 1;
        }
        
        .toast-close:hover {
            opacity: 1;
        }
        
        @media (max-width: 640px) {
            .toast {
                left: 20px;
                right: 20px;
                max-width: none;
            }
        }
    `;
    document.head.appendChild(style);
}
