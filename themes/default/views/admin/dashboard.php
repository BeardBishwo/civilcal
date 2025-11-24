<?php
$page_title = 'Dashboard - Bishwo Calculator';
require_once dirname(__DIR__, 2) . '/partials/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 700; color: #f9fafb; margin: 0 0 0.5rem 0;">Dashboard Overview</h1>
            <p style="font-size: 1.125rem; opacity: 0.9; margin: 0;">Monitor your system's performance and activity.</p>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <button onclick="window.location.reload()" style="background: rgba(76, 201, 240, 0.1); color: #4cc9f0; border: 1px solid rgba(76, 201, 240, 0.3); padding: 0.625rem 1.25rem; border-radius: 6px; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s ease;">
                <i class="fas fa-sync-alt"></i>
                <span>Refresh</span>
            </button>
            <a href="<?php echo app_base_url('/admin/analytics/reports'); ?>" style="background: rgba(52, 211, 153, 0.1); color: #34d399; border: 1px solid rgba(52, 211, 153, 0.3); padding: 0.625rem 1.25rem; border-radius: 6px; font-size: 0.875rem; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s ease;">
                <i class="fas fa-file-alt"></i>
                <span>Reports</span>
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card" style="--stat-color: #3b82f6;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
            <div style="width: 50px; height: 50px; background: rgba(76, 201, 240, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-users" style="font-size: 1.5rem; color: #4cc9f0;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">All Time</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.75rem;"><?php echo number_format($stats['total_users'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Total Users</div>
        <?php
        $growth = $stats['user_growth'] ?? 0;
        $is_positive = $growth >= 0;
        ?>
        <small style="display: block; margin-top: 0.75rem; color: <?php echo $is_positive ? '#10b981' : '#ef4444'; ?>; font-size: 0.75rem;">
            <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
            <?php echo abs($growth); ?>% from last month
        </small>
    </div>

    <div class="stat-card" style="--stat-color: #10b981;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
            <div style="width: 50px; height: 50px; background: rgba(52, 211, 153, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-calculator" style="font-size: 1.5rem; color: #34d399;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">All Time</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #34d399; margin-bottom: 0.75rem;"><?php echo number_format($stats['total_calculations'] ?? 0); ?></div>
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

    <div class="stat-card" style="--stat-color: #f59e0b;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
            <div style="width: 50px; height: 50px; background: rgba(245, 158, 11, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-chart-line" style="font-size: 1.5rem; color: #fbbf24;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">Growth Rate</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.75rem;"><?php echo number_format($stats['growth_rate'] ?? 0, 2); ?>%</div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Monthly Growth</div>
        <?php
        $growth = $stats['growth_trend'] ?? 0;
        $is_positive = $growth >= 0;
        ?>
        <small style="display: block; margin-top: 0.75rem; color: <?php echo $is_positive ? '#10b981' : '#ef4444'; ?>; font-size: 0.75rem;">
            <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
            <?php echo abs($growth); ?>% from last month
        </small>
    </div>

    <div class="stat-card" style="--stat-color: #38bdf8;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
            <div style="width: 50px; height: 50px; background: rgba(56, 189, 248, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-shield-alt" style="font-size: 1.5rem; color: #38bdf8;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">System Status</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #38bdf8; margin-bottom: 0.75rem;"><?php echo $stats['system_health'] ?? '100%'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">System Health</div>
        <?php
        $status = $stats['system_health_status'] ?? 'healthy';
        $status_color = $status === 'healthy' ? '#34d399' : '#f87171';
        ?>
        <small style="display: block; margin-top: 0.75rem; color: <?php echo $status_color; ?>; font-size: 0.75rem;">
            <i class="fas fa-<?php echo $status === 'healthy' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
            <?php echo ucfirst($status); ?>
        </small>
    </div>
</div>

<!-- Chart Section -->
<div class="widget-card">
    <h3 class="widget-title" style="margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fas fa-chart-area" style="color: #4cc9f0;"></i>
        User Activity Trends
    </h3>
    <div style="height: 400px; background: rgba(15, 23, 42, 0.5); border-radius: 8px; padding: 1rem;">
        <canvas id="userGrowthChart" style="width: 100%; height: 350px;"></canvas>
    </div>
</div>

