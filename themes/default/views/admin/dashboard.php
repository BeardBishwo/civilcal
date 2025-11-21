<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Main Dashboard</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">System overview and key metrics dashboard</p>
        </div>
    </div>
</div>

<!-- Main Metrics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-users" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Users</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_users'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Registered</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +12% this month</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-calculator" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Calculations</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_calculations'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Processed</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-bolt"></i> High Activity</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-chart-line" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Monthly Growth</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($stats['growth_rate'] ?? 0, 2); ?>%</div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Increase</div>
        <small style="color: #f59e0b; font-size: 0.75rem;"><i class="fas fa-chart-line"></i> Growing</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-shield-alt" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">System Health</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo $stats['system_health'] ?? '100%'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Status</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Healthy</small>
    </div>
</div>

<!-- Chart Section -->
<div class="admin-card">
    <h2 class="admin-card-title">System Overview</h2>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-chart-bar" style="color: #4cc9f0;"></i>
                User Activity
            </h3>
            <div style="height: 250px; display: flex; align-items: end; justify-content: space-around; padding: 1rem;">
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <div style="width: 20px; background: #4cc9f0; height: <?php echo min(200, ($stats['activity']['monday'] ?? 0) * 2); ?>px; margin-bottom: 0.5rem;"></div>
                    <span style="color: #9ca3af; font-size: 0.75rem;">Mon</span>
                </div>
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <div style="width: 20px; background: #34d399; height: <?php echo min(200, ($stats['activity']['tuesday'] ?? 0) * 2); ?>px; margin-bottom: 0.5rem;"></div>
                    <span style="color: #9ca3af; font-size: 0.75rem;">Tue</span>
                </div>
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <div style="width: 20px; background: #fbbf24; height: <?php echo min(200, ($stats['activity']['wednesday'] ?? 0) * 2); ?>px; margin-bottom: 0.5rem;"></div>
                    <span style="color: #9ca3af; font-size: 0.75rem;">Wed</span>
                </div>
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <div style="width: 20px; background: #f87171; height: <?php echo min(200, ($stats['activity']['thursday'] ?? 0) * 2); ?>px; margin-bottom: 0.5rem;"></div>
                    <span style="color: #9ca3af; font-size: 0.75rem;">Thu</span>
                </div>
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <div style="width: 20px; background: #a78bfa; height: <?php echo min(200, ($stats['activity']['friday'] ?? 0) * 2); ?>px; margin-bottom: 0.5rem;"></div>
                    <span style="color: #9ca3af; font-size: 0.75rem;">Fri</span>
                </div>
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <div style="width: 20px; background: #22d3ee; height: <?php echo min(200, ($stats['activity']['saturday'] ?? 0) * 2); ?>px; margin-bottom: 0.5rem;"></div>
                    <span style="color: #9ca3af; font-size: 0.75rem;">Sat</span>
                </div>
                <div style="display: flex; flex-direction: column; align-items: center;">
                    <div style="width: 20px; background: #ec4899; height: <?php echo min(200, ($stats['activity']['sunday'] ?? 0) * 2); ?>px; margin-bottom: 0.5rem;"></div>
                    <span style="color: #9ca3af; font-size: 0.75rem;">Sun</span>
                </div>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-server" style="color: #34d399;"></i>
                Server Resources
            </h3>
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: #9ca3af;">CPU Usage</span>
                    <span style="color: #f9fafb;"><?php echo $resources['cpu_usage'] ?? '0%'; ?></span>
                </div>
                <div style="height: 8px; background: rgba(102, 126, 234, 0.2); border-radius: 4px; overflow: hidden;">
                    <div style="height: 100%; width: <?php echo $resources['cpu_usage'] ?? 0; ?>%; background: <?php echo ($resources['cpu_usage'] ?? 0) > 80 ? '#f87171' : ($resources['cpu_usage'] ?? 0) > 60 ? '#fbbf24' : '#34d399'; ?>;"></div>
                </div>
            </div>
            
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: #9ca3af;">Memory Usage</span>
                    <span style="color: #f9fafb;"><?php echo $resources['memory_usage'] ?? '0%'; ?></span>
                </div>
                <div style="height: 8px; background: rgba(102, 126, 234, 0.2); border-radius: 4px; overflow: hidden;">
                    <div style="height: 100%; width: <?php echo $resources['memory_usage'] ?? 0; ?>%; background: <?php echo ($resources['memory_usage'] ?? 0) > 80 ? '#f87171' : ($resources['memory_usage'] ?? 0) > 60 ? '#fbbf24' : '#34d399'; ?>;"></div>
                </div>
            </div>
            
            <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: #9ca3af;">Disk Usage</span>
                    <span style="color: #f9fafb;"><?php echo $resources['disk_usage'] ?? '0%'; ?></span>
                </div>
                <div style="height: 8px; background: rgba(102, 126, 234, 0.2); border-radius: 4px; overflow: hidden;">
                    <div style="height: 100%; width: <?php echo $resources['disk_usage'] ?? 0; ?>%; background: <?php echo ($resources['disk_usage'] ?? 0) > 80 ? '#f87171' : ($resources['disk_usage'] ?? 0) > 60 ? '#fbbf24' : '#34d399'; ?>;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="admin-card">
    <h2 class="admin-card-title">Recent Activity</h2>
    <div class="admin-card-content">
        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">User</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Action</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Time</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_activity)): ?>
                        <?php foreach (array_slice($recent_activity, 0, 10) as $activity): ?>
                            <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <td style="padding: 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="width: 32px; height: 32px; background: rgba(76, 201, 240, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <span style="color: #4cc9f0; font-size: 0.875rem;"><?php echo strtoupper(substr($activity['user'] ?? 'U', 0, 1)); ?></span>
                                        </div>
                                        <span style="color: #f9fafb;"><?php echo htmlspecialchars($activity['user'] ?? 'Unknown'); ?></span>
                                    </div>
                                </td>
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars($activity['action'] ?? 'Unknown Action'); ?></td>
                                <td style="padding: 0.75rem;"><?php echo $activity['time'] ?? 'Unknown'; ?></td>
                                <td style="padding: 0.75rem;"><?php echo $activity['ip'] ?? 'Unknown'; ?></td>
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
</div>

<!-- Quick Actions -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-user-plus" style="font-size: 2rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="color: #f9fafb; margin-bottom: 1rem;">New User Registration</h3>
        <a href="<?php echo app_base_url('/admin/users/create'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem;">
            <i class="fas fa-plus"></i>
            <span>Create User</span>
        </a>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-file-alt" style="font-size: 2rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="color: #f9fafb; margin-bottom: 1rem;">Create Content</h3>
        <a href="<?php echo app_base_url('/admin/content/create'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
            <i class="fas fa-plus"></i>
            <span>New Page</span>
        </a>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-cog" style="font-size: 2rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="color: #f9fafb; margin-bottom: 1rem;">System Settings</h3>
        <a href="<?php echo app_base_url('/admin/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24; font-size: 0.875rem;">
            <i class="fas fa-cog"></i>
            <span>Configure</span>
        </a>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-chart-bar" style="font-size: 2rem; color: #22d3ee; margin-bottom: 1rem;"></i>
        <h3 style="color: #f9fafb; margin-bottom: 1rem;">View Analytics</h3>
        <a href="<?php echo app_base_url('/admin/analytics'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee; font-size: 0.875rem;">
            <i class="fas fa-chart-bar"></i>
            <span>Reports</span>
        </a>
    </div>
</div>

<!-- System Status -->
<div class="admin-card">
    <h2 class="admin-card-title">System Status</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-database" style="color: #4cc9f0;"></i>
                Database
            </h3>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Status:</span>
                <span style="color: #34d399; background: rgba(52, 211, 153, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px;">Connected</span>
            </div>
            <div style="margin-top: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Tables:</span>
                <span style="color: #f9fafb;"><?php echo $system_status['db_tables'] ?? 0; ?></span>
            </div>
            <div style="margin-top: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Size:</span>
                <span style="color: #f9fafb;"><?php echo $system_status['db_size'] ?? '0MB'; ?></span>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-network-wired" style="color: #34d399;"></i>
                Cache
            </h3>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Status:</span>
                <span style="color: #34d399; background: rgba(52, 211, 153, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px;">Active</span>
            </div>
            <div style="margin-top: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Hits:</span>
                <span style="color: #f9fafb;"><?php echo number_format($system_status['cache_hits'] ?? 0); ?></span>
            </div>
            <div style="margin-top: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Misses:</span>
                <span style="color: #f9fafb;"><?php echo number_format($system_status['cache_misses'] ?? 0); ?></span>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-file" style="color: #fbbf24;"></i>
                Files
            </h3>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Status:</span>
                <span style="color: #34d399; background: rgba(52, 211, 153, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px;">OK</span>
            </div>
            <div style="margin-top: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Total Files:</span>
                <span style="color: #f9fafb;"><?php echo number_format($system_status['total_files'] ?? 0); ?></span>
            </div>
            <div style="margin-top: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Storage:</span>
                <span style="color: #f9fafb;"><?php echo $system_status['total_storage'] ?? '0MB'; ?></span>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-bolt" style="color: #22d3ee;"></i>
                Performance
            </h3>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Response Time:</span>
                <span style="color: <?php echo ($system_status['response_time'] ?? 0) > 2 ? '#f87171' : '#34d399'; ?>;"><?php echo $system_status['response_time'] ?? '0ms'; ?></span>
            </div>
            <div style="margin-top: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Uptime:</span>
                <span style="color: #f9fafb;"><?php echo $system_status['uptime'] ?? '0d 0h'; ?></span>
            </div>
            <div style="margin-top: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #9ca3af;">Avg Load:</span>
                <span style="color: #f9fafb;"><?php echo $system_status['avg_load'] ?? '0%'; ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Notifications -->
<div class="admin-card">
    <h2 class="admin-card-title">Notifications</h2>
    <div class="admin-card-content">
        <ul style="list-style: none; padding: 0; margin: 0;">
            <?php if (!empty($notifications)): ?>
                <?php foreach (array_slice($notifications, 0, 5) as $notification): ?>
                    <li style="margin-bottom: 1rem; padding: 1rem; background: rgba(15, 23, 42, 0.5); border-left: 3px solid <?php echo $notification['type'] === 'error' ? '#f87171' : ($notification['type'] === 'warning' ? '#fbbf24' : '#34d399'); ?>; border-radius: 0 6px 6px 0;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                            <div style="flex: 1;">
                                <h4 style="color: <?php echo $notification['type'] === 'error' ? '#f87171' : ($notification['type'] === 'warning' ? '#fbbf24' : '#34d399'); ?>; margin: 0;"><?php echo htmlspecialchars($notification['title'] ?? 'Notification'); ?></h4>
                                <p style="color: #9ca3af; margin: 0.5rem 0 0 0; font-size: 0.875rem;"><?php echo htmlspecialchars($notification['message'] ?? ''); ?></p>
                            </div>
                            <span style="color: #9ca3af; font-size: 0.75rem; white-space: nowrap;"><?php echo $notification['time'] ?? 'Just now'; ?></span>
                        </div>
                        <div style="display: flex; gap: 1rem; margin-top: 0.5rem;">
                            <?php if (isset($notification['actions'])): ?>
                                <?php foreach (array_slice($notification['actions'], 0, 2) as $action): ?>
                                    <a href="<?php echo $action['url'] ?? '#'; ?>" 
                                       style="color: <?php echo $notification['type'] === 'error' ? '#f87171' : ($notification['type'] === 'warning' ? '#fbbf24' : '#34d399'); ?>; font-size: 0.875rem; text-decoration: underline;"><?php echo htmlspecialchars($action['text'] ?? 'Action'); ?></a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li style="text-align: center; padding: 2rem; color: #9ca3af;">
                    <i class="fas fa-bell" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                    <p>No notifications at this time</p>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>