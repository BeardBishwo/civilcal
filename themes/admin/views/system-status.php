<?php
$page_title = $page_title ?? 'System Status';
$system_status = $system_status ?? [];
$health_checks = $health_checks ?? [];
$performance_metrics = $performance_metrics ?? [];
$recent_logs = $recent_logs ?? [];
require_once __DIR__ . '/../layouts/admin.php';
?>

<div class="admin-content">
    <div class="page-header">
        <h1><i class="fas fa-heartbeat"></i> System Status</h1>
        <p>Monitor system health, performance, and operational status</p>
        <div class="page-actions">
            <button class="btn btn-outline-info" onclick="refreshStatus()">
                <i class="fas fa-sync"></i> Refresh Status
            </button>
            <button class="btn btn-outline-primary" onclick="runDiagnostics()">
                <i class="fas fa-stethoscope"></i> Run Diagnostics
            </button>
            <button class="btn btn-outline-warning" onclick="exportStatusReport()">
                <i class="fas fa-download"></i> Export Report
            </button>
        </div>
    </div>

    <!-- System Overview -->
    <div class="status-overview">
        <div class="status-card status-<?= $system_status['overall_status'] ?? 'unknown' ?>">
            <div class="status-icon">
                <i class="fas fa-<?= getSystemStatusIcon($system_status['overall_status'] ?? 'unknown') ?>"></i>
            </div>
            <div class="status-content">
                <h3>System Status</h3>
                <div class="status-text"><?= ucfirst($system_status['overall_status'] ?? 'Unknown') ?></div>
                <small>Last checked: <?= date('Y-m-d H:i:s') ?></small>
            </div>
        </div>

        <div class="status-card">
            <div class="status-icon">
                <i class="fas fa-server"></i>
            </div>
            <div class="status-content">
                <h3>Server Load</h3>
                <div class="status-text"><?= $performance_metrics['server_load'] ?? 'N/A' ?></div>
                <small>Average: <?= $performance_metrics['avg_load'] ?? 'N/A' ?></small>
            </div>
        </div>

        <div class="status-card">
            <div class="status-icon">
                <i class="fas fa-memory"></i>
            </div>
            <div class="status-content">
                <h3>Memory Usage</h3>
                <div class="status-text"><?= $performance_metrics['memory_usage'] ?? 'N/A' ?></div>
                <small>Available: <?= $performance_metrics['memory_available'] ?? 'N/A' ?></small>
            </div>
        </div>

        <div class="status-card">
            <div class="status-icon">
                <i class="fas fa-database"></i>
            </div>
            <div class="status-content">
                <h3>Database</h3>
                <div class="status-text status-<?= $health_checks['database']['status'] ?? 'unknown' ?>">
                    <?= ucfirst($health_checks['database']['status'] ?? 'Unknown') ?>
                </div>
                <small>Response: <?= $health_checks['database']['response_time'] ?? 'N/A' ?>ms</small>
            </div>
        </div>
    </div>

    <!-- Health Checks -->
    <div class="status-section">
        <h3>System Health Checks</h3>
        <div class="health-checks-grid">
            <?php foreach ($health_checks as $check_name => $check): ?>
            <div class="health-check-item status-<?= $check['status'] ?? 'unknown' ?>">
                <div class="check-header">
                    <div class="check-icon">
                        <i class="fas fa-<?= getHealthCheckIcon($check['status'] ?? 'unknown') ?>"></i>
                    </div>
                    <div class="check-info">
                        <h4><?= ucfirst(str_replace('_', ' ', $check_name)) ?></h4>
                        <span class="check-status"><?= ucfirst($check['status'] ?? 'Unknown') ?></span>
                    </div>
                    <?php if (isset($check['response_time'])): ?>
                    <div class="check-metrics">
                        <span class="response-time"><?= $check['response_time'] ?>ms</span>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="check-details">
                    <p><?= $check['message'] ?? 'No details available' ?></p>
                    <?php if (!empty($check['additional_info'])): ?>
                    <ul>
                        <?php foreach ($check['additional_info'] as $info): ?>
                        <li><?= $info ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="status-section">
        <h3>Performance Metrics</h3>
        <div class="metrics-container">
            <div class="metric-chart">
                <h4>Server Load (Last 24 Hours)</h4>
                <canvas id="serverLoadChart"></canvas>
            </div>
            <div class="metric-chart">
                <h4>Memory Usage (Last 24 Hours)</h4>
                <canvas id="memoryUsageChart"></canvas>
            </div>
            <div class="metric-chart">
                <h4>Response Time (Last 24 Hours)</h4>
                <canvas id="responseTimeChart"></canvas>
            </div>
            <div class="metric-chart">
                <h4>Database Queries (Last 24 Hours)</h4>
                <canvas id="dbQueriesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="status-section">
        <h3>Recent System Activity</h3>
        <div class="activity-container">
            <div class="activity-tabs">
                <button class="tab-btn active" onclick="showActivityTab('logs')">System Logs</button>
                <button class="tab-btn" onclick="showActivityTab('errors')">Recent Errors</button>
                <button class="tab-btn" onclick="showActivityTab('performance')">Performance Events</button>
            </div>
            
            <div class="activity-content">
                <div id="logs-tab" class="tab-content active">
                    <?php if (!empty($recent_logs)): ?>
                    <div class="log-list">
                        <?php foreach (array_slice($recent_logs, 0, 10) as $log): ?>
                        <div class="log-item level-<?= $log['level'] ?? 'info' ?>">
                            <div class="log-header">
                                <span class="log-time"><?= $log['timestamp'] ?? 'Unknown time' ?></span>
                                <span class="log-level"><?= strtoupper($log['level'] ?? 'INFO') ?></span>
                            </div>
                            <div class="log-message"><?= htmlspecialchars($log['message'] ?? 'No message') ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="no-data">No recent logs available</div>
                    <?php endif; ?>
                </div>
                
                <div id="errors-tab" class="tab-content">
                    <div class="no-data">No recent errors found</div>
                </div>
                
                <div id="performance-tab" class="tab-content">
                    <div class="no-data">No performance events recorded</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="status-section">
        <h3>Quick Actions</h3>
        <div class="quick-actions-grid">
            <button class="action-btn" onclick="clearSystemCache()">
                <i class="fas fa-broom"></i>
                <span>Clear System Cache</span>
            </button>
            <button class="action-btn" onclick="restartServices()">
                <i class="fas fa-redo"></i>
                <span>Restart Services</span>
            </button>
            <button class="action-btn" onclick="optimizeDatabase()">
                <i class="fas fa-database"></i>
                <span>Optimize Database</span>
            </button>
            <button class="action-btn" onclick="checkUpdates()">
                <i class="fas fa-download"></i>
                <span>Check for Updates</span>
            </button>
            <button class="action-btn" onclick="runBackup()">
                <i class="fas fa-save"></i>
                <span>Run Backup</span>
            </button>
            <button class="action-btn" onclick="viewDetailedLogs()">
                <i class="fas fa-file-alt"></i>
                <span>View Detailed Logs</span>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeSystemStatus();
    initializeCharts();
});

