<?php
$page_title = 'Calculator Analytics - Bishwo Calculator';
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

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }

    .page-content {
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

    .admin-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    table {
        border-radius: 8px;
        overflow: hidden;
    }

    th {
        background-color: rgba(102, 126, 234, 0.1);
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
            <li><a href="<?php echo app_base_url('/admin'); ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
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
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-content">
                <h1 style="font-size: 2rem; font-weight: 700; margin: 0 0 0.5rem 0;">
                    Calculator Analytics
                </h1>
                <p style="font-size: 1.125rem; opacity: 0.9; margin: 0;">
                    Track calculator usage and performance metrics.
                </p>
            </div>
        </div>

        <!-- Calculator Stats -->
        <div class="stats-grid">
            <?php if (isset($calc_stats['by_type']) && is_array($calc_stats['by_type'])): ?>
                <?php foreach ($calc_stats['by_type'] as $type_data): ?>
                    <div class="stat-card" style="--stat-color: #3b82f6;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
                            <div style="width: 50px; height: 50px; background: rgba(76, 201, 240, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-calculator" style="font-size: 1.5rem; color: #4cc9f0;"></i>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">Total</div>
                            </div>
                        </div>
                        <div style="font-size: 2.25rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.75rem;"><?php echo number_format($type_data['count'] ?? 0); ?></div>
                        <div style="color: #9ca3af; font-size: 0.875rem;"><?php echo ucfirst($type_data['type'] ?? 'Unknown'); ?> Calculators</div>
                        <?php
                        $growth = $type_data['growth'] ?? 0;
                        $is_positive = $growth >= 0;
                        ?>
                        <small style="display: block; margin-top: 0.75rem; color: <?php echo $is_positive ? '#10b981' : '#ef4444'; ?>; font-size: 0.75rem;">
                            <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
                            <?php echo abs($growth); ?>% from last month
                        </small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="stat-card" style="--stat-color: #10b981;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
                    <div style="width: 50px; height: 50px; background: rgba(52, 211, 153, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check-circle" style="font-size: 1.5rem; color: #34d399;"></i>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">Success Rate</div>
                    </div>
                </div>
                <div style="font-size: 2.25rem; font-weight: 700; color: #34d399; margin-bottom: 0.75rem;"><?php echo $calc_stats['success_rate'] ?? '0%'; ?></div>
                <div style="color: #9ca3af; font-size: 0.875rem;">Success Rate</div>
                <?php
                $growth = $calc_stats['success_growth'] ?? 0;
                $is_positive = $growth >= 0;
                ?>
                <small style="display: block; margin-top: 0.75rem; color: <?php echo $is_positive ? '#10b981' : '#ef4444'; ?>; font-size: 0.75rem;">
                    <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
                    <?php echo abs($growth); ?>% from last month
                </small>
            </div>
        </div>

<!-- Calculator Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.75rem; transition: transform 0.2s ease;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
            <div style="width: 50px; height: 50px; background: rgba(76, 201, 240, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-calculator" style="font-size: 1.5rem; color: #4cc9f0;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">All Time</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.75rem;"><?php echo number_format($stats['total_calculations'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Total Calculations</div>
        <?php
        $growth = $stats['calculation_growth'] ?? 0;
        $is_positive = $growth >= 0;
        ?>
        <small style="display: block; margin-top: 0.75rem; color: <?php echo $is_positive ? '#10b981' : '#ef4444'; ?>; font-size: 0.75rem;">
            <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
            <?php echo abs($growth); ?>% from last month
        </small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.75rem; transition: transform 0.2s ease;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
            <div style="width: 50px; height: 50px; background: rgba(52, 211, 153, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-check-circle" style="font-size: 1.5rem; color: #34d399;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">Success Rate</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #34d399; margin-bottom: 0.75rem;"><?php echo $stats['success_rate'] ?? '0%'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Success Rate</div>
        <?php
        $growth = $stats['success_rate_growth'] ?? 0;
        $is_positive = $growth >= 0;
        ?>
        <small style="display: block; margin-top: 0.75rem; color: <?php echo $is_positive ? '#10b981' : '#ef4444'; ?>; font-size: 0.75rem;">
            <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
            <?php echo abs($growth); ?>% from last month
        </small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.75rem; transition: transform 0.2s ease;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
            <div style="width: 50px; height: 50px; background: rgba(251, 191, 36, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-clock" style="font-size: 1.5rem; color: #fbbf24;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">Response Time</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.75rem;"><?php echo $stats['avg_response_time'] ?? '0ms'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Avg. Response Time</div>
        <?php
        $growth = $stats['response_time_trend'] ?? 0;
        $is_positive = $growth >= 0;
        ?>
        <small style="display: block; margin-top: 0.75rem; color: <?php echo $is_positive ? '#10b981' : '#ef4444'; ?>; font-size: 0.75rem;">
            <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
            <?php echo abs($growth); ?>% trend
        </small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.75rem; transition: transform 0.2s ease;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
            <div style="width: 50px; height: 50px; background: rgba(34, 211, 238, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-chart-line" style="font-size: 1.5rem; color: #22d3ee;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">Today</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.75rem;"><?php echo number_format($stats['today_calculations'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Today's Calculations</div>
        <?php
        $growth = $stats['daily_growth'] ?? 0;
        $is_positive = $growth >= 0;
        ?>
        <small style="display: block; margin-top: 0.75rem; color: <?php echo $is_positive ? '#10b981' : '#ef4444'; ?>; font-size: 0.75rem;">
            <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
            <?php echo abs($growth); ?>% from yesterday
        </small>
    </div>
</div>

<!-- Calculator Usage Chart -->
<div class="admin-card">
    <h2 class="admin-card-title">Calculator Usage Patterns (Last 30 Days)</h2>
    <div style="height: 400px; background: rgba(15, 23, 42, 0.5); border-radius: 8px; padding: 1rem;">
        <canvas id="calculatorUsageChart" style="width: 100%; height: 350px;"></canvas>
    </div>
</div>

<!-- Top Performing Calculators -->
<div class="admin-card">
    <h2 class="admin-card-title">Top Performing Calculators</h2>
    <div class="admin-card-content">
        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600; font-size: 0.85rem;">Calculator</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600; font-size: 0.85rem;">Uses</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600; font-size: 0.85rem;">Success Rate</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600; font-size: 0.85rem;">Avg. Time</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600; font-size: 0.85rem;">Trend</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($top_calculators)): ?>
                        <?php foreach ($top_calculators as $calc): ?>
                            <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <td style="padding: 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="<?php echo $calc['icon'] ?? 'fas fa-calculator'; ?>" style="color: #4cc9f0;"></i>
                                        <span style="color: #f9fafb;"><?php echo htmlspecialchars($calc['name'] ?? 'Unknown Calculator'); ?></span>
                                    </div>
                                </td>
                                <td style="padding: 0.75rem;"><?php echo number_format($calc['uses'] ?? 0); ?></td>
                                <td style="padding: 0.75rem;"><?php echo number_format($calc['success_rate'] ?? 0, 2); ?>%</td>
                                <td style="padding: 0.75rem;"><?php echo $calc['avg_time'] ?? '0s'; ?></td>
                                <td style="padding: 0.75rem;">
                                    <span style="color: <?php echo ($calc['trend'] ?? 0) >= 0 ? '#34d399' : '#f87171'; ?>; display: flex; align-items: center; gap: 0.25rem;">
                                        <i class="fas <?php echo ($calc['trend'] ?? 0) >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'; ?>"></i>
                                        <?php echo abs($calc['trend'] ?? 0); ?>%
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 1.5rem; color: #9ca3af;">No calculator data available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Calculator Performance Analysis -->
<div class="admin-card">
    <h2 class="admin-card-title">Calculator Performance Analysis</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-bolt" style="color: #4cc9f0;"></i>
                Fastest Calculators
            </h3>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php if (!empty($fastest_calcs)): ?>
                    <?php foreach (array_slice($fastest_calcs, 0, 5) as $calc): ?>
                        <li style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                            <span style="color: #f9fafb;"><?php echo htmlspecialchars($calc['name'] ?? 'Unknown'); ?></span>
                            <span style="color: #34d399;"><?php echo $calc['avg_time'] ?? '0ms'; ?></span>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="color: #9ca3af; text-align: center; padding: 1rem;">No data available</li>
                <?php endif; ?>
            </ul>
        </div>

        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-hourglass-half" style="color: #f87171;"></i>
                Slowest Calculators
            </h3>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php if (!empty($slowest_calcs)): ?>
                    <?php foreach (array_slice($slowest_calcs, 0, 5) as $calc): ?>
                        <li style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                            <span style="color: #f9fafb;"><?php echo htmlspecialchars($calc['name'] ?? 'Unknown'); ?></span>
                            <span style="color: #f87171;"><?php echo $calc['avg_time'] ?? '0ms'; ?></span>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="color: #9ca3af; text-align: center; padding: 1rem;">No data available</li>
                <?php endif; ?>
            </ul>
        </div>

        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-exclamation-triangle" style="color: #f87171;"></i>
                Error Prone Calculators
            </h3>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php if (!empty($error_calcs)): ?>
                    <?php foreach (array_slice($error_calcs, 0, 5) as $calc): ?>
                        <li style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                            <span style="color: #f9fafb;"><?php echo htmlspecialchars($calc['name'] ?? 'Unknown'); ?></span>
                            <span style="color: #f87171;"><?php echo number_format($calc['error_rate'] ?? 0, 2); ?>%</span>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="color: #9ca3af; text-align: center; padding: 1rem;">No error data available</li>
                <?php endif; ?>
            </ul>
        </div>

        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-layer-group" style="color: #4cc9f0;"></i>
                By Category
            </h3>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php if (!empty($by_category)): ?>
                    <?php foreach ($by_category as $category_item): ?>
                        <li style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                            <span style="color: #f9fafb;"><?php echo htmlspecialchars($category_item['category'] ?? 'Unknown'); ?></span>
                            <span style="color: #4cc9f0;"><?php echo number_format($category_item['count'] ?? 0); ?></span>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="color: #9ca3af; text-align: center; padding: 1rem;">No category data available</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<!-- Calculator Usage Distribution -->
<div class="admin-card">
    <h2 class="admin-card-title">Calculator Usage Distribution</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-calendar-day" style="color: #4cc9f0;"></i>
                Peak Usage Times
            </h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php if (!empty($peak_times)): ?>
                    <?php foreach ($peak_times as $time_data): ?>
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                                <span style="color: #f9fafb;"><?php echo htmlspecialchars($time_data['hour'] ?? 'Unknown'); ?></span>
                                <span style="color: #9ca3af;"><?php echo number_format($time_data['count'] ?? 0); ?> calculations</span>
                            </div>
                            <div style="height: 6px; background: rgba(102, 126, 234, 0.2); border-radius: 3px; overflow: hidden;">
                                <div style="height: 100%; width: <?php echo $time_data['percentage'] ?? 0; ?>%; background: #4cc9f0;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="color: #9ca3af; text-align: center; padding: 1rem;">No peak time data available</div>
                <?php endif; ?>
            </div>
        </div>

        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user-clock" style="color: #34d399;"></i>
                User Engagement
            </h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                        <span style="color: #f9fafb;">Avg. Sessions/Day</span>
                        <span style="color: #9ca3af;"><?php echo number_format($engagement['avg_sessions_per_day'] ?? 0, 1); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                        <span style="color: #f9fafb;">Avg. Calculations/Session</span>
                        <span style="color: #9ca3af;"><?php echo number_format($engagement['avg_calcs_per_session'] ?? 0, 1); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #f9fafb;">Repeat Users</span>
                        <span style="color: #f9fafb;"><?php echo number_format($engagement['repeat_user_rate'] ?? 0, 1); ?>%</span>
                    </div>
                </div>
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                        <span style="color: #f9fafb;">Bounce Rate</span>
                        <span style="color: <?php echo ($engagement['bounce_rate'] ?? 0) > 50 ? '#f87171' : '#34d399'; ?>"><?php echo number_format($engagement['bounce_rate'] ?? 0, 1); ?>%</span>
                    </div>
                    <div style="height: 6px; background: rgba(102, 126, 234, 0.2); border-radius: 3px; overflow: hidden;">
                        <div style="height: 100%; width: <?php echo $engagement['bounce_rate'] ?? 0; ?>%; background: <?php echo ($engagement['bounce_rate'] ?? 0) > 50 ? '#f87171' : '#34d399'; ?>"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Calculator Filters -->
<div class="admin-card">
    <h2 class="admin-card-title">Calculator Analytics Filters</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <select style="padding: 0.5rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
            <option value="">All Calculator Types</option>
            <option value="basic">Basic Calculator</option>
            <option value="scientific">Scientific Calculator</option>
            <option value="financial">Financial Calculator</option>
            <option value="engineering">Engineering Calculator</option>
        </select>

        <select style="padding: 0.5rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
            <option value="">All Time Periods</option>
            <option value="today">Today</option>
            <option value="week">This Week</option>
            <option value="month">This Month</option>
            <option value="quarter">This Quarter</option>
            <option value="year">This Year</option>
        </select>

        <input type="date" style="padding: 0.5rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">

        <button style="padding: 0.5rem 1rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer;">
            <i class="fas fa-search"></i> Apply Filters
        </button>
    </div>
</div>

<!-- Calculator Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Calculator Analytics Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/analytics/calculators/export'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-file-export"></i>
            <span>Export Data</span>
        </a>

        <a href="<?php echo app_base_url('/admin/analytics/calculators/optimize'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-bolt"></i>
            <span>Optimize Calculators</span>
        </a>

        <a href="<?php echo app_base_url('/admin/analytics/calculators/performance-report'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-chart-line"></i>
            <span>Performance Report</span>
        </a>

        <a href="<?php echo app_base_url('/admin/analytics/calculators/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-cog"></i>
            <span>Analytics Settings</span>
        </a>
    </div>
</div>

<script>
const usageData = <?php echo json_encode($calculator_stats['usage_by_day'] ?? []); ?>;
const calcUsageData = <?php echo json_encode($calculator_stats['daily_calculator_usage'] ?? []); ?>;

document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart !== 'undefined' && usageData && usageData.length > 0) {
        const ctx = document.getElementById('calculatorUsageChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: usageData.map(d => d.date),
                    datasets: [{
                        label: 'Calculations',
                        data: usageData.map(d => d.count),
                        borderColor: '#4cc9f0',
                        backgroundColor: 'rgba(76, 201, 240, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#f9fafb',
                                font: {
                                    size: 12
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: {
                                color: '#9ca3af',
                                font: {
                                    size: 11
                                }
                            },
                            grid: {
                                color: 'rgba(102, 126, 234, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#9ca3af',
                                font: {
                                    size: 11
                                }
                            },
                            grid: {
                                color: 'rgba(102, 126, 234, 0.1)'
                            }
                        }
                    }
                }
            });
        }
    }
});
</script>

<style>
.admin-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.admin-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(67, 97, 238, 0.3);
}

a[href*="/admin/analytics/"],
button[onclick*="location.reload"] {
    transition: transform 0.2s ease;
}

a[href*="/admin/analytics/"]:hover,
button[onclick*="location.reload"]:hover {
    transform: translateY(-2px);
}

.admin-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

table {
    border-radius: 8px;
    overflow: hidden;
}

th {
    background-color: rgba(102, 126, 234, 0.1);
}

@media (max-width: 768px) {
    .admin-card-header div {
        flex-direction: column;
        align-items: flex-start;
    }

    .admin-card-header div:last-child {
        width: 100%;
    }

    .admin-card-header button,
    .admin-card-header a {
        width: 100%;
        justify-content: center;
    }

    .admin-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>