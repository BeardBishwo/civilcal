<?php
/**
 * PREMIUM ROLES MANAGEMENT
 * View and manage user roles and permissions.
 */
$page_title = 'User Roles';
$roles = $roles ?? [];
$role_stats = $role_stats ?? [];
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-user-tag"></i>
                    <h1>User Roles</h1>
                </div>
                <div class="header-subtitle"><?php echo count($roles); ?> Roles Configured</div>
            </div>
            
            <div class="header-actions">
                <a href="<?= app_base_url('/admin/users') ?>" class="btn-cancel-premium">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <button onclick="openRoleModal()" class="btn-save-premium">
                    <i class="fas fa-plus"></i> Add New Role
                </button>
            </div>
        </div>

        <!-- Premium Stats Grid -->
        <div class="stats-grid-premium">
            <?php foreach ($roles as $role_key => $role_info): ?>
                <?php 
                    $is_admin = $role_key === 'admin';
                    $count = $role_stats[$role_key] ?? 0;
                    $bg_class = $is_admin ? 'warning' : ($role_key === 'engineer' ? 'info' : ($role_key === 'user' ? 'primary' : 'secondary'));
                ?>
                <div class="stat-card-premium">
                    <div class="stat-icon-wrapper bg-<?= $bg_class ?>-soft">
                        <i class="fas fa-user-<?= $is_admin ? 'shield' : 'tag' ?> text-<?= $bg_class ?>"></i>
                    </div>
                    <div>
                        <div class="stat-value-premium"><?php echo $count; ?></div>
                        <div class="stat-label-premium"><?php echo htmlspecialchars($role_info['name']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Roles Table Card -->
        <div class="content-card mt-4">
            <div class="card-header-clean">
                <h3><i class="fas fa-layer-group"></i> Role Configuration</h3>
            </div>
            <div class="card-body-clean p-0">
                <div class="table-responsive">
                    <table class="table-premium">
                        <thead>
                            <tr>
                                <th>Role Identity</th>
                                <th>Description</th>
                                <th class="text-center">Users</th>
                                <th>Permissions</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($roles as $role_key => $role_info): 
                                $is_admin = $role_key === 'admin';
                                $bg_class = $is_admin ? 'warning' : ($role_key === 'engineer' ? 'info' : ($role_key === 'user' ? 'primary' : 'secondary'));
                            ?>
                                <tr>
                                    <td>
                                        <div class="role-cell">
                                            <div class="role-icon-small bg-<?= $bg_class ?>-soft text-<?= $bg_class ?>">
                                                <i class="fas fa-user-<?= $is_admin ? 'shield' : 'tag' ?>"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-800"><?php echo htmlspecialchars($role_info['name']); ?></div>
                                                <div class="text-xs text-gray-500">Key: <?php echo $role_key; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-sm text-gray-600"><?php echo htmlspecialchars($role_info['description']); ?></td>
                                    <td class="text-center">
                                        <span class="badge-pill badge-<?= $bg_class ?>">
                                            <?php echo $role_stats[$role_key] ?? 0; ?> Users
                                        </span>
                                    </td>
                                    <td>
                                        <div class="permission-tags">
                                            <?php
                                            // Simulated Permissions - Replace with dynamic if available
                                            $permissions_map = [
                                                'admin' => ['All Access', 'System Config', 'User Mgr'],
                                                'user' => ['Profile', 'Basic Calculators'],
                                                'engineer' => ['Profile', 'Adv. Calculators', 'Project Mgr'],
                                                'editor' => ['Content Editor', 'Blog Mgr']
                                            ];
                                            $permissions = $permissions_map[$role_key] ?? ['Basic Access'];
                                            foreach ($permissions as $perm): ?>
                                                <span class="badge-tag"><?php echo htmlspecialchars($perm); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <button class="action-btn-icon text-indigo" onclick='openRoleModal(<?= json_encode($role_info + ["key" => $role_key]) ?>)' title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if(!$is_admin && $role_key !== 'user'): ?>
                                                <button class="action-btn-icon text-red" onclick="deleteRole('<?= $role_key ?>')" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openRoleModal(role = null) {
        const isEdit = !!role;
        Swal.fire({
            title: isEdit ? 'Edit Role' : 'Add New Role',
            html: `
                <div class="text-left">
                    <label class="block text-sm font-bold mb-1">Role Name</label>
                    <input id="swal-name" class="swal2-input" placeholder="e.g. Senior Editor" value="${role ? role.name : ''}" style="margin: 0 0 1rem 0; width:100%;">
                    
                    <label class="block text-sm font-bold mb-1">Role Key (slug)</label>
                    <input id="swal-key" class="swal2-input" placeholder="e.g. senior_editor" value="${role ? role.key : ''}" ${isEdit ? 'disabled' : ''} style="margin: 0 0 1rem 0; width:100%;">
                    
                    <label class="block text-sm font-bold mb-1">Description</label>
                    <textarea id="swal-desc" class="swal2-textarea" placeholder="Describe the role..." style="margin: 0; width:100%;">${role ? role.description : ''}</textarea>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: isEdit ? 'Update Role' : 'Create Role',
            confirmButtonColor: '#667eea',
            preConfirm: () => {
                return {
                    name: document.getElementById('swal-name').value,
                    key: document.getElementById('swal-key').value,
                    description: document.getElementById('swal-desc').value
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value;
                if(!data.name || !data.key) return Swal.fire('Error', 'Name and Key are required', 'error');

                const endpoint = isEdit 
                    ? `<?= app_base_url('/admin/users/roles/') ?>${data.key}/update`
                    : `<?= app_base_url('/admin/users/roles/store') ?>`;

                // Simulate API call since backend might not exist yet
                console.log('Saving role:', data);
                
                // For demonstration/frontend-verification purposes:
                Swal.fire({
                    icon: 'success', 
                    title: 'Saved', 
                    text: 'Role has been saved (Simulated)',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => location.reload());

                /* UNCOMMENT WHEN BACKEND IS READY
                fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content },
                    body: JSON.stringify(data)
                })
                .then(r => r.json())
                .then(d => {
                    if(d.success) location.reload();
                    else Swal.fire('Error', d.message, 'error');
                });
                */
            }
        });
    }

    function deleteRole(key) {
        Swal.fire({
            title: 'Delete Role?',
            text: "This cannot be undone. Users with this role may lose access.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, Delete'
        }).then((result) => {
            if (result.isConfirmed) {
                 // Simulate API
                 Swal.fire('Deleted', 'Role deleted (Simulated)', 'success').then(() => location.reload());
            }
        });
    }
</script>

<style>
/* CORE SYSTEM */
:root { --admin-primary: #667eea; --admin-secondary: #764ba2; --admin-bg: #f8f9fa; }
body { background: var(--admin-bg); font-family: 'Inter', sans-serif; }
.admin-wrapper-container { padding: 1rem; max-width: 1200px; margin: 0 auto; }

/* Header */
.compact-header { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem; background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%); color: white; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
.header-title { display: flex; align-items: center; gap: 0.75rem; }
.header-title h1 { margin: 0; font-size: 1.5rem; font-weight: 700; color: white; }
.header-title i { font-size: 1.25rem; opacity: 0.9; }
.header-subtitle { font-size: 0.85rem; opacity: 0.8; margin-top: 4px; font-weight: 500; }

.header-actions { display: flex; gap: 10px; }
.btn-save-premium { background: white; color: var(--admin-primary); border: none; padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
.btn-save-premium:hover { background: #f8fafc; transform: translateY(-1px); box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.btn-cancel-premium { background: rgba(255,255,255,0.2); color: white; padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 600; text-decoration: none; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; }
.btn-cancel-premium:hover { background: rgba(255,255,255,0.3); }

/* Stats Grid */
.stats-grid-premium { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
.stat-card-premium { background: white; padding: 1.25rem; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 2px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 1rem; }
.stat-icon-wrapper { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
.stat-value-premium { font-size: 1.5rem; font-weight: 700; color: #1e293b; line-height: 1; margin-bottom: 4px; }
.stat-label-premium { font-size: 0.85rem; color: #64748b; font-weight: 500; }

/* Colors */
.bg-primary-soft { background: #e0e7ff; } .text-primary { color: #4338ca; }
.bg-warning-soft { background: #fef3c7; } .text-warning { color: #d97706; }
.bg-info-soft { background: #dbeafe; } .text-info { color: #2563eb; }
.bg-secondary-soft { background: #f1f5f9; } .text-secondary { color: #475569; }

/* Card & Table */
.content-card { background: white; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; }
.card-header-clean { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; background: white; }
.card-header-clean h3 { margin: 0; font-size: 1.1rem; font-weight: 700; color: #334155; display: flex; align-items: center; gap: 8px; }
.card-header-clean i { color: #94a3b8; }

.table-premium { width: 100%; border-collapse: collapse; }
.table-premium th { text-align: left; padding: 1rem 1.5rem; background: #f8fafc; color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; }
.table-premium td { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #334155; font-size: 0.9rem; }
.table-premium tr:last-child td { border-bottom: none; }

.role-cell { display: flex; align-items: center; gap: 1rem; }
.role-icon-small { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; }

/* Badges */
.badge-pill { display: inline-flex; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 700; white-space: nowrap; }
.badge-warning { background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
.badge-primary { background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }
.badge-info { background: #f0f9ff; color: #0369a1; border: 1px solid #e0f2fe; }
.badge-secondary { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }

.permission-tags { display: flex; flex-wrap: wrap; gap: 6px; }
.badge-tag { background: white; border: 1px solid #e2e8f0; color: #64748b; padding: 2px 8px; border-radius: 6px; font-size: 0.75rem; }

.action-btn-icon { width: 32px; height: 32px; border: 1px solid #e2e8f0; border-radius: 8px; background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; color: #94a3b8; }
.action-btn-icon:hover { background: #f8fafc; border-color: #cbd5e1; transform: translateY(-1px); }
.action-btn-icon.text-red:hover { color: #ef4444; border-color: #fca5a5; background: #fef2f2; }
.action-btn-icon.text-indigo:hover { color: #4f46e5; border-color: #c7d2fe; background: #eef2ff; }

/* Utils */
.flex { display: flex; }
.justify-end { justify-content: flex-end; }
.gap-2 { gap: 0.5rem; }
.font-bold { font-weight: 700; }
.text-xs { font-size: 0.75rem; }
.text-gray-500 { color: #64748b; }
.text-gray-800 { color: #1e293b; }
</style>