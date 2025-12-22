<?php
/**
 * Live Error Monitor View
 */
$page_title = 'Live Error Monitor';
$breadcrumbs = [
    ['title' => 'Debug', 'url' => app_base_url('/admin/debug')],
    ['title' => 'Live Monitor']
];
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-broadcast-tower"></i>
                    <h1>Live Monitor</h1>
                </div>
                <div class="header-subtitle">Real-time system error tracking</div>
            </div>
            <div class="header-actions">
                <div id="connectionStatus" class="status-badge status-success" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                    <i class="fas fa-circle"></i> Connected
                </div>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="compact-toolbar" style="justify-content: flex-end;">
            <button id="clearMonitor" class="btn btn-outline-danger btn-compact">
                <i class="fas fa-trash"></i> <span>Clear Console</span>
            </button>
        </div>

        <!-- Monitor Console -->
        <div class="settings-card">
            <div class="settings-card-body p-0">
                <div id="errorContainer" class="monitor-console">
                    <div class="console-empty">
                        <i class="fas fa-satellite-dish"></i>
                        <p>Monitoring for errors...</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('errorContainer');
    const statusBadge = document.getElementById('connectionStatus');
    let lastCheck = '<?= date('Y-m-d H:i:s') ?>';
    let isPolling = true;

    function addLogEntry(error) {
        // Remove empty state if present
        if (container.querySelector('.console-empty')) {
            container.innerHTML = '';
        }

        const div = document.createElement('div');
        div.className = 'log-entry';

        let colorClass = 'text-console-info';
        if (error.level === 'error' || error.level === 'fatal') colorClass = 'text-console-danger';
        else if (error.level === 'warning') colorClass = 'text-console-warning';
        else if (error.level === 'success') colorClass = 'text-console-success';

        div.innerHTML = `
            <div class="log-entry-header">
                <span class="timestamp">[${error.timestamp}]</span>
                <span style="font-weight: bold;">${error.level.toUpperCase()}</span>
            </div>
            <div class="log-entry-message ${colorClass}">${error.message}</div>
        `;

        container.insertBefore(div, container.firstChild);
    }

    function updateStatus(connected) {
        if (connected) {
            statusBadge.className = 'status-badge status-success';
            statusBadge.innerHTML = '<i class="fas fa-circle"></i> Connected';
        } else {
            statusBadge.className = 'status-badge status-danger';
            statusBadge.innerHTML = '<i class="fas fa-times-circle"></i> Disconnected';
        }
    }

    function poll() {
        if (!isPolling) return;

        const csrfToken = '<?= csrf_token() ?>';
        fetch('<?= app_base_url('/admin/debug/live-errors') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `since=${lastCheck}&csrf_token=${encodeURIComponent(csrfToken)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateStatus(true);

                    if (data.errors && data.errors.length > 0) {
                        data.errors.forEach(addLogEntry);
                        // Update last check time
                        lastCheck = new Date().toISOString().slice(0, 19).replace('T', ' ');
                    }
                } else {
                    throw new Error(data.error);
                }
            })
            .catch(err => {
                console.error('Polling error:', err);
                updateStatus(false);
            })
            .finally(() => {
                setTimeout(poll, 3000);
            });
    }

    // Start polling
    poll();

    // Clear button
    document.getElementById('clearMonitor').addEventListener('click', function() {
        container.innerHTML = `
        <div class="console-empty">
            <i class="fas fa-satellite-dish"></i>
            <p>Monitoring for errors...</p>
        </div>
    `;
    });
});
</script>
