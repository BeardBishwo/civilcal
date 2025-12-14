<?php
$page_title = $page_title ?? 'Performance Dashboard';
$performance_metrics = $performance_metrics ?? [];
$system_stats = $system_stats ?? [];
$database_stats = $database_stats ?? [];
$cache_stats = $cache_stats ?? [];
?>

<div class="admin-content">
    <div class="page-header">
        <h1><i class="fas fa-tachometer-alt"></i> Performance Dashboard</h1>
        <p>Monitor system performance and optimize application speed</p>
        <div class="page-actions">
            <button class="btn btn-secondary" onclick="refreshMetrics()">
                <i class="fas fa-sync-alt"></i> Refresh Metrics
            </button>
            <button class="btn btn-primary" onclick="exportPerformanceReport()">
                <i class="fas fa-download"></i> Export Report
            </button>
        </div>
    </div>

    <!-- Performance Overview Cards -->
    <div class="performance-overview">
        <div class="metric-card">
            <div class="metric-icon">
                <i class="fas fa-server"></i>
            </div>
            <div class="metric-content">
                <h3>Server Response Time</h3>
                <div class="metric-value"><?= number_format($performance_metrics['response_time'] ?? 0, 2) ?>ms</div>
                <div class="metric-change <?= ($performance_metrics['response_time_change'] ?? 0) < 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-<?= ($performance_metrics['response_time_change'] ?? 0) < 0 ? 'arrow-down' : 'arrow-up' ?>"></i>
                    <?= abs($performance_metrics['response_time_change'] ?? 0) ?>% from last hour
                </div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon">
                <i class="fas fa-memory"></i>
            </div>
            <div class="metric-content">
                <h3>Memory Usage</h3>
                <div class="metric-value"><?= number_format($system_stats['memory_usage_percent'] ?? 0, 1) ?>%</div>
                <div class="metric-progress">
                    <div class="progress-bar" style="width: <?= $system_stats['memory_usage_percent'] ?? 0 ?>%"></div>
                </div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon">
                <i class="fas fa-hdd"></i>
            </div>
            <div class="metric-content">
                <h3>Disk Usage</h3>
                <div class="metric-value"><?= number_format($system_stats['disk_usage_percent'] ?? 0, 1) ?>%</div>
                <div class="metric-progress">
                    <div class="progress-bar" style="width: <?= $system_stats['disk_usage_percent'] ?? 0 ?>%"></div>
                </div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon">
                <i class="fas fa-database"></i>
            </div>
            <div class="metric-content">
                <h3>Database Queries</h3>
                <div class="metric-value"><?= number_format($database_stats['queries_per_second'] ?? 0) ?>/s</div>
                <div class="metric-change <?= ($database_stats['query_change'] ?? 0) < 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-<?= ($database_stats['query_change'] ?? 0) < 0 ? 'arrow-down' : 'arrow-up' ?>"></i>
                    <?= abs($database_stats['query_change'] ?? 0) ?>% from last hour
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Charts -->
    <div class="performance-charts">
        <div class="chart-container">
            <div class="chart-header">
                <h3>Response Time Trend</h3>
                <div class="chart-controls">
                    <select id="response-time-range" onchange="updateResponseTimeChart()">
                        <option value="1h">Last Hour</option>
                        <option value="24h">Last 24 Hours</option>
                        <option value="7d">Last 7 Days</option>
                        <option value="30d">Last 30 Days</option>
                    </select>
                </div>
            </div>
            <div class="chart-content">
                <canvas id="response-time-chart"></canvas>
            </div>
        </div>

        <div class="chart-container">
            <div class="chart-header">
                <h3>Resource Usage</h3>
                <div class="chart-controls">
                    <select id="resource-range" onchange="updateResourceChart()">
                        <option value="1h">Last Hour</option>
                        <option value="24h">Last 24 Hours</option>
                        <option value="7d">Last 7 Days</option>
                    </select>
                </div>
            </div>
            <div class="chart-content">
                <canvas id="resource-chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Detailed Performance Metrics -->
    <div class="performance-details">
        <div class="detail-section">
            <h3>System Performance</h3>
            <div class="detail-grid">
                <div class="detail-item">
                    <label>CPU Usage</label>
                    <span><?= number_format($system_stats['cpu_usage'] ?? 0, 1) ?>%</span>
                </div>
                <div class="detail-item">
                    <label>Load Average</label>
                    <span><?= $system_stats['load_average'] ?? 'N/A' ?></span>
                </div>
                <div class="detail-item">
                    <label>Uptime</label>
                    <span><?= $system_stats['uptime'] ?? 'N/A' ?></span>
                </div>
                <div class="detail-item">
                    <label>Processes</label>
                    <span><?= $system_stats['processes'] ?? 0 ?></span>
                </div>
            </div>
        </div>

        <div class="detail-section">
            <h3>Database Performance</h3>
            <div class="detail-grid">
                <div class="detail-item">
                    <label>Active Connections</label>
                    <span><?= $database_stats['active_connections'] ?? 0 ?></span>
                </div>
                <div class="detail-item">
                    <label>Slow Queries</label>
                    <span><?= $database_stats['slow_queries'] ?? 0 ?></span>
                </div>
                <div class="detail-item">
                    <label>Query Cache Hit Rate</label>
                    <span><?= number_format($database_stats['cache_hit_rate'] ?? 0, 1) ?>%</span>
                </div>
                <div class="detail-item">
                    <label>Avg Query Time</label>
                    <span><?= number_format($database_stats['avg_query_time'] ?? 0, 2) ?>ms</span>
                </div>
            </div>
        </div>

        <div class="detail-section">
            <h3>Cache Performance</h3>
            <div class="detail-grid">
                <div class="detail-item">
                    <label>Cache Hit Rate</label>
                    <span><?= number_format($cache_stats['hit_rate'] ?? 0, 1) ?>%</span>
                </div>
                <div class="detail-item">
                    <label>Memory Usage</label>
                    <span><?= number_format($cache_stats['memory_usage'] ?? 0, 1) ?>%</span>
                </div>
                <div class="detail-item">
                    <label>Total Operations</label>
                    <span><?= number_format($cache_stats['total_operations'] ?? 0) ?></span>
                </div>
                <div class="detail-item">
                    <label>Evictions</label>
                    <span><?= number_format($cache_stats['evictions'] ?? 0) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Recommendations -->
    <div class="performance-recommendations">
        <h3>Performance Recommendations</h3>
        <div class="recommendations-list">
            <?php if (!empty($performance_recommendations)): ?>
                <?php foreach ($performance_recommendations as $recommendation): ?>
                    <div class="recommendation-item priority-<?= htmlspecialchars($recommendation['priority'] ?? 'medium') ?>">
                        <div class="recommendation-icon">
                            <i class="fas fa-<?= htmlspecialchars($recommendation['icon'] ?? 'lightbulb') ?>"></i>
                        </div>
                        <div class="recommendation-content">
                            <h4><?= htmlspecialchars($recommendation['title']) ?></h4>
                            <p><?= htmlspecialchars($recommendation['description']) ?></p>
                            <?php if (!empty($recommendation['action'])): ?>
                                <button class="btn btn-sm btn-primary" onclick="applyRecommendation('<?= htmlspecialchars($recommendation['id']) ?>')">
                                    <?= htmlspecialchars($recommendation['action']) ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-recommendations">
                    <i class="fas fa-check-circle"></i>
                    <p>Your system is performing well! No immediate recommendations.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializePerformanceCharts();
});

