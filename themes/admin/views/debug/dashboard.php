<?php
/**
 * Debug Dashboard View
 */
$page_title = 'Debug Dashboard';
$breadcrumbs = [['title' => 'Debug Dashboard']];
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">
        
        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-bug"></i>
                    <h1>Debug Dashboard</h1>
                </div>
                <div class="header-subtitle">System testing, error monitoring, and debugging tools</div>
            </div>
            <div class="header-actions">
                <button onclick="runAllTests()" class="btn btn-primary btn-compact">
                    <i class="fas fa-play"></i>
                    <span>Run All Tests</span>
                </button>
            </div>
        </div>

        <!-- Quick Actions Toolbar -->
        <div class="compact-toolbar" style="justify-content: flex-start; gap: 10px;">
            <a href="<?= app_base_url('/admin/debug/error-logs') ?>" class="btn btn-outline-secondary btn-compact">
                <i class="fas fa-file-alt"></i> <span>View Error Logs</span>
            </a>
            <a href="<?= app_base_url('/admin/debug/live-errors') ?>" class="btn btn-outline-primary btn-compact">
                <i class="fas fa-broadcast-tower"></i> <span>Live Monitor</span>
            </a>
            <button onclick="clearErrorLogs()" class="btn btn-outline-danger btn-compact">
                <i class="fas fa-trash"></i> <span>Clear Logs</span>
            </button>
        </div>

        <!-- System Tests Results -->
        <div class="settings-card">
            <div class="settings-card-header">
                <i class="fas fa-check-circle"></i>
                <h3>System Tests</h3>
                <div class="header-actions-inline">
                    <button onclick="refreshTests()" class="btn btn-sm btn-outline-light">
                        <i class="fas fa-sync"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="settings-card-body">
                <div class="test-results" id="testResults">
                    <?php if (!empty($test_results)): ?>
                        <?php foreach ($test_results as $testName => $result): 
                            $statusClass = $result['status'] === 'pass' ? 'success' : ($result['status'] === 'warning' ? 'warning' : 'danger');
                            $statusIcon = $result['status'] === 'pass' ? 'fa-check-circle' : ($result['status'] === 'warning' ? 'fa-exclamation-triangle' : 'fa-times-circle');
                        ?>
                        <div class="test-item">
                            <div class="test-header">
                                <span class="test-name"><?= htmlspecialchars($testName) ?></span>
                                <span class="test-status status-<?= $statusClass ?>">
                                    <i class="fas <?= $statusIcon ?>"></i>
                                    <?= strtoupper($result['status']) ?>
                                </span>
                            </div>
                            <?php if (!empty($result['messages'])): ?>
                                <div class="test-messages">
                                    <?php foreach ($result['messages'] as $message): ?>
                                        <div class="test-message"><?= htmlspecialchars($message) ?></div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-microscope" style="font-size: 3rem; color: #cbd5e0; margin-bottom: 1rem;"></i>
                            <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;">No Tests Run</h3>
                            <p style="color: #718096;">Click "Run All Tests" above to start the system check.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- System Information Grid -->
        <div class="form-row-compact">
            
            <!-- PHP Information -->
            <div class="settings-card" style="margin-bottom: 0;">
                <div class="settings-card-header">
                    <i class="fab fa-php"></i>
                    <h3>PHP Information</h3>
                </div>
                <div class="settings-card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Version</span>
                            <span class="info-value"><?= htmlspecialchars($system_info['php']['version'] ?? 'Unknown') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Memory Limit</span>
                            <span class="info-value"><?= htmlspecialchars($system_info['php']['memory_limit'] ?? 'Unknown') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Memory Usage</span>
                            <span class="info-value"><?= htmlspecialchars($system_info['php']['memory_usage'] ?? 'Unknown') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Peak Usage</span>
                            <span class="info-value"><?= htmlspecialchars($system_info['php']['memory_peak'] ?? 'Unknown') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Error Logging</span>
                            <span class="info-value"><?= ($system_info['php']['log_errors'] ?? false) ? 'Enabled' : 'Disabled' ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Database Information -->
            <div class="settings-card" style="margin-bottom: 0;">
                <div class="settings-card-header">
                    <i class="fas fa-database"></i>
                    <h3>Database Information</h3>
                </div>
                <div class="settings-card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Status</span>
                            <?php $dbStatus = $system_info['database']['status'] ?? 'Unknown'; ?>
                            <span class="info-value status-<?= $dbStatus === 'Connected' ? 'success' : 'danger' ?>">
                                <?= htmlspecialchars($dbStatus) ?>
                            </span>
                        </div>
                        <?php if (isset($system_info['database']['version'])): ?>
                            <div class="info-item">
                                <span class="info-label">Version</span>
                                <span class="info-value"><?= htmlspecialchars($system_info['database']['version']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Charset</span>
                                <span class="info-value"><?= htmlspecialchars($system_info['database']['charset']) ?></span>
                            </div>
                            <?php if (isset($system_info['database']['tables']) && is_array($system_info['database']['tables'])): ?>
                                <div class="info-item">
                                    <span class="info-label">Tables</span>
                                    <span class="info-value"><?= count($system_info['database']['tables']) ?> tables</span>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- Recent Errors -->
        <div class="settings-card" style="margin-top: 2rem;">
            <div class="settings-card-header">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Recent Errors</h3>
                <div class="header-actions-inline">
                    <a href="<?= app_base_url('/admin/debug/error-logs') ?>" class="btn btn-sm btn-outline-light">View All</a>
                </div>
            </div>
            <div class="settings-card-body">
                <div class="error-list">
                    <?php if (!empty($recent_errors)): ?>
                        <?php foreach (array_slice($recent_errors, 0, 10) as $error): 
                            $levelClass = 'info';
                            switch ($error['level']) {
                                case 'fatal':
                                case 'error': $levelClass = 'danger'; break;
                                case 'warning': $levelClass = 'warning'; break;
                            }
                        ?>
                        <div class="error-item">
                            <div class="error-time"><?= htmlspecialchars($error['timestamp']) ?></div>
                            <div class="error-level level-<?= $levelClass ?>"><?= strtoupper($error['level']) ?></div>
                            <div class="error-message" title="<?= htmlspecialchars($error['message']) ?>">
                                <?= htmlspecialchars(substr($error['message'], 0, 100)) . (strlen($error['message']) > 100 ? '...' : '') ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state" style="padding: 2rem;">
                            <i class="fas fa-check-circle" style="font-size: 2rem; color: #cbd5e0; margin-bottom: 0.5rem;"></i>
                            <p style="margin: 0;">No recent errors found</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Test runner functions
const csrfToken = '<?= csrf_token() ?>';

async function runAllTests() {
    const button = event.currentTarget; // Changed to currentTarget to handle icon click
    const originalContent = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Running...</span>';
    
    try {
        const response = await fetch("<?= app_base_url('/admin/debug/run-tests') ?>", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `test_type=all&csrf_token=${encodeURIComponent(csrfToken)}`
        });
        
        const result = await response.json();
        
        if (result.success) {
            displayTestResults(result.results);
            showNotification("All tests completed", "success");
        } else {
            showNotification("Test execution failed", "error");
        }
    } catch (error) {
        showNotification("Network error during testing", "error");
    } finally {
        button.disabled = false;
        button.innerHTML = originalContent;
    }
}

function displayTestResults(results) {
    const container = document.getElementById("testResults");
    let html = "";
    
    for (const [testName, result] of Object.entries(results)) {
        let statusClass = "danger";
        let statusIcon = "fa-times-circle";
        
        if (result.status === "pass") {
            statusClass = "success";
            statusIcon = "fa-check-circle";
        } else if (result.status === "warning") {
            statusClass = "warning";
            statusIcon = "fa-exclamation-triangle";
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
    showConfirmModal('Clear Logs', "Are you sure you want to clear all error logs?", async () => {
        try {
            const response = await fetch("<?= app_base_url('/admin/debug/clear-logs') ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `csrf_token=${encodeURIComponent(csrfToken)}`
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification("Error logs cleared", "success");
                location.reload();
            } else {
                showNotification("Failed to clear logs", "error");
            }
        } catch (error) {
            showNotification("Network error", "error");
        }
    });
}
</script>
