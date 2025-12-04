<?php
$page_title = 'Analytics Overview - Bishwo Calculator';

$overviewStats = $stats ?? [];
$chartData = $charts['daily_calculations'] ?? [];
$topCalculators = $stats['top_calculators'] ?? [];
?>

<section class="analytics-hero">
    <div class="analytics-hero__content">
        <div>
            <h1>
                <span class="icon-circle icon-circle--primary">
                    <i class="fas fa-chart-line"></i>
                </span>
                Analytics Overview
            </h1>
            <p>Track adoption, engagement, and calculator performance at a glance.</p>
        </div>
        <div class="analytics-hero__actions">
            <button type="button" class="btn btn-secondary" onclick="window.location.reload()">
                <i class="fas fa-sync-alt"></i>
                Refresh
            </button>
            <a class="btn btn-primary" href="<?php echo app_base_url('/admin/analytics/reports'); ?>">
                <i class="fas fa-file-alt"></i>
                Reports
            </a>
        </div>
    </div>
    <div class="analytics-hero__summary">
        <div class="summary-pill">
            <span class="summary-pill__label">New Users (30d)</span>
            <span class="summary-pill__value"><?php echo number_format($overviewStats['new_users'] ?? 0); ?></span>
        </div>
        <div class="divider"></div>
        <div class="summary-pill">
            <span class="summary-pill__label">Bounce Rate</span>
            <span class="summary-pill__value"><?php echo number_format($overviewStats['bounce_rate'] ?? 0, 1); ?>%</span>
        </div>
        <div class="divider"></div>
        <div class="summary-pill">
            <span class="summary-pill__label">Avg. Session</span>
            <span class="summary-pill__value"><?php echo htmlspecialchars($overviewStats['avg_session_duration'] ?? '0m 00s'); ?></span>
        </div>
    </div>
</section>

<section class="analytics-section">
    <div class="analytics-metric-grid">
        <?php
        $metrics = [
            [
                'label' => 'Total Users',
                'value' => number_format($overviewStats['total_users'] ?? 0),
                'trend' => $overviewStats['user_growth'] ?? 0,
                'description' => 'All registered users across the platform',
                'icon' => 'fas fa-users',
                'accent' => 'primary'
            ],
            [
                'label' => 'Active Users (30d)',
                'value' => number_format($overviewStats['active_users'] ?? 0),
                'trend' => $overviewStats['active_user_growth'] ?? 0,
                'description' => 'Unique users running calculations in the last 30 days',
                'icon' => 'fas fa-user-check',
                'accent' => 'success'
            ],
            [
                'label' => 'Total Calculations',
                'value' => number_format($overviewStats['total_calculations'] ?? 0),
                'trend' => $overviewStats['calculation_growth'] ?? 0,
                'description' => 'All-time calculations completed',
                'icon' => 'fas fa-calculator',
                'accent' => 'warning'
            ],
            [
                'label' => 'Calculations (30d)',
                'value' => number_format($overviewStats['monthly_calculations'] ?? 0),
                'trend' => $overviewStats['monthly_calculation_growth'] ?? 0,
                'description' => 'Recent calculation volume compared to the previous month',
                'icon' => 'fas fa-chart-area',
                'accent' => 'info'
            ],
        ];

        foreach ($metrics as $metric):
            $trend = $metric['trend'];
            $isPositive = $trend >= 0;
        ?>
            <article class="analytics-metric-card analytics-metric-card--<?php echo $metric['accent']; ?>">
                <div class="analytics-metric-card__icon">
                    <i class="<?php echo $metric['icon']; ?>"></i>
                </div>
                <div class="analytics-metric-card__body">
                    <h3><?php echo htmlspecialchars($metric['label']); ?></h3>
                    <p class="metric-value"><?php echo $metric['value']; ?></p>
                    <p class="metric-description"><?php echo htmlspecialchars($metric['description']); ?></p>
                </div>
                <div class="analytics-metric-card__trend <?php echo $isPositive ? 'trend--up' : 'trend--down'; ?>">
                    <i class="fas fa-<?php echo $isPositive ? 'arrow-up' : 'arrow-down'; ?>"></i>
                    <span><?php echo number_format(abs($trend), 1); ?>% vs. prev. period</span>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="analytics-section analytics-section--split">
    <div class="analytics-card analytics-card--wide">
        <div class="analytics-card__header">
            <div>
                <h3><i class="fas fa-chart-area"></i> Activity Trend</h3>
                <span class="analytics-card__subtitle">Daily calculation volume for the last 30 days</span>
            </div>
            <div class="chip chip--neutral">Live</div>
        </div>
        <div class="analytics-card__body analytics-card__body--chart">
            <canvas id="analytics-daily-activity"></canvas>
        </div>
    </div>

    <div class="analytics-card analytics-card--compact">
        <div class="analytics-card__header">
            <div>
                <h3><i class="fas fa-fire"></i> Top Calculators</h3>
                <span class="analytics-card__subtitle">Most used calculators across the platform</span>
            </div>
        </div>
        <div class="analytics-card__body">
            <?php if (!empty($topCalculators)): ?>
                <ul class="top-calculator-list">
                    <?php foreach ($topCalculators as $calculator): ?>
                        <li class="top-calculator-list__item">
                            <div class="avatar avatar--gradient">
                                <i class="fas fa-calculator"></i>
                            </div>
                            <div class="top-calculator-list__content">
                                <span class="top-calculator-list__name"><?php echo htmlspecialchars($calculator['name']); ?></span>
                                <span class="top-calculator-list__meta"><?php echo number_format($calculator['uses']); ?> uses · <?php echo number_format($calculator['share'] ?? 0, 1); ?>% share</span>
                            </div>
                            <span class="badge badge--pill badge--soft-primary"><?php echo number_format($calculator['uses']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-info-circle"></i>
                    <p>No calculator usage data yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="analytics-section">
    <div class="analytics-card">
        <div class="analytics-card__header">
            <div>
                <h3><i class="fas fa-compass"></i> Deep Dive</h3>
                <span class="analytics-card__subtitle">Explore detailed analytics segments</span>
            </div>
        </div>
        <div class="analytics-card__body analytics-card__body--grid">
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

            foreach ($links as $link): ?>
                <a class="insight-card insight-card--<?php echo $link['accent']; ?>" href="<?php echo $link['href']; ?>">
                    <span class="insight-card__icon"><i class="<?php echo $link['icon']; ?>"></i></span>
                    <span class="insight-card__title"><?php echo htmlspecialchars($link['title']); ?></span>
                    <span class="insight-card__description"><?php echo htmlspecialchars($link['description']); ?></span>
                    <span class="insight-card__cta">
                        View details
                        <i class="fas fa-arrow-right"></i>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

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
                backgroundColor: 'rgba(102, 126, 234, 0.15)',
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
                    backgroundColor: '#0f172a',
                    titleColor: '#fff',
                    bodyColor: '#cbd5f5',
                    borderColor: '#1e293b',
                    borderWidth: 1,
                    padding: 12,
                    callbacks: {
                        label: (context) => `${context.parsed.y.toLocaleString()} calculations`
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: '#94a3b8',
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
                        color: 'rgba(148, 163, 184, 0.15)',
                        drawBorder: false,
                    },
                    ticks: {
                        color: '#94a3b8',
                        callback: (value) => value.toLocaleString()
                    }
                }
            }
        }
    });
});
</script>