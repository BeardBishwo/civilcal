<?php
ob_start();
?>

<!-- Page Header -->
<div class="page-header" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 600; color: #f9fafb; margin: 0 0 0.5rem 0;">Calculator Analytics</h1>
            <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Calculator usage statistics and trends.</p>
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
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="stat-card" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem; transition: transform 0.2s ease;">
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
    </div>

    <div class="stat-card" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem; transition: transform 0.2s ease;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
            <div style="width: 50px; height: 50px; background: rgba(52, 211, 153, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-check-circle" style="font-size: 1.5rem; color: #34d399;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">Success Rate</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #34d399; margin-bottom: 0.75rem;"><?php echo $calculator_stats['success_rate'] ?? '0%'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Success Rate</div>
    </div>

    <div class="stat-card" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem; transition: transform 0.2s ease;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
            <div style="width: 50px; height: 50px; background: rgba(251, 191, 36, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-clock" style="font-size: 1.5rem; color: #fbbf24;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">Response Time</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.75rem;"><?php echo $calculator_stats['avg_response_time'] ?? '0ms'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Avg. Response Time</div>
    </div>

    <div class="stat-card" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem; transition: transform 0.2s ease;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
            <div style="width: 50px; height: 50px; background: rgba(34, 211, 238, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-chart-line" style="font-size: 1.5rem; color: #22d3ee;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">Today</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.75rem;"><?php echo number_format($calculator_stats['today_calculations'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Today's Calculations</div>
    </div>
</div>

<!-- Calculator Usage Chart -->
<div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem; margin-bottom: 2rem;">
    <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fas fa-chart-area" style="color: #4cc9f0;"></i>
        Calculator Usage Patterns (Last 30 Days)
    </h5>
    <canvas id="calculatorUsageChart" style="width: 100%; max-height: 400px;"></canvas>
</div>

<!-- Top Performing Calculators -->
<div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem; margin-bottom: 2rem;">
    <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0;">Top Performing Calculators</h5>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
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
                <?php if (isset($calculator_stats['top_calculators']) && is_array($calculator_stats['top_calculators'])): ?>
                    <?php foreach ($calculator_stats['top_calculators'] as $calc): ?>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                        <td style="padding: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-calculator" style="color: #4cc9f0;"></i>
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

<!-- Calculator Performance Analysis -->
<div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem; margin-bottom: 2rem;">
    <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0;">Calculator Performance Analysis</h5>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-bolt" style="color: #4cc9f0;"></i>
                Fastest Calculators
            </h3>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php if (isset($calculator_stats['fastest_calcs']) && is_array($calculator_stats['fastest_calcs'])): ?>
                    <?php foreach (array_slice($calculator_stats['fastest_calcs'], 0, 5) as $calc): ?>
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
                <?php if (isset($calculator_stats['slowest_calcs']) && is_array($calculator_stats['slowest_calcs'])): ?>
                    <?php foreach (array_slice($calculator_stats['slowest_calcs'], 0, 5) as $calc): ?>
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
                <?php if (isset($calculator_stats['error_calcs']) && is_array($calculator_stats['error_calcs'])): ?>
                    <?php foreach (array_slice($calculator_stats['error_calcs'], 0, 5) as $calc): ?>
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

<!-- Calculator Usage Distribution -->
<div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem;">
    <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0;">Calculator Usage Distribution</h5>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-layer-group" style="color: #4cc9f0;"></i>
                By Category
            </h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php if (isset($calculator_stats['by_category']) && is_array($calculator_stats['by_category'])): ?>
                    <?php foreach ($calculator_stats['by_category'] as $category_data): ?>
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                                <span style="color: #f9fafb;"><?php echo htmlspecialchars($category_data['category'] ?? 'Unknown'); ?></span>
                                <span style="color: #9ca3af;"><?php echo number_format($category_data['count'] ?? 0); ?> (<?php echo number_format($category_data['percentage'] ?? 0, 1); ?>%)</span>
                            </div>
                            <div style="height: 6px; background: rgba(102, 126, 234, 0.2); border-radius: 3px; overflow: hidden;">
                                <div style="height: 100%; width: <?php echo $category_data['percentage'] ?? 0; ?>%; background: #4cc9f0;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="color: #9ca3af; text-align: center; padding: 1rem;">No category data available</div>
                <?php endif; ?>
            </div>
        </div>

        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-clock" style="color: #34d399;"></i>
                Peak Usage Times
            </h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php if (isset($calculator_stats['peak_times']) && is_array($calculator_stats['peak_times'])): ?>
                    <?php foreach (array_slice($calculator_stats['peak_times'], 0, 5) as $time_data): ?>
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                                <span style="color: #f9fafb;"><?php echo htmlspecialchars($time_data['hour'] ?? 'Unknown'); ?></span>
                                <span style="color: #9ca3af;"><?php echo number_format($time_data['count'] ?? 0); ?> calculations</span>
                            </div>
                            <div style="height: 6px; background: rgba(102, 126, 234, 0.2); border-radius: 3px; overflow: hidden;">
                                <div style="height: 100%; width: <?php echo $time_data['percentage'] ?? 0; ?>%; background: #34d399;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="color: #9ca3af; text-align: center; padding: 1rem;">No peak time data available</div>
                <?php endif; ?>
            </div>
        </div>
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
.stat-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(67, 97, 238, 0.3);
}

button:hover,
a[style*="border: 1px"][href] {
    transform: translateY(-2px);
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

    .stat-card {
        padding: 1.25rem;
    }
}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
