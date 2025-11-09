<?php
/**
 * Log Viewer for Bishwo Calculator Debug System
 * Development-only tool for viewing system logs
 */

require_once 'debug-config.php';

if (!DEBUG_MODE) {
    die('Log viewer only available in debug mode');
}

$log_type = $_GET['type'] ?? 'debug';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;

// Get recent logs
$logs = get_recent_logs($limit, $log_type);

// Clear logs if requested
if (isset($_GET['clear']) && $_GET['clear'] === 'true') {
    clear_logs($log_type);
    $logs = get_recent_logs($limit, $log_type);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Bishwo Calculator - Log Viewer</title>
    <style>
        body { 
            font-family: 'Courier New', monospace; 
            background: #1e1e1e; 
            color: #d4d4d4; 
            margin: 0; 
            padding: 20px; 
        }
        .header {
            background: #2d2d30; 
            padding: 20px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
        }
        .log-controls {
            background: #252526; 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
        }
        .log-controls a {
            background: #0078d4; 
            color: white; 
            padding: 8px 16px; 
            text-decoration: none; 
            border-radius: 4px; 
            margin-right: 10px;
        }
        .log-controls a:hover {
            background: #106ebe;
        }
        .log-container {
            background: #1e1e1e; 
            border: 1px solid #3c3c3c; 
            border-radius: 8px; 
            max-height: 70vh; 
            overflow-y: auto;
        }
        .log-entry {
            padding: 8px 12px;
            border-bottom: 1px solid #2d2d30;
            font-size: 13px;
            line-height: 1.4;
        }
        .log-entry:hover {
            background: #2d2d30;
        }
        .log-timestamp {
            color: #569cd6;
        }
        .log-level {
            font-weight: bold;
        }
        .log-level.ERROR { color: #f48771; }
        .log-level.WARNING { color: #dcdcaa; }
        .log-level.INFO { color: #4ec9b0; }
        .log-level.DEBUG { color: #9cdcfe; }
        .log-level.SYSTEM { color: #ce9178; }
        .error-count {
            color: #f48771;
            font-weight: bold;
        }
        .refresh-btn {
            background: #107c10;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-left: 10px;
        }
        .refresh-btn:hover {
            background: #0e6b0e;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🐛 Bishwo Calculator - Log Viewer</h1>
        <p>Real-time debugging and monitoring system</p>
    </div>
    
    <div class="log-controls">
        <strong>Log Type:</strong>
        <a href="?type=debug" <?php echo $log_type === 'debug' ? 'style="background: #0e6b0e;"' : ''; ?>>Debug</a>
        <a href="?type=error" <?php echo $log_type === 'error' ? 'style="background: #0e6b0e;"' : ''; ?>>Error</a>
        <a href="?type=access" <?php echo $log_type === 'access' ? 'style="background: #0e6b0e;"' : ''; ?>>Access</a>
        <a href="?type=system" <?php echo $log_type === 'system' ? 'style="background: #0e6b0e;"' : ''; ?>>System</a>
        
        <strong style="margin-left: 20px;">Limit:</strong>
        <a href="?type=<?php echo $log_type; ?>&limit=25" <?php echo $limit === 25 ? 'style="background: #0e6b0e;"' : ''; ?>>25</a>
        <a href="?type=<?php echo $log_type; ?>&limit=50" <?php echo $limit === 50 ? 'style="background: #0e6b0e;"' : ''; ?>>50</a>
        <a href="?type=<?php echo $log_type; ?>&limit=100" <?php echo $limit === 100 ? 'style="background: #0e6b0e;"' : ''; ?>>100</a>
        
        <a href="?type=<?php echo $log_type; ?>&clear=true" style="background: #d13438;" onclick="return confirm('Clear all <?php echo $log_type; ?> logs?')">Clear <?php echo ucfirst($log_type); ?> Logs</a>
        <a href="?" class="refresh-btn">🔄 Refresh</a>
    </div>
    
    <div class="log-container">
        <?php
        $error_count = 0;
        if (empty($logs)) {
            echo '<div class="log-entry">No logs found in ' . $log_type . '.log</div>';
        } else {
            foreach ($logs as $log_line) {
                // Parse log line
                if (preg_match('/\[([^\]]+)\]\s+\[([^\]]+)\]\s+(\[([^\]]+)\])?\s*(.*)/', $log_line, $matches)) {
                    $timestamp = $matches[1];
                    $level = $matches[2];
                    $context = $matches[4] ?? '';
                    $message = $matches[5];
                    
                    if ($level === 'ERROR') {
                        $error_count++;
                    }
                    
                    echo '<div class="log-entry">';
                    echo '<span class="log-timestamp">' . htmlspecialchars($timestamp) . '</span> ';
                    echo '<span class="log-level ' . htmlspecialchars($level) . '">' . htmlspecialchars($level) . '</span> ';
                    if ($context) {
                        echo '<span style="color: #ce9178;">' . htmlspecialchars($context) . '</span> ';
                    }
                    echo htmlspecialchars($message);
                    echo '</div>';
                } else {
                    // Fallback for unparseable lines
                    echo '<div class="log-entry">' . htmlspecialchars($log_line) . '</div>';
                }
            }
        }
        ?>
    </div>
    
    <div style="margin-top: 20px; color: #888; font-size: 12px;">
        <strong>Statistics:</strong> 
        <?php echo count($logs); ?> entries loaded
        <?php if ($error_count > 0): ?>
        | <span class="error-count"><?php echo $error_count; ?> errors</span>
        <?php endif; ?>
        | Last updated: <?php echo date('H:i:s'); ?>
    </div>
    
    <script>
        // Auto-refresh every 5 seconds
        setTimeout(function() {
            location.reload();
        }, 5000);
    </script>
</body>
</html>
