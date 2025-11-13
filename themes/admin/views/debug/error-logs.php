<?php
// Error Logs View
$content = '
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-file-alt"></i>
            Error Logs
        </h1>
        <p class="page-description">View and manage system error logs for debugging</p>
    </div>
    
    <!-- Log Controls -->
    <div class="log-controls" style="margin-bottom: 32px;">
        <div class="controls-left">
            <div class="filter-group">
                <label>Filter by Level:</label>
                <select id="logFilter" onchange="filterLogs(this.value)" class="form-control" style="width: 150px;">
                    <option value="all"' . ($filter === 'all' ? ' selected' : '') . '>All Levels</option>
                    <option value="error"' . ($filter === 'error' ? ' selected' : '') . '>Errors Only</option>
                    <option value="warning"' . ($filter === 'warning' ? ' selected' : '') . '>Warnings Only</option>
                    <option value="notice"' . ($filter === 'notice' ? ' selected' : '') . '>Notices Only</option>
                    <option value="info"' . ($filter === 'info' ? ' selected' : '') . '>Info Only</option>
                </select>
            </div>
        </div>
        
        <div class="controls-right">
            <button onclick="refreshLogs()" class="btn btn-secondary">
                <i class="fas fa-sync"></i>
                Refresh
            </button>
            <button onclick="downloadLogs()" class="btn btn-info">
                <i class="fas fa-download"></i>
                Download
            </button>
            <button onclick="clearLogs()" class="btn btn-warning">
                <i class="fas fa-trash"></i>
                Clear Logs
            </button>
        </div>
    </div>
    
    <!-- Log Statistics -->
    <div class="log-stats" style="margin-bottom: 32px;">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">' . count(array_filter($logs['logs'], fn($l) => $l['level'] === 'error')) . '</div>
                    <div class="stat-label">Errors</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">' . count(array_filter($logs['logs'], fn($l) => $l['level'] === 'warning')) . '</div>
                    <div class="stat-label">Warnings</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">' . $logs['total'] . '</div>
                    <div class="stat-label">Total Entries</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="stat-details">
                    <div class="stat-value">' . $logs['pages'] . '</div>
                    <div class="stat-label">Pages</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Error Logs Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i>
                Error Log Entries
            </h3>
            <div class="card-actions">
                <span class="log-info">Page ' . $current_page . ' of ' . $logs['pages'] . '</span>
            </div>
        </div>
        <div class="card-content" style="padding: 0;">
            <div class="log-table-container">
                <table class="log-table" id="logTable">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Level</th>
                            <th style="width: 160px;">Timestamp</th>
                            <th>Message</th>
                            <th style="width: 60px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>';

if (!empty($logs['logs'])) {
    foreach ($logs['logs'] as $index => $log) {
        $levelClass = '';
        $levelIcon = '';
        
        switch ($log['level']) {
            case 'fatal':
            case 'error':
                $levelClass = 'danger';
                $levelIcon = 'fa-times-circle';
                break;
            case 'warning':
                $levelClass = 'warning';
                $levelIcon = 'fa-exclamation-triangle';
                break;
            case 'notice':
                $levelClass = 'info';
                $levelIcon = 'fa-info-circle';
                break;
            case 'info':
                $levelClass = 'success';
                $levelIcon = 'fa-check-circle';
                break;
            default:
                $levelClass = 'secondary';
                $levelIcon = 'fa-cog';
        }
        
        $content .= '
                        <tr class="log-row" data-level="' . $log['level'] . '">
                            <td>
                                <span class="log-level level-' . $levelClass . '">
                                    <i class="fas ' . $levelIcon . '"></i>
                                    ' . strtoupper($log['level']) . '
                                </span>
                            </td>
                            <td class="log-timestamp">' . htmlspecialchars($log['timestamp']) . '</td>
                            <td class="log-message">
                                <div class="message-preview">' . htmlspecialchars(substr($log['message'], 0, 100)) . '</div>
                                <div class="message-full" style="display: none;">' . htmlspecialchars($log['message']) . '</div>
                            </td>
                            <td class="log-actions">
                                <button onclick="toggleMessage(' . $index . ')" class="btn btn-sm btn-secondary" title="Toggle Full Message">
                                    <i class="fas fa-expand"></i>
                                </button>
                            </td>
                        </tr>';
    }
} else {
    $content .= '
                        <tr>
                            <td colspan="4" class="no-logs">
                                <div style="text-align: center; padding: 40px; color: #666;">
                                    <i class="fas fa-check-circle" style="font-size: 48px; margin-bottom: 16px; color: #10b981;"></i>
                                    <h3>No Error Logs Found</h3>
                                    <p>Your system is running smoothly with no recorded errors.</p>
                                </div>
                            </td>
                        </tr>';
}

$content .= '
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Pagination -->
    <div class="pagination-container" style="margin-top: 32px;">';

