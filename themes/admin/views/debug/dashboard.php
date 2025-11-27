<?php
// Debug Dashboard View
$content = '
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-bug"></i>
            Debug Dashboard
        </h1>
        <p class="page-description">System testing, error monitoring, and debugging tools</p>
    </div>
    
    <!-- Debug Actions -->
    <div class="debug-actions" style="margin-bottom: 32px;">
        <div class="btn-group">
            <button onclick="runAllTests()" class="btn btn-primary">
                <i class="fas fa-play"></i>
                Run All Tests
            </button>
            <a href="' . app_base_url('admin/debug/error-logs') . '" class="btn btn-secondary">
                <i class="fas fa-file-alt"></i>
                View Error Logs
            </a>
            <a href="' . app_base_url('admin/debug/live-errors') . '" class="btn btn-info">
                <i class="fas fa-broadcast-tower"></i>
                Live Monitor
            </a>
            <button onclick="clearErrorLogs()" class="btn btn-warning">
                <i class="fas fa-trash"></i>
                Clear Logs
            </button>
        </div>
    </div>
    
    <!-- System Tests Results -->
    <div class="card" style="margin-bottom: 32px;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-check-circle"></i>
                System Tests
            </h3>
            <button onclick="refreshTests()" class="btn btn-sm btn-secondary">
                <i class="fas fa-sync"></i>
                Refresh
            </button>
        </div>
        <div class="card-content">
            <div class="test-results" id="testResults">
';

if (!empty($test_results)) {
    foreach ($test_results as $testName => $result) {
        $statusClass = '';
        $statusIcon = '';

        switch ($result['status']) {
            case 'pass':
                $statusClass = 'success';
                $statusIcon = 'fa-check-circle';
                break;
            case 'warning':
                $statusClass = 'warning';
                $statusIcon = 'fa-exclamation-triangle';
                break;
            case 'fail':
                $statusClass = 'danger';
                $statusIcon = 'fa-times-circle';
                break;
        }

        $content .= '
                <div class="test-item">
                    <div class="test-header">
                        <span class="test-name">' . htmlspecialchars($testName) . '</span>
                        <span class="test-status status-' . $statusClass . '">
                            <i class="fas ' . $statusIcon . '"></i>
                            ' . strtoupper($result['status']) . '
                        </span>
                    </div>';

        if (!empty($result['messages'])) {
            $content .= '<div class="test-messages">';
            foreach ($result['messages'] as $message) {
                $content .= '<div class="test-message">' . htmlspecialchars($message) . '</div>';
            }
            $content .= '</div>';
        }

        $content .= '</div>';
    }
} else {
    $content .= '<div class="no-tests">Click "Run All Tests" to start system testing</div>';
}

$content .= '
            </div>
        </div>
    </div>
    
    <!-- System Information -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 32px;">
        
        <!-- PHP Information -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fab fa-php"></i>
                    PHP Information
                </h3>
            </div>
            <div class="card-content">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Version:</span>
                        <span class="info-value">' . htmlspecialchars($system_info['php']['version']) . '</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Memory Limit:</span>
                        <span class="info-value">' . htmlspecialchars($system_info['php']['memory_limit']) . '</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Memory Usage:</span>
                        <span class="info-value">' . htmlspecialchars($system_info['php']['memory_usage']) . '</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Peak Usage:</span>
                        <span class="info-value">' . htmlspecialchars($system_info['php']['memory_peak']) . '</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Error Logging:</span>
                        <span class="info-value">' . ($system_info['php']['log_errors'] ? 'Enabled' : 'Disabled') . '</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Database Information -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-database"></i>
                    Database Information
                </h3>
            </div>
            <div class="card-content">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Status:</span>
                        <span class="info-value status-' . ($system_info['database']['status'] === 'Connected' ? 'success' : 'danger') . '">
                            ' . htmlspecialchars($system_info['database']['status']) . '
                        </span>
                    </div>';

if (isset($system_info['database']['version'])) {
    $content .= '
                    <div class="info-item">
                        <span class="info-label">Version:</span>
                        <span class="info-value">' . htmlspecialchars($system_info['database']['version']) . '</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Charset:</span>
                        <span class="info-value">' . htmlspecialchars($system_info['database']['charset']) . '</span>
                    </div>';

    if (isset($system_info['database']['tables']) && is_array($system_info['database']['tables'])) {
        $content .= '
                    <div class="info-item">
                        <span class="info-label">Tables:</span>
                        <span class="info-value">' . count($system_info['database']['tables']) . ' tables</span>
                    </div>';
    }
}

$content .= '
                </div>
            </div>
        </div>
        
    </div>
    
    <!-- Recent Errors -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-exclamation-triangle"></i>
                Recent Errors
            </h3>
            <a href="' . app_base_url('admin/debug/error-logs') . '" class="btn btn-sm btn-primary">View All</a>
        </div>
        <div class="card-content">
            <div class="error-list">';

if (!empty($recent_errors)) {
    foreach (array_slice($recent_errors, 0, 10) as $error) {
        $levelClass = '';
        switch ($error['level']) {
            case 'fatal':
            case 'error':
                $levelClass = 'danger';
                break;
            case 'warning':
                $levelClass = 'warning';
                break;
            default:
                $levelClass = 'info';
        }

        $content .= '
                <div class="error-item">
                    <div class="error-time">' . htmlspecialchars($error['timestamp']) . '</div>
                    <div class="error-level level-' . $levelClass . '">' . strtoupper($error['level']) . '</div>
                    <div class="error-message">' . htmlspecialchars(substr($error['message'], 0, 100)) . '</div>
                </div>';
    }
} else {
    $content .= '<div class="no-errors">No recent errors found</div>';
}

