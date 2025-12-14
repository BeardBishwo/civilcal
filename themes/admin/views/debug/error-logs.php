<?php
/**
 * Error Logs View
 */
$page_title = 'Error Logs';
$breadcrumbs = [
    ['title' => 'Debug', 'url' => app_base_url('/admin/debug')],
    ['title' => 'Error Logs']
];
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-file-alt"></i>
                    <h1>Error Logs</h1>
                </div>
                <div class="header-subtitle">View and manage system error logs for debugging</div>
            </div>
            <div class="header-actions">
                <button onclick="downloadLogs()" class="btn btn-outline-secondary btn-compact">
                    <i class="fas fa-download"></i> <span>Download</span>
                </button>
                <button onclick="clearLogs()" class="btn btn-danger btn-compact">
                    <i class="fas fa-trash"></i> <span>Clear Logs</span>
                </button>
            </div>
        </div>

        <!-- Compact Stats Cards -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= count(array_filter($logs['logs'], fn($l) => $l['level'] === 'error')) ?></div>
                    <div class="stat-label">Errors</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= count(array_filter($logs['logs'], fn($l) => $l['level'] === 'warning')) ?></div>
                    <div class="stat-label">Warnings</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= $logs['total'] ?></div>
                    <div class="stat-label">Total Entries</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?= $logs['pages'] ?></div>
                    <div class="stat-label">Pages</div>
                </div>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <div class="filter-group">
                    <select id="logFilter" onchange="filterLogs(this.value)" class="form-control-compact" style="width: 150px;">
                        <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All Levels</option>
                        <option value="error" <?= $filter === 'error' ? 'selected' : '' ?>>Errors Only</option>
                        <option value="warning" <?= $filter === 'warning' ? 'selected' : '' ?>>Warnings Only</option>
                        <option value="notice" <?= $filter === 'notice' ? 'selected' : '' ?>>Notices Only</option>
                        <option value="info" <?= $filter === 'info' ? 'selected' : '' ?>>Info Only</option>
                    </select>
                </div>
            </div>
            <div class="toolbar-right">
                <button onclick="refreshLogs()" class="btn btn-outline-secondary btn-compact">
                    <i class="fas fa-sync"></i> <span>Refresh</span>
                </button>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="settings-card">
            <div class="settings-card-body" style="padding: 0;">
                <table class="table-compact">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Level</th>
                            <th style="width: 180px;">Timestamp</th>
                            <th>Message</th>
                            <th style="width: 80px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($logs['logs'])): ?>
                            <?php foreach ($logs['logs'] as $index => $log): 
                                $levelClass = '';
                                $levelIcon = '';
                                switch ($log['level']) {
                                    case 'fatal':
                                    case 'error': $levelClass = 'danger'; $levelIcon = 'fa-times-circle'; break;
                                    case 'warning': $levelClass = 'warning'; $levelIcon = 'fa-exclamation-triangle'; break;
                                    case 'notice': $levelClass = 'info'; $levelIcon = 'fa-info-circle'; break;
                                    case 'info': $levelClass = 'success'; $levelIcon = 'fa-check-circle'; break;
                                    default: $levelClass = 'secondary'; $levelIcon = 'fa-cog';
                                }
                            ?>
                            <tr class="log-row">
                                <td>
                                    <span class="status-badge status-<?= $levelClass ?>">
                                        <i class="fas <?= $levelIcon ?>"></i> <?= strtoupper($log['level']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="log-timestamp"><?= htmlspecialchars($log['timestamp']) ?></span>
                                </td>
                                <td>
                                    <div class="message-preview"><?= htmlspecialchars(substr($log['message'], 0, 100)) . (strlen($log['message']) > 100 ? '...' : '') ?></div>
                                    <div class="message-full" style="display: none;">
                                        <strong>Message:</strong><br><?= htmlspecialchars($log['message']) ?>
                                        <?php if (!empty($log['context'])): ?>
                                            <br><br><strong>Context:</strong><br>
                                            <pre style="background: rgba(0,0,0,0.05); padding: 8px; border-radius: 4px; font-size: 11px;"><?= htmlspecialchars(json_encode($log['context'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) ?></pre>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td style="text-align: right;">
                                    <div class="actions-compact" style="justify-content: flex-end;">
                                        <button onclick="toggleMessage(<?= $index ?>)" class="action-btn-icon" title="Toggle Details">
                                            <i class="fas fa-expand"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <i class="fas fa-check-circle"></i>
                                        <h3>No Logs Found</h3>
                                        <p>System is running smoothly.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($logs['pages'] > 1): ?>
        <div class="pagination-container">
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?= $current_page - 1 ?>&filter=<?= $filter ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php
                $start = max(1, $current_page - 2);
                $end = min($logs['pages'], $current_page + 2);
                for ($i = $start; $i <= $end; $i++):
                    $activeClass = $i === $current_page ? 'btn-primary' : 'btn-outline-secondary';
                ?>
                    <a href="?page=<?= $i ?>&filter=<?= $filter ?>" class="btn <?= $activeClass ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php if ($current_page < $logs['pages']): ?>
                    <a href="?page=<?= $current_page + 1 ?>&filter=<?= $filter ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<script>
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
    const icon = row.querySelector(".action-btn-icon i");
    
    if (full.style.display === "none") {
        preview.style.display = "none";
        full.style.display = "block";
        icon.classList.remove("fa-expand");
        icon.classList.add("fa-compress");
    } else {
        preview.style.display = "block";
        full.style.display = "none";
        icon.classList.remove("fa-compress");
        icon.classList.add("fa-expand");
    }
}

async function clearLogs() {
    showConfirmModal('Clear Logs', "Are you sure you want to clear all error logs?", async () => {
        try {
            const response = await fetch("<?= app_base_url('/admin/debug/clear-logs') ?>", { method: "POST" });
            const result = await response.json();
            
            if (result.success) {
                showNotification("Logs cleared", "success");
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showNotification("Failed to clear logs", "error");
            }
        } catch (e) {
            showNotification("Network error", "error");
        }
    });
}

function downloadLogs() {
    window.open("<?= app_base_url('/admin/debug/download-logs') ?>", "_blank");
}
</script>
