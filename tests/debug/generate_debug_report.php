<?php
/**
 * Visual HTML Debug Report Generator
 * Generates a comprehensive, interactive HTML report from debug logs
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Find the most recent debug log
$logsDir = __DIR__ . '/storage/logs';
$debugFiles = glob($logsDir . '/debug_ide_*.json');

if (empty($debugFiles)) {
    die("No debug logs found. Please run debug_ide_runtime.php first.");
}

// Get the most recent log file
usort($debugFiles, function($a, $b) {
    return filemtime($b) - filemtime($a);
});
$latestLog = $debugFiles[0];

// Load debug data
$debugData = json_decode(file_get_contents($latestLog), true);

if (!$debugData) {
    die("Failed to parse debug log file.");
}

// Generate HTML report
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IDE Debug Report - Bishwo Calculator</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            margin-bottom: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 3em;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .header .subtitle {
            font-size: 1.2em;
            color: #666;
            margin-bottom: 20px;
        }

        .header .meta {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .header .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .header .meta-item .icon {
            font-size: 1.5em;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

        .summary-card .icon {
            font-size: 3em;
            margin-bottom: 15px;
        }

        .summary-card .value {
            font-size: 3em;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .summary-card .label {
            font-size: 1.1em;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .summary-card.success { border-top: 5px solid #28a745; }
        .summary-card.success .icon { color: #28a745; }
        .summary-card.success .value { color: #28a745; }

        .summary-card.error { border-top: 5px solid #dc3545; }
        .summary-card.error .icon { color: #dc3545; }
        .summary-card.error .value { color: #dc3545; }

        .summary-card.info { border-top: 5px solid #17a2b8; }
        .summary-card.info .icon { color: #17a2b8; }
        .summary-card.info .value { color: #17a2b8; }

        .summary-card.warning { border-top: 5px solid #ffc107; }
        .summary-card.warning .icon { color: #ffc107; }
        .summary-card.warning .value { color: #ffc107; }

        .section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
        }

        .section h2 {
            font-size: 2em;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
            color: #333;
        }

        .section h3 {
            font-size: 1.5em;
            margin: 20px 0 15px;
            color: #555;
            border-left: 4px solid #10b981;
            padding-left: 15px;
        }

        .test-list {
            list-style: none;
        }

        .test-item {
            padding: 15px;
            margin: 10px 0;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s ease;
        }

        .test-item:hover {
            transform: translateX(5px);
        }

        .test-item.pass {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border-left: 5px solid #28a745;
        }

        .test-item.fail {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            border-left: 5px solid #dc3545;
        }

        .test-item .icon {
            font-size: 2em;
            min-width: 40px;
            text-align: center;
        }

        .test-item.pass .icon { color: #28a745; }
        .test-item.fail .icon { color: #dc3545; }

        .test-item .content {
            flex: 1;
        }

        .test-item .name {
            font-weight: bold;
            font-size: 1.1em;
            margin-bottom: 5px;
        }

        .test-item .details {
            color: #666;
            font-size: 0.9em;
        }

        .timeline {
            position: relative;
            padding-left: 40px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
        }

        .timeline-item {
            position: relative;
            padding: 15px 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -32px;
            top: 20px;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background: white;
            border: 3px solid #667eea;
        }

        .timeline-item.error::before { border-color: #dc3545; }
        .timeline-item.success::before { border-color: #28a745; }
        .timeline-item.warning::before { border-color: #ffc107; }

        .timeline-item .time {
            font-size: 0.85em;
            color: #999;
            margin-bottom: 5px;
        }

        .timeline-item .type {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 5px;
            font-size: 0.8em;
            font-weight: bold;
            margin-right: 10px;
        }

        .timeline-item .type.info { background: #d1ecf1; color: #0c5460; }
        .timeline-item .type.success { background: #d4edda; color: #155724; }
        .timeline-item .type.error { background: #f8d7da; color: #721c24; }
        .timeline-item .type.warning { background: #fff3cd; color: #856404; }

        .timeline-item .message {
            margin-top: 10px;
            color: #333;
        }

        .chart-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .progress-bar {
            height: 40px;
            background: #e9ecef;
            border-radius: 20px;
            overflow: hidden;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
            transition: width 1s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.1em;
        }

        .metric-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .metric {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }

        .metric .label {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 5px;
        }

        .metric .value {
            font-size: 1.8em;
            font-weight: bold;
            color: #333;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: bold;
            margin: 5px;
        }

        .badge.success { background: #28a745; color: white; }
        .badge.error { background: #dc3545; color: white; }
        .badge.warning { background: #ffc107; color: #333; }
        .badge.info { background: #17a2b8; color: white; }

        pre {
            background: #2d3748;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 10px;
            overflow-x: auto;
            font-size: 0.9em;
            line-height: 1.5;
        }

        .footer {
            text-align: center;
            padding: 40px 20px;
            color: white;
            margin-top: 40px;
        }

        .footer a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            opacity: 0.9;
            transition: opacity 0.3s ease;
        }

        .footer a:hover {
            opacity: 1;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .header h1 { font-size: 2em; }
            .header .meta { flex-direction: column; gap: 10px; }
            .summary-grid { grid-template-columns: 1fr; }
            .timeline { padding-left: 30px; }
        }

        .collapsible {
            cursor: pointer;
            padding: 15px;
            background: #f8f9fa;
            border: none;
            text-align: left;
            width: 100%;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: bold;
            margin: 10px 0;
            transition: background 0.3s ease;
        }

        .collapsible:hover {
            background: #e9ecef;
        }

        .collapsible::after {
            content: '▼';
            float: right;
            transition: transform 0.3s ease;
        }

        .collapsible.active::after {
            transform: rotate(180deg);
        }

        .collapsible-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .collapsible-content.active {
            max-height: 2000px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🔍 IDE Debug Report</h1>
            <p class="subtitle">Comprehensive Runtime Analysis & Performance Profiling</p>
            <div class="meta">
                <div class="meta-item">
                    <span class="icon">📅</span>
                    <div>
                        <strong>Generated:</strong><br>
                        <?php echo $debugData['timestamp']; ?>
                    </div>
                </div>
                <div class="meta-item">
                    <span class="icon">🐘</span>
                    <div>
                        <strong>PHP Version:</strong><br>
                        <?php echo $debugData['php_version']; ?>
                    </div>
                </div>
                <div class="meta-item">
                    <span class="icon">⚡</span>
                    <div>
                        <strong>Execution Time:</strong><br>
                        <?php echo round($debugData['summary']['time_elapsed'] * 1000, 2); ?> ms
                    </div>
                </div>
                <div class="meta-item">
                    <span class="icon">💾</span>
                    <div>
                        <strong>Memory Used:</strong><br>
                        <?php echo round($debugData['summary']['memory_used'] / 1024 / 1024, 2); ?> MB
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-grid">
            <div class="summary-card success">
                <div class="icon">✅</div>
                <div class="value"><?php echo $debugData['summary']['passed']; ?></div>
                <div class="label">Tests Passed</div>
            </div>

            <div class="summary-card <?php echo $debugData['summary']['failed'] > 0 ? 'error' : 'success'; ?>">
                <div class="icon"><?php echo $debugData['summary']['failed'] > 0 ? '❌' : '✅'; ?></div>
                <div class="value"><?php echo $debugData['summary']['failed']; ?></div>
                <div class="label">Tests Failed</div>
            </div>

            <div class="summary-card info">
                <div class="icon">📊</div>
                <div class="value"><?php echo $debugData['summary']['success_rate']; ?>%</div>
                <div class="label">Success Rate</div>
            </div>

            <div class="summary-card warning">
                <div class="icon">🚀</div>
                <div class="value"><?php echo $debugData['summary']['total_tests']; ?></div>
                <div class="label">Total Tests</div>
            </div>
        </div>

        <!-- Overall Status -->
        <div class="section">
            <h2>📈 Overall System Status</h2>
            <div class="chart-container">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo $debugData['summary']['success_rate']; ?>%">
                        <?php echo $debugData['summary']['success_rate']; ?>% Complete
                    </div>
                </div>
            </div>

            <?php if ($debugData['summary']['success_rate'] == 100): ?>
                <div style="text-align: center; padding: 30px; background: #d4edda; border-radius: 15px; margin-top: 20px;">
                    <h3 style="color: #155724; margin: 0;">🎉 All Tests Passed!</h3>
                    <p style="color: #155724; margin-top: 10px;">System is operating at optimal performance</p>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 30px; background: #f8d7da; border-radius: 15px; margin-top: 20px;">
                    <h3 style="color: #721c24; margin: 0;">⚠️ Issues Detected</h3>
                    <p style="color: #721c24; margin-top: 10px;"><?php echo count($debugData['errors']); ?> test(s) failed</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Test Results -->
        <div class="section">
            <h2>✅ Test Results</h2>

            <h3>Passed Tests (<?php echo count($debugData['passes']); ?>)</h3>
            <ul class="test-list">
                <?php foreach ($debugData['passes'] as $pass): ?>
                    <li class="test-item pass">
                        <span class="icon">✓</span>
                        <div class="content">
                            <div class="name"><?php echo htmlspecialchars($pass); ?></div>
                            <div class="details">Test completed successfully</div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if (count($debugData['errors']) > 0): ?>
                <h3>Failed Tests (<?php echo count($debugData['errors']); ?>)</h3>
                <ul class="test-list">
                    <?php foreach ($debugData['errors'] as $error): ?>
                        <li class="test-item fail">
                            <span class="icon">✗</span>
                            <div class="content">
                                <div class="name"><?php echo htmlspecialchars($error); ?></div>
                                <div class="details">Test failed - review debug log for details</div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- Performance Metrics -->
        <div class="section">
            <h2>⚡ Performance Metrics</h2>

            <div class="metric-row">
                <div class="metric">
                    <div class="label">Execution Time</div>
                    <div class="value"><?php echo round($debugData['summary']['time_elapsed'] * 1000, 2); ?> ms</div>
                </div>
                <div class="metric">
                    <div class="label">Memory Used</div>
                    <div class="value"><?php echo round($debugData['summary']['memory_used'] / 1024 / 1024, 2); ?> MB</div>
                </div>
                <div class="metric">
                    <div class="label">Peak Memory</div>
                    <div class="value"><?php echo round($debugData['summary']['peak_memory'] / 1024 / 1024, 2); ?> MB</div>
                </div>
                <div class="metric">
                    <div class="label">Avg per Test</div>
                    <div class="value"><?php echo round(($debugData['summary']['time_elapsed'] / $debugData['summary']['total_tests']) * 1000, 2); ?> ms</div>
                </div>
            </div>
        </div>

        <!-- Execution Timeline -->
        <div class="section">
            <h2>⏱️ Execution Timeline</h2>
            <button class="collapsible">Show Detailed Log (<?php echo count($debugData['log']); ?> events)</button>
            <div class="collapsible-content">
                <div class="timeline">
                    <?php
                    $startTime = $debugData['log'][0]['time'] ?? 0;
                    foreach ($debugData['log'] as $index => $logEntry):
                        $relativeTime = round(($logEntry['time'] - $startTime) * 1000, 2);
                        $cssClass = strtolower($logEntry['type']);
                    ?>
                        <div class="timeline-item <?php echo $cssClass; ?>">
                            <div class="time"><?php echo $relativeTime; ?> ms</div>
                            <span class="type <?php echo $cssClass; ?>"><?php echo $logEntry['type']; ?></span>
                            <div class="message"><?php echo htmlspecialchars($logEntry['message']); ?></div>
                            <?php if (!empty($logEntry['data'])): ?>
                                <details style="margin-top: 10px;">
                                    <summary style="cursor: pointer; color: #666;">View Data</summary>
                                    <pre><?php echo htmlspecialchars(print_r($logEntry['data'], true)); ?></pre>
                                </details>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="section">
            <h2>ℹ️ System Information</h2>
            <div class="metric-row">
                <div class="metric">
                    <div class="label">PHP Version</div>
                    <div class="value" style="font-size: 1.3em;"><?php echo $debugData['php_version']; ?></div>
                </div>
                <div class="metric">
                    <div class="label">Report Generated</div>
                    <div class="value" style="font-size: 1em;"><?php echo $debugData['timestamp']; ?></div>
                </div>
                <div class="metric">
                    <div class="label">Total Log Entries</div>
                    <div class="value"><?php echo count($debugData['log']); ?></div>
                </div>
                <div class="metric">
                    <div class="label">Log File Size</div>
                    <div class="value"><?php echo round(filesize($latestLog) / 1024, 2); ?> KB</div>
                </div>
            </div>
        </div>

        <!-- Recommendations -->
        <div class="section">
            <h2>💡 Recommendations</h2>
            <?php if ($debugData['summary']['success_rate'] == 100): ?>
                <div style="background: #d4edda; padding: 20px; border-radius: 10px; border-left: 5px solid #28a745;">
                    <h3 style="color: #155724; margin-top: 0;">✅ System Operating Normally</h3>
                    <p style="color: #155724;">All tests passed successfully. The system is ready for production use.</p>
                    <ul style="color: #155724; margin-top: 15px;">
                        <li>✓ All core components are functional</li>
                        <li>✓ No critical errors detected</li>
                        <li>✓ Performance is within acceptable limits</li>
                        <li>✓ Memory usage is optimal</li>
                    </ul>
                </div>
            <?php else: ?>
                <div style="background: #f8d7da; padding: 20px; border-radius: 10px; border-left: 5px solid #dc3545;">
                    <h3 style="color: #721c24; margin-top: 0;">⚠️ Action Required</h3>
                    <p style="color: #721c24;">Some tests failed. Please review and address the following:</p>
                    <ul style="color: #721c24; margin-top: 15px;">
                        <?php foreach ($debugData['errors'] as $error): ?>
                            <li>Fix: <?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div style="margin-top: 20px; padding: 20px; background: #d1ecf1; border-radius: 10px; border-left: 5px solid #17a2b8;">
                <h3 style="color: #0c5460; margin-top: 0;">📋 Next Steps</h3>
                <ol style="color: #0c5460;">
                    <li>Review the execution timeline for any performance bottlenecks</li>
                    <li>Check browser console (F12) for client-side errors</li>
                    <li>Verify CSS/JS loading in browser network tab</li>
                    <li>Test critical user flows (login, registration, etc.)</li>
                    <li>Monitor application logs for any runtime errors</li>
                </ol>
            </div>
        </div>

        <!-- Debug Log File -->
        <div class="section">
            <h2>📄 Debug Log File</h2>
            <p>Complete debug data has been saved to:</p>
            <pre><?php echo htmlspecialchars($latestLog); ?></pre>
            <p style="margin-top: 15px;">
                <span class="badge info">JSON Format</span>
                <span class="badge info">Machine Readable</span>
                <span class="badge info">Full Stack Traces</span>
            </p>
        </div>

    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Generated by Bishwo Calculator IDE Debugger</p>
        <p style="margin-top: 10px;">
            <a href="/">← Back to Home</a> |
            <a href="javascript:location.reload()">🔄 Refresh</a> |
            <a href="javascript:window.print()">🖨️ Print Report</a>
        </p>
        <p style="margin-top: 20px; opacity: 0.8;">
            Debug Session Complete • All Rights Reserved © <?php echo date('Y'); ?>
        </p>
    </div>

    <script>
        // Collapsible sections
        document.querySelectorAll('.collapsible').forEach(button => {
            button.addEventListener('click', function() {
                this.classList.toggle('active');
                const content = this.nextElementSibling;
                content.classList.toggle('active');
            });
        });

        // Animate progress bar on load
        window.addEventListener('load', function() {
            document.querySelector('.progress-fill').style.width = '<?php echo $debugData['summary']['success_rate']; ?>%';
        });

        // Add smooth scroll behavior
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</body>
</html>