function initializeSystemStatus() {
    // Auto-refresh every 30 seconds
    setInterval(refreshStatus, 30000);
}

function initializeCharts() {
    // Server Load Chart
    const serverLoadCtx = document.getElementById('serverLoadChart').getContext('2d');
    new Chart(serverLoadCtx, {
        type: 'line',
        data: {
            labels: generateTimeLabels(24),
            datasets: [{
                label: 'Server Load',
                data: generateRandomData(24, 0.2, 0.8),
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
                    beginAtZero: true,
                    max: 1
                }
            }
        }
    });

    // Memory Usage Chart
    const memoryCtx = document.getElementById('memoryUsageChart').getContext('2d');
    new Chart(memoryCtx, {
        type: 'line',
        data: {
            labels: generateTimeLabels(24),
            datasets: [{
                label: 'Memory Usage (%)',
                data: generateRandomData(24, 40, 80),
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4
            }]
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

    // Response Time Chart
    const responseCtx = document.getElementById('responseTimeChart').getContext('2d');
    new Chart(responseCtx, {
        type: 'line',
        data: {
            labels: generateTimeLabels(24),
            datasets: [{
                label: 'Response Time (ms)',
                data: generateRandomData(24, 50, 300),
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
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

    // Database Queries Chart
    const dbCtx = document.getElementById('dbQueriesChart').getContext('2d');
    new Chart(dbCtx, {
        type: 'bar',
        data: {
            labels: generateTimeLabels(24),
            datasets: [{
                label: 'Database Queries',
                data: generateRandomData(24, 100, 500),
                backgroundColor: '#6f42c1',
                borderColor: '#6f42c1',
                borderWidth: 1
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
}

function generateTimeLabels(hours) {
    const labels = [];
    const now = new Date();
    for (let i = hours - 1; i >= 0; i--) {
        const time = new Date(now - i * 60 * 60 * 1000);
        labels.push(time.getHours() + ':00');
    }
    return labels;
}

function generateRandomData(count, min, max) {
    const data = [];
    for (let i = 0; i < count; i++) {
        data.push(Math.random() * (max - min) + min);
    }
    return data;
}

function refreshStatus() {
    showNotification('Refreshing system status...', 'info');
    
    fetch('<?= app_base_url('/admin/system-status/refresh') ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-Token': '<?= $this->csrfToken() ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('System status updated', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showNotification('Failed to refresh status', 'error');
        }
    })
    .catch(error => {
        showNotification('Error refreshing status', 'error');
    });
}

function runDiagnostics() {
    showNotification('Running system diagnostics...', 'info');
    
    fetch('<?= app_base_url('/admin/diagnostics/run') ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-Token': '<?= $this->csrfToken() ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Diagnostics completed', 'success');
            window.open('<?= app_base_url('/admin/diagnostics/results') ?>', '_blank');
        } else {
            showNotification('Diagnostics failed', 'error');
        }
    });
}

function exportStatusReport() {
    window.open('<?= app_base_url('/admin/system-status/export') ?>', '_blank');
}

function showActivityTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.add('active');
    
    // Add active class to clicked button
    event.target.classList.add('active');
}

function clearSystemCache() {
    if (confirm('Are you sure you want to clear the system cache?')) {
        fetch('<?= app_base_url('/admin/cache/clear') ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': '<?= $this->csrfToken() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('System cache cleared successfully', 'success');
            } else {
                showNotification('Failed to clear cache', 'error');
            }
        });
    }
}

function restartServices() {
    if (confirm('Are you sure you want to restart system services? This may temporarily interrupt service.')) {
        showNotification('Restarting services...', 'info');
        // Implementation would depend on your specific services
    }
}

function optimizeDatabase() {
    if (confirm('Are you sure you want to optimize the database? This may take some time.')) {
        fetch('<?= app_base_url('/admin/database/optimize') ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': '<?= $this->csrfToken() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Database optimization completed', 'success');
            } else {
                showNotification('Database optimization failed', 'error');
            }
        });
    }
}

function checkUpdates() {
    showNotification('Checking for updates...', 'info');
    // Implementation would check for system/application updates
}

function runBackup() {
    if (confirm('Are you sure you want to run a backup now?')) {
        fetch('<?= app_base_url('/admin/backup/run') ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': '<?= $this->csrfToken() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Backup started successfully', 'success');
            } else {
                showNotification('Failed to start backup', 'error');
            }
        });
    }
}

