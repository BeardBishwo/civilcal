<?php
/**
 * Admin Dashboard Complex View
 * Uses admin layout (sidebar, topbar, NO homepage header)
 */

$page_title = 'Admin Dashboard - Bishwo Calculator';

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
                <h3 class="stat-number"><?php echo number_format($stats['total_users']); ?></h3>
                <p class="stat-label">Total Users</p>
                <p class="stat-change positive">
                    <i class="fas fa-arrow-up me-1"></i> +12% from last month
                </p>
            </div>

            <div class="stat-card" style="--stat-color: #10b981;">
                <div class="stat-icon">
                    <i class="fas fa-calculator"></i>
                </div>
                <h3 class="stat-number"><?php echo number_format($stats['total_calculations']); ?></h3>
                <p class="stat-label">Total Calculations</p>
                <p class="stat-change positive">
                    <i class="fas fa-arrow-up me-1"></i> +8% from last month
                </p>
            </div>

            <div class="stat-card" style="--stat-color: #f59e0b;">
                <div class="stat-icon">
                    <i class="fas fa-puzzle-piece"></i>
                </div>
                <h3 class="stat-number"><?php echo $stats['active_modules']; ?></h3>
                <p class="stat-label">Active Modules</p>
                <p class="stat-change positive">
                    <i class="fas fa-check me-1"></i> All systems operational
                </p>
            </div>

            <div class="stat-card" style="--stat-color: #8b5cf6;">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="stat-number"><?php echo number_format($stats['api_requests']); ?></h3>
                <p class="stat-label">API Requests</p>
                <p class="stat-change positive">
                    <i class="fas fa-arrow-up me-1"></i> +15% from last week
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
                            <?php echo $stats['system_health']; ?>%
                        </div>
                        <div style="color: #6b7280; font-size: 0.875rem;">System Health</div>
                    </div>

                    <div style="text-align: center; padding: 1rem; background: #f9fafb; border-radius: 8px;">
                        <div style="font-size: 2rem; font-weight: 700; color: #3b82f6; margin-bottom: 0.5rem;">
                            <?php echo $stats['active_users']; ?>
                        </div>
                        <div style="color: #6b7280; font-size: 0.875rem;">Active Users</div>
                    </div>

                    <div style="text-align: center; padding: 1rem; background: #f9fafb; border-radius: 8px;">
                        <div style="font-size: 2rem; font-weight: 700; color: #f59e0b; margin-bottom: 0.5rem;">
                            <?php echo $stats['storage_used']; ?>%
                        </div>
                        <div style="color: #6b7280; font-size: 0.875rem;">Storage Used</div>
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
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="widget-card recent-activity">
            <h3 class="widget-title">
                <i class="fas fa-clock me-2"></i> Recent Activity
            </h3>

            <div class="activity-item">
                <div class="activity-icon" style="background: #3b82f6;">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="activity-content">
                    <h4 class="activity-title">New user registered</h4>
                    <p class="activity-time">2 hours ago</p>
                </div>
            </div>

            <div class="activity-item">
                <div class="activity-icon" style="background: #10b981;">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="activity-content">
                    <h4 class="activity-title">System settings updated</h4>
                    <p class="activity-time">4 hours ago</p>
                </div>
            </div>

            <div class="activity-item">
                <div class="activity-icon" style="background: #f59e0b;">
                    <i class="fas fa-database"></i>
                </div>
                <div class="activity-content">
                    <h4 class="activity-title">Database backup completed</h4>
                    <p class="activity-time">1 day ago</p>
                </div>
            </div>

            <div class="activity-item">
                <div class="activity-icon" style="background: #8b5cf6;">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="activity-content">
                    <h4 class="activity-title">Security scan completed</h4>
                    <p class="activity-time">2 days ago</p>
                </div>
            </div>
        </div>
    </main>
</div>
';

// Include admin layout - uses sidebar, topbar, NO homepage header/footer
include __DIR__ . '/layout.php';
?>