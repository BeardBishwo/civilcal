<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Main Dashboard</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Overview of system status and key metrics</p>
        </div>
    </div>
</div>

<!-- Main Dashboard Metrics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-users" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Users</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($dashboard_stats['total_users'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">All Time</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +12% this month</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-calculator" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Calculations</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($dashboard_stats['total_calculations'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Processed</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-bolt"></i> Active</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-server" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">System Health</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo $dashboard_stats['system_health'] ?? '98%'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Operation</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Stable</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-chart-line" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Revenue</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo $dashboard_stats['revenue'] ?? '$0'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Monthly</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> Growing</small>
    </div>
</div>

<!-- Dashboard Charts -->
<div class="admin-card">
    <h2 class="admin-card-title">System Overview</h2>
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-chart-area" style="color: #4cc9f0;"></i>
                User Activity
            </h3>
            <div style="height: 250px; display: flex; align-items: end; justify-content: space-around; gap: 0.5rem; padding: 1rem;">
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                    <div style="background: #4cc9f0; height: <?php echo rand(30, 200); ?>px; width: 20px; margin-bottom: 0.5rem;"></div>
                    <span style="color: #9ca3af; font-size: 0.75rem;">Mon</span>
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                    <div style="background: #34d399; height: <?php echo rand(30, 200); ?>px; width: 20px; margin-bottom: 0.5rem;"></div>
                    <span style="color: #9ca3af; font-size: 0.75rem;">Tue</span>
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                    <div style="background: #fbbf24; height: <?php echo rand(30, 200); ?>px; width: 20px; margin-bottom: 0.5rem;"></div>
                    <span style="color: #9ca3af; font-size: 0.75rem;">Wed</span>
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                    <div style="background: #f87171; height: <?php echo rand(30, 200); ?>px; width: 20px; margin-bottom: 0.5rem;"></div>
                    <span style="color: #9ca3af; font-size: 0.75rem;">Thu</span>
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                    <div style="background: #a78bfa; height: <?php echo rand(30, 200); ?>px; width: 20px; margin-bottom: 0.5rem;"></div>
                    <span style="color: #9ca3af; font-size: 0.75rem;">Fri</span>
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                    <div style="background: #22d3ee; height: <?php echo rand(30, 200); ?>px; width: 20px; margin-bottom: 0.5rem;"></div>
                    <span style="color: #9ca3af; font-size: 0.75rem;">Sat</span>
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                    <div style="background: #ec4899; height: <?php echo rand(30, 200); ?>px; width: 20px; margin-bottom: 0.5rem;"></div>
                    <span style="color: #9ca3af; font-size: 0.75rem;">Sun</span>
                </div>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-server" style="color: #34d399;"></i>
                Server Status
            </h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #9ca3af;">CPU Usage</span>
                    <span style="color: #f9fafb;"><?php echo $server_status['cpu_usage'] ?? '0%'; ?></span>
                </div>
                <div style="height: 8px; background: rgba(102, 126, 234, 0.2); border-radius: 4px; overflow: hidden;">
                    <div style="height: 100%; width: <?php echo $server_status['cpu_usage'] ?? 0; ?>%; background: <?php echo ($server_status['cpu_usage'] ?? 0) > 80 ? '#f87171' : (($server_status['cpu_usage'] ?? 0) > 60 ? '#fbbf24' : '#34d399'); ?>;"></div>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
                    <span style="color: #9ca3af;">Memory</span>
                    <span style="color: #f9fafb;"><?php echo $server_status['memory_usage'] ?? '0%'; ?></span>
                </div>
                <div style="height: 8px; background: rgba(102, 126, 234, 0.2); border-radius: 4px; overflow: hidden;">
                    <div style="height: 100%; width: <?php echo $server_status['memory_usage'] ?? 0; ?>%; background: <?php echo ($server_status['memory_usage'] ?? 0) > 80 ? '#f87171' : (($server_status['memory_usage'] ?? 0) > 60 ? '#fbbf24' : '#34d399'); ?>;"></div>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
                    <span style="color: #9ca3af;">Disk</span>
                    <span style="color: #f9fafb;"><?php echo $server_status['disk_usage'] ?? '0%'; ?></span>
                </div>
                <div style="height: 8px; background: rgba(102, 126, 234, 0.2); border-radius: 4px; overflow: hidden;">
                    <div style="height: 100%; width: <?php echo $server_status['disk_usage'] ?? 0; ?>%; background: <?php echo ($server_status['disk_usage'] ?? 0) > 90 ? '#f87171' : (($server_status['disk_usage'] ?? 0) > 70 ? '#fbbf24' : '#34d399'); ?>;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="admin-card">
    <h2 class="admin-card-title">Recent Activity</h2>
    <div style="overflow-x: auto;">
        <table class="admin-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">User</th>
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Action</th>
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Time</th>
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recent_activity)): ?>
                    <?php foreach (array_slice($recent_activity, 0, 10) as $activity): ?>
                        <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                            <td style="padding: 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 32px; height: 32px; background: rgba(76, 201, 240, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <span style="color: #4cc9f0; font-size: 0.875rem;"><?php echo strtoupper(substr($activity['user'] ?? 'U', 0, 1)); ?></span>
                                    </div>
                                    <span style="color: #f9fafb;"><?php echo htmlspecialchars($activity['username'] ?? $activity['user'] ?? 'Unknown'); ?></span>
                                </div>
                            </td>
                            <td style="padding: 0.75rem;"><?php echo htmlspecialchars($activity['action'] ?? 'Unknown action'); ?></td>
                            <td style="padding: 0.75rem;"><?php echo $activity['timestamp'] ?? 'Just now'; ?></td>
                            <td style="padding: 0.75rem;"><?php echo $activity['ip_address'] ?? 'Local'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 1rem; color: #9ca3af;">No recent activity</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Quick Stats -->
