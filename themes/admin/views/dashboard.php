<?php
// Dashboard View
$content = '
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-tachometer-alt"></i>
            Dashboard Overview
        </h1>
        <p class="page-description">Welcome back! Here\'s what\'s happening with your platform today.</p>
    </div>
    
    <!-- Stats Grid -->
    <div class="stats-grid">
        <!-- Total Users -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>12.5%</span>
                </div>
            </div>
            <div class="stat-value" id="total-users">' . ($stats['total_users'] ?? 0) . '</div>
            <div class="stat-label">Total Users</div>
        </div>
        
        <!-- Active Users -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon success">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>8.2%</span>
                </div>
            </div>
            <div class="stat-value" id="active-users">' . ($stats['active_users'] ?? 0) . '</div>
            <div class="stat-label">Active Users</div>
        </div>
        
        <!-- Calculator Usage -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon info">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    <span>15.3%</span>
                </div>
            </div>
            <div class="stat-value" id="calculator-usage">' . ($stats['monthly_calculations'] ?? 0) . '</div>
            <div class="stat-label">Calculations This Month</div>
        </div>
        
        <!-- Active Modules -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon warning">
                    <i class="fas fa-puzzle-piece"></i>
                </div>
            </div>
            <div class="stat-value" id="active-modules">' . ($stats['active_modules'] ?? 0) . '</div>
            <div class="stat-label">Active Modules</div>
        </div>
    </div>
    
    <!-- Dashboard Grid -->
    <div class="dashboard-grid">

        <!-- Left Column - Charts & Analytics -->
        <div class="dashboard-left">

            <!-- User Growth Chart -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i>
                        User Growth
                    </h3>
                    <div class="card-actions">
                        <button class="btn btn-sm btn-secondary">
                            <i class="fas fa-download"></i>
                            Export
                        </button>
                    </div>
                </div>
                <div class="card-content">
                    <canvas id="userGrowthChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Recent Activity Widget -->
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock"></i>
                        Recent Activity
                    </h3>
                    <a href="' . app_base_url('/admin/activity') . '" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-content">
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-avatar">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">New user registered</div>
                                <div class="activity-meta">john.doe@example.com • 5 minutes ago</div>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-avatar">
                                <i class="fas fa-calculator"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Concrete calculator used</div>
                                <div class="activity-meta">Anonymous user • 12 minutes ago</div>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-avatar">
                                <i class="fas fa-cog"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">System settings updated</div>
                                <div class="activity-meta">admin@example.com • 1 hour ago</div>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-avatar">
                                <i class="fas fa-puzzle-piece"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">Analytics module activated</div>
                                <div class="activity-meta">admin@example.com • 2 hours ago</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column - Dashboard Widgets -->
        <div class="dashboard-right">

            <!-- Error Monitoring Widget -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exclamation-triangle"></i>
                        Error Monitoring
                    </h3>
                    <a href="' . app_base_url('/admin/error-logs') . '" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-content">
                    <div class="error-stats-grid">
                        <div class="stat-item">
                            <div class="stat-value">12</div>
                            <div class="stat-label">Errors Today</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">4</div>
                            <div class="stat-label">Critical</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">8</div>
                            <div class="stat-label">Warnings</div>
                        </div>
                    </div>
                    <div style="margin-top: 16px;">
                        <div class="progress-bar-container">
                            <div class="progress-bar">
                                <div class="progress-fill error" style="width: 12%;"></div>
                            </div>
                            <div class="progress-label">Errors: 12/1000 requests</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue/Subscription Widget -->
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-dollar-sign"></i>
                        Revenue & Subscriptions
                    </h3>
                    <a href="' . app_base_url('/admin/subscriptions') . '" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-content">
                    <div class="revenue-stats-grid">
                        <div class="stat-item">
                            <div class="stat-value">$2,450</div>
                            <div class="stat-label">Monthly Revenue</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">47</div>
                            <div class="stat-label">Active Subscriptions</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">12</div>
                            <div class="stat-label">New This Month</div>
                        </div>
                    </div>
                    <div style="margin-top: 16px;">
                        <canvas id="revenueChart" width="300" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Calculator Usage Stats Widget -->
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i>
                        Calculator Usage Stats
                    </h3>
                    <a href="' . app_base_url('/admin/analytics/calculators') . '" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-content">
                    <div class="calculator-usage-list">
                        <div class="usage-item">
                            <div class="usage-label">Concrete Volume</div>
                            <div class="usage-value">1,250</div>
                            <div class="usage-bar">
                                <div class="usage-fill" style="width: 90%;"></div>
                            </div>
                        </div>
                        <div class="usage-item">
                            <div class="usage-label">Electrical Load</div>
                            <div class="usage-value">980</div>
                            <div class="usage-bar">
                                <div class="usage-fill" style="width: 75%;"></div>
                            </div>
                        </div>
                        <div class="usage-item">
                            <div class="usage-label">Beam Design</div>
                            <div class="usage-value">756</div>
                            <div class="usage-bar">
                                <div class="usage-fill" style="width: 60%;"></div>
                            </div>
                        </div>
                        <div class="usage-item">
                            <div class="usage-label">Pipe Sizing</div>
                            <div class="usage-value">542</div>
                            <div class="usage-bar">
                                <div class="usage-fill" style="width: 40%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="card-content">
                    <div class="quick-actions-grid">
                        <a href="' . app_base_url('/admin/users/create') . '" class="quick-action-item">
                            <div class="action-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="action-label">Add User</div>
                        </a>

                        <a href="' . app_base_url('/admin/content/pages') . '" class="quick-action-item">
                            <div class="action-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="action-label">New Page</div>
                        </a>

                        <button onclick="createBackup()" class="quick-action-item">
                            <div class="action-icon">
                                <i class="fas fa-download"></i>
                            </div>
                            <div class="action-label">Backup</div>
                        </button>

                        <button onclick="checkSystemHealth()" class="quick-action-item">
                            <div class="action-icon">
                                <i class="fas fa-heartbeat"></i>
                            </div>
                            <div class="action-label">Health Check</div>
                        </button>
                    </div>
                </div>
            </div>

        </div>

    </div>
    
