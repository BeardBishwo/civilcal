<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1>Analytics Overview</h1>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Comprehensive analytics and insights for your platform.</p>
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
</div>

<!-- Analytics Overview Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.75rem; transition: transform 0.2s ease;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
            <div style="width: 50px; height: 50px; background: rgba(76, 201, 240, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-chart-line" style="font-size: 1.5rem; color: #4cc9f0;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">All Time</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.75rem;"><?php echo number_format($stats['total_page_views'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Total Page Views</div>
        <?php
        $growth = $stats['page_view_growth'] ?? 0;
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
                <i class="fas fa-users" style="font-size: 1.5rem; color: #34d399;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">This Month</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #34d399; margin-bottom: 0.75rem;"><?php echo number_format($stats['unique_visitors'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Unique Visitors</div>
        <?php
        $growth = $stats['visitor_growth'] ?? 0;
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
                <i class="fas fa-calculator" style="font-size: 1.5rem; color: #fbbf24;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">Processed</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.75rem;"><?php echo number_format($stats['total_calculations'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Total Calculations</div>
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
            <div style="width: 50px; height: 50px; background: rgba(34, 211, 238, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-clock" style="font-size: 1.5rem; color: #22d3ee;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">Duration</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.75rem;"><?php echo $stats['avg_session_duration'] ?? '0m'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Avg. Session</div>
        <?php
        $growth = $stats['session_growth'] ?? 0;
        $is_positive = $growth >= 0;
        ?>
        <small style="display: block; margin-top: 0.75rem; color: <?php echo $is_positive ? '#10b981' : '#ef4444'; ?>; font-size: 0.75rem;">
            <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
            <?php echo abs($growth); ?>% from last month
        </small>
    </div>
</div>

<!-- Charts Section -->
<div class="admin-card">
    <h2 class="admin-card-title">Analytics Charts</h2>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div style="height: 350px; background: rgba(15, 23, 42, 0.5); border-radius: 8px; padding: 1rem;">
            <h5 style="color: #f9fafb; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-chart-line" style="color: #4cc9f0;"></i>
                User Activity
            </h5>
            <canvas id="userActivityChart" style="width: 100%; height: 280px;"></canvas>
        </div>
        <div style="height: 350px; background: rgba(15, 23, 42, 0.5); border-radius: 8px; padding: 1rem;">
            <h5 style="color: #f9fafb; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-chart-bar" style="color: #34d399;"></i>
                Calculations Trend
            </h5>
            <canvas id="calculationTrendChart" style="width: 100%; height: 280px;"></canvas>
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
                    <?php if (!empty($stats['top_calculators'])): ?>
                        <?php foreach ($stats['top_calculators'] as $calculator): ?>
                            <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <td style="padding: 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fas fa-calculator" style="color: #4cc9f0;"></i>
                                        <span style="color: #f9fafb;"><?php echo htmlspecialchars($calculator['name'] ?? ''); ?></span>
                                    </div>
                                </td>
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

<!-- Recent Activity -->
<div class="admin-card">
    <h2 class="admin-card-title">Recent Activity</h2>
    <div class="admin-card-content">
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
                    <?php
                    $activities = $stats['recent_activities'] ?? [];
                    if (!empty($activities) && is_array($activities)):
                        foreach (array_slice($activities, 0, 5) as $activity): ?>
                        <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                            <td style="padding: 0.75rem; color: #f9fafb;"><?php echo htmlspecialchars($activity['user'] ?? 'Unknown'); ?></td>
                            <td style="padding: 0.75rem; color: #f9fafb;"><?php echo htmlspecialchars($activity['action'] ?? 'Unknown action'); ?></td>
                            <td style="padding: 0.75rem; color: #9ca3af; font-size: 0.85rem;"><?php echo htmlspecialchars($activity['time'] ?? 'Unknown time'); ?></td>
                            <td style="padding: 0.75rem; color: #9ca3af; font-size: 0.85rem;"><?php echo htmlspecialchars($activity['ip'] ?? 'Unknown IP'); ?></td>
                        </tr>
                    <?php
                        endforeach;
                    else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 1.5rem; color: #9ca3af;">No recent activity to display</td>
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

<script>
// Initialize charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart !== 'undefined') {
        // User Activity Chart
        const userActivityCtx = document.getElementById('userActivityChart');
        if (userActivityCtx) {
            new Chart(userActivityCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Active Users',
                        data: [1200, 1900, 1500, 1800, 2200, 2500],
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

        // Calculation Trend Chart
        const calcTrendCtx = document.getElementById('calculationTrendChart');
        if (calcTrendCtx) {
            new Chart(calcTrendCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Calculations',
                        data: [3000, 4500, 3800, 5200, 6100, 7300],
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

    div[style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>