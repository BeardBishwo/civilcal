<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>User Analytics</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">User engagement and behavior analytics</p>
        </div>
    </div>
</div>

<!-- User Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-user-plus" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Users</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_users'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">All Time</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +12% this month</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-user-clock" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Active Users</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['active_users'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Currently Online</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-bolt"></i> High Activity</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-user-check" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Daily Signups</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($stats['daily_signups'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Today</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-chart-line"></i> Growing</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-user-times" style="font-size: 1.5rem; color: #f87171; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Churn Rate</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #f87171; margin-bottom: 0.5rem;"><?php echo $stats['churn_rate'] ?? '0%'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Rate</div>
        <small style="color: #f87171; font-size: 0.75rem;"><i class="fas fa-exclamation-triangle"></i> Monitor</small>
    </div>
</div>

<!-- User Growth Chart -->
<div class="admin-card">
    <h2 class="admin-card-title">User Growth Over Time</h2>
    <div style="height: 350px; background: rgba(15, 23, 42, 0.5); border-radius: 8px; padding: 1rem; display: flex; align-items: center; justify-content: center;">
        <p style="color: #9ca3af; text-align: center;">User Growth Chart<br>(Placeholder for chart showing user growth over time)</p>
    </div>
</div>

<!-- User Demographics -->
<div class="admin-card">
    <h2 class="admin-card-title">User Demographics</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user-tag" style="color: #4cc9f0;"></i>
                By Role
            </h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                        <span style="color: #f9fafb;">Regular Users</span>
                        <span style="color: #9ca3af;"><?php echo number_format($demographics['regular_users'] ?? 0); ?> (<?php echo number_format($demographics['regular_percentage'] ?? 0, 1); ?>%)</span>
                    </div>
                    <div style="height: 6px; background: rgba(102, 126, 234, 0.2); border-radius: 3px; overflow: hidden;">
                        <div style="height: 100%; width: <?php echo $demographics['regular_percentage'] ?? 0; ?>%; background: #4cc9f0;"></div>
                    </div>
                </div>
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                        <span style="color: #f9fafb;">Administrators</span>
                        <span style="color: #9ca3af;"><?php echo number_format($demographics['admin_users'] ?? 0); ?> (<?php echo number_format($demographics['admin_percentage'] ?? 0, 1); ?>%)</span>
                    </div>
                    <div style="height: 6px; background: rgba(102, 126, 234, 0.2); border-radius: 3px; overflow: hidden;">
                        <div style="height: 100%; width: <?php echo $demographics['admin_percentage'] ?? 0; ?>%; background: #34d399;"></div>
                    </div>
                </div>
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                        <span style="color: #f9fafb;">Engineers</span>
                        <span style="color: #9ca3af;"><?php echo number_format($demographics['engineer_users'] ?? 0); ?> (<?php echo number_format($demographics['engineer_percentage'] ?? 0, 1); ?>%)</span>
                    </div>
                    <div style="height: 6px; background: rgba(102, 126, 234, 0.2); border-radius: 3px; overflow: hidden;">
                        <div style="height: 100%; width: <?php echo $demographics['engineer_percentage'] ?? 0; ?>%; background: #fbbf24;"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-chart-pie" style="color: #34d399;"></i>
                Engagement Metrics
            </h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                        <span style="color: #f9fafb;">Avg. Session Time</span>
                        <span style="color: #9ca3af;"><?php echo $engagement['avg_session_time'] ?? '0m'; ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                        <span style="color: #f9fafb;">Pages/Session</span>
                        <span style="color: #9ca3af;"><?php echo number_format($engagement['pages_per_session'] ?? 0, 1); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #f9fafb;">Bounce Rate</span>
                        <span style="color: #f87171;"><?php echo $engagement['bounce_rate'] ?? '0%'; ?></span>
                    </div>
                </div>
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                        <span style="color: #f9fafb;">Return Visitors</span>
                        <span style="color: #f9fafb;"><?php echo number_format($engagement['return_visitors'] ?? 0, 1); ?>%</span>
                    </div>
                    <div style="height: 6px; background: rgba(102, 126, 234, 0.2); border-radius: 3px; overflow: hidden;">
                        <div style="height: 100%; width: <?php echo $engagement['return_visitors'] ?? 0; ?>%; background: #fbbf24;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Performing Users -->
<div class="admin-card">
    <h2 class="admin-card-title">Top Active Users</h2>
    <div class="admin-card-content">
        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">User</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Email</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Calculations</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Last Active</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Total Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($top_users)): ?>
                        <?php foreach ($top_users as $user): ?>
                            <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars($user['username'] ?? 'Unknown'); ?></td>
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
                                <td style="padding: 0.75rem;"><?php echo number_format($user['calculations'] ?? 0); ?></td>
                                <td style="padding: 0.75rem;"><?php echo $user['last_active'] ?? 'Unknown'; ?></td>
                                <td style="padding: 0.75rem;"><?php echo $user['total_time'] ?? '0m'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 1rem; color: #9ca3af;">No active users found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- User Analytics Filters -->
<div class="admin-card">
    <h2 class="admin-card-title">User Analytics Filters</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <select style="padding: 0.5rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
            <option value="">All Time Periods</option>
            <option value="today">Today</option>
            <option value="week">This Week</option>
            <option value="month">This Month</option>
            <option value="quarter">This Quarter</option>
            <option value="year">This Year</option>
        </select>
        
        <select style="padding: 0.5rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
            <option value="">All Roles</option>
            <option value="admin">Administrators</option>
            <option value="user">Regular Users</option>
            <option value="engineer">Engineers</option>
        </select>
        
        <input type="text" placeholder="Search users..." style="padding: 0.5rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
        
        <button style="padding: 0.5rem 1rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer;">
            <i class="fas fa-search"></i> Apply Filters
        </button>
    </div>
</div>

<!-- User Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">User Analytics Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/analytics/users/export'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-file-export"></i>
            <span>Export Data</span>
        </a>

        <a href="<?php echo app_base_url('/admin/analytics/users/report'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-chart-bar"></i>
            <span>Generate Report</span>
        </a>

        <a href="<?php echo app_base_url('/admin/analytics/users/segments'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-tags"></i>
            <span>User Segments</span>
        </a>

        <a href="<?php echo app_base_url('/admin/analytics/users/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-cog"></i>
            <span>Analytics Settings</span>
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>