<!-- Server Resources -->
<div class="widget-card" style="margin: 2rem 0;">
    <h3 class="widget-title" style="margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fas fa-server" style="color: #34d399;"></i>
        Server Resources
    </h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h4 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-microchip" style="color: #4cc9f0;"></i>
                CPU Usage
            </h4>
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: #9ca3af;">Current Usage</span>
                    <span style="color: #f9fafb;"><?php echo $resources['cpu_usage'] ?? '0%'; ?></span>
                </div>
                <div style="height: 8px; background: rgba(102, 126, 234, 0.2); border-radius: 4px; overflow: hidden;">
                    <div style="height: 100%; width: <?php echo $resources['cpu_usage'] ?? 0; ?>%; background: <?php echo ($resources['cpu_usage'] ?? 0) > 80 ? '#f87171' : ($resources['cpu_usage'] ?? 0) > 60 ? '#fbbf24' : '#34d399'; ?>;"></div>
                </div>
            </div>
            <div style="text-align: right; margin-top: 0.5rem;">
                <span style="color: #9ca3af; font-size: 0.875rem;">Peak: <?php echo $resources['cpu_peak'] ?? '0%'; ?></span>
            </div>
        </div>

        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h4 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-memory" style="color: #34d399;"></i>
                Memory Usage
            </h4>
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: #9ca3af;">Current Usage</span>
                    <span style="color: #f9fafb;"><?php echo $resources['memory_usage'] ?? '0%'; ?></span>
                </div>
                <div style="height: 8px; background: rgba(102, 126, 234, 0.2); border-radius: 4px; overflow: hidden;">
                    <div style="height: 100%; width: <?php echo $resources['memory_usage'] ?? 0; ?>%; background: <?php echo ($resources['memory_usage'] ?? 0) > 80 ? '#f87171' : ($resources['memory_usage'] ?? 0) > 60 ? '#fbbf24' : '#34d399'; ?>;"></div>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 0.5rem;">
                <span style="color: #9ca3af; font-size: 0.875rem;"><?php echo $resources['memory_used'] ?? '0GB'; ?> of <?php echo $resources['memory_total'] ?? '0GB'; ?></span>
                <span style="color: <?php echo ($resources['memory_usage'] ?? 0) > 80 ? '#f87171' : (($resources['memory_usage'] ?? 0) > 60 ? '#fbbf24' : '#34d399'); ?>; font-size: 0.875rem;">Available: <?php echo $resources['memory_available'] ?? '0GB'; ?></span>
            </div>
        </div>

        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h4 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-hdd" style="color: #fbbf24;"></i>
                Disk Usage
            </h4>
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: #9ca3af;">Storage Used</span>
                    <span style="color: #f9fafb;"><?php echo $resources['disk_usage'] ?? '0%'; ?></span>
                </div>
                <div style="height: 8px; background: rgba(102, 126, 234, 0.2); border-radius: 4px; overflow: hidden;">
                    <div style="height: 100%; width: <?php echo $resources['disk_usage'] ?? 0; ?>%; background: <?php echo ($resources['disk_usage'] ?? 0) > 80 ? '#f87171' : ($resources['disk_usage'] ?? 0) > 60 ? '#fbbf24' : '#34d399'; ?>;"></div>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 0.5rem;">
                <span style="color: #9ca3af; font-size: 0.875rem;"><?php echo $resources['disk_used'] ?? '0GB'; ?> of <?php echo $resources['disk_total'] ?? '0GB'; ?></span>
                <span style="color: <?php echo ($resources['disk_usage'] ?? 0) > 80 ? '#f87171' : (($resources['disk_usage'] ?? 0) > 60 ? '#fbbf24' : '#34d399'); ?>; font-size: 0.875rem;">Free: <?php echo $resources['disk_available'] ?? '0GB'; ?></span>
            </div>
        </div>

        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h4 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-bolt" style="color: #22d3ee;"></i>
                Network I/O
            </h4>
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: #9ca3af;">Bandwidth</span>
                    <span style="color: #f9fafb;"><?php echo $resources['network_io'] ?? '0 Mbps'; ?></span>
                </div>
                <div style="height: 8px; background: rgba(102, 126, 234, 0.2); border-radius: 4px; overflow: hidden;">
                    <div style="height: 100%; width: <?php echo min(100, ($resources['network_usage'] ?? 0)); ?>%; background: <?php echo ($resources['network_usage'] ?? 0) > 80 ? '#f87171' : ($resources['network_usage'] ?? 0) > 60 ? '#fbbf24' : '#34d399'; ?>;"></div>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 0.5rem;">
                <span style="color: #9ca3af; font-size: 0.875rem;">In: <?php echo $resources['network_in'] ?? '0 MB'; ?></span>
                <span style="color: #9ca3af; font-size: 0.875rem;">Out: <?php echo $resources['network_out'] ?? '0 MB'; ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="widget-card recent-activity" style="margin: 2rem 0;">
    <h3 class="widget-title" style="margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fas fa-history" style="color: #34d399;"></i>
        Recent Activity
    </h3>
    <div style="overflow-x: auto;">
        <table class="admin-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600; font-size: 0.85rem;">User</th>
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600; font-size: 0.85rem;">Action</th>
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600; font-size: 0.85rem;">Time</th>
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600; font-size: 0.85rem;">IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recent_activity)): ?>
                    <?php foreach (array_slice($recent_activity, 0, 10) as $activity): ?>
                        <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                            <td style="padding: 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 40px; height: 40px; background: rgba(76, 201, 240, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <span style="color: #4cc9f0; font-size: 0.875rem;"><?php echo strtoupper(substr($activity['user'] ?? 'U', 0, 1)); ?></span>
                                    </div>
                                    <span style="color: #f9fafb;"><?php echo htmlspecialchars($activity['user'] ?? 'Unknown User'); ?></span>
                                </div>
                            </td>
                            <td style="padding: 0.75rem; color: #f9fafb;"><?php echo htmlspecialchars($activity['action'] ?? 'Unknown Action'); ?></td>
                            <td style="padding: 0.75rem; color: #9ca3af;"><?php echo $activity['time'] ?? 'Unknown'; ?></td>
                            <td style="padding: 0.75rem; color: #9ca3af;"><?php echo $activity['ip'] ?? 'Unknown'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 1.5rem; color: #9ca3af;">No recent activity to display</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Quick Actions Section -->
