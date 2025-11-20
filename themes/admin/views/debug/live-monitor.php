<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Live Error Monitor</h4>
                <div class="card-tools">
                    <span id="connectionStatus" class="badge badge-success">Connected</span>
                    <button type="button" class="btn btn-tool" id="clearMonitor">
                        <i class="fas fa-trash"></i> Clear
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="errorContainer" class="p-3" style="height: 500px; overflow-y: auto; background: #1e1e1e; color: #d4d4d4; font-family: monospace;">
                    <div class="text-center text-muted mt-5">
                        <i class="fas fa-satellite-dish fa-3x mb-3"></i>
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
            if (container.querySelector('.text-center')) {
                container.innerHTML = '';
            }

            const div = document.createElement('div');
            div.className = 'mb-2 p-2 border-bottom border-secondary';
            div.style.animation = 'fadeIn 0.5s';

            let colorClass = 'text-info';
            if (error.level === 'error' || error.level === 'fatal') colorClass = 'text-danger';
            else if (error.level === 'warning') colorClass = 'text-warning';

            div.innerHTML = `
            <div class="d-flex justify-content-between">
                <span class="text-muted small">[${error.timestamp}]</span>
                <span class="badge badge-outline-${error.level === 'error' ? 'danger' : 'info'}">${error.level.toUpperCase()}</span>
            </div>
            <div class="${colorClass} mt-1">${error.message}</div>
        `;

            container.insertBefore(div, container.firstChild);
        }

        function poll() {
            if (!isPolling) return;

            fetch('/Bishwo_Calculator/admin/debug/live-errors', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `since=${lastCheck}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        statusBadge.className = 'badge badge-success';
                        statusBadge.textContent = 'Connected';

                        if (data.errors && data.errors.length > 0) {
                            data.errors.forEach(addLogEntry);
                            // Update last check time to the most recent error or current time
                            lastCheck = new Date().toISOString().slice(0, 19).replace('T', ' ');
                        }
                    } else {
                        throw new Error(data.error);
                    }
                })
                .catch(err => {
                    console.error('Polling error:', err);
                    statusBadge.className = 'badge badge-danger';
                    statusBadge.textContent = 'Disconnected';
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
            <div class="text-center text-muted mt-5">
                <i class="fas fa-satellite-dish fa-3x mb-3"></i>
                <p>Monitoring for errors...</p>
            </div>
        `;
        });
    });
</script>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>