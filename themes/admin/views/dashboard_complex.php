<?php
$page_title = $page_title ?? 'Complex Analytics Dashboard';
$analytics_data = $analytics_data ?? [];
$user_metrics = $user_metrics ?? [];
$calculator_stats = $calculator_stats ?? [];
$revenue_data = $revenue_data ?? [];
$engagement_metrics = $engagement_metrics ?? [];
?>

<div class="admin-content">
    <div class="page-header">
        <h1><i class="fas fa-chart-line"></i> Complex Analytics Dashboard</h1>
        <p>Advanced analytics and insights for comprehensive system monitoring</p>
        <div class="page-actions">
            <div class="date-range-selector">
                <input type="date" id="start-date" value="<?= date('Y-m-d', strtotime('-30 days')) ?>">
                <span>to</span>
                <input type="date" id="end-date" value="<?= date('Y-m-d') ?>">
                <button class="btn btn-secondary" onclick="applyDateRange()">Apply</button>
            </div>
            <button class="btn btn-primary" onclick="exportAnalytics()">
                <i class="fas fa-download"></i> Export Analytics
            </button>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="kpi-section">
        <div class="kpi-card">
            <div class="kpi-header">
                <h3>Total Users</h3>
                <div class="kpi-trend <?= ($user_metrics['growth_rate'] ?? 0) > 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-<?= ($user_metrics['growth_rate'] ?? 0) > 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                    <?= number_format(abs($user_metrics['growth_rate'] ?? 0), 1) ?>%
                </div>
            </div>
            <div class="kpi-value"><?= number_format($user_metrics['total_users'] ?? 0) ?></div>
            <div class="kpi-details">
                <span><?= number_format($user_metrics['new_users'] ?? 0) ?> new this month</span>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-header">
                <h3>Calculator Usage</h3>
                <div class="kpi-trend <?= ($calculator_stats['usage_change'] ?? 0) > 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-<?= ($calculator_stats['usage_change'] ?? 0) > 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                    <?= number_format(abs($calculator_stats['usage_change'] ?? 0), 1) ?>%
                </div>
            </div>
            <div class="kpi-value"><?= number_format($calculator_stats['total_calculations'] ?? 0) ?></div>
            <div class="kpi-details">
                <span><?= number_format($calculator_stats['daily_average'] ?? 0) ?> per day</span>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-header">
                <h3>Revenue</h3>
                <div class="kpi-trend <?= ($revenue_data['growth_rate'] ?? 0) > 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-<?= ($revenue_data['growth_rate'] ?? 0) > 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                    <?= number_format(abs($revenue_data['growth_rate'] ?? 0), 1) ?>%
                </div>
            </div>
            <div class="kpi-value">$<?= number_format($revenue_data['total_revenue'] ?? 0, 2) ?></div>
            <div class="kpi-details">
                <span>$<?= number_format($revenue_data['monthly_average'] ?? 0, 2) ?> monthly avg</span>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-header">
                <h3>Engagement Rate</h3>
                <div class="kpi-trend <?= ($engagement_metrics['change'] ?? 0) > 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-<?= ($engagement_metrics['change'] ?? 0) > 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                    <?= number_format(abs($engagement_metrics['change'] ?? 0), 1) ?>%
                </div>
            </div>
            <div class="kpi-value"><?= number_format($engagement_metrics['rate'] ?? 0, 1) ?>%</div>
            <div class="kpi-details">
                <span><?= number_format($engagement_metrics['avg_session_time'] ?? 0, 1) ?> min avg session</span>
            </div>
        </div>
    </div>

    <!-- Complex Analytics Charts -->
    <div class="analytics-charts">
        <!-- User Growth & Retention Chart -->
        <div class="chart-container large">
            <div class="chart-header">
                <h3>User Growth & Retention Analysis</h3>
                <div class="chart-controls">
                    <select id="user-growth-period" onchange="updateUserGrowthChart()">
                        <option value="7d">Last 7 Days</option>
                        <option value="30d" selected>Last 30 Days</option>
                        <option value="90d">Last 90 Days</option>
                        <option value="1y">Last Year</option>
                    </select>
                </div>
            </div>
            <div class="chart-content">
                <canvas id="user-growth-chart"></canvas>
            </div>
        </div>

        <!-- Calculator Usage Heatmap -->
        <div class="chart-container">
            <div class="chart-header">
                <h3>Calculator Usage Heatmap</h3>
                <div class="chart-legend">
                    <span class="legend-item low">Low</span>
                    <span class="legend-item medium">Medium</span>
                    <span class="legend-item high">High</span>
                </div>
            </div>
            <div class="chart-content">
                <div id="usage-heatmap"></div>
            </div>
        </div>

        <!-- Revenue Distribution -->
        <div class="chart-container">
            <div class="chart-header">
                <h3>Revenue Distribution</h3>
                <div class="chart-controls">
                    <select id="revenue-period" onchange="updateRevenueChart()">
                        <option value="month">This Month</option>
                        <option value="quarter">This Quarter</option>
                        <option value="year">This Year</option>
                    </select>
                </div>
            </div>
            <div class="chart-content">
                <canvas id="revenue-chart"></canvas>
            </div>
        </div>

        <!-- Geographic Distribution -->
        <div class="chart-container">
            <div class="chart-header">
                <h3>Geographic Distribution</h3>
                <div class="chart-controls">
                    <select id="geo-metric" onchange="updateGeoChart()">
                        <option value="users">Users</option>
                        <option value="usage">Usage</option>
                        <option value="revenue">Revenue</option>
                    </select>
                </div>
            </div>
            <div class="chart-content">
                <div id="geo-chart"></div>
            </div>
        </div>
    </div>

    <!-- Advanced Metrics Table -->
    <div class="metrics-table-section">
        <div class="table-header">
            <h3>Advanced Performance Metrics</h3>
            <div class="table-controls">
                <input type="text" id="metrics-search" placeholder="Search metrics..." onkeyup="filterMetrics()">
                <button class="btn btn-secondary" onclick="exportMetricsTable()">
                    <i class="fas fa-file-excel"></i> Export CSV
                </button>
            </div>
        </div>
        <div class="table-container">
            <table class="metrics-table" id="metrics-table">
                <thead>
                    <tr>
                        <th onclick="sortTable(0)">Metric <i class="fas fa-sort"></i></th>
                        <th onclick="sortTable(1)">Current Value <i class="fas fa-sort"></i></th>
                        <th onclick="sortTable(2)">Previous Period <i class="fas fa-sort"></i></th>
                        <th onclick="sortTable(3)">Change <i class="fas fa-sort"></i></th>
                        <th onclick="sortTable(4)">Trend <i class="fas fa-sort"></i></th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($analytics_data['metrics'] ?? [] as $metric): ?>
                        <tr>
                            <td><?= htmlspecialchars($metric['name']) ?></td>
                            <td><?= htmlspecialchars($metric['current_value']) ?></td>
                            <td><?= htmlspecialchars($metric['previous_value']) ?></td>
                            <td class="<?= $metric['change_class'] ?? '' ?>">
                                <?= htmlspecialchars($metric['change']) ?>
                            </td>
                            <td>
                                <canvas id="trend-<?= htmlspecialchars($metric['id']) ?>" width="50" height="20"></canvas>
                            </td>
                            <td>
                                <span class="status-badge status-<?= htmlspecialchars($metric['status']) ?>">
                                    <?= htmlspecialchars($metric['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Predictive Analytics -->
    <div class="predictive-section">
        <h3>Predictive Analytics & Insights</h3>
        <div class="predictive-cards">
            <div class="predictive-card">
                <div class="predictive-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h4>Growth Forecast</h4>
                <div class="forecast-value">
                    <?= number_format($analytics_data['predictions']['user_growth'] ?? 0, 1) ?>%
                </div>
                <p>Expected user growth in next 30 days</p>
                <div class="confidence">
                    Confidence: <?= number_format($analytics_data['predictions']['growth_confidence'] ?? 0, 1) ?>%
                </div>
            </div>

            <div class="predictive-card">
                <div class="predictive-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <h4>Revenue Projection</h4>
                <div class="forecast-value">
                    $<?= number_format($analytics_data['predictions']['revenue_projection'] ?? 0, 0) ?>
                </div>
                <p>Projected revenue for next quarter</p>
                <div class="confidence">
                    Confidence: <?= number_format($analytics_data['predictions']['revenue_confidence'] ?? 0, 1) ?>%
                </div>
            </div>

            <div class="predictive-card">
                <div class="predictive-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h4>Risk Assessment</h4>
                <div class="forecast-value risk-<?= htmlspecialchars($analytics_data['predictions']['risk_level'] ?? 'low') ?>">
                    <?= ucfirst(htmlspecialchars($analytics_data['predictions']['risk_level'] ?? 'Low')) ?>
                </div>
                <p>System performance risk level</p>
                <div class="risk-factors">
                    <?php foreach ($analytics_data['predictions']['risk_factors'] ?? [] as $factor): ?>
                        <span class="risk-factor"><?= htmlspecialchars($factor) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeComplexCharts();
    initializeHeatmap();
    initializeTrendCharts();
});