function viewDetailedLogs() {
    window.open('<?= app_base_url('/admin/logs/view') ?>', '_blank');
}

<?php
// Helper functions for view
function getSystemStatusIcon($status) {
    switch ($status) {
        case 'healthy': return 'check-circle';
        case 'warning': return 'exclamation-triangle';
        case 'critical': return 'times-circle';
        default: return 'question-circle';
    }
}

function getHealthCheckIcon($status) {
    switch ($status) {
        case 'healthy': return 'check';
        case 'warning': return 'exclamation';
        case 'critical': return 'times';
        default: return 'question';
    }
}
?>
</script>

<style>
.status-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.status-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.status-card.status-healthy {
    border-left: 4px solid #28a745;
}

.status-card.status-warning {
    border-left: 4px solid #ffc107;
}

.status-card.status-critical {
    border-left: 4px solid #dc3545;
}

.status-icon {
    font-size: 24px;
    color: #007bff;
}

.status-card.status-healthy .status-icon {
    color: #28a745;
}

.status-card.status-warning .status-icon {
    color: #ffc107;
}

.status-card.status-critical .status-icon {
    color: #dc3545;
}

.status-content h3 {
    margin: 0 0 5px 0;
    font-size: 14px;
    color: #6c757d;
    text-transform: uppercase;
}