<div class="widget-card" style="margin: 2rem 0;">
    <h3 class="widget-title" style="margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fas fa-bolt" style="color: #fbbf24;"></i>
        Quick Actions
    </h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.25rem;">
        <a href="<?php echo app_base_url('/admin/users/create'); ?>" class="quick-action-card" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(76, 201, 240, 0.2); border-radius: 8px; text-decoration: none; transition: all 0.2s ease; color: inherit;">
            <div style="width: 50px; height: 50px; background: rgba(76, 201, 240, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-user-plus" style="font-size: 1.5rem; color: #4cc9f0;"></i>
            </div>
            <div>
                <h4 style="color: #f9fafb; margin: 0 0 0.25rem 0; font-size: 1.125rem;">Create User</h4>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Add a new user account</p>
            </div>
        </a>

        <a href="<?php echo app_base_url('/admin/content/create'); ?>" class="quick-action-card" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 8px; text-decoration: none; transition: all 0.2s ease; color: inherit;">
            <div style="width: 50px; height: 50px; background: rgba(52, 211, 153, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-file-alt" style="font-size: 1.5rem; color: #34d399;"></i>
            </div>
            <div>
                <h4 style="color: #f9fafb; margin: 0 0 0.25rem 0; font-size: 1.125rem;">Create Content</h4>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Add new content</p>
            </div>
        </a>

        <a href="<?php echo app_base_url('/admin/settings'); ?>" class="quick-action-card" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 8px; text-decoration: none; transition: all 0.2s ease; color: inherit;">
            <div style="width: 50px; height: 50px; background: rgba(245, 158, 11, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-cog" style="font-size: 1.5rem; color: #fbbf24;"></i>
            </div>
            <div>
                <h4 style="color: #f9fafb; margin: 0 0 0.25rem 0; font-size: 1.125rem;">Configure Settings</h4>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">System configuration</p>
            </div>
        </a>

        <a href="<?php echo app_base_url('/admin/analytics'); ?>" class="quick-action-card" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 8px; text-decoration: none; transition: all 0.2s ease; color: inherit;">
            <div style="width: 50px; height: 50px; background: rgba(34, 211, 238, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-chart-bar" style="font-size: 1.5rem; color: #22d3ee;"></i>
            </div>
            <div>
                <h4 style="color: #f9fafb; margin: 0 0 0.25rem 0; font-size: 1.125rem;">View Analytics</h4>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Detailed reports & data</p>
            </div>
        </a>
    </div>