if ($logs['pages'] > 1) {
    $content .= '<div class="pagination">';
    
    // Previous button
    if ($current_page > 1) {
        $content .= '<a href="?page=' . ($current_page - 1) . '&filter=' . $filter . '" class="btn btn-secondary">
            <i class="fas fa-chevron-left"></i> Previous
        </a>';
    }
    
    // Page numbers
    $start = max(1, $current_page - 2);
    $end = min($logs['pages'], $current_page + 2);
    
    for ($i = $start; $i <= $end; $i++) {
        $activeClass = $i === $current_page ? 'btn-primary' : 'btn-secondary';
        $content .= '<a href="?page=' . $i . '&filter=' . $filter . '" class="btn ' . $activeClass . '">' . $i . '</a>';
    }
    
    // Next button
    if ($current_page < $logs['pages']) {
        $content .= '<a href="?page=' . ($current_page + 1) . '&filter=' . $filter . '" class="btn btn-secondary">
            Next <i class="fas fa-chevron-right"></i>
        </a>';
    }
    
    $content .= '</div>';
}

$content .= '
    </div>
    
</div>

<script>
// Log management functions
function filterLogs(level) {
    window.location.href = "?page=1&filter=" + level;
}

function refreshLogs() {
    window.location.reload();
}

function toggleMessage(index) {
    const row = document.querySelectorAll(".log-row")[index];
    const preview = row.querySelector(".message-preview");
    const full = row.querySelector(".message-full");
    const button = row.querySelector(".log-actions button");
    
    if (full.style.display === "none") {
        preview.style.display = "none";
        full.style.display = "block";
        button.innerHTML = \'<i class="fas fa-compress"></i>\';
        button.title = "Show Preview";
    } else {
        preview.style.display = "block";
        full.style.display = "none";
        button.innerHTML = \'<i class="fas fa-expand"></i>\';
        button.title = "Show Full Message";
    }
}

async function clearLogs() {
    if (!confirm("Are you sure you want to clear all error logs? This action cannot be undone.")) {
        return;
    }
    
    try {
        const response = await fetch("/admin/debug/clear-logs", {
            method: "POST"
        });
        
        const result = await response.json();
        
        if (result.success) {
            AdminApp.showNotification("Error logs cleared successfully", "success");
            setTimeout(() => window.location.reload(), 1500);
        } else {
            AdminApp.showNotification("Failed to clear logs: " + result.error, "error");
        }
    } catch (error) {
        AdminApp.showNotification("Network error occurred", "error");
    }
}

function downloadLogs() {
    window.open("/admin/debug/download-logs", "_blank");
}

// Auto-refresh every 30 seconds
setInterval(() => {
    if (document.hidden === false) {
        const indicator = document.createElement("div");
        indicator.style.cssText = "position: fixed; top: 20px; right: 20px; background: var(--admin-info); color: white; padding: 8px 12px; border-radius: 4px; z-index: 9999; font-size: 12px;";
        indicator.textContent = "Refreshing logs...";
        document.body.appendChild(indicator);
        
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }
}, 30000);
</script>

<style>
.log-controls {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: var(--admin-shadow);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.controls-left {
    display: flex;
    gap: 16px;
    align-items: center;
}

.controls-right {
    display: flex;
    gap: 12px;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-group label {
    font-weight: 500;
    color: var(--admin-gray-700);
}

.log-stats .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.log-table-container {
    overflow-x: auto;
}

.log-table {
    width: 100%;
    border-collapse: collapse;
}

.log-table th {
    background: var(--admin-gray-50);
    padding: 16px;
    text-align: left;
    font-weight: 600;
    color: var(--admin-gray-700);
    border-bottom: 2px solid var(--admin-gray-200);
}

.log-table td {
    padding: 12px 16px;
    border-bottom: 1px solid var(--admin-gray-200);
    vertical-align: top;
}

.log-row:hover {
    background: var(--admin-gray-50);
}

.log-level {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.log-level.level-danger {
    background: rgba(239, 68, 68, 0.1);
    color: var(--admin-danger);
}

.log-level.level-warning {
    background: rgba(245, 158, 11, 0.1);
    color: var(--admin-warning);
}

.log-level.level-info {
    background: rgba(59, 130, 246, 0.1);
    color: var(--admin-info);
}

.log-level.level-success {
    background: rgba(16, 185, 129, 0.1);
    color: var(--admin-success);
}

.log-level.level-secondary {
    background: var(--admin-gray-100);
    color: var(--admin-gray-600);
}

.log-timestamp {
    font-family: monospace;
    font-size: 12px;
    color: var(--admin-gray-600);
}

.log-message {
    font-family: monospace;
    font-size: 13px;
    line-height: 1.4;
}

.message-preview {
    color: var(--admin-gray-800);
}

.message-full {
    color: var(--admin-gray-800);
    white-space: pre-wrap;
    word-break: break-all;
}

.log-actions {
    text-align: center;
}

.pagination {
    display: flex;
    gap: 8px;
    justify-content: center;
    align-items: center;
}

.pagination .btn {
    min-width: 40px;
    text-align: center;
}

.log-info {
    font-size: 14px;
    color: var(--admin-gray-600);
}

@media (max-width: 768px) {
    .log-controls {
        flex-direction: column;
        gap: 16px;
    }
    
    .controls-left,
    .controls-right {
        width: 100%;
        justify-content: center;
    }
    
    .log-table th:nth-child(2),
    .log-table td:nth-child(2) {
        display: none;
    }
}
</style>
';

// Set breadcrumbs
$breadcrumbs = [
    ['title' => 'Debug', 'url' => '/admin/debug'],
    ['title' => 'Error Logs']
];

// Include the layout
include __DIR__ . '/../../layouts/main.php';
?>
