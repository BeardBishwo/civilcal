<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title">System Status</h1>
                <p class="text-muted">Server information and health checks.</p>
            </div>
            <button class="btn btn-primary" onclick="location.reload()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Server Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th style="width: 40%;">PHP Version</th>
                        <td><?= htmlspecialchars($php_version) ?></td>
                    </tr>
                    <tr>
                        <th>Server Software</th>
                        <td><?= htmlspecialchars($server_software) ?></td>
                    </tr>
                    <tr>
                        <th>Database Version</th>
                        <td><?= htmlspecialchars($database_version) ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Health Checks</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Database Connection
                        <?php if ($system_health['database']['status'] === 'ok'): ?>
                            <span class="badge badge-success">OK</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Error</span>
                        <?php endif; ?>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        File Permissions
                        <?php if ($system_health['permissions']['status'] === 'ok'): ?>
                            <span class="badge badge-success">OK</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Warning</span>
                        <?php endif; ?>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        PHP Extensions
                        <?php if ($system_health['extensions']['status'] === 'ok'): ?>
                            <span class="badge badge-success">OK</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Error</span>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Resource Usage</h5>
            </div>
            <div class="card-body">
                <h6 class="mb-2">Disk Space</h6>
                <div class="progress mb-2" style="height: 20px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width: <?= $disk_space['percent_used'] ?>%">
                        <?= $disk_space['percent_used'] ?>%
                    </div>
                </div>
                <small class="text-muted">
                    Used: <?= round($disk_space['used'] / 1024 / 1024 / 1024, 2) ?> GB / 
                    Total: <?= round($disk_space['total'] / 1024 / 1024 / 1024, 2) ?> GB
                </small>
                
                <h6 class="mt-4 mb-2">Memory Usage</h6>
                <table class="table table-sm">
                    <tr>
                        <td>Current</td>
                        <td><?= round($memory_usage['current'] / 1024 / 1024, 2) ?> MB</td>
                    </tr>
                    <tr>
                        <td>Peak</td>
                        <td><?= round($memory_usage['peak'] / 1024 / 1024, 2) ?> MB</td>
                    </tr>
                    <tr>
                        <td>Limit</td>
                        <td><?= $memory_usage['limit'] ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
