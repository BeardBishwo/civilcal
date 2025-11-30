<?php

/**
 * Admin User Permissions View
 * Path: app/Views/admin/users/permissions.php
 */
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">User Permissions</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?= app_base_url('/admin/dashboard') ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?= app_base_url('/admin/users') ?>">Users</a></li>
                            <li class="breadcrumb-item active">Permissions</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="<?= app_base_url('/admin/users/roles') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Roles
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions Matrix -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Permissions Matrix</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Permission</th>
                            <th class="text-center">Administrator</th>
                            <th class="text-center">Engineer</th>
                            <th class="text-center">Regular User</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $allPermissions = [
                            'manage_users' => 'Manage Users',
                            'manage_system' => 'Manage System Settings',
                            'view_analytics' => 'View Analytics',
                            'manage_modules' => 'Manage Modules',
                            'use_calculators' => 'Use Calculators',
                            'view_profile' => 'View Profile',
                            'advanced_tools' => 'Access Advanced Tools',
                            'manage_content' => 'Manage Content',
                            'view_logs' => 'View System Logs'
                        ];
                        ?>
                        <?php foreach ($allPermissions as $permKey => $permName): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($permName) ?></strong></td>
                                <td class="text-center">
                                    <?php if (in_array($permKey, $permissions['admin'] ?? [])): ?>
                                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <?php else: ?>
                                        <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if (in_array($permKey, $permissions['engineer'] ?? [])): ?>
                                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <?php else: ?>
                                        <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if (in_array($permKey, $permissions['user'] ?? [])): ?>
                                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <?php else: ?>
                                        <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Permission Details -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0"><i class="bi bi-shield-check me-2"></i>Administrator</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Full system access with all permissions</p>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($permissions['admin'] ?? [] as $perm): ?>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <small><?= ucwords(str_replace('_', ' ', $perm)) ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h6 class="mb-0"><i class="bi bi-tools me-2"></i>Engineer</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Access to engineering tools and calculators</p>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($permissions['engineer'] ?? [] as $perm): ?>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <small><?= ucwords(str_replace('_', ' ', $perm)) ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="bi bi-person me-2"></i>Regular User</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Standard user access to basic features</p>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($permissions['user'] ?? [] as $perm): ?>
                            <li class="mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                <small><?= ucwords(str_replace('_', ' ', $perm)) ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="alert alert-info mt-4" role="alert">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Note:</strong> Permissions are role-based and automatically assigned when a user is given a specific role.
        To change a user's permissions, update their role in the user management section.
    </div>
</div>