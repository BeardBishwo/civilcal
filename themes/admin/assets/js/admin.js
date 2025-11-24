/**
 * Bishwo Calculator Admin JavaScript
 * Beautiful, Interactive Admin Interface
 */

// Admin App Object
const AdminApp = {
    init() {
        this.initSidebar();
        this.initUserMenu();
        this.initNotifications();
        this.initCharts();
        this.initAjaxForms();
        this.loadDashboardData();
    },

    // Sidebar Management
    initSidebar() {
        // Sidebar toggle
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const mobileSidebarToggle = document.getElementById('mobile-sidebar-toggle');
        const sidebar = document.getElementById('admin-sidebar');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
            });
        }

        if (mobileSidebarToggle) {
            mobileSidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });
        }

        // Submenu handling
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', (e) => {
                const parent = link.closest('.nav-item');
                const submenu = parent.querySelector('.nav-submenu');

                if (submenu) {
                    e.preventDefault();
                    parent.classList.toggle('active');

                    // Close other submenus
                    document.querySelectorAll('.nav-item').forEach(item => {
                        if (item !== parent && !item.querySelector('.nav-submenu a[href="' + window.location.pathname + '"]')) {
                            item.classList.remove('active');
                        }
                    });
                }
            });
        });

        // Auto-open active submenu
        const activeSubmenuLink = document.querySelector('.nav-submenu a[href="' + window.location.pathname + '"]');
        if (activeSubmenuLink) {
            activeSubmenuLink.closest('.nav-item').classList.add('active');
        }
    },

    // User Menu Management
    initUserMenu() {
        const userAvatar = document.querySelector('.user-avatar');
        const userDropdown = document.getElementById('user-dropdown');

        if (userAvatar && userDropdown) {
            userAvatar.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', () => {
                userDropdown.classList.remove('show');
            });
        }
    },

    // Notification System
    initNotifications() {
        // Auto-hide alerts after 5 seconds
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });
    },

    // Charts Initialization
    initCharts() {
        // User Growth Chart
        const userGrowthCanvas = document.getElementById('userGrowthChart');
        if (userGrowthCanvas) {
            new Chart(userGrowthCanvas, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'New Users',
                        data: [12, 19, 3, 5, 2, 3],
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#e5e7eb'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Calculator Usage Pie Chart
        const calculatorUsageCanvas = document.getElementById('calculatorUsageChart');
        if (calculatorUsageCanvas) {
            new Chart(calculatorUsageCanvas, {
                type: 'doughnut',
                data: {
                    labels: ['Concrete Volume', 'Rebar Calculator', 'Foundation Design', 'Others'],
                    datasets: [{
                        data: [35, 25, 20, 20],
                        backgroundColor: [
                            '#4f46e5',
                            '#10b981',
                            '#f59e0b',
                            '#ef4444'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    }
                }
            });
        }
    },

    // AJAX Form Handling
    initAjaxForms() {
        document.querySelectorAll('.ajax-form').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const submitBtn = form.querySelector('[type="submit"]');
                const originalText = submitBtn.textContent;

                // Show loading
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

                try {
                    const formData = new FormData(form);
                    const response = await fetch(form.action, {
                        method: form.method,
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.showNotification(result.message || 'Operation completed successfully!', 'success');
                        if (result.redirect) {
                            setTimeout(() => window.location.href = result.redirect, 1500);
                        }
                    } else {
                        this.showNotification(result.error || 'An error occurred', 'error');
                    }
                } catch (error) {
                    this.showNotification('Network error occurred', 'error');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            });
        });
    },

    // Load Dashboard Data
    async loadDashboardData() {
        // Only attempt to load dashboard data if we're on the dashboard page
        const isDashboardPage = window.location.pathname.includes('/admin/dashboard') || window.location.pathname.includes('/admin');
        if (!isDashboardPage) return;

        try {
            const response = await fetch('/api/admin/dashboard/stats');
            if (!response.ok) {
                // API endpoint doesn't exist yet, silently fail
                console.log('Dashboard stats API not available yet');
                return;
            }
            const data = await response.json();

            if (data.success) {
                this.updateDashboardStats(data.stats);
            }
        } catch (error) {
            // Silently fail - dashboard stats are not critical
            console.log('Dashboard stats not available:', error.message);
        }
    },

    // Update Dashboard Statistics
    updateDashboardStats(stats) {
        // Update user stats
        if (stats.users) {
            this.updateStatCard('total-users', stats.users.total);
            this.updateStatCard('active-users', stats.users.active);
            this.updateStatCard('new-users-today', stats.users.new_today);
        }

        // Update system stats
        if (stats.system) {
            this.updateStatCard('memory-usage', stats.system.memory_usage);
            this.updateStatCard('storage-used', stats.system.storage_used);
        }

        // Update modules stats
        if (stats.modules) {
            this.updateStatCard('active-modules', stats.modules.active);
            this.updateStatCard('total-modules', stats.modules.total);
        }
    },

    // Update Individual Stat Card
    updateStatCard(cardId, value) {
        const card = document.getElementById(cardId);
        if (card) {
            const valueElement = card.querySelector('.stat-value');
            if (valueElement) {
                // Animate value change
                this.animateValue(valueElement, 0, value, 1000);
            }
        }
    },

    // Animate Number Values
    animateValue(element, start, end, duration) {
        const startTime = performance.now();
        const startValue = parseInt(start);
        const endValue = parseInt(end);

        const updateValue = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            const currentValue = Math.floor(startValue + (endValue - startValue) * progress);
            element.textContent = currentValue.toLocaleString();

            if (progress < 1) {
                requestAnimationFrame(updateValue);
            }
        };

        requestAnimationFrame(updateValue);
    },

    // Show Notification Toast
    showNotification(message, type = 'info') {
        const toast = document.getElementById('notification-toast');
        if (!toast) return;

        toast.className = `notification-toast ${type}`;
        toast.innerHTML = `
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fas ${this.getNotificationIcon(type)}"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.classList.remove('show')" 
                        style="margin-left: auto; background: none; border: none; font-size: 16px; cursor: pointer;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        toast.classList.add('show');

        setTimeout(() => {
            toast.classList.remove('show');
        }, 5000);
    },

    // Get Notification Icon
    getNotificationIcon(type) {
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };
        return icons[type] || icons.info;
    }
};

// Module Management Functions
const ModuleManager = {
    async toggleModule(moduleName, action) {
        AdminApp.showLoading(true);

        try {
            const response = await fetch('/api/admin/modules/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    module: moduleName,
                    action: action
                })
            });

            const result = await response.json();

            if (result.success) {
                AdminApp.showNotification(result.message, 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                AdminApp.showNotification(result.error, 'error');
            }
        } catch (error) {
            AdminApp.showNotification('Network error occurred', 'error');
        } finally {
            AdminApp.showLoading(false);
        }
    }
};

// Backup Functions
async function createBackup() {
    AdminApp.showLoading(true, 'Creating backup...');

    try {
        const response = await fetch('/api/admin/backup/create', {
            method: 'POST'
        });

        const result = await response.json();

        if (result.success) {
            AdminApp.showNotification(`Backup created: ${result.backup_name} (${result.file_size})`, 'success');
        } else {
            AdminApp.showNotification('Backup creation failed', 'error');
        }
    } catch (error) {
        AdminApp.showNotification('Network error occurred', 'error');
    } finally {
        AdminApp.showLoading(false);
    }
}

// System Health Check
async function checkSystemHealth() {
    AdminApp.showLoading(true, 'Checking system health...');

    try {
        const response = await fetch('/api/admin/system/health');
        const result = await response.json();

        if (result.success) {
            displayHealthResults(result.health);
        } else {
            AdminApp.showNotification('Health check failed', 'error');
        }
    } catch (error) {
        AdminApp.showNotification('Network error occurred', 'error');
    } finally {
        AdminApp.showLoading(false);
    }
}

function displayHealthResults(health) {
    const modal = document.createElement('div');
    modal.className = 'health-modal';
    modal.innerHTML = `
        <div class="modal-overlay" onclick="this.parentElement.remove()">
            <div class="modal-content" onclick="event.stopPropagation()">
                <div class="modal-header">
                    <h3>System Health Check</h3>
                    <button onclick="this.closest('.health-modal').remove()" class="close-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="health-status ${health.overall_status}">
                        <i class="fas ${getHealthIcon(health.overall_status)}"></i>
                        <span>System Status: ${health.overall_status.toUpperCase()}</span>
                    </div>
                    <div class="health-checks">
                        ${Object.entries(health.checks).map(([key, check]) => `
                            <div class="health-check ${check.status}">
                                <i class="fas ${getHealthIcon(check.status)}"></i>
                                <span class="check-name">${key.replace(/_/g, ' ').toUpperCase()}</span>
                                <span class="check-message">${check.message}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);
}

function getHealthIcon(status) {
    const icons = {
        pass: 'fa-check-circle',
        warning: 'fa-exclamation-triangle',
        fail: 'fa-times-circle',
        healthy: 'fa-heart',
        critical: 'fa-exclamation-circle'
    };
    return icons[status] || 'fa-question-circle';
}

// Loading Overlay Management
AdminApp.showLoading = function (show, message = 'Loading...') {
    const overlay = document.getElementById('loading-overlay');
    const spinner = overlay?.querySelector('.loading-spinner span');

    if (show) {
        if (spinner) spinner.textContent = message;
        overlay?.style.setProperty('display', 'flex');
    } else {
        overlay?.style.setProperty('display', 'none');
    }
};

// User Dropdown Toggle
function toggleUserDropdown() {
    const dropdown = document.getElementById('user-dropdown');
    dropdown.classList.toggle('show');
}

// Data Table Functions
function initDataTables() {
    document.querySelectorAll('.data-table').forEach(table => {
        // Add search functionality
        const searchInput = table.parentElement.querySelector('.table-search');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                filterTable(table, e.target.value);
            });
        }

        // Add sorting
        table.querySelectorAll('th[data-sortable]').forEach(th => {
            th.style.cursor = 'pointer';
            th.addEventListener('click', () => {
                sortTable(table, th.cellIndex, th.dataset.type || 'string');
            });
        });
    });
}

function filterTable(table, query) {
    const rows = table.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query.toLowerCase()) ? '' : 'none';
    });
}

function sortTable(table, columnIndex, type) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));

    rows.sort((a, b) => {
        const aVal = a.cells[columnIndex].textContent.trim();
        const bVal = b.cells[columnIndex].textContent.trim();

        if (type === 'number') {
            return parseFloat(aVal) - parseFloat(bVal);
        } else if (type === 'date') {
            return new Date(aVal) - new Date(bVal);
        } else {
            return aVal.localeCompare(bVal);
        }
    });

    rows.forEach(row => tbody.appendChild(row));
}

// Initialize Admin App when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    AdminApp.init();
    initDataTables();
});

// Global error handler - only log to console, don't show notifications
window.addEventListener('error', (e) => {
    console.error('Admin Error:', e.error);
    // Don't show notification for every error - too aggressive
    // AdminApp.showNotification('An unexpected error occurred', 'error');
});

// Export for global use
window.AdminApp = AdminApp;
window.ModuleManager = ModuleManager;
