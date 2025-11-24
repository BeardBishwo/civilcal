<?php
$page_title = 'User Analytics - Bishwo Calculator';
require_once dirname(__DIR__, 2) . '/themes/default/views/partials/header.php';
?>

<!-- Page Header -->
<div class="page-header" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 700; color: #f9fafb; margin: 0 0 0.5rem 0;">User Analytics</h1>
            <p style="color: #9ca3af; margin: 0; font-size: 1.125rem;">Detailed user metrics and growth analysis.</p>
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
    <?php if (isset($user_stats['by_role']) && is_array($user_stats['by_role'])): ?>
        <?php foreach ($user_stats['by_role'] as $role_data): ?>
            <div class="stat-card" style="--stat-color: #3b82f6;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
                    <div style="width: 50px; height: 50px; background: rgba(76, 201, 240, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user" style="font-size: 1.5rem; color: #4cc9f0;"></i>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">Total</div>
                    </div>
                </div>
                <div style="font-size: 2.25rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.75rem;"><?php echo number_format($role_data['count'] ?? 0); ?></div>
                <div style="color: #9ca3af; font-size: 0.875rem;"><?php echo ucfirst($role_data['role'] ?? 'Unknown'); ?> Users</div>
                <?php
                $growth = $role_data['growth'] ?? 0;
                $is_positive = $growth >= 0;
                ?>
                <small style="display: block; margin-top: 0.75rem; color: <?php echo $is_positive ? '#10b981' : '#ef4444'; ?>; font-size: 0.75rem;">
                    <i class="fas fa-<?php echo $is_positive ? 'arrow-up' : 'arrow-down'; ?>"></i>
                    <?php echo abs($growth); ?>% from last month
                </small>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="stat-card" style="--stat-color: #10b981;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.25rem;">
            <div style="width: 50px; height: 50px; background: rgba(52, 211, 153, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-user-plus" style="font-size: 1.5rem; color: #34d399;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">This Month</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #34d399; margin-bottom: 0.75rem;"><?php echo number_format($user_stats['new_this_month'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">New This Month</div>
        <?php
        $growth = $user_stats['new_users_growth'] ?? 0;
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
                <i class="fas fa-user-check" style="font-size: 1.5rem; color: #fbbf24;"></i>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">Active (30d)</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.75rem;"><?php echo number_format($user_stats['active_users'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Active Users (30d)</div>
        <?php
        $growth = $user_stats['active_user_growth'] ?? 0;
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
                <div style="font-size: 1rem; color: #9ca3af; font-size: 0.875rem;">Growth Rate</div>
            </div>
        </div>
        <div style="font-size: 2.25rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.75rem;"><?php echo number_format($user_stats['growth_rate'] ?? 0, 2); ?>%</div>
        <div style="color: #9ca3af; font-size: 0.875rem;">User Growth Rate</div>
        <?php
        $growth = $user_stats['growth_trend'] ?? 0;
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
        <i class="fas fa-chart-line" style="color: #4cc9f0;"></i>
        User Growth Patterns (Last 90 Days)
    </h3>
    <div style="height: 400px; background: rgba(15, 23, 42, 0.5); border-radius: 8px; padding: 1rem;">
        <canvas id="userGrowthChart" style="width: 100%; height: 350px;"></canvas>
    </div>
</div>

<!-- User Demographics -->
<div class="widget-card" style="margin-bottom: 2rem;">
    <h3 class="widget-title" style="margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fas fa-users" style="color: #34d399;"></i>
        User Demographics
    </h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h4 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user-tag" style="color: #4cc9f0;"></i>
                By Role
            </h4>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php if (!empty($user_stats['by_role'])): ?>
                    <?php foreach ($user_stats['by_role'] as $role_data): ?>
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                                <span style="color: #f9fafb;"><?php echo ucfirst($role_data['role'] ?? 'Unknown'); ?></span>
                                <span style="color: #9ca3af;"><?php echo number_format($role_data['count'] ?? 0); ?> (<?php echo number_format($role_data['percentage'] ?? 0, 1); ?>%)</span>
                            </div>
                            <div style="height: 6px; background: rgba(102, 126, 234, 0.2); border-radius: 3px; overflow: hidden;">
                                <div style="height: 100%; width: <?php echo $role_data['percentage'] ?? 0; ?>%; background: #4cc9f0;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="color: #9ca3af; text-align: center; padding: 1rem;">No demographics data available</div>
                <?php endif; ?>
            </div>
        </div>

        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h4 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-globe-americas" style="color: #34d399;"></i>
                Geographic Distribution
            </h4>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php if (!empty($user_stats['by_location'])): ?>
                    <?php foreach ($user_stats['by_location'] as $location_data): ?>
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                                <span style="color: #f9fafb;"><?php echo htmlspecialchars($location_data['country'] ?? 'Unknown'); ?></span>
                                <span style="color: #9ca3af;"><?php echo number_format($location_data['count'] ?? 0); ?></span>
                            </div>
                            <div style="height: 6px; background: rgba(102, 126, 234, 0.2); border-radius: 3px; overflow: hidden;">
                                <div style="height: 100%; width: <?php echo $location_data['percentage'] ?? 0; ?>%; background: #34d399;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="color: #9ca3af; text-align: center; padding: 1rem;">No location data available</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Top Active Users -->
<div class="widget-card">
    <h3 class="widget-title" style="margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fas fa-crown" style="color: #fbbf24;"></i>
        Top Active Users
    </h3>
    <div style="overflow-x: auto;">
        <table class="admin-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600; font-size: 0.85rem;">User</th>
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600; font-size: 0.85rem;">Email</th>
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600; font-size: 0.85rem;">Calculations</th>
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600; font-size: 0.85rem;">Last Active</th>
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600; font-size: 0.85rem;">Account Age</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($user_stats['top_active_users'])): ?>
                    <?php foreach ($user_stats['top_active_users'] as $user): ?>
                        <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                            <td style="padding: 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 32px; height: 32px; background: rgba(76, 201, 240, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <span style="color: #4cc9f0; font-size: 0.75rem;"><?php echo strtoupper(substr($user['username'] ?? 'U', 0, 1)); ?></span>
                                    </div>
                                    <span style="color: #f9fafb;"><?php echo htmlspecialchars($user['username'] ?? 'Unknown'); ?></span>
                                </div>
                            </td>
                            <td style="padding: 0.75rem; color: #f9fafb;"><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
                            <td style="padding: 0.75rem; color: #4cc9f0; font-weight: 600;"><?php echo number_format($user['calculations'] ?? 0); ?></td>
                            <td style="padding: 0.75rem; color: #9ca3af;"><?php echo $user['last_active'] ?? 'Unknown'; ?></td>
                            <td style="padding: 0.75rem; color: #9ca3af;"><?php echo $user['account_age'] ?? '0 days'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 1.5rem; color: #9ca3af;">No active user data available</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Chart.js Integration -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Chart data from PHP
const userData = <?php echo json_encode($growth_data ?? $user_stats['growth_data'] ?? []); ?>;
const userGrowthData = {
    labels: userData.map(d => d.date),
    datasets: [{
        label: 'New Users',
        data: userData.map(d => d.count),
        borderColor: '#4cc9f0',
        backgroundColor: 'rgba(76, 201, 240, 0.1)',
        tension: 0.4,
        fill: true
    }]
};

// Initialize charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart !== 'undefined' && userData.length > 0) {
        const ctx = document.getElementById('userGrowthChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: userGrowthData,
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