</div>

';

// Set breadcrumbs
$breadcrumbs = [
    ['title' => 'Dashboard']
];

$page_title = $page_title ?? 'Dashboard - Admin Panel';
$currentPage = $currentPage ?? 'dashboard';

// Add JavaScript for dashboard widgets
$scripts = [
    app_base_url('themes/admin/assets/js/admin.js')
];

// Include the layout
include __DIR__ . '/../layouts/main.php';
?>

<script>
    // Initialize charts when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize revenue chart
        if (document.getElementById('revenueChart')) {
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    datasets: [{
                        label: 'Revenue',
                        data: [500, 750, 1200, 2450],
                        borderColor: 'var(--admin-success)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
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
                            display: false
                        },
                        y: {
                            display: false
                        }
                    }
                }
            });
        }

        // Initialize user growth chart (if it exists)
        if (document.getElementById('userGrowthChart')) {
            const userCtx = document.getElementById('userGrowthChart').getContext('2d');
            new Chart(userCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Users',
                        data: [120, 190, 300, 500, 750, 1234],
                        borderColor: 'var(--admin-primary)',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
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
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Initialize calculator usage chart (if it exists)
        if (document.getElementById('calculatorUsageChart')) {
            const calcCtx = document.getElementById('calculatorUsageChart').getContext('2d');
            new Chart(calcCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Concrete', 'Electrical', 'Structural', 'HVAC', 'Other'],
                    datasets: [{
                        data: [35, 25, 20, 15, 5],
                        backgroundColor: [
                            'var(--admin-primary)',
                            'var(--admin-warning)',
                            'var(--admin-danger)',
                            'var(--admin-info)',
                            'var(--admin-gray-400)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
        }
    });

    // Function for backup (if called from quick actions)
    function createBackup() {
        alert('Backup functionality will be implemented here');
    }

    // Function for system health check (if called from quick actions)
    function checkSystemHealth() {
        alert('System health check will be performed here');
    }
</script>