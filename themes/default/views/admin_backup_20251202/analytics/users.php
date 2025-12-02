<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1>User Analytics</h1>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Detailed user metrics and growth analysis.</p>
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

<!-- User Statistics -->
<div class="admin-grid">
    <?php if (isset($user_stats['by_role']) && is_array($user_stats['by_role'])): ?>
        <?php foreach ($user_stats['by_role'] as $role_data): ?>
            <div class="admin-card" style="text-align: center; padding: 1.75rem; transition: transform 0.2s ease;">
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

    <div class="admin-card" style="text-align: center; padding: 1.75rem; transition: transform 0.2s ease;">
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
<div class="admin-card">
    <h2 class="admin-card-title">User Growth (Last 90 Days)</h2>
    <div style="height: 350px; background: rgba(15, 23, 42, 0.5); border-radius: 8px; padding: 1rem;">
        <canvas id="userGrowthChart" style="width: 100%; height: 280px;"></canvas>
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
<div class="admin-card">
    <h2 class="admin-card-title">Top Active Users</h2>
    <div class="admin-card-content">
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
}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>