</div>

<!-- System Status Section -->
<div class="widget-card" style="margin: 2rem 0;">
    <h3 class="widget-title" style="margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fas fa-server" style="color: #22d3ee;"></i>
        System Status
    </h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h4 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-database" style="color: #4cc9f0;"></i>
                Database
            </h4>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                <span style="color: #9ca3af;">Status:</span>
                <span style="color: #34d399; background: rgba(52, 211, 153, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px;">Connected</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                <span style="color: #9ca3af;">Tables:</span>
                <span style="color: #f9fafb;"><?php echo $system_status['db_tables'] ?? 0; ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Size:</span>
                <span style="color: #f9fafb;"><?php echo $system_status['db_size'] ?? '0MB'; ?></span>
            </div>
        </div>

        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h4 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-network-wired" style="color: #34d399;"></i>
                Cache
            </h4>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                <span style="color: #9ca3af;">Status:</span>
                <span style="color: #34d399; background: rgba(52, 211, 153, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px;">Active</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                <span style="color: #9ca3af;">Hits:</span>
                <span style="color: #f9fafb;"><?php echo number_format($system_status['cache_hits'] ?? 0); ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Misses:</span>
                <span style="color: #f9fafb;"><?php echo number_format($system_status['cache_misses'] ?? 0); ?></span>
            </div>
        </div>

        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h4 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-file" style="color: #fbbf24;"></i>
                Files
            </h4>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                <span style="color: #9ca3af;">Status:</span>
                <span style="color: #34d399; background: rgba(52, 211, 153, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px;">Optimal</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                <span style="color: #9ca3af;">Total Files:</span>
                <span style="color: #f9fafb;"><?php echo number_format($system_status['total_files'] ?? 0); ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Storage:</span>
                <span style="color: #f9fafb;"><?php echo $system_status['total_storage'] ?? '0MB'; ?></span>
            </div>
        </div>

        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h4 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-bolt" style="color: #22d3ee;"></i>
                Performance
            </h4>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                <span style="color: #9ca3af;">Response Time:</span>
                <span style="color: <?php echo ($system_status['response_time'] ?? 0) > 200 ? '#f87171' : '#34d399'; ?>;"><?php echo $system_status['response_time'] ?? '0ms'; ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                <span style="color: #9ca3af;">Uptime:</span>
                <span style="color: #f9fafb;"><?php echo $system_status['uptime'] ?? '0d 0h'; ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Avg Load:</span>
                <span style="color: #f9fafb;"><?php echo $system_status['avg_load'] ?? '0%'; ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Notifications Section -->
