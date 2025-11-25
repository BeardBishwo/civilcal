<?php
// System Status View
$serverMetrics = $systemHealth['server'] ?? [];
$dbMetrics = $systemHealth['database'] ?? [];
$storageMetrics = $systemHealth['storage'] ?? [];
$appMetrics = $systemHealth['application'] ?? [];
$securityMetrics = $systemHealth['security'] ?? [];

$content = '
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-server"></i>
            System Status
        </h1>
        <p class="page-description">Real-time monitoring of your application and server health</p>
    </div>

    <!-- System Health Overview -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon ' . ($serverMetrics['status'] === 'online' ? 'success' : ($serverMetrics['status'] === 'warning' ? 'warning' : 'danger')) . '">
                <i class="fas fa-server"></i>
            </div>
            <div class="stat-value">Server</div>
            <div class="stat-label">' . ucfirst($serverMetrics['status'] ?? 'unknown') . '</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon ' . ($dbMetrics['status'] === 'online' ? 'success' : ($dbMetrics['status'] === 'warning' ? 'warning' : 'danger')) . '">
                <i class="fas fa-database"></i>
            </div>
            <div class="stat-value">Database</div>
            <div class="stat-label">' . ucfirst($dbMetrics['status'] ?? 'unknown') . '</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon ' . ($storageMetrics['status'] === 'online' ? 'success' : ($storageMetrics['status'] === 'warning' ? 'warning' : 'danger')) . '">
                <i class="fas fa-hdd"></i>
            </div>
            <div class="stat-value">Storage</div>
            <div class="stat-label">' . ucfirst($storageMetrics['status'] ?? 'unknown') . '</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon ' . ($appMetrics['status'] === 'online' ? 'success' : ($appMetrics['status'] === 'warning' ? 'warning' : 'danger')) . '">
                <i class="fas fa-cube"></i>
            </div>
            <div class="stat-value">Application</div>
            <div class="stat-label">' . ucfirst($appMetrics['status'] ?? 'unknown') . '</div>
        </div>
    </div>

    <!-- System Health Checks -->
    <div class="system-health-grid">
        <!-- Server Health -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-server"></i>
                    Server Health
                </h3>
                <div class="status-badge status-' . ($serverMetrics['status'] ?? 'unknown') . '">
                    ' . ucfirst($serverMetrics['status'] ?? 'unknown') . '
                </div>
            </div>
            <div class="card-content">
                <div class="metric-grid">
                    <div class="metric-item">
                        <div class="metric-label">Load Average (1min)</div>
                        <div class="metric-value">' . ($serverMetrics['load_average']['1min'] ?? 'N/A') . '</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-label">Memory Usage</div>
                        <div class="metric-value">' . ($serverMetrics['memory_usage']['percent'] ?? 'N/A') . '%</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-label">CPU Usage</div>
                        <div class="metric-value">' . ($serverMetrics['cpu_usage']['usage_percent'] ?? 'N/A') . '</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-label">Uptime</div>
                        <div class="metric-value">' . ($serverMetrics['uptime'] ?? 'N/A') . '</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Database Health -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-database"></i>
                    Database Health
                </h3>
                <div class="status-badge status-' . ($dbMetrics['status'] ?? 'unknown') . '">
                    ' . ucfirst($dbMetrics['status'] ?? 'unknown') . '
                </div>
            </div>
            <div class="card-content">
                <div class="metric-grid">
                    <div class="metric-item">
                        <div class="metric-label">Status</div>
                        <div class="metric-value">' . ucfirst($dbMetrics['status'] ?? 'N/A') . '</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-label">Connections</div>
                        <div class="metric-value">' . ($dbMetrics['connections'] ?? 'N/A') . '</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-label">Response Time</div>
                        <div class="metric-value">' . ($dbMetrics['response_time'] ?? 'N/A') . '</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-label">Tables</div>
                        <div class="metric-value">' . ($dbMetrics['table_status']['count'] ?? 'N/A') . '</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Storage Health -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-hdd"></i>
                    Storage Health
                </h3>
                <div class="status-badge status-' . ($storageMetrics['status'] ?? 'unknown') . '">
                    ' . ucfirst($storageMetrics['status'] ?? 'unknown') . '
                </div>
            </div>
            <div class="card-content">
                <div class="metric-grid">
                    <div class="metric-item">
                        <div class="metric-label">Usage</div>
                        <div class="metric-value">' . ($storageMetrics['usage_percent'] ?? 'N/A') . '%</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-label">Total</div>
                        <div class="metric-value">' . ($storageMetrics['total_space'] ?? 'N/A') . '</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-label">Used</div>
                        <div class="metric-value">' . ($storageMetrics['used_space'] ?? 'N/A') . '</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-label">Free</div>
                        <div class="metric-value">' . ($storageMetrics['free_space'] ?? 'N/A') . '</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Application Health -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cube"></i>
                    Application Health
                </h3>
                <div class="status-badge status-' . ($appMetrics['status'] ?? 'unknown') . '">
                    ' . ucfirst($appMetrics['status'] ?? 'unknown') . '
                </div>
            </div>
            <div class="card-content">
                <div class="metric-grid">
                    <div class="metric-item">
                        <div class="metric-label">PHP Version</div>
                        <div class="metric-value">' . ($appMetrics['php_version'] ?? 'N/A') . '</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-label">Active Sessions</div>
                        <div class="metric-value">' . ($appMetrics['active_sessions'] ?? 'N/A') . '</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-label">Errors Today</div>
                        <div class="metric-value">' . ($appMetrics['errors_today'] ?? 'N/A') . '</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-label">RPM</div>
                        <div class="metric-value">' . ($appMetrics['requests_per_minute'] ?? 'N/A') . '</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Health -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shield-alt"></i>
                    Security Health
                </h3>
                <div class="status-badge status-' . ($securityMetrics['status'] ?? 'unknown') . '">
                    ' . ucfirst($securityMetrics['status'] ?? 'unknown') . '
                </div>
            </div>
            <div class="card-content">
                <div class="metric-grid">
                    <div class="metric-item">
                        <div class="metric-label">Failed Logins</div>
                        <div class="metric-value">' . ($securityMetrics['failed_login_attempts'] ?? 'N/A') . '</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-label">Security Events</div>
                        <div class="metric-value">' . ($securityMetrics['security_events'] ?? 'N/A') . '</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-label">SSL Status</div>
                        <div class="metric-value">' . ($securityMetrics['ssl_certificate_status']['valid'] ? 'Valid' : 'Invalid') . '</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-label">Last Scan</div>
                        <div class="metric-value">' . ($securityMetrics['last_security_scan']['date'] ?? 'N/A') . '</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Last Updated -->
    <div class="last-updated">
        <p><i class="fas fa-clock"></i> Last updated: <span id="last-update-time">' . date('Y-m-d H:i:s') . '</span></p>
    </div>

    <!-- System Actions -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-tools"></i>
                System Actions
            </h3>
        </div>
        <div class="card-content">
            <div class="system-actions-grid">
                <button id="run-health-check" class="action-btn">
                    <i class="fas fa-stethoscope"></i>
                    <span>Run Health Check</span>
                </button>
                
                <button id="refresh-status" class="action-btn">
                    <i class="fas fa-sync"></i>
                    <span>Refresh Status</span>
                </button>
                
                <button id="view-logs" class="action-btn">
                    <i class="fas fa-file-alt"></i>
                    <span>View Logs</span>
                </button>
                
                <button id="export-report" class="action-btn">
                    <i class="fas fa-file-export"></i>
                    <span>Export Report</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("run-health-check").addEventListener("click", runHealthCheck);
    document.getElementById("refresh-status").addEventListener("click", refreshStatus);
    document.getElementById("view-logs").addEventListener("click", viewLogs);
    document.getElementById("export-report").addEventListener("click", exportReport);
});

