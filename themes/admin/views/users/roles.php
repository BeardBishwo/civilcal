<?php
$page_title = $page_title ?? 'User Roles Management';
$roles = $roles ?? [];
$role_stats = $role_stats ?? [];
?>

<div class="page-create-container">
    <div class="page-create-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-create-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-user-tag"></i>
                    <h1><?php echo htmlspecialchars($page_title); ?></h1>
                </div>
                <div class="header-subtitle">
                    Configure user roles and their associated permissions
                </div>
            </div>
            <div class="header-actions">
                <a href="<?= app_base_url('/admin/users') ?>" class="btn btn-secondary btn-compact">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Users</span>
                </a>
            </div>
        </div>

        <!-- Premium Stats Grid -->
        <div class="stats-grid-premium">
            <?php foreach ($roles as $role_key => $role_info): ?>
                <?php 
                    $is_admin = $role_key === 'admin';
                    $count = $role_stats[$role_key] ?? 0;
                    $bg_class = $is_admin ? 'bg-warning-soft' : ($role_key === 'engineer' ? 'bg-info-soft' : 'bg-primary-soft');
                    $icon_color = $is_admin ? 'text-warning' : ($role_key === 'engineer' ? 'text-info' : 'text-primary');
                ?>
                <div class="stat-card-premium">
                    <div class="stat-icon-wrapper <?php echo $bg_class; ?>">
                        <i class="fas fa-user-<?php echo $is_admin ? 'shield' : 'user'; ?> <?php echo $icon_color; ?>"></i>
                    </div>
                    <div>
                        <div class="stat-value-premium"><?php echo $count; ?></div>
                        <div class="stat-label-premium"><?php echo htmlspecialchars($role_info['name']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Roles Table Card -->
        <div class="content-card" style="margin-top: 2rem;">
            <div class="card-header-clean">
                <h3 class="card-title">
                    <i class="fas fa-cogs"></i>
                    Role Configuration
                </h3>
            </div>
            <div class="card-body-clean p-0">
                <div class="table-responsive">
                    <table class="table-premium">
                        <thead>
                            <tr>
                                <th>Role Name</th>
                                <th>Description</th>
                                <th class="text-center">Users</th>
                                <th>Permissions</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($roles as $role_key => $role_info): ?>
                                <tr>
                                    <td>
                                        <div class="role-cell">
                                            <div class="role-icon-small <?php echo $role_key === 'admin' ? 'bg-warning-soft text-warning' : 'bg-primary-soft text-primary'; ?>">
                                                <i class="fas fa-user-<?php echo $role_key === 'admin' ? 'shield' : 'user'; ?>"></i>
                                            </div>
                                            <strong><?php echo htmlspecialchars($role_info['name']); ?></strong>
                                        </div>
                                    </td>
                                    <td class="text-muted"><?php echo htmlspecialchars($role_info['description']); ?></td>
                                    <td class="text-center">
                                        <span class="badge-pill <?php echo $role_key === 'admin' ? 'badge-warning' : 'badge-primary'; ?>">
                                            <?php echo $role_stats[$role_key] ?? 0; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="permission-tags">
                                            <?php
                                            $permissions_map = [
                                                'admin' => ['Full System Access', 'User Management', 'System Configuration'],
                                                'user' => ['Calculator Access', 'Profile Management'],
                                                'engineer' => ['Calculator Access', 'Profile Management', 'Advanced Tools']
                                            ];
                                            $permissions = $permissions_map[$role_key] ?? [];
                                            foreach ($permissions as $permission): ?>
                                                <span class="badge-tag"><?php echo htmlspecialchars($permission); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <button class="btn btn-outline-secondary btn-compact" onclick="editRole('<?php echo $role_key; ?>')">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
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
    function editRole(roleKey) {
        // Implement role editing functionality
        // console.log('Edit role:', roleKey);
        // Placeholder for now
        showNotification('Edit functionality for ' + roleKey + ' coming soon.', 'info');
    }
</script>

<style>
    /* ========================================
       PREMIUM DESIGN SYSTEM (PRODUCTION READY)
       ======================================== */
    :root {
        --primary-600: #4f46e5;
        --primary-700: #4338ca;
        --primary-50: #eef2ff;

        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;

        --success-500: #10b981;
        --warning-500: #f59e0b;
        --danger-500: #ef4444;
        --info-500: #3b82f6;

        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --radius-md: 0.5rem;
        --radius-lg: 0.75rem;
    }

    /* Layout */
    .page-create-container {
        padding-bottom: 5rem;
        background-color: var(--gray-50);
        min-height: 100vh;
    }
    .page-create-wrapper {
        max-width: 1200px; /* Wider for tables */
        margin: 0 auto;
        padding: 0 1rem;
    }

    /* Header */
    .compact-create-header {
        padding: 2rem 0 1.5rem 0;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .header-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
    }
    .header-title i {
        font-size: 1.75rem;
        color: var(--primary-600);
    }
    .header-title h1 {
        margin: 0;
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--gray-900);
    }
    .header-subtitle {
        font-size: 0.9375rem;
        color: var(--gray-500);
    }

    /* Buttons */
    .btn { display: inline-flex; align-items: center; gap: 0.5rem; border: 1px solid transparent; cursor: pointer; text-decoration: none; transition: all 0.2s; }
    .btn-compact { padding: 0.5rem 1rem; font-size: 0.875rem; border-radius: 8px; font-weight: 500; }
    .btn-secondary { background: var(--gray-200); color: var(--gray-800); }
    .btn-secondary:hover { background: var(--gray-300); }
    .btn-outline-secondary { background: white; border-color: var(--gray-300); color: var(--gray-700); }
    .btn-outline-secondary:hover { background: var(--gray-50); border-color: var(--gray-400); }

    /* Stats Grid */
    .stats-grid-premium {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .stat-card-premium {
        background: white;
        padding: 1.5rem;
        border-radius: var(--radius-lg);
        border: 1px solid var(--gray-200);
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }
    .stat-icon-wrapper {
        width: 48px; height: 48px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem;
    }
    .stat-value-premium { font-size: 1.5rem; font-weight: 700; color: var(--gray-900); line-height: 1.2; }
    .stat-label-premium { font-size: 0.875rem; color: var(--gray-500); font-weight: 500; }

    /* Cards */
    .content-card { background: white; border-radius: var(--radius-lg); border: 1px solid var(--gray-200); box-shadow: var(--shadow-sm); overflow: hidden; }
    .card-header-clean { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--gray-100); background: white; }
    .card-title { font-size: 1.125rem; font-weight: 600; color: var(--gray-900); margin: 0; display: flex; align-items: center; gap: 0.625rem; }
    .card-title i { color: var(--gray-400); font-size: 1rem; }
    .card-body-clean { padding: 1.5rem; }
    .card-body-clean.p-0 { padding: 0; }

    /* Tables */
    .table-premium { width: 100%; border-collapse: collapse; }
    .table-premium th { text-align: left; padding: 1rem 1.5rem; background: var(--gray-50); color: var(--gray-500); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--gray-200); }
    .table-premium td { padding: 1rem 1.5rem; border-bottom: 1px solid var(--gray-100); color: var(--gray-700); font-size: 0.875rem; vertical-align: middle; }
    .table-premium tr:last-child td { border-bottom: none; }
    .table-premium tr:hover td { background-color: var(--primary-50); }

    .role-cell { display: flex; align-items: center; gap: 0.75rem; }
    .role-icon-small { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; }

    /* Badges & Tags */
    .badge-pill { display: inline-flex; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
    .badge-primary { background: var(--primary-50); color: var(--primary-700); }
    .badge-warning { background: #fffbeb; color: var(--warning-500); }
    
    .permission-tags { display: flex; flex-wrap: wrap; gap: 0.5rem; }
    .badge-tag { background: var(--gray-100); color: var(--gray-600); border: 1px solid var(--gray-200); padding: 0.125rem 0.5rem; border-radius: 4px; font-size: 0.75rem; }

    /* Utilities */
    .bg-primary-soft { background: var(--primary-50); }
    .text-primary { color: var(--primary-600); }
    .bg-info-soft { background: #eff6ff; }
    .text-info { color: var(--info-500); }
    .bg-warning-soft { background: #fffbeb; }
    .text-warning { color: var(--warning-500); }
    .text-muted { color: var(--gray-500); }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
</style>