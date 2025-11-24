<?php
$page_title = 'Admin Dashboard - Bishwo Calculator';
require_once dirname(__DIR__, 2) . '/themes/default/views/partials/header.php';
?>

<style>
    .admin-layout {
        display: flex;
        min-height: calc(100vh - 80px);
        background: #f8fafc;
    }

    body.dark-theme .admin-layout {
        background: #0f172a;
    }

    .admin-sidebar {
        width: 280px;
        background: white;
        border-right: 1px solid #e2e8f0;
        padding: 0;
        position: sticky;
        top: 80px;
        height: calc(100vh - 80px);
        overflow-y: auto;
    }

    body.dark-theme .admin-sidebar {
        background: #1e293b;
        border-color: #334155;
    }

    .admin-content {
        flex: 1;
        padding: 2rem;
        max-width: calc(100% - 280px);
        overflow-y: auto;
    }

    .sidebar-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        background: #f9fafb;
    }

    body.dark-theme .sidebar-header {
        background: #0f172a;
        border-color: #334155;
    }

    .sidebar-nav {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .sidebar-nav a {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
        color: #4b5563;
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .sidebar-nav a:hover {
        background: #f3f4f6;
        color: #1f2937;
        border-left-color: #3b82f6;
    }

    .sidebar-nav a.active {
        background: #eff6ff;
        color: #2563eb;
        border-left-color: #3b82f6;
    }

    body.dark-theme .sidebar-nav a {
        color: #d1d5db;
    }

    body.dark-theme .sidebar-nav a:hover {
        background: #374151;
        color: #f9fafb;
    }

    body.dark-theme .sidebar-nav a.active {
        background: #1e3a8a;
        color: #93c5fd;
    }

    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }

    .dashboard-content {
        position: relative;
        z-index: 2;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--stat-color, #3b82f6);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
    }

    body.dark-theme .stat-card {
        background: #1e293b;
        border-color: #334155;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        margin-bottom: 1rem;
        background: var(--stat-color, #3b82f6);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    body.dark-theme .stat-number {
        color: #f9fafb;
    }

    .stat-label {
        color: #6b7280;
        font-size: 0.875rem;
        margin: 0;
    }

    body.dark-theme .stat-label {
        color: #9ca3af;
    }

    .stat-change {
        font-size: 0.75rem;
        font-weight: 500;
        margin-top: 0.5rem;
    }

    .stat-change.positive {
        color: #10b981;
    }

    .stat-change.negative {
        color: #ef4444;
    }

    .dashboard-widgets {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .widget-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }

    body.dark-theme .widget-card {
        background: #1e293b;
        border-color: #334155;
    }

    .widget-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 1.5rem 0;
    }

    body.dark-theme .widget-title {
        color: #f9fafb;
    }

    .quick-actions {
        display: grid;
        gap: 1rem;
    }

    .quick-action {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 8px;
        text-decoration: none;
        color: #1f2937;
        transition: all 0.2s ease;
    }

    .quick-action:hover {
        background: #f3f4f6;
        color: #1f2937;
        transform: translateX(4px);
    }

    body.dark-theme .quick-action {
        background: #0f172a;
        color: #d1d5db;
    }

    body.dark-theme .quick-action:hover {
        background: #374151;
        color: #f9fafb;
    }

    .action-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
    }

    .recent-activity {
        margin-top: 2rem;
    }

    .activity-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    body.dark-theme .activity-item {
        border-color: #334155;
    }

    .activity-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.875rem;
    }

    .activity-content {
        flex: 1;
    }

    .activity-title {
        font-weight: 500;
        color: #1f2937;
        margin: 0 0 0.25rem 0;
        font-size: 0.875rem;
    }

    body.dark-theme .activity-title {
        color: #f9fafb;
    }

    .activity-time {
        color: #6b7280;
        font-size: 0.75rem;
        margin: 0;
    }

    body.dark-theme .activity-time {
        color: #9ca3af;
    }

    @media (max-width: 1024px) {
        .admin-layout {
            flex-direction: column;
        }

        .admin-sidebar {
            width: 100%;
            height: auto;
            position: relative;
            top: 0;
        }

        .admin-content {
            max-width: 100%;
        }

        .dashboard-widgets {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="admin-layout">
    <!-- Admin Sidebar -->
    <nav class="admin-sidebar">
        <div class="sidebar-header">
            <h3 style="margin: 0; color: #1f2937; font-size: 1.125rem; font-weight: 600;">
                <i class="fas fa-tachometer-alt me-2"></i> Admin Panel
            </h3>
        </div>

        <ul class="sidebar-nav">
            <li><a href="<?php echo app_base_url('/admin'); ?>" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="<?php echo app_base_url('/admin/setup/checklist'); ?>"><i class="fas fa-tasks"></i> Setup Checklist</a></li>
            <li><a href="<?php echo app_base_url('/admin/users'); ?>"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="<?php echo app_base_url('/admin/settings'); ?>"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="<?php echo app_base_url('/admin/logo-settings'); ?>"><i class="fas fa-image"></i> Logo & Branding</a></li>
            <li><a href="<?php echo app_base_url('/admin/modules'); ?>"><i class="fas fa-puzzle-piece"></i> Modules</a></li>
            <li><a href="<?php echo app_base_url('/admin/system-status'); ?>"><i class="fas fa-server"></i> System Status</a></li>
            <li><a href="<?php echo app_base_url('/help'); ?>"><i class="fas fa-question-circle"></i> Help Center</a></li>
            <li><a href="<?php echo app_base_url('/developers'); ?>"><i class="fas fa-code"></i> API Docs</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="admin-content">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="dashboard-content">
                <h1 style="font-size: 2rem; font-weight: 700; margin: 0 0 0.5rem 0;">
                    Welcome back, <?php echo htmlspecialchars($currentUser['full_name'] ?? 'Administrator'); ?>! 👋
                </h1>
                <p style="font-size: 1.125rem; opacity: 0.9; margin: 0;">
                    Here's an overview of your engineering calculator platform
                </p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card" style="--stat-color: #3b82f6;">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="stat-number"><?php echo number_format($stats['total_users'] ?? 0); ?></h3>
                <p class="stat-label">Total Users</p>
                <p class="stat-change positive">
                    <i class="fas <?php echo ($stats['user_growth'] ?? 0) >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'; ?> me-1"></i>
                    <?php echo abs($stats['user_growth'] ?? 0); ?>% from last month
                </p>
            </div>

            <div class="stat-card" style="--stat-color: #10b981;">
                <div class="stat-icon">
                    <i class="fas fa-calculator"></i>
                </div>
                <h3 class="stat-number"><?php echo number_format($stats['total_calculations'] ?? 0); ?></h3>
                <p class="stat-label">Total Calculations</p>
                <p class="stat-change positive">
                    <i class="fas <?php echo ($stats['calculation_growth'] ?? 0) >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'; ?> me-1"></i>
                    <?php echo abs($stats['calculation_growth'] ?? 0); ?>% from last month
                </p>
            </div>

            <div class="stat-card" style="--stat-color: #f59e0b;">
                <div class="stat-icon">
                    <i class="fas fa-puzzle-piece"></i>
                </div>
                <h3 class="stat-number"><?php echo $stats['active_modules'] ?? 0; ?></h3>
                <p class="stat-label">Active Modules</p>
                <p class="stat-change positive">
                    <i class="fas <?php echo ($stats['module_growth'] ?? 0) >= 0 ? 'fa-check' : 'fa-exclamation'; ?> me-1"></i>
                    <?php echo ($stats['all_modules_operational'] ?? false) ? 'All systems operational' : 'Issues detected'; ?>
                </p>
            </div>

            <div class="stat-card" style="--stat-color: #8b5cf6;">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="stat-number"><?php echo number_format($stats['api_requests'] ?? 0); ?></h3>
                <p class="stat-label">API Requests</p>
                <p class="stat-change positive">
                    <i class="fas <?php echo ($stats['api_growth'] ?? 0) >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'; ?> me-1"></i>
                    <?php echo abs($stats['api_growth'] ?? 0); ?>% from last week
                </p>
            </div>
        </div>

        <!-- Dashboard Widgets -->
        <div class="dashboard-widgets">
            <!-- Main Widget Area -->
            <div class="widget-card">
                <h3 class="widget-title">
                    <i class="fas fa-chart-bar me-2"></i> System Overview
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                    <div style="text-align: center; padding: 1rem; background: #f9fafb; border-radius: 8px;">
                        <div style="font-size: 2rem; font-weight: 700; color: #10b981; margin-bottom: 0.5rem;">
                            <?php echo $stats['system_health'] ?? 0; ?>%
                        </div>
                        <div style="color: #6b7280; font-size: 0.875rem;">System Health</div>
                    </div>

                    <div style="text-align: center; padding: 1rem; background: #f9fafb; border-radius: 8px;">
                        <div style="font-size: 2rem; font-weight: 700; color: #3b82f6; margin-bottom: 0.5rem;">
                            <?php echo $stats['active_users'] ?? 0; ?>
                        </div>
                        <div style="color: #6b7280; font-size: 0.875rem;">Active Users</div>
                    </div>

                    <div style="text-align: center; padding: 1rem; background: #f9fafb; border-radius: 8px;">
                        <div style="font-size: 2rem; font-weight: 700; color: #f59e0b; margin-bottom: 0.5rem;">
                            <?php echo $stats['storage_used'] ?? 0; ?>%
                        </div>
                        <div style="color: #6b7280; font-size: 0.875rem;">Storage Used</div>
                    </div>

                    <div style="text-align: center; padding: 1rem; background: #f9fafb; border-radius: 8px;">
                        <div style="font-size: 2rem; font-weight: 700; color: #ec4899; margin-bottom: 0.5rem;">
                            <?php echo $stats['uptime'] ?? '99.9%'; ?>
                        </div>
                        <div style="color: #6b7280; font-size: 0.875rem;">System Uptime</div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
                    <div>
                        <h4 style="font-size: 1rem; font-weight: 600; color: #1f2937; margin: 0 0 1rem 0;">User Growth</h4>
                        <canvas id="userGrowthChart" height="200"></canvas>
                    </div>
                    <div>
                        <h4 style="font-size: 1rem; font-weight: 600; color: #1f2937; margin: 0 0 1rem 0;">Calculations</h4>
                        <canvas id="calculationChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="widget-card">
                <h3 class="widget-title">
                    <i class="fas fa-bolt me-2"></i> Quick Actions
                </h3>

                <div class="quick-actions">
                    <a href="<?php echo app_base_url('/admin/setup/checklist'); ?>" class="quick-action">
                        <div class="action-icon" style="background: #3b82f6;">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; margin-bottom: 0.25rem;">Setup Checklist</div>
                            <div style="font-size: 0.75rem; color: #6b7280;">Complete site setup</div>
                        </div>
                    </a>

                    <a href="<?php echo app_base_url('/admin/users'); ?>" class="quick-action">
                        <div class="action-icon" style="background: #10b981;">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; margin-bottom: 0.25rem;">Manage Users</div>
                            <div style="font-size: 0.75rem; color: #6b7280;">User accounts & roles</div>
                        </div>
                    </a>

                    <a href="<?php echo app_base_url('/admin/settings'); ?>" class="quick-action">
                        <div class="action-icon" style="background: #f59e0b;">
                            <i class="fas fa-cog"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; margin-bottom: 0.25rem;">Settings</div>
                            <div style="font-size: 0.75rem; color: #6b7280;">Configure system</div>
                        </div>
                    </a>

                    <a href="<?php echo app_base_url('/admin/modules'); ?>" class="quick-action">
                        <div class="action-icon" style="background: #8b5cf6;">
                            <i class="fas fa-puzzle-piece"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; margin-bottom: 0.25rem;">Modules</div>
                            <div style="font-size: 0.75rem; color: #6b7280;">Manage modules</div>
                        </div>
                    </a>

                    <a href="<?php echo app_base_url('/admin/analytics'); ?>" class="quick-action">
                        <div class="action-icon" style="background: #ef4444;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; margin-bottom: 0.25rem;">Analytics</div>
                            <div style="font-size: 0.75rem; color: #6b7280;">View reports</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="widget-card recent-activity">
            <h3 class="widget-title">
                <i class="fas fa-clock me-2"></i> Recent Activity
            </h3>

            <?php if (!empty($recent_activity)): ?>
                <?php foreach ($recent_activity as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-icon" style="background: #<?php echo $activity['color'] ?? '3b82f6'; ?>;">
                            <i class="fas <?php echo $activity['icon'] ?? 'fa-info-circle'; ?>"></i>
                        </div>
                        <div class="activity-content">
                            <h4 class="activity-title"><?php echo htmlspecialchars($activity['title'] ?? 'System Event'); ?></h4>
                            <p class="activity-time"><?php echo htmlspecialchars($activity['time'] ?? 'Just now'); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="activity-item">
                    <div class="activity-icon" style="background: #9ca3af;">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="activity-content">
                        <h4 class="activity-title">No recent activity to display</h4>
                        <p class="activity-time">System is operational</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Chart.js Integration -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Initialize charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart');
    if (userGrowthCtx) {
        new Chart(userGrowthCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'New Users',
                    data: [12, 19, 15, 18, 22, 30],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Calculation Chart
    const calcCtx = document.getElementById('calculationChart');
    if (calcCtx) {
        new Chart(calcCtx, {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Calculations',
                    data: [120, 190, 150, 180, 220, 130, 90],
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    }
});
</script>

<?php require_once dirname(__DIR__, 2) . '/themes/default/views/partials/footer.php'; ?>