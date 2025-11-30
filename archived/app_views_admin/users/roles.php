<?php

/**
 * Admin User Roles View
 * Path: app/Views/admin/users/roles.php
 */
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">User Roles Management</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?= app_base_url('/admin/dashboard') ?>">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="<?= app_base_url('/admin/users') ?>">Users</a></li>
                            <li class="breadcrumb-item active">Roles</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="<?= app_base_url('/admin/users') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Cards -->
    <div class="row">
        <?php foreach ($roles as $roleKey => $role): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header <?= $roleKey === 'admin' ? 'bg-danger text-white' : ($roleKey === 'engineer' ? 'bg-warning' : 'bg-secondary text-white') ?>">
                        <h5 class="mb-0">
                            <i class="bi bi-<?= $roleKey === 'admin' ? 'shield-check' : ($roleKey === 'engineer' ? 'tools' : 'person') ?> me-2"></i>
                            <?= htmlspecialchars($role['name']) ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted"><?= htmlspecialchars($role['description']) ?></p>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Users with this role:</span>
                            <span class="badge bg-primary fs-6"><?= $role_stats[$roleKey] ?? 0 ?></span>
                        </div>

                        <hr>

                        <h6 class="mb-2">Permissions:</h6>
                        <ul class="list-unstyled mb-0">
                            <?php
                            $permissions = match ($roleKey) {
                                'admin' => [
                                    'Full system access',
                                    'Manage users',
                                    'Manage calculators',
                                    'View analytics',
                                    'System settings'
                                ],
                                'engineer' => [
                                    'Use all calculators',
                                    'Advanced tools',
                                    'View profile',
                                    'Save calculations'
                                ],
                                default => [
                                    'Use basic calculators',
                                    'View profile',
                                    'Save calculations'
                                ]
                            };
                            ?>
                            <?php foreach ($permissions as $permission): ?>
                                <li class="mb-1">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    <?= htmlspecialchars($permission) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="<?= app_base_url('/admin/users/permissions') ?>" class="btn btn-sm btn-outline-primary w-100">
                            <i class="bi bi-gear me-2"></i>Manage Permissions
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Role Statistics -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Role Distribution</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <canvas id="roleDistributionChart"></canvas>
                </div>
                <div class="col-md-4">
                    <h6 class="mb-3">Role Summary</h6>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th class="text-end">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($roles as $roleKey => $role): ?>
                                <tr>
                                    <td><?= htmlspecialchars($role['name']) ?></td>
                                    <td class="text-end">
                                        <span class="badge bg-primary"><?= $role_stats[$roleKey] ?? 0 ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Role Distribution Chart
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('roleDistributionChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode(array_map(fn($r) => $r['name'], $roles)) ?>,
                    datasets: [{
                        data: <?= json_encode(array_values($role_stats)) ?>,
                        backgroundColor: [
                            '#dc3545',
                            '#ffc107',
                            '#6c757d'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    });
</script>