<div class="admin-grid">
    <div class="admin-card" style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
        <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-tachometer-alt" style="color: #4cc9f0;"></i>
            Performance
        </h3>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="color: #9ca3af;">Response Time</span>
            <span style="color: #f9fafb;"><?php echo $dashboard_stats['response_time'] ?? '120ms'; ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="color: #9ca3af;">Page Load</span>
            <span style="color: #f9fafb;"><?php echo $dashboard_stats['page_load_time'] ?? '0.8s'; ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="color: #9ca3af;">Uptime</span>
            <span style="color: #34d399;"><?php echo $dashboard_stats['uptime'] ?? '100%'; ?></span>
        </div>
    </div>
    
    <div class="admin-card" style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
        <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-database" style="color: #34d399;"></i>
            Database
        </h3>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="color: #9ca3af;">Connections</span>
            <span style="color: #f9fafb;"><?php echo $db_status['connections'] ?? '0'; ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="color: #9ca3af;">Size</span>
            <span style="color: #f9fafb;"><?php echo $db_status['size'] ?? '0 MB'; ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="color: #9ca3af;">Queries/s</span>
            <span style="color: #f9fafb;"><?php echo $db_status['queries_per_second'] ?? '0'; ?></span>
        </div>
    </div>
    
    <div class="admin-card" style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
        <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-shield-alt" style="color: #fbbf24;"></i>
            Security
        </h3>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="color: #9ca3af;">Active Threats</span>
            <span style="color: #f9fafb;"><?php echo $security_status['active_threats'] ?? '0'; ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="color: #9ca3af;">Last Scan</span>
            <span style="color: #f9fafb;"><?php echo $security_status['last_scan'] ?? 'Never'; ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="color: #9ca3af;">Status</span>
            <span style="color: #34d399;"><?php echo $security_status['status'] ?? 'Protected'; ?></span>
        </div>
    </div>
    
    <div class="admin-card" style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
        <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-bolt" style="color: #22d3ee;"></i>
            Cache
        </h3>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="color: #9ca3af;">Hit Rate</span>
            <span style="color: #f9fafb;"><?php echo $cache_stats['hit_rate'] ?? '0%'; ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="color: #9ca3af;">Objects</span>
            <span style="color: #f9fafb;"><?php echo number_format($cache_stats['objects'] ?? 0); ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="color: #9ca3af;">Size</span>
            <span style="color: #f9fafb;"><?php echo $cache_stats['size'] ?? '0 MB'; ?></span>
        </div>
    </div>
</div>

<!-- Dashboard Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Quick Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
        <a href="<?php echo app_base_url('/admin/users'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-users"></i>
            <span>Manage Users</span>
        </a>

        <a href="<?php echo app_base_url('/admin/analytics'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-chart-bar"></i>
            <span>View Analytics</span>
        </a>

        <a href="<?php echo app_base_url('/admin/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-cog"></i>
            <span>System Settings</span>
        </a>

        <a href="<?php echo app_base_url('/admin/backup'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-database"></i>
            <span>Backups</span>
        </a>

        <a href="<?php echo app_base_url('/admin/system-status'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 6px; text-decoration: none; color: #a78bfa;">
            <i class="fas fa-heartbeat"></i>
            <span>System Status</span>
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>