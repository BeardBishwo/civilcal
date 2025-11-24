<?php
$page_title = 'Calculator Analytics - Bishwo Calculator';
require_once dirname(__DIR__, 2) . '/themes/default/views/partials/header.php';
?>

<!-- Page Header -->
<div class="page-header" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 700; color: #f9fafb; margin: 0 0 0.5rem 0;">Calculator Analytics</h1>
            <p style="color: #9ca3af; margin: 0; font-size: 1.125rem;">Detailed calculator usage and performance metrics.</p>
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

<!-- Calculator Statistics -->
<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card" style="--stat-color: #3b82f6;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
            <div style="width: 50px; height: 50px; background: rgba(76, 201, 240, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-calculator" style="font-size: 1.5rem; color: #4cc9f0;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">All Time</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.75rem;"><?php echo number_format($calculator_stats['total_calculations'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Total Calculations</div>
        <?php
        $growth = $calculator_stats['calculation_growth'] ?? 0;
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
                <i class="fas fa-check-circle" style="font-size: 1.5rem; color: #34d399;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">Success Rate</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #34d399; margin-bottom: 0.75rem;"><?php echo number_format($calculator_stats['success_rate'] ?? 0, 2); ?>%</div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Success Rate</div>
        <?php
        $growth = $calculator_stats['success_rate_growth'] ?? 0;
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
                <i class="fas fa-bolt" style="font-size: 1.5rem; color: #fbbf24;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">Avg. Response</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.75rem;"><?php echo $calculator_stats['avg_response_time'] ?? '0ms'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Avg. Response Time</div>
        <?php
        $growth = $calculator_stats['response_time_trend'] ?? 0;
        $is_positive = $growth >= 0;
        ?>
        <small style="display: block; margin-top: 0.75rem; color: <?php echo $is_positive ? '#10b981' : '#ef4444'; ?>; font-size: 0.75rem;">
            <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
            <?php echo abs($growth); ?>% from last month
        </small>
    </div>

    <div class="stat-card" style="--stat-color: #38bdf8;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
            <div style="width: 50px; height: 50px; background: rgba(34, 211, 238, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-chart-line" style="font-size: 1.5rem; color: #22d3ee;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">This Month</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.75rem;"><?php echo number_format($calculator_stats['monthly_calculations'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Monthly Calculations</div>
        <?php
        $growth = $calculator_stats['monthly_calculation_growth'] ?? 0;
        $is_positive = $growth >= 0;
        ?>
        <small style="display: block; margin-top: 0.75rem; color: <?php echo $is_positive ? '#10b981' : '#ef4444'; ?>; font-size: 0.75rem;">
            <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
            <?php echo abs($growth); ?>% from last month
        </small>
    </div>
</div>

<!-- Calculator Usage Chart -->
<div class="widget-card" style="margin-bottom: 2rem;">
    <h3 class="widget-title" style="margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fas fa-chart-area" style="color: #4cc9f0;"></i>
        Calculator Usage Patterns (Last 90 Days)
    </h3>
    <div style="height: 400px; background: rgba(15, 23, 42, 0.5); border-radius: 8px; padding: 1rem;">
        <canvas id="calculatorUsageChart" style="width: 100%; height: 350px;"></canvas>
    </div>
</div>

<!-- Top Performing Calculators -->
<div class="widget-card" style="margin-bottom: 2rem;">
    <h3 class="widget-title" style="margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fas fa-trophy" style="color: #fbbf24;"></i>
        Top Performing Calculators
    </h3>
    <div style="overflow-x: auto;">
        <table class="admin-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600; font-size: 0.85rem;">Calculator</th>
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600; font-size: 0.85rem;">Uses</th>
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600; font-size: 0.85rem;">Success Rate</th>
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600; font-size: 0.85rem;">Avg. Time</th>
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600; font-size: 0.85rem;">Trend</th>
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600; font-size: 0.85rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($calculator_stats['top_calculators'])): ?>
                    <?php foreach ($calculator_stats['top_calculators'] as $calc): ?>
                        <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                            <td style="padding: 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i class="<?php echo $calc['icon'] ?? 'fas fa-calculator'; ?>" style="color: #4cc9f0; font-size: 1.25rem;"></i>
                                    <span style="color: #f9fafb;"><?php echo htmlspecialchars($calc['name'] ?? 'Unknown Calculator'); ?></span>
                                </div>
                            </td>
                            <td style="padding: 0.75rem; color: #f9fafb;"><?php echo number_format($calc['uses'] ?? 0); ?></td>
                            <td style="padding: 0.75rem; color: #34d399; font-weight: 600;"><?php echo number_format($calc['success_rate'] ?? 0, 2); ?>%</td>
                            <td style="padding: 0.75rem; color: #f9fafb;"><?php echo $calc['avg_time'] ?? '0s'; ?></td>
                            <td style="padding: 0.75rem;">
                                <span style="color: <?php echo ($calc['trend'] ?? 0) >= 0 ? '#34d399' : '#f87171'; ?>; display: flex; align-items: center; gap: 0.25rem;">
                                    <i class="fas <?php echo ($calc['trend'] ?? 0) >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'; ?>"></i>
                                    <?php echo abs($calc['trend'] ?? 0); ?>%
                                </span>
                            </td>
                            <td style="padding: 0.75rem;">
                                <a href="<?php echo app_base_url('/admin/calculators/' . ($calc['id'] ?? 0) . '/edit'); ?>" style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem; margin-right: 0.5rem;">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit</span>
                                </a>
                                <a href="<?php echo app_base_url('/admin/analytics/calculators/' . ($calc['id'] ?? 0) . '/details'); ?>" style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 4px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                                    <i class="fas fa-chart-bar"></i>
                                    <span>Details</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 1.5rem; color: #9ca3af;">No calculator data available</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Calculator Performance Analysis -->
