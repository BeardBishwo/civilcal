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
            
            <!-- Recent Activity -->
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock"></i>
                        Recent Activity
                    </h3>
                    <a href="/admin/activity" class="btn btn-sm btn-primary">View All</a>
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
        
        <!-- Right Column - Widgets & Info -->
        <div class="dashboard-right">
            
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="card-content">
                    <div class="quick-actions-grid">
                        <a href="/admin/users/create" class="quick-action-item">
                            <div class="action-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="action-label">Add User</div>
                        </a>
                        
                        <a href="/admin/content/pages" class="quick-action-item">
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
            
            <!-- Calculator Usage Breakdown -->
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie"></i>
                        Calculator Usage
                    </h3>
                </div>
                <div class="card-content">
                    <canvas id="calculatorUsageChart" width="300" height="300"></canvas>
                </div>
            </div>
            
            <!-- System Status -->
            <div class="card" style="margin-top: 24px;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-server"></i>
                        System Status
                    </h3>
                </div>
                <div class="card-content">
                    <div class="system-metrics">
                        <div class="metric-item">
                            <div class="metric-label">PHP Version</div>
                            <div class="metric-value">' . PHP_VERSION . '</div>
                            <div class="metric-status status-success">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        
                        <div class="metric-item">
                            <div class="metric-label">Memory Usage</div>
                            <div class="metric-value">' . round(memory_get_usage(true) / 1024 / 1024) . ' MB</div>
                            <div class="metric-status status-success">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        
                        <div class="metric-item">
                            <div class="metric-label">Database</div>
                            <div class="metric-value">Connected</div>
                            <div class="metric-status status-success">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        
                        <div class="metric-item">
                            <div class="metric-label">Storage</div>
                            <div class="metric-value">78% Used</div>
                            <div class="metric-status status-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div style="margin-top: 16px;">
                        <a href="/admin/system-status" class="btn btn-primary btn-sm" style="width: 100%;">
                            <i class="fas fa-external-link-alt"></i>
                            View Detailed Status
                        </a>
                    </div>
                </div>
            </div>
            
        </div>
        
    </div>
    
</div>

<style>
/* Dashboard Specific Styles */
.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.quick-action-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 16px;
    background: var(--admin-gray-50);
    border-radius: 8px;
    text-decoration: none;
    color: var(--admin-gray-700);
    border: 2px solid transparent;
    transition: var(--transition);
    cursor: pointer;
}

.quick-action-item:hover {
    background: white;
    border-color: var(--admin-primary);
    color: var(--admin-primary);
    transform: translateY(-2px);
}

.action-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: var(--admin-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.action-label {
    font-weight: 500;
    font-size: 14px;
    text-align: center;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 12px;
    background: var(--admin-gray-50);
    border-radius: 8px;
}

.activity-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--admin-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 500;
    color: var(--admin-gray-800);
    margin-bottom: 4px;
}

.activity-meta {
    font-size: 12px;
    color: var(--admin-gray-500);
}

.system-metrics {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.metric-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 0;
}

.metric-label {
    font-weight: 500;
    color: var(--admin-gray-700);
}

.metric-value {
    font-size: 14px;
    color: var(--admin-gray-600);
}

.metric-status {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

.status-success {
    background: var(--admin-success);
    color: white;
}

.status-warning {
    background: var(--admin-warning);
    color: white;
}

.status-danger {
    background: var(--admin-danger);
    color: white;
}

.card-actions {
    display: flex;
    gap: 8px;
}

@media (max-width: 768px) {
    .quick-actions-grid {
        grid-template-columns: 1fr;
    }
}
</style>
';

// Set breadcrumbs
$breadcrumbs = [
    ['title' => 'Dashboard']
];

$page_title = $page_title ?? 'Dashboard - Admin Panel';
$currentPage = $currentPage ?? 'dashboard';

// Include the layout
include __DIR__ . '/../layouts/main.php';
?>