async function runHealthCheck() {
    try {
        const response = await fetch("' . app_base_url('/admin/system-status/run-health-check') . '", {
            method: "POST"
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage("Health check completed. Found " + (result.issues?.length || 0) + " issue(s).", "success");
            
            if (result.issues && result.issues.length > 0) {
                showMessage("Issues found: " + result.issues.map(i => i.message).join(", "), "warning");
            }
        } else {
            showMessage("Error running health check: " + result.error, "error");
        }
    } catch (error) {
        showMessage("Error running health check: " + error.message, "error");
    }
}

async function refreshStatus() {
    try {
        window.location.reload();
    } catch (error) {
        showMessage("Error refreshing status: " + error.message, "error");
    }
}

async function viewLogs() {
    // Open logs page in a new tab/window
    window.open("' . app_base_url('/admin/logs') . '", "_blank");
}

async function exportReport() {
    try {
        showMessage("Report export feature would be implemented in a full implementation.", "info");
    } catch (error) {
        showMessage("Error exporting report: " + error.message, "error");
    }
}

function showMessage(message, type) {
    // Create a temporary message element
    const messageEl = document.createElement("div");
    messageEl.className = "alert alert-" + type;
    messageEl.textContent = message;
    messageEl.style.cssText = "position:fixed; top:20px; right:20px; padding:15px; border-radius:5px; z-index:9999; box-shadow: 0 4px 12px rgba(0,0,0,0.1);";
    
    if (type === "success") {
        messageEl.style.backgroundColor = "#d4edda";
        messageEl.style.color = "#155724";
        messageEl.style.border = "1px solid #c3e6cb";
    } else if (type === "warning") {
        messageEl.style.backgroundColor = "#fff3cd";
        messageEl.style.color = "#856404";
        messageEl.style.border = "1px solid #ffeeba";
    } else if (type === "error") {
        messageEl.style.backgroundColor = "#f8d7da";
        messageEl.style.color = "#721c24";
        messageEl.style.border = "1px solid #f5c6cb";
    } else {
        messageEl.style.backgroundColor = "#d1ecf1";
        messageEl.style.color = "#0c5460";
        messageEl.style.border = "1px solid #bee5eb";
    }
    
    document.body.appendChild(messageEl);
    
    // Remove after 5 seconds
    setTimeout(() => {
        document.body.removeChild(messageEl);
    }, 5000);
}
</script>


';

// Set breadcrumbs
$breadcrumbs = [
    ['title' => 'System Status']
];

$page_title = $page_title ?? 'System Status - Admin Panel';
$currentPage = $currentPage ?? 'system-status';

// Include the layout
include BASE_PATH . '/themes/admin/layouts/main.php';
?>