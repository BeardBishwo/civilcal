<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Analytics Dashboard</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Track usage patterns and system performance metrics</p>
        </div>
    </div>
</div>

<!-- Analytics Overview -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-users" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Users</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_users'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Active Users</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +15% this month</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-calculator" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Calculations</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_calculations'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">All Time</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-bolt"></i> High usage</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-chart-line" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Avg. Session</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo $stats['avg_session_duration'] ?? '0m'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Duration</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-clock"></i> Engaging experience</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-bolt" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">System Load</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo $stats['system_load'] ?? '0%'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Current</div>
        <small style="color: <?php echo ($stats['system_load'] ?? 0) > 80 ? '#f87171' : '#10b981'; ?>; font-size: 0.75rem;">
            <i class="fas <?php echo ($stats['system_load'] ?? 0) > 80 ? 'fa-exclamation-triangle' : 'fa-check-circle'; ?>"></i>
            <?php echo ($stats['system_load'] ?? 0) > 80 ? 'High Load' : 'Optimal'; ?>
        </small>
    </div>
</div>

<!-- Charts Section -->
<div class="admin-card">
    <h2 class="admin-card-title">Usage Analytics</h2>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div style="height: 300px; background: rgba(15, 23, 42, 0.5); border-radius: 8px; padding: 1rem; display: flex; align-items: center; justify-content: center;">
            <p style="color: #9ca3af; text-align: center;">User Activity Chart<br>(Placeholder for chart)</p>
        </div>
        <div style="height: 300px; background: rgba(15, 23, 42, 0.5); border-radius: 8px; padding: 1rem; display: flex; align-items: center; justify-content: center;">
            <p style="color: #9ca3af; text-align: center;">Calculations Trend<br>(Placeholder for chart)</p>
        </div>
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
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Calculator</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Uses</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Success Rate</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Avg. Time</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($top_calculators)): ?>
                        <?php foreach ($top_calculators as $calculator): ?>
                            <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars($calculator['name'] ?? ''); ?></td>
                                <td style="padding: 0.75rem;"><?php echo number_format($calculator['uses'] ?? 0); ?></td>
                                <td style="padding: 0.75rem;"><?php echo number_format($calculator['success_rate'] ?? 0, 2); ?>%</td>
                                <td style="padding: 0.75rem;"><?php echo $calculator['avg_time'] ?? '0s'; ?></td>
                                <td style="padding: 0.75rem;">
                                    <span style="color: #34d399; background: rgba(52, 211, 153, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px;">
                                        <i class="fas fa-check-circle"></i> Active
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 1rem; color: #9ca3af;">No calculator data available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Analytics Filters -->
<div class="admin-card">
    <h2 class="admin-card-title">Analytics Filters</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
        <label style="color: #f9fafb; font-size: 0.875rem;">Date Range:</label>
        <select style="flex: 1; padding: 0.5rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb; min-width: 150px;">
            <option value="today">Today</option>
            <option value="week">This Week</option>
            <option value="month">This Month</option>
            <option value="quarter">This Quarter</option>
            <option value="year">This Year</option>
            <option value="custom">Custom Range</option>
        </select>
        
        <button style="padding: 0.5rem 1rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer;">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </div>
</div>

<!-- Data Export -->
<div class="admin-card">
    <h2 class="admin-card-title">Data Export</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/analytics/export/csv'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-file-csv"></i>
            <span>Export CSV</span>
        </a>

        <a href="<?php echo app_base_url('/admin/analytics/export/xlsx'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-file-excel"></i>
            <span>Export Excel</span>
        </a>

        <a href="<?php echo app_base_url('/admin/analytics/print'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-print"></i>
            <span>Print Report</span>
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>