function initializeComplexCharts() {
    // User Growth Chart
    const userGrowthCtx = document.getElementById('user-growth-chart').getContext('2d');
    const userGrowthChart = new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($user_metrics['growth_labels'] ?? []) ?>,
            datasets: [
                {
                    label: 'New Users',
                    data: <?= json_encode($user_metrics['new_users_data'] ?? []) ?>,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Active Users',
                    data: <?= json_encode($user_metrics['active_users_data'] ?? []) ?>,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Retention Rate (%)',
                    data: <?= json_encode($user_metrics['retention_data'] ?? []) ?>,
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    position: 'left'
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });

    // Revenue Chart
    const revenueCtx = document.getElementById('revenue-chart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_keys($revenue_data['distribution'] ?? [])) ?>,
            datasets: [{
                data: <?= json_encode(array_values($revenue_data['distribution'] ?? [])) ?>,
                backgroundColor: [
                    '#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function initializeHeatmap() {
    // Initialize usage heatmap
    const heatmapData = <?= json_encode($calculator_stats['usage_heatmap'] ?? []) ?>;
    const heatmapContainer = document.getElementById('usage-heatmap');
    
    // Create heatmap visualization
    let heatmapHTML = '<div class="heatmap-grid">';
    for (let hour = 0; hour < 24; hour++) {
        heatmapHTML += '<div class="heatmap-row">';
        for (let day = 0; day < 7; day++) {
            const value = heatmapData[day] && heatmapData[day][hour] ? heatmapData[day][hour] : 0;
            const intensity = Math.min(value / 100, 1); // Normalize to 0-1
            heatmapHTML += `<div class="heatmap-cell" style="background-color: rgba(0, 123, 255, ${intensity})" title="${value} calculations"></div>`;
        }
        heatmapHTML += '</div>';
    }
    heatmapHTML += '</div>';
    
    // Add day labels
    const dayLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    heatmapHTML += '<div class="heatmap-labels">';
    dayLabels.forEach(day => {
        heatmapHTML += `<div class="day-label">${day}</div>`;
    });
    heatmapHTML += '</div>';
    
    heatmapContainer.innerHTML = heatmapHTML;
}

function initializeTrendCharts() {
    // Initialize mini trend charts for each metric
    <?php foreach ($analytics_data['metrics'] ?? [] as $metric): ?>
        if (document.getElementById('trend-<?= htmlspecialchars($metric['id']) ?>')) {
            const ctx = document.getElementById('trend-<?= htmlspecialchars($metric['id']) ?>').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?= json_encode($metric['trend_labels'] ?? []) ?>,
                    datasets: [{
                        data: <?= json_encode($metric['trend_data'] ?? []) ?>,
                        borderColor: '#007bff',
                        borderWidth: 2,
                        fill: false,
                        pointRadius: 0,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: false,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { display: false },
                        y: { display: false }
                    }
                }
            });
        }
    <?php endforeach; ?>
}

function applyDateRange() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    
    if (startDate && endDate) {
        window.location.href = `<?= app_base_url('/admin/dashboard/complex') ?>?start_date=${startDate}&end_date=${endDate}`;
    }
}

function updateUserGrowthChart() {
    const period = document.getElementById('user-growth-period').value;
    // Implementation to update chart based on selected period
    console.log('Updating user growth chart for period:', period);
}

function updateRevenueChart() {
    const period = document.getElementById('revenue-period').value;
    // Implementation to update chart based on selected period
    console.log('Updating revenue chart for period:', period);
}

function updateGeoChart() {
    const metric = document.getElementById('geo-metric').value;
    // Implementation to update geographic chart
    console.log('Updating geographic chart for metric:', metric);
}

function exportAnalytics() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    window.open(`<?= app_base_url('/admin/analytics/export') ?>?start_date=${startDate}&end_date=${endDate}`, '_blank');
}

