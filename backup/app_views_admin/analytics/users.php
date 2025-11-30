<?php
ob_start();
?>

<!-- Page Header -->
<div class="page-header" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 600; color: #f9fafb; margin: 0 0 0.5rem 0;">User Analytics</h1>
            <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Detailed user metrics and growth analysis.</p>
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

<!-- User Statistics -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <?php if (isset($user_stats['by_role']) && is_array($user_stats['by_role'])): ?>
        <?php foreach ($user_stats['by_role'] as $role_data): ?>
            <div class="stat-card" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem; transition: transform 0.2s ease;">
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
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="stat-card" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem; transition: transform 0.2s ease;">
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
    </div>
</div>

<!-- User Growth Chart -->
<div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem; margin-bottom: 2rem;">
    <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
        <i class="fas fa-chart-line" style="color: #4cc9f0;"></i>
        User Growth (Last 90 Days)
    </h5>
    <canvas id="userGrowthChart" style="width: 100%; max-height: 400px;"></canvas>
</div>

<!-- User Demographics -->
<div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem; margin-bottom: 2rem;">
    <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0;">User Demographics</h5>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user-tag" style="color: #4cc9f0;"></i>
                By Role
            </h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php if (isset($user_stats['by_role']) && is_array($user_stats['by_role'])): ?>
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
                <?php endif; ?>
            </div>
        </div>

        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-globe-americas" style="color: #34d399;"></i>
                Geographic Distribution
            </h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php if (isset($user_stats['by_location']) && is_array($user_stats['by_location'])): ?>
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
                    <div style="color: #9ca3af; text-align: center; padding: 1rem;">No geographic data available</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Top Active Users -->
<div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem;">
    <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0;">Top Active Users</h5>
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
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
                <?php if (isset($user_stats['top_active_users']) && is_array($user_stats['top_active_users'])): ?>
                    <?php foreach ($user_stats['top_active_users'] as $user): ?>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                        <td style="padding: 0.75rem; color: #f9fafb;"><?php echo htmlspecialchars($user['username'] ?? 'Unknown'); ?></td>
                        <td style="padding: 0.75rem; color: #f9fafb;"><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
                        <td style="padding: 0.75rem; color: #4cc9f0; font-weight: 600;"><?php echo number_format($user['calculations'] ?? 0); ?></td>
                        <td style="padding: 0.75rem; color: #9ca3af;"><?php echo $user['last_active'] ?? 'Unknown'; ?></td>
                        <td style="padding: 0.75rem; color: #9ca3af;"><?php echo $user['account_age'] ?? '0 days'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 1.5rem; color: #9ca3af;">No active user data to display</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
const growthData = <?php echo json_encode($growth_data ?? []); ?>;
const dailyUserData = <?php echo json_encode($user_stats['daily_users'] ?? []); ?>;

document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart !== 'undefined' && growthData.length > 0) {
        const ctx = document.getElementById('userGrowthChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: growthData.map(d => d.date),
                    datasets: [{
                        label: 'New Users',
                        data: growthData.map(d => d.count),
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