function initializePerformanceCharts() {
    // Response Time Chart
    const responseTimeCtx = document.getElementById('response-time-chart').getContext('2d');
    const responseTimeChart = new Chart(responseTimeCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($performance_metrics['time_labels'] ?? []) ?>,
            datasets: [{
                label: 'Response Time (ms)',
                data: <?= json_encode($performance_metrics['response_times'] ?? []) ?>,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Resource Usage Chart
    const resourceCtx = document.getElementById('resource-chart').getContext('2d');
    const resourceChart = new Chart(resourceCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($system_stats['time_labels'] ?? []) ?>,
            datasets: [
                {
                    label: 'CPU Usage (%)',
                    data: <?= json_encode($system_stats['cpu_usage_data'] ?? []) ?>,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Memory Usage (%)',
                    data: <?= json_encode($system_stats['memory_usage_data'] ?? []) ?>,
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
}

function refreshMetrics() {
    fetch('<?= app_base_url('/admin/performance/refresh') ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-Token': '<?= csrf_token() ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Performance metrics refreshed', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Failed to refresh metrics', 'error');
        }
    });
}

function updateResponseTimeChart() {
    const range = document.getElementById('response-time-range').value;
    // Implementation to update chart based on selected range
    console.log('Updating response time chart for range:', range);
}

function updateResourceChart() {
    const range = document.getElementById('resource-range').value;
    // Implementation to update chart based on selected range
    console.log('Updating resource chart for range:', range);
}

function exportPerformanceReport() {
    window.open('<?= app_base_url('/admin/performance/export') ?>', '_blank');
}

function applyRecommendation(recommendationId) {
    showConfirmModal('Apply Recommendation', 'Are you sure you want to apply this recommendation?', () => {
        fetch('<?= app_base_url('/admin/performance/apply-recommendation') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?= csrf_token() ?>'
            },
            body: JSON.stringify({ recommendation_id: recommendationId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Recommendation applied successfully', 'success');
                setTimeout(() => location.reload(), 2000);
            } else {
                showNotification('Failed to apply recommendation', 'error');
            }
        });
    });
}
</script>

<style>
.performance-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.metric-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.metric-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.metric-content {
    flex: 1;
}

.metric-content h3 {
    margin: 0 0 5px 0;
    font-size: 14px;
    color: #6c757d;
    font-weight: 500;
}

.metric-value {
    font-size: 24px;
    font-weight: bold;
    color: #212529;
    margin-bottom: 5px;
}

.metric-change {
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.metric-change.positive {
    color: #28a745;
}

.metric-change.negative {
    color: #dc3545;
}

.metric-progress {
    width: 100%;
    height: 4px;
    background: #e9ecef;
    border-radius: 2px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: #007bff;
    transition: width 0.3s ease;
}

.performance-charts {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin: 30px 0;
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

.performance-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin: 30px 0;
}

.detail-section {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
}

.detail-section h3 {
    margin: 0 0 15px 0;
    font-size: 18px;
    color: #212529;
}

.detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f8f9fa;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-item label {
    font-weight: 500;
    color: #6c757d;
}

.detail-item span {
    font-weight: bold;
    color: #212529;
}

.performance-recommendations {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin: 30px 0;
}

.performance-recommendations h3 {
    margin: 0 0 20px 0;
    font-size: 18px;
    color: #212529;
}

.recommendations-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.recommendation-item {
    display: flex;
    gap: 15px;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.recommendation-item.priority-high {
    border-left-color: #dc3545;
    background: #f8d7da;
}

.recommendation-item.priority-medium {
    border-left-color: #ffc107;
    background: #fff3cd;
}

.recommendation-item.priority-low {
    border-left-color: #28a745;
    background: #d4edda;
}

.recommendation-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.recommendation-content {
    flex: 1;
}

.recommendation-content h4 {
    margin: 0 0 5px 0;
    font-size: 16px;
    color: #212529;
}

.recommendation-content p {
    margin: 0 0 10px 0;
    color: #6c757d;
}

.no-recommendations {
    text-align: center;
    padding: 40px;
    color: #28a745;
}

.no-recommendations i {
    font-size: 48px;
    margin-bottom: 15px;
    display: block;
}

@media (max-width: 768px) {
    .performance-charts {
        grid-template-columns: 1fr;
    }
    
    .performance-details {
        grid-template-columns: 1fr;
    }
    
    .detail-grid {
        grid-template-columns: 1fr;
    }
}
</style>