function filterMetrics() {
    const searchTerm = document.getElementById('metrics-search').value.toLowerCase();
    const rows = document.querySelectorAll('#metrics-table tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

function sortTable(columnIndex) {
    const table = document.getElementById('metrics-table');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        const aValue = a.cells[columnIndex].textContent.trim();
        const bValue = b.cells[columnIndex].textContent.trim();
        
        if (columnIndex === 3) { // Change column
            return parseFloat(aValue.replace('%', '')) - parseFloat(bValue.replace('%', ''));
        }
        
        return aValue.localeCompare(bValue);
    });
    
    tbody.innerHTML = '';
    rows.forEach(row => tbody.appendChild(row));
}

function exportMetricsTable() {
    window.open('<?= app_base_url('/admin/analytics/export-metrics') ?>', '_blank');
}
</script>

<style>
.kpi-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.kpi-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    position: relative;
}

.kpi-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.kpi-header h3 {
    margin: 0;
    font-size: 14px;
    color: #6c757d;
    font-weight: 500;
}

.kpi-trend {
    display: flex;
    align-items: center;
    gap: 3px;
    font-size: 12px;
    font-weight: 600;
}

.kpi-trend.positive {
    color: #28a745;
}

.kpi-trend.negative {
    color: #dc3545;
}

