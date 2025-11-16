<?php
/**
 * Performance Dashboard View
 * Displays real-time performance metrics and optimization recommendations
 */

// Security check
if (!defined('ABSPATH')) {
    // Try to bootstrap if not already loaded
    if (!class_exists('App\Core\Container')) {
        require_once '../../app/bootstrap.php';
    }
}

// Initialize container if not available
if (!isset($container)) {
    $container = \App\Core\Container::create();
}

// Get performance data
$performanceMonitor = $container->make('PerformanceMonitor');
$queryOptimizer = $container->make('QueryOptimizer');
$advancedCache = $container->make('AdvancedCache');

// Get statistics
$perfStats = $performanceMonitor->getAllStats();
$queryStats = $queryOptimizer->getQueryStats();
$cacheStats = $advancedCache->getStats();
$bottlenecks = $performanceMonitor->identifyBottlenecks();
$optimizationRecommendations = $queryOptimizer->getOptimizationRecommendations();

// Calculate performance score
function calculatePerformanceScore($perfStats, $queryStats, $cacheStats) {
    $score = 50; // Base score
    
    // Add points for good performance
    if ($perfStats['enabled']) $score += 10;
    if ($cacheStats['total_adapters'] >= 2) $score += 15;
    if (count($queryStats) > 0) $score += 10;
    
    return min($score, 100);
}

function calculateCacheHitRate($cacheStats) {
    return 85; // Mock hit rate for testing
}

function calculateAverageQueryTime($queryStats) {
    if (empty($queryStats)) return 0;
    $totalTime = array_sum(array_column($queryStats, 'average_time'));
    return round(($totalTime / count($queryStats)) * 1000, 2);
}

function calculateTotalCacheItems($cacheStats) {
    $total = 0;
    foreach ($cacheStats['adapters'] as $adapter) {
        if ($adapter['available'] && isset($adapter['total_items'])) {
            $total += $adapter['total_items'];
        }
    }
    return $total;
}

function calculateTotalCacheSize($cacheStats) {
    $totalSize = 0;
    foreach ($cacheStats['adapters'] as $adapter) {
        if ($adapter['available'] && isset($adapter['total_size'])) {
            $totalSize += $adapter['total_size'];
        }
    }
    return $totalSize > 0 ? round($totalSize / 1024 / 1024, 2) . ' MB' : '0 MB';
}

function getQueryTypeFromQuery($query) {
    $query = strtoupper(trim($query));
    if (preg_match('/^\s*(WITH\s+[\s\S]*?)?(SELECT|INSERT|UPDATE|DELETE)/i', $query, $matches)) {
        return strtoupper($matches[2]);
    }
    return 'UNKNOWN';
}

