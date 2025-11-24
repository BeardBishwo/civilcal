<?php
$page_title = 'Analytics Overview - Bishwo Calculator';
require_once dirname(__DIR__, 2) . '/themes/default/views/partials/header.php';
?>

<!-- Page Header -->
<div class="page-header" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 700; color: #f9fafb; margin: 0 0 0.5rem 0;">Analytics Overview</h1>
            <p style="color: #9ca3af; margin: 0; font-size: 1.125rem;">Comprehensive analytics and insights for your platform.</p>
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
<div class="stats-grid" style="margin-bottom: 2rem;">
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
                <i class="fas fa-user-check" style="font-size: 1.5rem; color: #34d399;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">Last 30 Days</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #34d399; margin-bottom: 0.75rem;"><?php echo number_format($stats['active_users'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Active Users (30d)</div>
        <?php
        $growth = $stats['active_user_growth'] ?? 0;
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
                <i class="fas fa-calculator" style="font-size: 1.5rem; color: #fbbf24;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">All Time</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.75rem;"><?php echo number_format($stats['total_calculations'] ?? 0); ?></div>
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

    <div class="stat-card" style="--stat-color: #38bdf8;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
            <div style="width: 50px; height: 50px; background: rgba(34, 211, 238, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-chart-line" style="font-size: 1.5rem; color: #22d3ee;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">This Month</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.75rem;"><?php echo number_format($stats['monthly_calculations'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Monthly Calculations</div>
        <?php
        $growth = $stats['monthly_calculation_growth'] ?? 0;
        $is_positive = $growth >= 0;
        ?>
        <small style="display: block; margin-top: 0.75rem; color: <?php echo $is_positive ? '#10b981' : '#ef4444'; ?>; font-size: 0.75rem;">
            <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
            <?php echo abs($growth); ?>% from last month
        </small>
    </div>
</div>

<!-- Charts Section -->
<div class="widget-card" style="margin-bottom: 2rem;">
    <h3 class="widget-title" style="margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fas fa-chart-area" style="color: #4cc9f0;"></i>
        User Activity Trends
    </h3>
    <div style="height: 400px; background: rgba(15, 23, 42, 0.5); border-radius: 8px; padding: 1rem;">
        <canvas id="userActivityChart" style="width: 100%; height: 350px;"></canvas>
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
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($top_calculators)): ?>
                    <?php foreach ($top_calculators as $calc): ?>
                        <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                            <td style="padding: 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i class="<?php echo $calc['icon'] ?? 'fas fa-calculator'; ?>" style="color: #4cc9f0; font-size: 1.25rem;"></i>
                                    <span style="color: #f9fafb;"><?php echo htmlspecialchars($calc['name'] ?? 'Unknown Calculator'); ?></span>
                                </div>
                            </td>
                            <td style="padding: 0.75rem; color: #f9fafb;"><?php echo number_format($calc['uses'] ?? 0); ?></td>
                            <td style="padding: 0.75rem; color: #f9fafb;"><?php echo number_format($calc['success_rate'] ?? 0, 2); ?>%</td>
                            <td style="padding: 0.75rem; color: #f9fafb;"><?php echo $calc['avg_time'] ?? '0s'; ?></td>
                            <td style="padding: 0.75rem;">
                                <span style="color: <?php echo ($calc['trend'] ?? 0) >= 0 ? '#34d399' : '#f87171'; ?>; display: flex; align-items: center; gap: 0.25rem;">
                                    <i class="fas fa-<?php echo ($calc['trend'] ?? 0) >= 0 ? 'arrow-up' : 'arrow-down'; ?>"></i>
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

<!-- Quick Links -->
<div class="widget-card">
    <h3 class="widget-title" style="margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fas fa-compass" style="color: #34d399;"></i>
        Detailed Analytics
    </h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
        <a href="<?php echo app_base_url('/admin/analytics/users'); ?>" class="quick-action-card" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: rgba(76, 201, 240, 0.05); border: 1px solid rgba(76, 201, 240, 0.15); border-radius: 8px; text-decoration: none; transition: all 0.2s ease; color: inherit;">
            <div style="width: 50px; height: 50px; background: rgba(76, 201, 240, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-users" style="font-size: 1.5rem; color: #4cc9f0;"></i>
            </div>
            <div>
                <h4 style="color: #f9fafb; font-size: 1.125rem; margin: 0 0 0.25rem 0;">User Analytics</h4>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Detailed user metrics and behavior</p>
            </div>
        </a>

        <a href="<?php echo app_base_url('/admin/analytics/calculators'); ?>" class="quick-action-card" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: rgba(52, 211, 153, 0.05); border: 1px solid rgba(52, 211, 153, 0.15); border-radius: 8px; text-decoration: none; transition: all 0.2s ease; color: inherit;">
            <div style="width: 50px; height: 50px; background: rgba(52, 211, 153, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-calculator" style="font-size: 1.5rem; color: #34d399;"></i>
            </div>
            <div>
                <h4 style="color: #f9fafb; font-size: 1.125rem; margin: 0 0 0.25rem 0;">Calculator Analytics</h4>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Performance and usage statistics</p>
            </div>
        </a>

        <a href="<?php echo app_base_url('/admin/analytics/performance'); ?>" class="quick-action-card" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: rgba(245, 158, 11, 0.05); border: 1px solid rgba(245, 158, 11, 0.15); border-radius: 8px; text-decoration: none; transition: all 0.2s ease; color: inherit;">
            <div style="width: 50px; height: 50px; background: rgba(245, 158, 11, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-tachometer-alt" style="font-size: 1.5rem; color: #fbbf24;"></i>
            </div>
            <div>
                <h4 style="color: #f9fafb; font-size: 1.125rem; margin: 0 0 0.25rem 0;">Performance</h4>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">System performance metrics</p>
            </div>
        </a>
    </div>
</div>

<!-- Chart.js Integration -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const dailyData = <?php echo json_encode($stats['recent_data'] ?? $stats['daily_calculations'] ?? []); ?>;

document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart !== 'undefined' && dailyData.length > 0) {
        const ctx = document.getElementById('userActivityChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dailyData.map(d => d.date),
                    datasets: [{
                        label: 'User Activity',
                        data: dailyData.map(d => d.count),
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

.quick-action-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.quick-action-card:hover {
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
    margin-bottom: 2rem;
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