.status-text {
    font-size: 18px;
    font-weight: 600;
    color: #212529;
}

.status-section {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.status-section h3 {
    margin: 0 0 20px 0;
    font-size: 18px;
    color: #212529;
}

.health-checks-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 15px;
}

.health-check-item {
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 15px;
}

.health-check-item.status-healthy {
    border-left: 4px solid #28a745;
}

.health-check-item.status-warning {
    border-left: 4px solid #ffc107;
}

.health-check-item.status-critical {
    border-left: 4px solid #dc3545;
}

.check-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.check-icon {
    font-size: 20px;
}

.health-check-item.status-healthy .check-icon {
    color: #28a745;
}

.health-check-item.status-warning .check-icon {
    color: #ffc107;
}

.health-check-item.status-critical .check-icon {
    color: #dc3545;
}

.check-info {
    flex: 1;
}

.check-info h4 {
    margin: 0 0 3px 0;
    font-size: 16px;
    color: #212529;
}

.check-status {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.check-metrics {
    text-align: right;
}

.response-time {
    font-size: 14px;
    font-weight: 600;
    color: #6c757d;
}

.check-details {
    font-size: 14px;
    color: #6c757d;
}

.check-details ul {
    margin: 5px 0 0 0;
    padding-left: 20px;
}

.metrics-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
}

.metric-chart {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 15px;
    height: 300px;
}

.metric-chart h4 {
    margin: 0 0 15px 0;
    font-size: 16px;
    color: #212529;
}

.activity-tabs {
    display: flex;
    border-bottom: 1px solid #e9ecef;
    margin-bottom: 20px;
}

.tab-btn {
    background: none;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    font-size: 14px;
    color: #6c757d;
    border-bottom: 2px solid transparent;
}

.tab-btn.active {
    color: #007bff;
    border-bottom-color: #007bff;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.log-list {
    max-height: 400px;
    overflow-y: auto;
}

.log-item {
    border-bottom: 1px solid #f8f9fa;
    padding: 10px 0;
}

.log-item:last-child {
    border-bottom: none;
}

.log-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
}

.log-time {
    font-size: 12px;
    color: #6c757d;
}

.log-level {
    font-size: 11px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 3px;
}

.log-item.level-error .log-level {
    background: #dc3545;
    color: white;
}

.log-item.level-warning .log-level {
    background: #ffc107;
    color: #212529;
}

.log-item.level-info .log-level {
    background: #17a2b8;
    color: white;
}

.log-message {
    font-size: 14px;
    color: #212529;
}

.no-data {
    text-align: center;
    color: #6c757d;
    font-style: italic;
    padding: 40px;
}

.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.action-btn {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 15px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.action-btn:hover {
    background: #f8f9fa;
    border-color: #007bff;
}

.action-btn i {
    font-size: 24px;
    color: #007bff;
}

.action-btn span {
    font-size: 14px;
    color: #212529;
    text-align: center;
}

@media (max-width: 768px) {
    .status-overview {
        grid-template-columns: 1fr;
    }
    
    .health-checks-grid {
        grid-template-columns: 1fr;
    }
    
    .metrics-container {
        grid-template-columns: 1fr;
    }
    
    .quick-actions-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>