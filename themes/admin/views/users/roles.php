<?php
$page_title = $page_title ?? 'User Roles Management';
$roles = $roles ?? [];
$role_stats = $role_stats ?? [];
?>

<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-user-tag"></i>
            <?php echo htmlspecialchars($page_title); ?>
        </h1>
        <p class="page-description">Configure user roles and their associated permissions</p>
    </div>

    <!-- Stats Overview -->
    <div class="stats-grid">
        <?php foreach ($roles as $role_key => $role_info): ?>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon <?php echo $role_key === 'admin' ? 'warning' : 'primary'; ?>">
                        <i class="fas fa-user-<?php echo $role_key === 'admin' ? 'shield' : 'user'; ?>"></i>
                    </div>
                </div>
                <div class="stat-value"><?php echo $role_stats[$role_key] ?? 0; ?></div>
                <div class="stat-label"><?php echo htmlspecialchars($role_info['name']); ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Roles Table -->
    <div class="table-container">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-cogs"></i>
                Role Configuration
            </h3>
        </div>
        <div class="card-content">
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>Description</th>
                            <th>User Count</th>
                            <th>Permissions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $role_key => $role_info): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--admin-<?php echo $role_key === 'admin' ? 'warning' : 'primary'; ?>); color: white; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-user-<?php echo $role_key === 'admin' ? 'shield' : 'user'; ?>"></i>
                                        </div>
                                        <strong><?php echo htmlspecialchars($role_info['name']); ?></strong>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($role_info['description']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $role_key === 'admin' ? 'warning' : 'primary'; ?>">
                                        <?php echo $role_stats[$role_key] ?? 0; ?> users
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $permissions_map = [
                                        'admin' => ['Full System Access', 'User Management', 'System Configuration'],
                                        'user' => ['Calculator Access', 'Profile Management'],
                                        'engineer' => ['Calculator Access', 'Profile Management', 'Advanced Tools']
                                    ];
                                    $permissions = $permissions_map[$role_key] ?? [];
                                    foreach ($permissions as $permission): ?>
                                        <span class="badge badge-secondary" style="margin: 2px;"><?php echo htmlspecialchars($permission); ?></span>
                                    <?php endforeach; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editRole('<?php echo $role_key; ?>')">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card" style="margin-top: 24px;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-bolt"></i>
                Quick Actions
            </h3>
        </div>
        <div class="card-content">
            <div style="display: flex; gap: 16px; flex-wrap: wrap;">
                <button class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    <script>
                        function editRole(roleKey) {
                            // Implement role editing functionality
                            console.log('Edit role:', roleKey);
                            alert('Role editing functionality would be implemented here for: ' + roleKey);
                        }
                    </script>