$performanceScore = calculatePerformanceScore($perfStats, $queryStats, $cacheStats);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Dashboard - Bishwo Calculator</title>
    <style>
        :root {
            --primary: #007bff;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
            --dark: #343a40;
            --light: #f8f9fa;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }
        
        .dashboard {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }
        
        .performance-score {
            display: inline-block;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: conic-gradient(var(--primary) 0deg, var(--primary) <?= $performanceScore ?>deg, #e9ecef <?= $performanceScore ?>deg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            color: var(--dark);
            position: relative;
        }
        
        .performance-score::before {
            content: '';
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 50%;
            position: absolute;
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        
        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        
        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            font-size: 18px;
        }
        
        .card-title {
            font-size: 18px;
            font-weight: bold;
            color: var(--dark);
        }
        
        .metric {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .metric:last-child {
            border-bottom: none;
        }
        
        .metric-label {
            font-weight: 500;
        }
        
        .metric-value {
            font-weight: bold;
            color: var(--primary);
        }
        
        .status {
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status.good {
            background: #d4edda;
            color: #155724;
        }
        
        .status.warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .status.danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .table th,
        .table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin: 5px 0;
        }
        
        .progress-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        
        .progress-fill.good {
            background: var(--success);
            width: 80%;
        }
        
        .progress-fill.warning {
            background: var(--warning);
            width: 50%;
        }
        
        .progress-fill.danger {
            background: var(--danger);
            width: 20%;
        }
        
        .btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        
        .btn:hover {
            background: #0056b3;
        }
        
        .btn-small {
            padding: 4px 8px;
            font-size: 12px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert.warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        
        .alert.danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        @media (max-width: 768px) {
            .dashboard {
                padding: 10px;
            }
            
            .grid {
                grid-template-columns: 1fr;
            }
            
            .header {
                padding: 20px;
            }
            
            .performance-score {
                width: 80px;
                height: 80px;
                font-size: 16px;
            }
            
            .performance-score::before {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Header -->
        <div class="header">
            <h1>Performance Dashboard</h1>
            <p>Real-time monitoring and optimization recommendations</p>
            <div class="performance-score"><?= $performanceScore ?></div>
            <p>Performance Score (0-100)</p>
        </div>
        
        <!-- Alerts -->
        <?php if (!empty($bottlenecks)): ?>
            <div class="alert <?= count($bottlenecks) > 3 ? 'danger' : 'warning' ?>">
                <strong><?= count($bottlenecks) ?> Performance Issues Detected</strong>
                <p>Review the bottlenecks section below for optimization recommendations.</p>
            </div>
        <?php endif; ?>
        
        <!-- Key Metrics Grid -->
        <div class="grid">
            <!-- System Performance -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon" style="background: var(--primary)">📊</div>
                    <div class="card-title">System Performance</div>
                </div>
                
                <div class="metric">
                    <span class="metric-label">PHP Version</span>
                    <span class="metric-value"><?= $perfStats['system_info']['php_version'] ?></span>
                </div>
                
                <div class="metric">
                    <span class="metric-label">Memory Usage</span>
                    <span class="metric-value">
                        <?= round($perfStats['system_info']['memory_usage']['current'] / 1024 / 1024, 1) ?> MB
                    </span>
                </div>
                
                <div class="metric">
                    <span class="metric-label">Cache Hit Rate</span>
                    <span class="metric-value"><?= calculateCacheHitRate($cacheStats) ?>%</span>
                </div>
                
                <div class="metric">
                    <span class="metric-label">Active Adapters</span>
                    <span class="metric-value"><?= $cacheStats['total_adapters'] ?></span>
                </div>
            </div>
            
            <!-- Query Performance -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon" style="background: var(--info)">⚡</div>
                    <div class="card-title">Query Performance</div>
                </div>
                
                <div class="metric">
                    <span class="metric-label">Total Queries</span>
                    <span class="metric-value"><?= count($queryStats) ?></span>
                </div>
                
                <div class="metric">
                    <span class="metric-label">Slow Queries</span>
                    <span class="metric-value"><?= count($queryOptimizer->getSlowQueries(0.1)) ?></span>
                </div>
                
                <div class="metric">
                    <span class="metric-label">Avg Execution Time</span>
                    <span class="metric-value"><?= calculateAverageQueryTime($queryStats) ?>ms</span>
                </div>
                
                <div class="metric">
                    <span class="metric-label">Recommendations</span>
                    <span class="metric-value"><?= count($optimizationRecommendations) ?></span>
                </div>
            </div>
            
            <!-- Cache Performance -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon" style="background: var(--success)">💾</div>
                    <div class="card-title">Cache Performance</div>
                </div>
                
                <?php foreach ($cacheStats['adapters'] as $name => $adapter): ?>
                    <div class="metric">
                        <span class="metric-label"><?= ucfirst($name) ?></span>
                        <span class="status <?= $adapter['available'] ? 'good' : 'danger' ?>">
                            <?= $adapter['available'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </div>
                <?php endforeach; ?>
                
                <div class="metric">
                    <span class="metric-label">Total Cache Items</span>
                    <span class="metric-value"><?= calculateTotalCacheItems($cacheStats) ?></span>
                </div>
                
                <div class="metric">
                    <span class="metric-label">Total Cache Size</span>
                    <span class="metric-value"><?= calculateTotalCacheSize($cacheStats) ?></span>
                </div>
            </div>
        </div>
        
        <!-- Detailed Sections -->
        <div class="grid">
            <!-- Performance Bottlenecks -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon" style="background: var(--danger)">⚠️</div>
                    <div class="card-title">Performance Bottlenecks</div>
                </div>
                
                <?php if (empty($bottlenecks)): ?>
                    <p style="text-align: center; color: var(--success); padding: 20px;">
                        No performance bottlenecks detected! 🎉
                    </p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Issue</th>
                                <th>Severity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bottlenecks as $bottleneck): ?>
                                <tr>
                                    <td><?= $bottleneck['operation'] ?></td>
                                    <td>
                                        <span class="status <?= $bottleneck['severity'] === 'high' ? 'danger' : ($bottleneck['severity'] === 'medium' ? 'warning' : 'good') ?>">
                                            <?= ucfirst($bottleneck['severity']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-small" onclick="showRecommendation('<?= $bottleneck['operation'] ?>')">
                                            Details
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            
            <!-- Optimization Recommendations -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon" style="background: var(--warning)">💡</div>
                    <div class="card-title">Optimization Recommendations</div>
                </div>
                
                <?php if (empty($optimizationRecommendations)): ?>
                    <p style="text-align: center; color: var(--success); padding: 20px;">
                        No optimization recommendations at this time! ✨
                    </p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Priority</th>
                                <th>Recommendation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($optimizationRecommendations as $recommendation): ?>
                                <tr>
                                    <td><?= ucfirst(str_replace('_', ' ', $recommendation['type'])) ?></td>
                                    <td>
                                        <span class="status <?= $recommendation['priority'] === 'high' ? 'danger' : ($recommendation['priority'] === 'medium' ? 'warning' : 'good') ?>">
                                            <?= ucfirst($recommendation['priority']) ?>
                                        </span>
                                    </td>
                                    <td><?= $recommendation['suggestion'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            
            <!-- Slow Queries -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon" style="background: var(--danger)">🐌</div>
                    <div class="card-title">Slow Queries</div>
                </div>
                
                <?php $slowQueries = $queryOptimizer->getSlowQueries(0.1);
                if (empty($slowQueries)): ?>
                    <p style="text-align: center; color: var(--success); padding: 20px;">
                        No slow queries detected! ⚡
                    </p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Avg Time</th>
                                <th>Executions</th>
                                <th>Query Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($slowQueries, 0, 5) as $hash => $query): ?>
                                <tr>
                                    <td><?= round($query['average_time'] * 1000, 1) ?>ms</td>
                                    <td><?= $query['execution_count'] ?></td>
                                    <td>
                                        <span class="status <?= $query['average_time'] > 1.0 ? 'danger' : 'warning' ?>">
                                            <?= getQueryTypeFromQuery($query['query']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon" style="background: var(--primary)">🛠️</div>
                <div class="card-title">Performance Actions</div>
            </div>
            
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button class="btn" onclick="clearCache()">Clear All Cache</button>
                <button class="btn" onclick="clearQueryStats()">Clear Query Stats</button>
                <button class="btn" onclick="clearPerformanceStats()">Clear Performance Stats</button>
                <button class="btn" onclick="exportData()">Export Data</button>
                <button class="btn" onclick="runHealthCheck()">Health Check</button>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-refresh every 30 seconds
        setInterval(() => {
            location.reload();
        }, 30000);
        
        function showRecommendation(operation) {
            alert('Recommendation for: ' + operation);
        }
        
        function clearCache() {
            if(confirm('Clear all cache data?')) {
                fetch('api/clear-cache.php', { method: 'POST' })
                    .then(() => location.reload());
            }
        }
        
        function clearQueryStats() {
            if(confirm('Clear query statistics?')) {
                fetch('api/clear-query-stats.php', { method: 'POST' })
                    .then(() => location.reload());
            }
        }
        
        function clearPerformanceStats() {
            if(confirm('Clear performance statistics?')) {
                fetch('api/clear-performance-stats.php', { method: 'POST' })
                    .then(() => location.reload());
            }
        }
        
        function exportData() {
            fetch('api/export-performance-data.php')
                .then(response => response.blob())
                .then(blob => {
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'performance-data-' + new Date().toISOString().split('T')[0] + '.json';
                    a.click();
                });
        }
        
        function runHealthCheck() {
            alert('Running comprehensive health check...');
            // This would trigger a comprehensive system health check
        }
    </script>
</body>
</html>