<div class="widget-card" style="margin-bottom: 2rem;">
    <h3 class="widget-title" style="margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fas fa-rocket" style="color: #8b5cf6;"></i>
        Performance Analysis
    </h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h4 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-bolt" style="color: #4cc9f0;"></i>
                Fastest Calculators
            </h4>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php if (!empty($calculator_stats['fastest_calcs'])): ?>
                    <?php foreach (array_slice($calculator_stats['fastest_calcs'], 0, 5) as $calc): ?>
                        <li style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                            <span style="color: #f9fafb;"><?php echo htmlspecialchars($calc['name'] ?? 'Unknown'); ?></span>
                            <span style="color: #34d399; font-weight: 600;"><?php echo $calc['avg_time'] ?? '0ms'; ?></span>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="color: #9ca3af; text-align: center; padding: 1rem;">No data available</li>
                <?php endif; ?>
            </ul>
        </div>

        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h4 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-hourglass-half" style="color: #f87171;"></i>
                Slowest Calculators
            </h4>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php if (!empty($calculator_stats['slowest_calcs'])): ?>
                    <?php foreach (array_slice($calculator_stats['slowest_calcs'], 0, 5) as $calc): ?>
                        <li style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                            <span style="color: #f9fafb;"><?php echo htmlspecialchars($calc['name'] ?? 'Unknown'); ?></span>
                            <span style="color: #f87171; font-weight: 600;"><?php echo $calc['avg_time'] ?? '0ms'; ?></span>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="color: #9ca3af; text-align: center; padding: 1rem;">No data available</li>
                <?php endif; ?>
            </ul>
        </div>

        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h4 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-exclamation-triangle" style="color: #f87171;"></i>
                Error Prone Calculators
            </h4>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php if (!empty($calculator_stats['error_calcs'])): ?>
                    <?php foreach (array_slice($calculator_stats['error_calcs'], 0, 5) as $calc): ?>
                        <li style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                            <span style="color: #f9fafb;"><?php echo htmlspecialchars($calc['name'] ?? 'Unknown'); ?></span>
                            <span style="color: #f87171; font-weight: 600;"><?php echo number_format($calc['error_rate'] ?? 0, 2); ?>%</span>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="color: #9ca3af; text-align: center; padding: 1rem;">No error data available</li>
                <?php endif; ?>
            </ul>
        </div>

        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h4 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-layer-group" style="color: #4cc9f0;"></i>
                By Category
            </h4>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php if (!empty($calculator_stats['by_category'])): ?>
                    <?php foreach ($calculator_stats['by_category'] as $category): ?>
                        <li style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                            <span style="color: #f9fafb;"><?php echo htmlspecialchars($category['category'] ?? 'Unknown'); ?></span>
                            <span style="color: #4cc9f0; font-weight: 600;"><?php echo number_format($category['count'] ?? 0); ?></span>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="color: #9ca3af; text-align: center; padding: 1rem;">No category data available</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<!-- Calculator Filters -->
<div class="widget-card" style="margin-bottom: 2rem;">
    <h3 class="widget-title" style="margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fas fa-filter" style="color: #fbbf24;"></i>
        Calculator Analytics Filters
    </h3>
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
<div class="widget-card">
    <h3 class="widget-title" style="margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fas fa-tools" style="color: #8b5cf6;"></i>
        Calculator Analytics Actions
    </h3>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/analytics/calculators/export'); ?>" style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-file-export"></i>
            <span>Export Data</span>
        </a>

        <a href="<?php echo app_base_url('/admin/analytics/calculators/optimize'); ?>" style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-bolt"></i>
            <span>Optimize Calculators</span>
        </a>

        <a href="<?php echo app_base_url('/admin/analytics/calculators/performance-report'); ?>" style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-chart-line"></i>
            <span>Performance Report</span>
        </a>

        <a href="<?php echo app_base_url('/admin/analytics/calculators/settings'); ?>" style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-cog"></i>
            <span>Analytics Settings</span>
        </a>
    </div>
</div>

<!-- Chart.js Integration -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const usageData = <?php echo json_encode($calculator_stats['usage_by_day'] ?? []); ?>;
const calcUsageData = <?php echo json_encode($calculator_stats['daily_calculator_usage'] ?? []); ?>;
const chartData = {
    labels: usageData.map(d => d.date),
    datasets: [{
        label: 'Calculations',
        data: usageData.map(d => d.count),
        borderColor: '#4cc9f0',
        backgroundColor: 'rgba(76, 201, 240, 0.1)',
        tension: 0.4,
        fill: true
    }]
};

// Initialize charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart !== 'undefined' && usageData.length > 0) {
        const ctx = document.getElementById('calculatorUsageChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: chartData,
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
.stat-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(67, 97, 238, 0.3);
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

.stats-grid {
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
    .page-header div {
        flex-direction: column;
        align-items: flex-start;
    }

    .page-header div:last-child {
        width: 100%;
    }

    .page-header button,
    .page-header a {
        width: 100%;
        justify-content: center;
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