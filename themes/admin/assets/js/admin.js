/**
 * Bishwo Calculator Admin JavaScript
 * Beautiful, Interactive Admin Interface
 */

// Admin App Object
const AdminApp = {
    sidebarElement: null,
    submenuItems: [],
    submenuInitialized: false,
    submenuResizeHandler: null,
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
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const mobileSidebarToggle = document.getElementById('mobile-sidebar-toggle');
        const sidebar = document.getElementById('admin-sidebar');
        const mainContent = document.getElementById('admin-main');
        this.sidebarElement = sidebar;

        const syncSidebarState = (shouldCollapse, persist = true) => {
            if (!sidebar) {
                return;
            }

            sidebar.classList.toggle('collapsed', shouldCollapse);

            if (mainContent) {
                mainContent.classList.toggle('sidebar-collapsed', shouldCollapse);

                // Force reflow to ensure content area adjusts properly
                setTimeout(() => {
                    mainContent.style.display = 'none';
                    mainContent.offsetHeight; // Trigger reflow
                    mainContent.style.display = '';

                    // Force charts and other elements to resize
                    const charts = mainContent.querySelectorAll('canvas');
                    charts.forEach(chart => {
                        if (chart.chart) {
                            chart.chart.resize();
                        }
                    });

                    // Sync dashboard layout specifically
                    this.syncDashboardLayout();

                    // Dispatch custom event for other components to listen to
                    window.dispatchEvent(new CustomEvent('sidebarStateChanged', {
                        detail: { collapsed: shouldCollapse }
                    }));
                }, 50);
            }

            this.syncActiveSubmenuDisplay();

            if (persist) {
                localStorage.setItem('adminSidebarCollapsed', shouldCollapse ? 'true' : 'false');
            }
        };

        if (sidebarToggle && sidebar) {
            const newSidebarToggle = sidebarToggle.cloneNode(true);
            sidebarToggle.parentNode.replaceChild(newSidebarToggle, sidebarToggle);

            newSidebarToggle.addEventListener('click', (e) => {
                e.preventDefault();
                const shouldCollapse = !sidebar.classList.contains('collapsed');
                syncSidebarState(shouldCollapse);
            });

            const savedState = localStorage.getItem('adminSidebarCollapsed');
            if (savedState === 'true') {
                syncSidebarState(true, false);
            } else {
                this.syncActiveSubmenuDisplay();
            }
        }

        if (mobileSidebarToggle && sidebar) {
            mobileSidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });
        }

        this.setupSubmenus(sidebar);
    },

    setupSubmenus(sidebar) {
        if (this.submenuInitialized) {
            this.syncActiveSubmenuDisplay();
            return;
        }

        const submenuItems = Array.from(document.querySelectorAll('.nav-item')).filter(item => item.querySelector('.nav-submenu'));

        if (!submenuItems.length) {
            return;
        }

        this.submenuItems = submenuItems;

        submenuItems.forEach(item => {
            const link = item.querySelector('.nav-link');
            if (!link) {
                return;
            }

            link.setAttribute('aria-haspopup', 'true');
            link.setAttribute('aria-expanded', item.classList.contains('active') ? 'true' : 'false');

            if (!link.dataset.submenuInit) {
                link.dataset.submenuInit = 'true';
                link.addEventListener('click', (event) => {
                    event.preventDefault();
                    const shouldExpand = !item.classList.contains('active');

                    this.submenuItems.forEach(other => {
                        this.setSubmenuExpanded(other, other === item ? shouldExpand : false, sidebar);
                    });
                });
            }

            this.setSubmenuExpanded(item, item.classList.contains('active'), sidebar);
        });

        const hasServerActive = submenuItems.some(item => item.classList.contains('active'));
        if (!hasServerActive) {
            this.autoActivateSubmenuForCurrentPath(sidebar);
        } else {
            this.syncActiveSubmenuDisplay();
        }

        if (!this.submenuResizeHandler) {
            this.submenuResizeHandler = () => this.syncActiveSubmenuDisplay();
            window.addEventListener('resize', this.submenuResizeHandler);
        }

        this.submenuInitialized = true;
    },

    setSubmenuExpanded(item, expand, sidebar) {
        const submenu = item.querySelector('.nav-submenu');
        if (!submenu) {
            return;
        }

        item.classList.toggle('active', expand);

        const link = item.querySelector('.nav-link');
        if (link) {
            link.setAttribute('aria-expanded', expand ? 'true' : 'false');
        }

        if (expand) {
            if (sidebar && sidebar.classList.contains('collapsed')) {
                submenu.style.maxHeight = 'none';
                this.positionFloatingSubmenu(item, submenu, sidebar);
            } else {
                this.resetFloatingSubmenu(submenu);
                submenu.style.maxHeight = submenu.scrollHeight + 'px';
            }
        } else {
            submenu.style.maxHeight = null;
            this.resetFloatingSubmenu(submenu);
        }
    },

    positionFloatingSubmenu(item, submenu, sidebar) {
        if (!sidebar || !submenu) {
            return;
        }

        const sidebarWidth = sidebar.offsetWidth || 70;
        submenu.style.position = 'absolute';
        submenu.style.left = `${sidebarWidth}px`;
        submenu.style.top = `${item.offsetTop - sidebar.scrollTop}px`;
        submenu.style.background = 'var(--admin-white)';
        submenu.style.boxShadow = 'var(--admin-shadow-lg)';
        submenu.style.borderRadius = '0 8px 8px 0';
        submenu.style.minWidth = '200px';
        submenu.style.zIndex = '1001';
        submenu.dataset.floating = 'true';
    },

    resetFloatingSubmenu(submenu) {
        if (!submenu) {
            return;
        }

        submenu.style.position = '';
        submenu.style.left = '';
        submenu.style.top = '';
        submenu.style.background = '';
        submenu.style.boxShadow = '';
        submenu.style.borderRadius = '';
        submenu.style.minWidth = '';
        submenu.style.zIndex = '';
        if (submenu.dataset && submenu.dataset.floating) {
            delete submenu.dataset.floating;
        }
    },

    syncActiveSubmenuDisplay() {
        const sidebar = this.sidebarElement || document.getElementById('admin-sidebar');
        if (!sidebar) {
            return;
        }

        const items = this.submenuItems.length ? this.submenuItems : Array.from(document.querySelectorAll('.nav-item')).filter(item => item.querySelector('.nav-submenu'));

        items.forEach(item => {
            if (item.classList.contains('active')) {
                this.setSubmenuExpanded(item, true, sidebar);
            } else {
                const submenu = item.querySelector('.nav-submenu');
                if (submenu) {
                    submenu.style.maxHeight = null;
                    this.resetFloatingSubmenu(submenu);
                }
                const link = item.querySelector('.nav-link');
                if (link) {
                    link.setAttribute('aria-expanded', 'false');
                }
            }
        });
    },

    autoActivateSubmenuForCurrentPath(sidebar) {
        const currentPath = this.normalizePath(window.location.pathname);

        for (const item of this.submenuItems) {
            const submenuLinks = item.querySelectorAll('.nav-submenu a[href]');
            for (const submenuLink of submenuLinks) {
                const linkPath = this.normalizePath(submenuLink.getAttribute('href'));
                if (linkPath && linkPath === currentPath) {
                    this.submenuItems.forEach(other => {
                        this.setSubmenuExpanded(other, other === item, sidebar);
                    });
                    this.syncActiveSubmenuDisplay();
                    return;
                }
            }
        }

        this.syncActiveSubmenuDisplay();
    },

    normalizePath(href) {
        if (!href) {
            return '';
        }

        const stripTrailingSlash = (path) => {
            const cleaned = (path || '').replace(/\/+$/, '') || '/';
            return cleaned.replace(/^\/index\.php/, '') || '/';
        };

        try {
            const url = new URL(href, window.location.origin);
            return stripTrailingSlash(url.pathname);
        } catch (error) {
            return stripTrailingSlash(href);
        }
    },

    // Dashboard Layout Synchronization
    syncDashboardLayout() {
        const dashboardGrid = document.querySelector('.dashboard-grid');
        if (!dashboardGrid) {
            return;
        }

        // Force grid recalculation
        dashboardGrid.style.display = 'none';
        dashboardGrid.offsetHeight; // Trigger reflow
        dashboardGrid.style.display = '';

        // Resize all charts in dashboard
        const charts = dashboardGrid.querySelectorAll('canvas');
        charts.forEach(chart => {
            if (chart.chart) {
                chart.chart.resize();
            }
        });

        // Force dashboard columns to recalculate
        const dashboardLeft = dashboardGrid.querySelector('.dashboard-left');
        const dashboardRight = dashboardGrid.querySelector('.dashboard-right');

        if (dashboardLeft && dashboardRight) {
            // Trigger layout recalculation for columns
            dashboardLeft.style.minWidth = '';
            dashboardRight.style.minWidth = '';

            // Force browser to recalculate layout
            dashboardGrid.style.gridTemplateColumns = '';
            dashboardGrid.offsetHeight; // Force reflow
            dashboardGrid.style.gridTemplateColumns = '';
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
            document.addEventListener('click', (e) => {
                if (!userAvatar.contains(e.target)) {
                    userDropdown.classList.remove('show');
                }
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
        // Check if Chart.js is loaded
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js is not loaded. Charts will not be initialized.');
            // Try to load Chart.js dynamically
            this.loadChartJS().then(() => {
                this.initializeAllCharts();
            }).catch(error => {
                console.error('Failed to load Chart.js:', error);
            });
            return;
        }

        // Set default Chart.js configuration
        this.configureChartDefaults();

        // Initialize all charts
        this.initializeAllCharts();
    },

    // Load Chart.js dynamically if not available
    async loadChartJS() {
        return new Promise((resolve, reject) => {
            // Check if already loading
            if (window.chartJSLoading) {
                window.chartJSLoading.then(resolve).catch(reject);
                return;
            }

            window.chartJSLoading = new Promise((res, rej) => {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js';
                script.onload = () => {
                    console.log('Chart.js loaded successfully');
                    res();
                };
                script.onerror = () => {
                    console.error('Failed to load Chart.js from CDN');
                    rej(new Error('Chart.js failed to load'));
                };
                document.head.appendChild(script);
            });

            window.chartJSLoading.then(resolve).catch(reject);
        });
    },

    // Configure Chart.js defaults
    configureChartDefaults() {
        if (typeof Chart === 'undefined') return;

        Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
        Chart.defaults.color = '#6b7280';
        Chart.defaults.borderColor = '#e5e7eb';
        Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(0, 0, 0, 0.8)';
        Chart.defaults.plugins.tooltip.padding = 12;
        Chart.defaults.plugins.tooltip.cornerRadius = 8;
        Chart.defaults.plugins.tooltip.titleFont = { size: 14, weight: '600' };
        Chart.defaults.plugins.tooltip.bodyFont = { size: 13 };
    },

    // Initialize all charts
    initializeAllCharts() {
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js not available, skipping chart initialization');
            return;
        }

        try {
            this.initializeServerLoadChart();
            this.initializeMemoryUsageChart();
            this.initializeResponseTimeChart();
            this.initializeDbQueriesChart();
            this.initializePerformanceCharts();
            this.initializeDashboardCharts();
            this.initializeAdvancedCharts();
        } catch (error) {
            console.error('Error initializing charts:', error);
        }
    },

    // Initialize additional advanced charts
    initializeAdvancedCharts() {
        // System Performance Chart
        this.initializeSystemPerformanceChart();

        // Revenue Chart
        this.initializeRevenueChart();

        // Activity Heatmap
        this.initializeActivityHeatmap();

        // Resource Usage Chart
        this.initializeResourceUsageChart();
    },

    // Server Load Chart
    initializeServerLoadChart() {
        const ctx = document.getElementById('serverLoadChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: this.generateTimeLabels(24),
                datasets: [{
                    label: 'Server Load',
                    data: this.generateRandomData(24, 0.2, 0.8),
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 1
                    }
                }
            }
        });
    },

    // Memory Usage Chart
    initializeMemoryUsageChart() {
        const ctx = document.getElementById('memoryUsageChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: this.generateTimeLabels(24),
                datasets: [{
                    label: 'Memory Usage (%)',
                    data: this.generateRandomData(24, 40, 80),
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    },

    // Response Time Chart
    initializeResponseTimeChart() {
        const ctx = document.getElementById('responseTimeChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: this.generateTimeLabels(24),
                datasets: [{
                    label: 'Response Time (ms)',
                    data: this.generateRandomData(24, 50, 300),
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    },

    // Database Queries Chart
    initializeDbQueriesChart() {
        const ctx = document.getElementById('dbQueriesChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: this.generateTimeLabels(24),
                datasets: [{
                    label: 'Database Queries',
                    data: this.generateRandomData(24, 100, 500),
                    backgroundColor: '#6f42c1',
                    borderColor: '#6f42c1',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    },

    // Performance Dashboard Charts
    initializePerformanceCharts() {
        // Initialize all performance dashboard charts
        const chartIds = [
            'serverLoadChart', 'memoryUsageChart', 'responseTimeChart',
            'dbQueriesChart', 'userGrowthChart', 'calculatorUsageChart'
        ];

        chartIds.forEach(id => {
            const ctx = document.getElementById(id);
            if (ctx && !ctx.chart) {
                // Chart will be initialized by specific functions above
                ctx.chart = 'initialized';
            }
        });
    },

    // Dashboard Charts
    initializeDashboardCharts() {
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

    // System Performance Chart
    initializeSystemPerformanceChart() {
        const canvas = document.getElementById('systemPerformanceChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00'],
                datasets: [{
                    label: 'CPU Usage (%)',
                    data: [25, 22, 35, 68, 72, 58, 32],
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Memory Usage (%)',
                    data: [45, 48, 52, 65, 70, 62, 50],
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            color: '#f3f4f6'
                        },
                        ticks: {
                            callback: function (value) {
                                return value + '%';
                            }
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
    },

    // Revenue Chart
    initializeRevenueChart() {
        const canvas = document.getElementById('revenueChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Revenue',
                    data: [12500, 15800, 18200, 16800, 22100, 24500],
                    backgroundColor: '#4f46e5',
                    borderRadius: 6,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6'
                        },
                        ticks: {
                            callback: function (value) {
                                return '$' + value.toLocaleString();
                            }
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
    },

    // Activity Heatmap
    initializeActivityHeatmap() {
        const canvas = document.getElementById('activityHeatmap');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        const heatmapData = [];
        for (let i = 0; i < 7; i++) {
            for (let j = 0; j < 24; j++) {
                heatmapData.push({
                    x: j,
                    y: i,
                    v: Math.floor(Math.random() * 100)
                });
            }
        }

        new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: [{
                    label: 'Activity',
                    data: heatmapData,
                    backgroundColor: function (context) {
                        const value = context.raw.v;
                        const alpha = value / 100;
                        return `rgba(79, 70, 229, ${alpha})`;
                    },
                    pointRadius: 8,
                    pointHoverRadius: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        type: 'linear',
                        position: 'bottom',
                        min: 0,
                        max: 23,
                        ticks: {
                            stepSize: 1,
                            callback: function (value) {
                                return value + ':00';
                            }
                        }
                    },
                    y: {
                        type: 'linear',
                        min: 0,
                        max: 6,
                        ticks: {
                            stepSize: 1,
                            callback: function (value) {
                                const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                                return days[value];
                            }
                        }
                    }
                }
            }
        });
    },

    // Resource Usage Chart
    initializeResourceUsageChart() {
        const canvas = document.getElementById('resourceUsageChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['CPU', 'Memory', 'Storage', 'Network', 'Database', 'Cache'],
                datasets: [{
                    label: 'Current Usage',
                    data: [68, 72, 45, 58, 82, 35],
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.2)',
                    borderWidth: 2,
                    pointBackgroundColor: '#4f46e5',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }, {
                    label: 'Optimal Range',
                    data: [50, 50, 50, 50, 50, 50],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 20,
                            callback: function (value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    },

    // Helper function to generate time labels
    generateTimeLabels(hours) {
        const labels = [];
        const now = new Date();
        for (let i = hours - 1; i >= 0; i--) {
            const time = new Date(now - i * 60 * 60 * 1000);
            labels.push(time.getHours() + ':00');
        }
        return labels;
    },

    // Helper function to generate random data
    generateRandomData(count, min, max) {
        const data = [];
        for (let i = 0; i < count; i++) {
            data.push(Math.random() * (max - min) + min);
        }
        return data;
    },

    // AJAX Form Handling
    initAjaxForms() {
        console.log('AdminApp.initAjaxForms() called');
        document.querySelectorAll('.ajax-form').forEach(form => {
            console.log('Found ajax-form:', form.id || form.className);
            form.addEventListener('submit', async (e) => {
                console.log('Ajax form submitted:', e.target.id || e.target.className);
                e.preventDefault();

                const submitBtn = form.querySelector('[type="submit"]');
                const originalText = submitBtn.textContent;

                // Show loading
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

                try {
                    const formData = new FormData(form);
                    console.log('FormData created, action:', form.action);

                    // Debug: log all form data
                    for (let [key, value] of formData.entries()) {
                        console.log('Form data:', key, value);
                    }

                    const response = await fetch(form.action, {
                        method: form.method,
                        body: formData
                    });

                    console.log('Response status:', response.status);
                    console.log('Response headers:', [...response.headers.entries()]);

                    const responseText = await response.text();
                    console.log('Raw response text:', responseText);

                    let result;
                    try {
                        result = JSON.parse(responseText);
                    } catch (parseError) {
                        console.error('JSON parse error:', parseError);
                        console.error('Response was:', responseText.substring(0, 500));
                        throw new Error('Server returned invalid JSON: ' + responseText.substring(0, 100));
                    }

                    console.log('Parsed result:', result);
                    console.log('result.success =', result.success, 'type:', typeof result.success);

                    if (result.success) {
                        // Show appropriate message based on the result
                        const message = result.message || 'Settings saved successfully!';

                        // Check if any changes were made
                        const hasChanges = message.match(/(\d+) settings? updated/);

                        if (hasChanges && parseInt(hasChanges[1]) > 0) {
                            // Green toast for actual changes saved
                            this.showNotification(message, 'success');
                        } else {
                            // Info toast (not error) for no changes made - still a success
                            this.showNotification(message, 'info');
                        }

                        if (result.redirect) {
                            setTimeout(() => window.location.href = result.redirect, 1500);
                        }
                    } else {
                        // Show actual error message from server
                        this.showNotification(result.message || result.error || 'An error occurred', 'error');
                    }
                } catch (error) {
                    console.error('AJAX form error:', error);
                    this.showNotification('Network error occurred: ' + error.message, 'error');
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
function bootAdminApp() {
    if (window.adminAppInitialized) {
        return;
    }
    AdminApp.init();
    initDataTables();
    initDashboardLayoutObserver();
    window.adminAppInitialized = true;
}

// Dashboard Layout Observer
function initDashboardLayoutObserver() {
    const dashboardGrid = document.querySelector('.dashboard-grid');
    if (!dashboardGrid) {
        return;
    }

    // Create ResizeObserver to monitor dashboard grid changes
    const dashboardObserver = new ResizeObserver(entries => {
        entries.forEach(entry => {
            const charts = entry.target.querySelectorAll('canvas');
            charts.forEach(chart => {
                if (chart.chart) {
                    chart.chart.resize();
                }
            });
        });
    });

    // Observe dashboard grid
    dashboardObserver.observe(dashboardGrid);

    // Also observe window resize for additional safety
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            const charts = dashboardGrid.querySelectorAll('canvas');
            charts.forEach(chart => {
                if (chart.chart) {
                    chart.chart.resize();
                }
            });
        }, 250);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(bootAdminApp, 100);
});

window.addEventListener('load', bootAdminApp);

// Global error handler - only log to console, don't show notifications
window.addEventListener('error', (e) => {
    console.error('Admin Error:', e.error);
    // Don't show notification for every error - too aggressive
    // AdminApp.showNotification('An unexpected error occurred', 'error');
});

// Export for global use
window.AdminApp = AdminApp;
window.ModuleManager = ModuleManager;

// Global showToast wrapper for compatibility with other theme scripts
// Usage: showToast(type, titleOrMessage, message?)
// Examples:
//  showToast('success', 'Saved successfully')
//  showToast('info', 'Mode', 'Switched to dark mode')
window.showToast = function (type = 'info', titleOrMessage = '', message = '') {
    try {
        let text = '';

        if (message && titleOrMessage) {
            text = `${titleOrMessage} — ${message}`;
        } else {
            text = titleOrMessage || message || '';
        }

        // Prefer AdminApp.showNotification if available
        if (window.AdminApp && typeof window.AdminApp.showNotification === 'function') {
            window.AdminApp.showNotification(text, type);
            return;
        }

        // Fallback to ProCalculator's showNotification if present
        if (window.ProCalculator && typeof window.ProCalculator.showNotification === 'function') {
            window.ProCalculator.showNotification(type, text);
            return;
        }

        // Generic fallback: create a simple DOM toast
        const existing = document.getElementById('notification-toast');
        if (existing) {
            existing.className = `notification-toast ${type}`;
            existing.innerHTML = `<div style="display:flex;align-items:center;gap:12px;"><i class="fas fa-info-circle"></i><span>${text}</span></div>`;
            existing.classList.add('show');
            setTimeout(() => existing.classList.remove('show'), 5000);
            return;
        }

        // Last resort: alert
        alert(text || (type + ' notification'));
    } catch (e) {
        console.error('showToast error:', e);
    }
};