$content .= '
            </div>
        </div>
    </div>
    
</div>

<script>
// Test runner functions
const csrfToken = \'' . (isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '') . '\';

async function runAllTests() {
    const button = event.target;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = \'<i class="fas fa-spinner fa-spin"></i> Running Tests...\';
    
    try {
        const response = await fetch("' . app_base_url('admin/debug/run-tests') . '", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `test_type=all&csrf_token=${encodeURIComponent(csrfToken)}`
        });
        
        const result = await response.json();
        
        if (result.success) {
            displayTestResults(result.results);
            AdminApp.showNotification("All tests completed", "success");
        } else {
            AdminApp.showNotification("Test execution failed", "error");
        }
    } catch (error) {
        AdminApp.showNotification("Network error during testing", "error");
    } finally {
        button.disabled = false;
        button.innerHTML = originalText;
    }
}

function displayTestResults(results) {
    const container = document.getElementById("testResults");
    let html = "";
    
    for (const [testName, result] of Object.entries(results)) {
        let statusClass = "";
        let statusIcon = "";
        
        switch (result.status) {
            case "pass":
                statusClass = "success";
                statusIcon = "fa-check-circle";
                break;
            case "warning":
                statusClass = "warning";
                statusIcon = "fa-exclamation-triangle";
                break;
            case "fail":
                statusClass = "danger";
                statusIcon = "fa-times-circle";
                break;
        }
        
        html += `
            <div class="test-item">
                <div class="test-header">
                    <span class="test-name">${testName}</span>
                    <span class="test-status status-${statusClass}">
                        <i class="fas ${statusIcon}"></i>
                        ${result.status.toUpperCase()}
                    </span>
                </div>`;
        
        if (result.messages && result.messages.length > 0) {
            html += `<div class="test-messages">`;
            result.messages.forEach(message => {
                html += `<div class="test-message">${message}</div>`;
            });
            html += `</div>`;
        }
        
        html += `</div>`;
    }
    
    container.innerHTML = html;
}

function refreshTests() {
    location.reload();
}

async function clearErrorLogs() {
    if (!confirm("Are you sure you want to clear all error logs?")) {
        return;
    }
    
    try {
        const response = await fetch("' . app_base_url('admin/debug/clear-logs') . '", {
            method: "POST"
        });
        
        const result = await response.json();
        
        if (result.success) {
            AdminApp.showNotification("Error logs cleared", "success");
            location.reload();
        } else {
            AdminApp.showNotification("Failed to clear logs", "error");
        }
    } catch (error) {
        AdminApp.showNotification("Network error", "error");
    }
}
</script>

<style>
.debug-actions {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: var(--admin-shadow);
}

.btn-group {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.test-results {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.test-item {
    border: 1px solid var(--admin-gray-200);
    border-radius: 8px;
    padding: 16px;
    background: var(--admin-gray-50);
}

.test-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.test-name {
    font-weight: 600;
    color: var(--admin-gray-800);
}

.test-status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

.test-status.status-success {
    background: rgba(16, 185, 129, 0.1);
    color: var(--admin-success);
}

.test-status.status-warning {
    background: rgba(245, 158, 11, 0.1);
    color: var(--admin-warning);
}

.test-status.status-danger {
    background: rgba(239, 68, 68, 0.1);
    color: var(--admin-danger);
}

.test-messages {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.test-message {
    font-size: 13px;
    color: var(--admin-gray-600);
    padding-left: 8px;
    border-left: 2px solid var(--admin-gray-300);
}

.info-grid {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid var(--admin-gray-200);
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 500;
    color: var(--admin-gray-700);
}

.info-value {
    color: var(--admin-gray-900);
}

.info-value.status-success {
    color: var(--admin-success);
}

.info-value.status-danger {
    color: var(--admin-danger);
}

.error-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.error-item {
    display: grid;
    grid-template-columns: 160px 80px 1fr;
    gap: 16px;
    padding: 8px 0;
    border-bottom: 1px solid var(--admin-gray-200);
    align-items: center;
}

.error-time {
    font-size: 12px;
    color: var(--admin-gray-500);
}

.error-level {
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 3px;
    font-weight: 600;
}

.error-level.level-danger {
    background: rgba(239, 68, 68, 0.1);
    color: var(--admin-danger);
}

.error-level.level-warning {
    background: rgba(245, 158, 11, 0.1);
    color: var(--admin-warning);
}

.error-level.level-info {
    background: rgba(59, 130, 246, 0.1);
    color: var(--admin-info);
}

.error-message {
    font-size: 14px;
    color: var(--admin-gray-700);
}

.no-tests, .no-errors {
    text-align: center;
    color: var(--admin-gray-500);
    padding: 32px;
}

@media (max-width: 768px) {
    .error-item {
        grid-template-columns: 1fr;
        gap: 4px;
    }
    
    .btn-group {
        flex-direction: column;
    }
}
</style>
';

// Set breadcrumbs
$breadcrumbs = [
    ['title' => 'Debug Dashboard']
];

// Include the layout
include __DIR__ . '/../../layouts/main.php';