<div class="widget-card" style="margin: 2rem 0;">
    <h3 class="widget-title" style="margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fas fa-bell" style="color: #fbbf24;"></i>
        Notifications
    </h3>
    <div style="max-height: 400px; overflow-y: auto;">
        <ul style="list-style: none; padding: 0; margin: 0;">
            <?php if (!empty($notifications)): ?>
                <?php foreach (array_slice($notifications, 0, 5) as $notification): ?>
                    <li style="margin-bottom: 1rem; padding: 1.25rem; background: rgba(15, 23, 42, 0.5); border-left: 4px solid <?php echo $notification['type'] === 'error' ? '#f87171' : ($notification['type'] === 'warning' ? '#fbbf24' : '#34d399'); ?>; border-radius: 0 8px 8px 0; transition: transform 0.2s ease;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                            <div style="flex: 1;">
                                <h4 style="color: <?php echo $notification['type'] === 'error' ? '#f87171' : ($notification['type'] === 'warning' ? '#fbbf24' : '#34d399'); ?>; margin: 0 0 0.25rem 0; font-size: 0.95rem;"><?php echo htmlspecialchars($notification['title'] ?? 'Notification'); ?></h4>
                                <p style="color: #9ca3af; margin: 0; font-size: 0.85rem;"><?php echo htmlspecialchars($notification['message'] ?? ''); ?></p>
                            </div>
                            <span style="color: #9ca3af; font-size: 0.75rem; white-space: nowrap; align-self: flex-start;"><?php echo $notification['time'] ?? 'Just now'; ?></span>
                        </div>
                        <?php if (isset($notification['actions']) && is_array($notification['actions'])): ?>
                            <div style="display: flex; gap: 1.25rem; margin-top: 0.75rem;">
                                <?php foreach (array_slice($notification['actions'], 0, 2) as $action): ?>
                                    <a href="<?php echo $action['url'] ?? '#'; ?>"
                                       style="color: <?php echo $notification['type'] === 'error' ? '#f87171' : ($notification['type'] === 'warning' ? '#fbbf24' : '#34d399'); ?>; font-size: 0.85rem; text-decoration: none; border-bottom: 1px solid transparent; transition: border-color 0.2s ease;">
                                        <?php echo htmlspecialchars($action['text'] ?? 'Action'); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li style="text-align: center; padding: 3rem; color: #9ca3af; background: rgba(15, 23, 42, 0.3); border-radius: 8px;">
                    <i class="fas fa-bell" style="font-size: 2.5rem; margin-bottom: 1rem; display: block;"></i>
                    <p style="margin: 0; font-size: 1.125rem;">No notifications at this time</p>
                    <small style="display: block; margin-top: 0.5rem; opacity: 0.8;">All caught up! Everything is running smoothly.</small>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<!-- Chart.js Integration -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// System data from PHP backend
const userGrowthData = <?php echo json_encode($user_stats['growth_data'] ?? []); ?>;
const calcData = <?php echo json_encode($user_stats['calculation_data'] ?? []); ?>;

// Initialize charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart !== 'undefined') {
        // User Growth Chart
        const userGrowthCtx = document.getElementById('userGrowthChart');
        if (userGrowthCtx && userGrowthData.length > 0) {
            new Chart(userGrowthCtx, {
                type: 'line',
                data: {
                    labels: userGrowthData.map(d => d.date),
                    datasets: [{
                        label: 'User Growth',
                        data: userGrowthData.map(d => d.count),
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
                            grid: { color: 'rgba(102, 126, 234, 0.1)' }
                        },
                        x: {
                            ticks: {
                                color: '#9ca3af',
                                font: {
                                    size: 11
                                }
                            },
                            grid: { color: 'rgba(102, 126, 234, 0.1)' }
                        }
                    }
                }
            });
        }

        // Calculation Chart
        const calcCtx = document.getElementById('calculationChart');
        if (calcCtx && calcData.length > 0) {
            new Chart(calcCtx, {
                type: 'bar',
                data: {
                    labels: calcData.map(d => d.date),
                    datasets: [{
                        label: 'Calculations',
                        data: calcData.map(d => d.count),
                        backgroundColor: 'rgba(52, 211, 153, 0.5)',
                        borderColor: '#34d399',
                        borderWidth: 1
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
                            grid: { color: 'rgba(102, 126, 234, 0.1)' }
                        },
                        x: {
                            ticks: {
                                color: '#9ca3af',
                                font: {
                                    size: 11
                                }
                            },
                            grid: { color: 'rgba(102, 126, 234, 0.1)' }
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

a[href^="<?php echo app_base_url('/admin/') ?>"],
button[onclick*="location.reload"] {
    transition: transform 0.2s ease;
}

a[href^="<?php echo app_base_url('/admin/') ?>"]:hover,
button[onclick*="location.reload"]:hover {
    transform: translateY(-2px);
}

.widget-card {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(102, 126, 234, 0.2);
    border-radius: 12px;
    padding: 1.75rem;
    transition: transform 0.2s ease;
}

.widget-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #f9fafb;
    margin: 0 0 1.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.quick-action-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.quick-action-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(67, 97, 238, 0.3);
}

table {
    border-radius: 8px;
    overflow: hidden;
}

th {
    background-color: rgba(102, 126, 234, 0.1);
}

@media (max-width: 768px) {
    .admin-content {
        padding: 1rem;
        max-width: 100%;
    }

    .stats-grid {
        grid-template-columns: 1fr !important;
    }

    .dashboard-widgets {
        grid-template-columns: 1fr !important;
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