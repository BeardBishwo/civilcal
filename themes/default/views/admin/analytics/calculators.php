<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Calculator Analytics</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Track calculator usage and performance metrics</p>
        </div>
    </div>
</div>

<!-- Calculator Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-calculator" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Calculations</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_calculations'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">All Time</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +25% this month</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-check-circle" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Success Rate</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo $stats['success_rate'] ?? '0%'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Successful</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-chart-line"></i> Stable Performance</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-clock" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Avg. Response</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo $stats['avg_response_time'] ?? '0ms'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Processing</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-bolt"></i> Fast Processing</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-chart-line" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Today's Usage</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo number_format($stats['today_calculations'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Completed</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-fire"></i> High Activity</small>
    </div>
</div>

<!-- Calculator Usage Chart -->
<div class="admin-card">
    <h2 class="admin-card-title">Calculator Usage Patterns</h2>
    <div style="height: 350px; background: rgba(15, 23, 42, 0.5); border-radius: 8px; padding: 1rem; display: flex; align-items: center; justify-content: center;">
        <p style="color: #9ca3af; text-align: center;">Calculator Usage Chart<br>(Placeholder for chart showing usage patterns)</p>
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
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Trend</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($top_calculators)): ?>
                        <?php foreach ($top_calculators as $calc): ?>
                            <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <td style="padding: 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="<?php echo $calc['icon'] ?? 'fa-calculator'; ?>" style="color: #4cc9f0;"></i>
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
                                <td style="padding: 0.75rem;">
                                    <a href="<?php echo app_base_url('/admin/calculators/'.($calc['id'] ?? 0).'/edit'); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem; margin-right: 0.5rem;">
                                        <i class="fas fa-edit"></i>
                                        <span>Edit</span>
                                    </a>
                                    <a href="<?php echo app_base_url('/admin/analytics/calculators/'.($calc['id'] ?? 0).'/details'); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 4px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                                        <i class="fas fa-chart-bar"></i>
                                        <span>Details</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 1rem; color: #9ca3af;">No calculator data available</td>
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
                <i class="fas fa-thermometer-half" style="color: #fbbf24;"></i>
                Moderate Performers
            </h3>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php if (!empty($moderate_calcs)): ?>
                    <?php foreach (array_slice($moderate_calcs, 0, 5) as $calc): ?>
                        <li style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                            <span style="color: #f9fafb;"><?php echo htmlspecialchars($calc['name'] ?? 'Unknown'); ?></span>
                            <span style="color: #fbbf24;"><?php echo $calc['avg_time'] ?? '0ms'; ?></span>
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

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>