<?php
$page_title = 'Analytics Overview - ' . \App\Services\SettingsService::get('site_name', 'Engineering Calculator Pro');

$overviewStats = $stats ?? [];
$chartData = $charts['daily_calculations'] ?? [];
$topCalculators = $stats['top_calculators'] ?? [];

// Calculate stats for top badges
$totalUsers = $overviewStats['total_users'] ?? 0;
$activeUsers = $overviewStats['active_users'] ?? 0;
$totalCalcs = $overviewStats['total_calculations'] ?? 0;
$monthlyCalcs = $overviewStats['monthly_calculations'] ?? 0;
?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-chart-line"></i>
                    <h1>Analytics Overview</h1>
                </div>
                <div class="header-subtitle">Track adoption, engagement, and calculator performance at a glance</div>
            </div>
            <div class="header-actions">
                <button onclick="window.location.reload()" class="btn btn-secondary btn-compact" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); margin-right: 0.5rem;">
                    <i class="fas fa-sync-alt"></i>
                    <span>Refresh</span>
                </button>
                <a href="<?php echo app_base_url('/admin/analytics/reports'); ?>" class="btn btn-primary btn-compact" style="background: white; color: #667eea;">
                    <i class="fas fa-file-alt"></i>
                    <span>Reports</span>
                </a>
            </div>
        </div>

        <!-- Compact Stats Cards -->
        <div class="compact-stats">
            <!-- Total Users -->
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($totalUsers); ?></div>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-trend <?php echo ($overviewStats['user_growth'] ?? 0) >= 0 ? 'text-success' : 'text-danger'; ?>">
                        <i class="fas fa-<?php echo ($overviewStats['user_growth'] ?? 0) >= 0 ? 'arrow-up' : 'arrow-down'; ?>"></i>
                        <?php echo number_format(abs($overviewStats['user_growth'] ?? 0), 1); ?>%
                    </div>
                </div>
            </div>

            <!-- Active Users -->
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($activeUsers); ?></div>
                    <div class="stat-label">Active Users (30d)</div>
                    <div class="stat-trend <?php echo ($overviewStats['active_user_growth'] ?? 0) >= 0 ? 'text-success' : 'text-danger'; ?>">
                        <i class="fas fa-<?php echo ($overviewStats['active_user_growth'] ?? 0) >= 0 ? 'arrow-up' : 'arrow-down'; ?>"></i>
                        <?php echo number_format(abs($overviewStats['active_user_growth'] ?? 0), 1); ?>%
                    </div>
                </div>
            </div>

            <!-- Total Calculations -->
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($totalCalcs); ?></div>
                    <div class="stat-label">Total Calculations</div>
                    <div class="stat-trend <?php echo ($overviewStats['calculation_growth'] ?? 0) >= 0 ? 'text-success' : 'text-danger'; ?>">
                        <i class="fas fa-<?php echo ($overviewStats['calculation_growth'] ?? 0) >= 0 ? 'arrow-up' : 'arrow-down'; ?>"></i>
                        <?php echo number_format(abs($overviewStats['calculation_growth'] ?? 0), 1); ?>%
                    </div>
                </div>
            </div>

            <!-- Monthly Calculations -->
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-chart-area"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo number_format($monthlyCalcs); ?></div>
                    <div class="stat-label">Calculations (30d)</div>
                    <div class="stat-trend <?php echo ($overviewStats['monthly_calculation_growth'] ?? 0) >= 0 ? 'text-success' : 'text-danger'; ?>">
                        <i class="fas fa-<?php echo ($overviewStats['monthly_calculation_growth'] ?? 0) >= 0 ? 'arrow-up' : 'arrow-down'; ?>"></i>
                        <?php echo number_format(abs($overviewStats['monthly_calculation_growth'] ?? 0), 1); ?>%
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Content Area -->
        <div class="analytics-content-body">
            
            <!-- Main Grid: Activity & Top Calcs -->
            <div class="analytics-split-grid">
                
                <!-- Activity Chart -->
                <div class="page-card-compact chart-card">
                    <div class="card-header-compact">
                        <div class="header-title-sm">
                            <i class="fas fa-chart-area text-primary"></i>
                            Activity Trend
                        </div>
                        <div class="status-badge status-published">Live</div>
                    </div>
                    <div class="card-content-compact chart-container">
                        <canvas id="analytics-daily-activity"></canvas>
                    </div>
                </div>

                <!-- Top Calculators -->
                <div class="page-card-compact">
                    <div class="card-header-compact">
                        <div class="header-title-sm">
                            <i class="fas fa-fire text-warning"></i>
                            Top Calculators
                        </div>
                    </div>
                    <div class="card-content-compact p-0">
                        <?php if (!empty($topCalculators)): ?>
                            <div class="top-list">
                                <?php foreach ($topCalculators as $calculator): ?>
                                    <div class="top-list-item">
                                        <div class="top-item-icon">
                                            <i class="fas fa-calculator"></i>
                                        </div>
                                        <div class="top-item-info">
                                            <div class="top-item-name"><?php echo htmlspecialchars($calculator['name']); ?></div>
                                            <div class="top-item-meta">
                                                <?php echo number_format($calculator['uses']); ?> uses · 
                                                <?php echo number_format($calculator['share'] ?? 0, 1); ?>% share
                                            </div>
                                        </div>
                                        <div class="status-badge status-published">
                                            <?php echo number_format($calculator['uses']); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state-compact py-4">
                                <i class="fas fa-info-circle" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-2">No calculator usage data yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Deep Dive Section -->
            <div class="section-header">
                <h3><i class="fas fa-compass"></i> Deep Dive</h3>
                <p>Explore detailed analytics segments</p>
            </div>

            <div class="pages-grid-compact">
                <?php
                $links = [
                    [
                        'title' => 'User Analytics',
                        'description' => 'Conversion funnel, cohort retention, and growth trends',
                        'icon' => 'fas fa-users',
                        'accent' => 'primary',
                        'href' => app_base_url('/admin/analytics/users')
                    ],
                    [
                        'title' => 'Calculator Performance',
                        'description' => 'Success rate, completion time, and error hotspots',
                        'icon' => 'fas fa-calculator',
                        'accent' => 'success',
                        'href' => app_base_url('/admin/analytics/calculators')
                    ],
                    [
                        'title' => 'System Performance',
                        'description' => 'Server response times, uptime, and bottlenecks',
                        'icon' => 'fas fa-tachometer-alt',
                        'accent' => 'warning',
                        'href' => app_base_url('/admin/analytics/performance')
                    ],
                    [
                        'title' => 'Reports & Exports',
                        'description' => 'Downloadable CSV reports and scheduled digests',
                        'icon' => 'fas fa-file-export',
                        'accent' => 'info',
                        'href' => app_base_url('/admin/analytics/reports')
                    ],
                ];

                foreach ($links as $link): 
                    $colorClass = $link['accent'] === 'primary' ? '#667eea' : 
                                 ($link['accent'] === 'success' ? '#48bb78' : 
                                 ($link['accent'] === 'warning' ? '#ed8936' : '#4299e1'));
                ?>
                    <a href="<?php echo $link['href']; ?>" class="page-card-compact deep-dive-card">
                        <div class="card-content-compact">
                            <div class="stat-icon" style="background: <?php echo $colorClass; ?>; margin-bottom: 1rem;">
                                <i class="<?php echo $link['icon']; ?>"></i>
                            </div>
                            <h3 class="card-title-compact"><?php echo htmlspecialchars($link['title']); ?></h3>
                            <p class="text-muted small"><?php echo htmlspecialchars($link['description']); ?></p>
                        </div>
                        <div class="card-footer-compact justify-content-between text-primary">
                            <span style="font-size: 0.85rem; font-weight: 500;">View Details</span>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('analytics-daily-activity');
    if (!ctx || !window.Chart) {
        return;
    }

    const series = <?php echo json_encode($chartData); ?>;
    const labels = series.map(point => point.date);
    const values = series.map(point => point.count);

    if (window.analyticsDailyChart) {
        window.analyticsDailyChart.destroy();
    }

    window.analyticsDailyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Daily Calculations',
                data: values,
                borderColor: 'rgba(102, 126, 234, 1)',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.35,
                fill: true,
                pointRadius: 3,
                pointBackgroundColor: '#667eea',
                pointBorderWidth: 0,
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleColor: '#fff',
                    bodyColor: '#e5e7eb',
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: (context) => `${context.parsed.y.toLocaleString()} calculations`
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: '#9ca3af',
                        maxTicksLimit: 7,
                        callback: (value, index) => {
                            const date = labels[index];
                            return new Date(date).toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)',
                        drawBorder: false,
                    },
                    ticks: {
                        color: '#9ca3af',
                        callback: (value) => value.toLocaleString()
                    }
                }
            }
        }
    });
});
</script>

