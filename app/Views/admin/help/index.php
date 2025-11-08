<?php
$content = '
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Help & System Logs</h2>
            <p class="text-muted mb-0">System information, logs, and maintenance</p>
        </div>
        <div class="quick-actions">
            <button class="btn btn-primary btn-sm" id="createBackup">
                <i class="bi bi-download me-2"></i>Create Backup
            </button>
            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#systemInfoModal">
                <i class="bi bi-info-circle me-2"></i>System Info
            </button>
        </div>
    </div>

    <!-- System Health -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Health</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="mb-3">
                                <i class="bi bi-cpu fs-1 text-success"></i>
                                <h5 class="mt-2">Server</h5>
                                <div class="badge bg-success">Healthy</div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="mb-3">
                                <i class="bi bi-database fs-1 text-success"></i>
                                <h5 class="mt-2">Database</h5>
                                <div class="badge bg-success">Connected</div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="mb-3">
                                <i class="bi bi-envelope fs-1 text-warning"></i>
                                <h5 class="mt-2">Email</h5>
                                <div class="badge bg-warning">Configured</div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="mb-3">
                                <i class="bi bi-shield-check fs-1 text-success"></i>
                                <h5 class="mt-2">Security</h5>
                                <div class="badge bg-success">Protected</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Logs -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">System Logs</h6>
                    <div>
                        <button class="btn btn-sm btn-outline-danger me-2" id="clearLogs">
                            <i class="bi bi-trash me-1"></i>Clear Logs
                        </button>
                        <button class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-download me-1"></i>Export Logs
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="100">Level</th>
                                    <th>Message</th>
                                    <th width="180">Timestamp</th>
                                    <th width="120">IP Address</th>
                                </tr>
                            </thead>
                            <tbody>';
                            
                            foreach ($logs as $log) {
                                $levelClass = $log['level'] === 'ERROR' ? 'danger' : 
                                             ($log['level'] === 'WARNING' ? 'warning' : 'info');
                                
                                $content .= '
                                <tr>
                                    <td>
                                        <span class="badge bg-' . $levelClass . '">' . $log['level'] . '</span>
                                    </td>
                                    <td>' . htmlspecialchars($log['message']) . '</td>
                                    <td>' . $log['timestamp'] . '</td>
                                    <td><code>' . $log['ip'] . '</code></td>
                                </tr>';
                            }
                            
                            $content .= '
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-book me-2"></i>Documentation
                        </button>
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-question-circle me-2"></i>Help Center
                        </button>
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-bug me-2"></i>Report Bug
                        </button>
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-lightning me-2"></i>Clear Cache
                        </button>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Status</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>CPU Usage</span>
                            <span class="text-success">24%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 24%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Memory Usage</span>
                            <span class="text-warning">65%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 65%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Disk Space</span>
                            <span class="text-info">42%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 42%"></div>
                        </div>
                    </div>
                    
                    <div class="alert alert-success mt-3">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>System Uptime:</strong> ' . $systemInfo['system_uptime'] . '
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Info Modal -->
<div class="modal fade" id="systemInfoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">System Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>PHP Information</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>PHP Version</strong></td>
                                <td>' . $systemInfo['php_version'] . '</td>
                            </tr>
                            <tr>
                                <td><strong>Server Software</strong></td>
                                <td>' . $systemInfo['server_software'] . '</td>
                            </tr>
                            <tr>
                                <td><strong>Memory Limit</strong></td>
                                <td>' . $systemInfo['memory_limit'] . '</td>
                            </tr>
                            <tr>
                                <td><strong>Upload Max Filesize</strong></td>
                                <td>' . $systemInfo['upload_max_filesize'] . '</td>
                            </tr>
                            <tr>
                                <td><strong>Max Execution Time</strong></td>
                                <td>' . $systemInfo['max_execution_time'] . 's</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Application Information</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Database Version</strong></td>
                                <td>' . $systemInfo['database_version'] . '</td>
                            </tr>
                            <tr>
                                <td><strong>System Uptime</strong></td>
                                <td>' . $systemInfo['system_uptime'] . '</td>
                            </tr>
                            <tr>
                                <td><strong>Last Backup</strong></td>
                                <td>' . $systemInfo['last_backup'] . '</td>
                            </tr>
                            <tr>
                                <td><strong>Loaded Extensions</strong></td>
                                <td>' . count($systemInfo['loaded_extensions']) . ' extensions</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="mt-3">
                    <h6>PHP Extensions</h6>
                    <div class="bg-light p-3 rounded" style="max-height: 200px; overflow-y: auto;">
                        <div class="row">';
                        
                        foreach (array_chunk($systemInfo['loaded_extensions'], 4) as $chunk) {
                            $content .= '<div class="col-md-3">';
                            foreach ($chunk as $extension) {
                                $content .= '<div class="text-monospace small">' . $extension . '</div>';
                            }
                            $content .= '</div>';
                        }
                        
                        $content .= '
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Clear logs
document.getElementById("clearLogs").addEventListener("click", function() {
    if (confirm("Are you sure you want to clear all system logs? This action cannot be undone.")) {
        fetch("/admin/help/clear-logs", {
            method: "POST"
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Logs cleared successfully!");
                location.reload();
            } else {
                alert("Error: " + data.message);
            }
        });
    }
});

// Create backup
document.getElementById("createBackup").addEventListener("click", function() {
    if (confirm("Create a system backup? This may take a few minutes.")) {
        fetch("/admin/help/backup", {
            method: "POST"
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Backup created successfully!");
            } else {
                alert("Error: " + data.message);
            }
        });
    }
});
</script>
';

include __DIR__ . '/../../layouts/admin.php';
?>
