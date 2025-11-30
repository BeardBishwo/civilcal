<?php
ob_start();
?>

<!-- Page Header -->
<div class="page-header" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 600; color: #f9fafb; margin: 0 0 0.5rem 0;">Analytics Overview</h1>
            <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Comprehensive analytics and insights for your platform.</p>
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
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">

    <!-- Total Users -->
    <div class="stat-card" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem; transition: transform 0.2s ease;">
        <div style="display: flex; justify-content: space-between; align-items-start; margin-bottom: 1.25rem;">
            <div style="width: 50px; height: 50px; background: rgba(76, 201, 240, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-users" style="font-size: 1.5rem; color: #4cc9f0;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">Total</div>
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

    <!-- Active Users -->
    <div class="stat-card" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem; transition: transform 0.2s ease;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
            <div style="width: 50px; height: 50px; background: rgba(52, 211, 153, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-user-check" style="font-size: 1.5rem; color: #34d399;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">30d</div>
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

    <!-- Total Calculations -->
    <div class="stat-card" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem; transition: transform 0.2s ease;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
            <div style="width: 50px; height: 50px; background: rgba(251, 191, 36, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
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

    <!-- Monthly Calculations -->
    <div class="stat-card" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem; transition: transform 0.2s ease;">
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
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.75rem; margin-bottom: 2rem;">

    <!-- Daily Calculations Chart -->
    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem;">
        <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-chart-area" style="color: #4cc9f0;"></i>
            Daily Calculations (Last 30 Days)
        </h5>
        <canvas id="dailyCalculationsChart" style="width: 100%; max-height: 300px;"></canvas>
    </div>

    <!-- User Growth Chart -->
    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem;">
        <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fas fa-users" style="color: #34d399;"></i>
            User Growth
        </h5>
        <canvas id="userGrowthChart" style="width: 100%; max-height: 300px;"></canvas>
    </div>

</div>

<!-- Quick Links -->
<div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem; margin-bottom: 2rem;">
    <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0;">Detailed Analytics</h5>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
        <a href="<?php echo app_base_url('/admin/analytics/users'); ?>" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: rgba(76, 201, 240, 0.05); border: 1px solid rgba(76, 201, 240, 0.15); border-radius: 8px; text-decoration: none; transition: all 0.2s ease;">
            <div style="width: 50px; height: 50px; background: rgba(76, 201, 240, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-users" style="color: #4cc9f0; font-size: 1.5rem;"></i>
            </div>
            <div>
                <strong style="color: #f9fafb; font-size: 0.95rem; display: block; margin-bottom: 0.25rem;">User Analytics</strong>
                <small style="color: #9ca3af; font-size: 0.85rem;">Detailed user metrics</small>
            </div>
        </a>
        <a href="<?php echo app_base_url('/admin/analytics/calculators'); ?>" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: rgba(52, 211, 153, 0.05); border: 1px solid rgba(52, 211, 153, 0.15); border-radius: 8px; text-decoration: none; transition: all 0.2s ease;">
            <div style="width: 50px; height: 50px; background: rgba(52, 211, 153, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-calculator" style="color: #34d399; font-size: 1.5rem;"></i>
            </div>
            <div>
                <strong style="color: #f9fafb; font-size: 0.95rem; display: block; margin-bottom: 0.25rem;">Calculator Analytics</strong>
                <small style="color: #9ca3af; font-size: 0.85rem;">Usage statistics</small>
            </div>
        </a>
        <a href="<?php echo app_base_url('/admin/analytics/performance'); ?>" style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: rgba(251, 191, 36, 0.05); border: 1px solid rgba(251, 191, 36, 0.15); border-radius: 8px; text-decoration: none; transition: all 0.2s ease;">
            <div style="width: 50px; height: 50px; background: rgba(251, 191, 36, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-tachometer-alt" style="color: #fbbf24; font-size: 1.5rem;"></i>
            </div>
            <div>
                <strong style="color: #f9fafb; font-size: 0.95rem; display: block; margin-bottom: 0.25rem;">Performance</strong>
                <small style="color: #9ca3af; font-size: 0.85rem;">System metrics</small>
            </div>
        </a>
    </div>
</div>

<!-- Recent Activity -->
<div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem;">
    <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0;">Recent Activity</h5>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
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

<script>
// Chart data from PHP
const dailyCalculationsData = <?php echo json_encode($charts['daily_calculations'] ?? []); ?>;
const userGrowthData = <?php echo json_encode($charts['user_growth'] ?? []); ?>;

// Initialize charts if Chart.js is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart !== 'undefined') {
        // Daily Calculations Chart
        const dailyCtx = document.getElementById('dailyCalculationsChart');
        if (dailyCtx) {
            new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: dailyCalculationsData.map(d => d.date),
                    datasets: [{
                        label: 'Daily Calculations',
                        data: dailyCalculationsData.map(d => d.count),
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

        // User Growth Chart
        const userGrowthCtx = document.getElementById('userGrowthChart');
        if (userGrowthCtx) {
            new Chart(userGrowthCtx, {
                type: 'line',
                data: {
                    labels: userGrowthData.map(d => d.date),
                    datasets: [{
                        label: 'User Growth',
                        data: userGrowthData.map(d => d.count),
                        borderColor: '#34d399',
                        backgroundColor: 'rgba(52, 211, 153, 0.1)',
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

a[href*="/admin/analytics/"]:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
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

    div[style*="grid-template-columns: repeat(auto-fit, minmax(400px, 1fr))"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