<style>
    /* ========================================
       SHARED STYLES (Ported from Optimized Pages)
       ======================================== */

    .admin-wrapper-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 1rem;
        background: var(--admin-gray-50, #f8f9fa);
        min-height: calc(100vh - 70px);
    }

    .admin-content-wrapper {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    /* HEADER */
    .compact-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .header-left { flex: 1; }
    
    .header-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.25rem;
    }

    .header-title h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
    }

    .header-title i {
        font-size: 1.5rem;
        opacity: 0.9;
    }

    .header-subtitle {
        font-size: 0.875rem;
        opacity: 0.85;
        margin: 0;
        color: rgba(255,255,255,0.9);
    }

    .btn-compact {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        border-radius: 6px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .btn-compact:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* STATS */
    .compact-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: #fff;
    }

    .stat-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem;
        background: var(--admin-gray-50, #f8f9fa);
        border-radius: 8px;
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        transition: all 0.2s ease;
    }

    .stat-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
    }

    .stat-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .stat-icon.primary { background: #667eea; }
    .stat-icon.success { background: #48bb78; }
    .stat-icon.warning { background: #ed8936; }
    .stat-icon.info { background: #4299e1; }

    .stat-info { flex: 1; }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--admin-gray-900, #1f2937);
        line-height: 1.1;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.75rem;
        color: var(--admin-gray-600, #6b7280);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        margin-bottom: 0.25rem;
    }
    
    .stat-trend {
        font-size: 0.75rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .text-success { color: #48bb78; }
    .text-danger { color: #f56565; }
    .text-warning { color: #ed8936; }
    .text-primary { color: #667eea; }

    /* CONTENT BODY */
    .analytics-content-body {
        padding: 2rem;
    }

    .analytics-split-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .page-card-compact {
        background: white;
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .page-card-compact:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1;
    }

    .card-header-compact {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: #f8f9fa;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-title-sm {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-content-compact {
        padding: 1.25rem;
        flex: 1;
    }
    
    .p-0 { padding: 0 !important; }

    .chart-container {
        position: relative;
        height: 350px;
        width: 100%;
    }

    /* TOP LIST */
    .top-list {
        display: flex;
        flex-direction: column;
    }

    .top-list-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.875rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.1s;
    }
    
    .top-list-item:last-child {
        border-bottom: none;
    }
    
    .top-list-item:hover {
        background: #f8f9fa;
    }

    .top-item-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .top-item-info {
        flex: 1;
        min-width: 0;
    }

    .top-item-name {
        font-weight: 600;
        color: #1f2937;
        font-size: 0.9rem;
        margin-bottom: 0.125rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .top-item-meta {
        font-size: 0.75rem;
        color: #6b7280;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.625rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }
    
    .status-published {
        background: rgba(72, 187, 120, 0.1);
        color: #48bb78;
    }

    /* SECTION HEADER */
    .section-header {
        margin-bottom: 1.5rem;
    }

    .section-header h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 0.25rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-header p {
        color: #6b7280;
        margin: 0;
        font-size: 0.9rem;
    }

    /* GRID */
    .pages-grid-compact {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }
    
    .deep-dive-card {
        text-decoration: none;
        display: block;
    }
    
    .deep-dive-card .card-title-compact {
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }
    
    .deep-dive-card .card-footer-compact {
        display: flex;
        align-items: center;
        background: #f8f9fa;
        padding: 0.75rem 1.25rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .text-muted { color: #6b7280; }
    .small { font-size: 0.875rem; }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
        .analytics-split-grid {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .compact-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
            padding: 1.25rem;
        }
        
        .compact-stats {
            grid-template-columns: 1fr;
            padding: 1.25rem;
        }
        
        .analytics-content-body {
            padding: 1.25rem;
        }
        
        .pages-grid-compact {
            grid-template-columns: 1fr;
        }
    }
</style>