<?php

/**
 * System Status View
 */
?>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">System Status</h1>
        <a href="<?php echo app_base_url('/admin/debug'); ?>" class="btn btn-sm btn-info shadow-sm">
            <i class="fas fa-bug fa-sm text-white-50"></i> Advanced Debug
        </a>
    </div>

    <div class="row">
        <!-- Server Info -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Server Information</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <tbody>
                                <tr>
                                    <td class="bg-light font-weight-bold" width="40%">PHP Version</td>
                                    <td><?= phpversion() ?></td>
                                </tr>
                                <tr>
                                    <td class="bg-light font-weight-bold">Server Software</td>
                                    <td><?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?></td>
                                </tr>
                                <tr>
                                    <td class="bg-light font-weight-bold">Database Driver</td>
                                    <td><?= $systemInfo['database_driver'] ?? 'Unknown' ?></td>
                                </tr>
                                <tr>
                                    <td class="bg-light font-weight-bold">Server OS</td>
                                    <td><?= PHP_OS ?></td>
                                </tr>
                                <tr>
                                    <td class="bg-light font-weight-bold">Memory Limit</td>
                                    <td><?= ini_get('memory_limit') ?></td>
                                </tr>
                                <tr>
                                    <td class="bg-light font-weight-bold">Max Execution Time</td>
                                    <td><?= ini_get('max_execution_time') ?>s</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Module Status -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Module Status</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Module</th>
                                    <th>Status</th>
                                    <th>Version</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($moduleStatus)): ?>
                                    <?php foreach ($moduleStatus as $module): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($module['name']) ?></td>
                                            <td>
                                                <?php if ($module['active']): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($module['version'] ?? 'N/A') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No modules found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Directory Permissions -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Directory Permissions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php
                        $directories = [
                            'storage' => is_writable(__DIR__ . '/../../../storage'),
                            'storage/logs' => is_writable(__DIR__ . '/../../../storage/logs'),
                            'storage/cache' => is_writable(__DIR__ . '/../../../storage/cache'),
                            'storage/uploads' => is_writable(__DIR__ . '/../../../storage/uploads'),
                        ];
                        ?>
                        <?php foreach ($directories as $dir => $writable): ?>
                            <div class="col-md-3 mb-3">
                                <div class="card border-left-<?= $writable ? 'success' : 'danger' ?> shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-<?= $writable ? 'success' : 'danger' ?> text-uppercase mb-1">
                                                    <?= htmlspecialchars($dir) ?>
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    <?= $writable ? 'Writable' : 'Not Writable' ?>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas <?= $writable ? 'fa-check-circle' : 'fa-times-circle' ?> fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>