.kpi-value {
    font-size: 32px;
    font-weight: bold;
    color: #212529;
    margin-bottom: 5px;
}

.kpi-details {
    font-size: 12px;
    color: #6c757d;
}

.analytics-charts {
    display: grid;
    grid-template-columns: 2fr 1fr;
    grid-template-rows: auto auto;
    gap: 20px;
    margin: 30px 0;
}

.chart-container.large {
    grid-column: 1;
    grid-row: 1 / 3;
}

.chart-container {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.chart-header h3 {
    margin: 0;
    font-size: 18px;
}

.chart-controls select {
    padding: 5px 10px;
    border: 1px solid #e9ecef;
    border-radius: 4px;
}

.chart-content {
    height: 300px;
    position: relative;
}

.chart-container.large .chart-content {
    height: 400px;
}

.chart-legend {
    display: flex;
    gap: 15px;
    align-items: center;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
}

.legend-item::before {
    content: '';
    width: 12px;
    height: 12px;
    border-radius: 2px;
}

.legend-item.low::before {
    background: #e3f2fd;
}

.legend-item.medium::before {
    background: #90caf9;
}

.legend-item.high::before {
    background: #1976d2;
}

.heatmap-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
    margin-bottom: 10px;
}

.heatmap-row {
    display: flex;
    gap: 2px;
}

.heatmap-cell {
    width: 20px;
    height: 20px;
    border-radius: 2px;
    cursor: pointer;
    transition: transform 0.2s;
}

.heatmap-cell:hover {
    transform: scale(1.2);
}

.heatmap-labels {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
}

.day-label {
    text-align: center;
    font-size: 10px;
    color: #6c757d;
}

.metrics-table-section {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin: 30px 0;
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.table-header h3 {
    margin: 0;
    font-size: 18px;
}

.table-controls {
    display: flex;
    gap: 10px;
    align-items: center;
}

.table-controls input {
    padding: 5px 10px;
    border: 1px solid #e9ecef;
    border-radius: 4px;
}

.table-container {
    overflow-x: auto;
}

.metrics-table {
    width: 100%;
    border-collapse: collapse;
}

.metrics-table th,
.metrics-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #e9ecef;
}

.metrics-table th {
    background: #f8f9fa;
    font-weight: 600;
    cursor: pointer;
    user-select: none;
}

.metrics-table th:hover {
    background: #e9ecef;
}

.metrics-table th i {
    margin-left: 5px;
    font-size: 12px;
}

.positive {
    color: #28a745;
}

.negative {
    color: #dc3545;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.status-good {
    background: #d4edda;
    color: #155724;
}

.status-warning {
    background: #fff3cd;
    color: #856404;
}

.status-critical {
    background: #f8d7da;
    color: #721c24;
}

.predictive-section {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin: 30px 0;
}

.predictive-section h3 {
    margin: 0 0 20px 0;
    font-size: 18px;
    color: #212529;
}

.predictive-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.predictive-card {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
}

.predictive-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin: 0 auto 15px;
}

.predictive-card h4 {
    margin: 0 0 10px 0;
    font-size: 16px;
    color: #212529;
}

.forecast-value {
    font-size: 28px;
    font-weight: bold;
    color: #007bff;
    margin-bottom: 10px;
}

.forecast-value.risk-low {
    color: #28a745;
}

.forecast-value.risk-medium {
    color: #ffc107;
}

.forecast-value.risk-high {
    color: #dc3545;
}

.predictive-card p {
    margin: 0 0 10px 0;
    color: #6c757d;
    font-size: 14px;
}

.confidence {
    font-size: 12px;
    color: #6c757d;
}

.risk-factors {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: 10px;
}

.risk-factor {
    background: #e9ecef;
    color: #495057;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 10px;
}

.date-range-selector {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-right: 15px;
}

.date-range-selector input {
    padding: 5px 10px;
    border: 1px solid #e9ecef;
    border-radius: 4px;
}

.date-range-selector span {
    color: #6c757d;
    font-size: 14px;
}

@media (max-width: 768px) {
    .analytics-charts {
        grid-template-columns: 1fr;
        grid-template-rows: auto;
    }
    
    .chart-container.large {
        grid-column: 1;
        grid-row: 1;
    }
    
    .predictive-cards {
        grid-template-columns: 1fr;
    }
    
    .date-range-selector {
        flex-direction: column;
        align-items: stretch;
        gap: 5px;
    }
}
</style>
