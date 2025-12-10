<?php
/**
 * OPTIMIZED MODULES MANAGEMENT INTERFACE
 * Adapted from Calculators/Pages UI
 */

$totalModules = $stats['total_modules'] ?? count($modules);
$activeModules = $stats['active_modules'] ?? 0;
$inactiveModules = $stats['inactive_modules'] ?? 0;
// Calculate unique categories for stats
$uniqueCategories = count(array_unique(array_column($modules, 'category')));

?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-box-open"></i>
                    <h1>Modules</h1>
                </div>
                <div class="header-subtitle"><?php echo $totalModules; ?> modules • <?php echo $uniqueCategories; ?> categories • <?php echo $activeModules; ?> active</div>
            </div>
            <!-- Header Right: Add New Button (Placeholder for future) -->
        </div>

        <!-- Compact Stats Cards -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $totalModules; ?></div>
                    <div class="stat-label">Total Modules</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $activeModules; ?></div>
                    <div class="stat-label">Active</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-power-off"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $inactiveModules; ?></div>
                    <div class="stat-label">Inactive</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $uniqueCategories; ?></div>
                    <div class="stat-label">Module Types</div>
                </div>
            </div>
        </div>

        <!-- Compact Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                 <!-- Client-side Search (Simple Implementation) -->
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" id="moduleSearch" placeholder="Search modules..." onkeyup="filterModules()">
                </div>
            </div>
            <div class="toolbar-right">
                <div class="view-controls">
                    <button class="view-btn active" data-view="table" title="Table View">
                        <i class="fas fa-table"></i>
                    </button>
                    <button class="view-btn" data-view="grid" title="Grid View">
                        <i class="fas fa-th-large"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="pages-content">

            <!-- Table View -->
            <div id="table-view" class="view-section active">
                <div class="table-container">
                    <?php if (empty($modules)): ?>
                         <div class="empty-state-compact">
                            <i class="fas fa-box-open"></i>
                            <h3>No modules found</h3>
                            <p>You haven't installed any modules yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="table-compact" id="modulesTable">
                                <thead>
                                    <tr>
                                        <th class="col-title">Module Name</th>
                                        <th class="col-author">Description</th>
                                        <th class="col-cat">Category</th>
                                        <th class="col-calcs">Calculators</th>
                                        <th class="col-actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($modules as $module): ?>
                                        <tr class="module-row" data-name="<?php echo strtolower($module['display_name'] ?? $module['name']); ?>" data-cat="<?php echo strtolower($module['category']); ?>">
                                            <td>
                                                <div class="page-info">
                                                    <div class="page-title-compact"><?php echo htmlspecialchars($module['display_name'] ?? ucwords(str_replace(['-', '_'], ' ', $module['name']))); ?></div>
                                                    <div class="page-slug-compact">v<?php echo htmlspecialchars($module['version']); ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="desc-compact text-muted" style="max-width: 300px; white-space: normal; font-size: 0.8rem; line-height: 1.4;">
                                                    <?php echo htmlspecialchars($module['description']); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    <?php echo htmlspecialchars($module['category']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    <span class="badge bg-primary text-white">
                                                        <?php echo $module['calculators_count'] ?? 0; ?> tools
                                                    </span>
                                                    <?php if (isset($module['subcategories_count']) && $module['subcategories_count'] > 0): ?>
                                                        <span class="badge bg-info text-white">
                                                            <?php echo $module['subcategories_count']; ?> categories
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="actions-compact">
                                                    <!-- Toggle Button -->
                                                    <?php if (($module['status'] ?? 'inactive') === 'active'): ?>
                                                        <button class="action-btn-icon delete-btn" 
                                                                onclick="toggleModule('<?php echo $module['name']; ?>', 'deactivate')" 
                                                                title="Deactivate">
                                                            <i class="fas fa-power-off"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="action-btn-icon edit-btn" 
                                                                onclick="toggleModule('<?php echo $module['name']; ?>', 'activate')" 
                                                                title="Activate">
                                                            <i class="fas fa-power-off"></i>
                                                        </button>
                                                    <?php endif; ?>

                                                    <!-- Settings Button -->
                                                    <a href="<?php echo get_app_url(); ?>/admin/modules/<?php echo urlencode($module['name']); ?>/settings" class="action-btn-icon preview-btn" title="Settings">
                                                        <i class="fas fa-cog"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

             <!-- Grid View -->
            <div id="grid-view" class="view-section">
                <div class="grid-container">
                    <?php if (empty($modules)): ?>
                        <div class="empty-state-compact">
                            <i class="fas fa-box-open"></i>
                            <h3>No modules found</h3>
                        </div>
                    <?php else: ?>
                        <div class="pages-grid-compact" id="modulesGrid">
                            <?php foreach ($modules as $module): ?>
                                <div class="page-card-compact module-card" data-name="<?php echo strtolower($module['name']); ?>">
                                    <div class="card-header-compact">
                                        <div class="card-status">
                                            <span class="status-badge status-<?php echo (($module['status'] ?? 'inactive') === 'active') ? 'published' : 'draft'; ?>">
                                                <i class="fas fa-<?php echo (($module['status'] ?? 'inactive') === 'active') ? 'check-circle' : 'power-off'; ?>"></i>
                                                <?php echo (($module['status'] ?? 'inactive') === 'active') ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </div>
                                        <div class="card-actions">
                                            <a href="<?php echo get_app_url(); ?>/admin/modules/<?php echo urlencode($module['name']); ?>/settings" class="action-btn-icon" title="Settings">
                                                <i class="fas fa-cog"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-content-compact">
                                        <h3 class="card-title-compact"><?php echo htmlspecialchars($module['name']); ?></h3>
                                        <div class="card-meta-compact mb-2">
                                            <span class="badge bg-light text-dark border">
                                                <?php echo htmlspecialchars($module['category']); ?>
                                            </span>
                                        </div>
                                        <p class="text-muted small mb-0" style="line-height: 1.4;">
                                            <?php echo htmlspecialchars($module['description']); ?>
                                        </p>
                                    </div>
                                    <div class="card-footer-compact">
                                         <?php if (($module['status'] ?? 'inactive') === 'active'): ?>
                                            <button class="btn btn-sm btn-outline-danger w-100" onclick="toggleModule('<?php echo $module['name']; ?>', 'deactivate')">
                                                <i class="fas fa-power-off"></i> Deactivate
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-success w-100" onclick="toggleModule('<?php echo $module['name']; ?>', 'activate')">
                                                <i class="fas fa-power-off"></i> Activate
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // View toggle
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                switchView(this.dataset.view);
            });
        });
    });

    function switchView(view) {
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.view === view);
        });
        document.querySelectorAll('.view-section').forEach(section => {
            section.classList.toggle('active', section.id === `${view}-view`);
        });
    }

    function toggleModule(moduleName, action) {
        if (!confirm('Are you sure you want to ' + action + ' this module?')) {
            return;
        }

        const formData = new FormData();
        formData.append('module', moduleName);

        fetch('<?php echo get_app_url(); ?>/admin/modules/' + action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while communicating with the server.');
            });
    }

    // Simple Client-side Filter
    function filterModules() {
        const input = document.getElementById('moduleSearch');
        const filter = input.value.toLowerCase();
        
        // Filter Table
        const rows = document.querySelectorAll('.module-row');
        rows.forEach(row => {
            const name = row.getAttribute('data-name');
            const cat = row.getAttribute('data-cat');
            if (name.includes(filter) || cat.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Filter Grid
        const cards = document.querySelectorAll('.module-card');
        cards.forEach(card => {
             const name = card.getAttribute('data-name');
             if (name.includes(filter)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>

<style>
    /* ========================================
       OPTIMIZED ADMIN WRAPPER CONTAINER
       (Consistent with pages/calculators UI)
       ======================================== */

    .admin-wrapper-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 1rem;
        background: var(--admin-gray-50, #f8f9fa);
        min-height: calc(100vh - 70px);
    }

    .admin-content-wrapper {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    /* COMPACT HEADER */
    .compact-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .header-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.25rem;
    }

    .header-title h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
    }

    .header-title i {
        font-size: 1.5rem;
        opacity: 0.9;
    }

    .header-subtitle {
        font-size: 0.875rem;
        opacity: 0.8;
        margin: 0;
    }

    /* COMPACT STATS */
    .compact-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: var(--admin-gray-50, #f8f9fa);
        border-radius: 8px;
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        transition: all 0.2s ease;
    }

    .stat-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
    }

    .stat-icon.primary { background: #667eea; }
    .stat-icon.success { background: #48bb78; }
    .stat-icon.warning { background: #ed8936; }
    .stat-icon.info { background: #4299e1; }

    .stat-info { flex: 1; }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--admin-gray-900, #1f2937);
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.75rem;
        color: var(--admin-gray-600, #6b7280);
        font-weight: 500;
    }

    /* COMPACT TOOLBAR */
    .compact-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        gap: 1rem;
    }

    .toolbar-left {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex: 1;
    }

    .search-compact {
        position: relative;
        flex: 1;
        max-width: 400px;
    }

    .search-compact i {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--admin-gray-400, #9ca3af);
        font-size: 0.875rem;
    }

    .search-compact input {
        width: 100%;
        padding: 0.625rem 0.75rem 0.625rem 2.5rem;
        border: 1px solid var(--admin-gray-300, #d1d5db);
        border-radius: 6px;
        font-size: 0.875rem;
        background: white;
        transition: all 0.2s ease;
    }

    .search-compact input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .toolbar-right {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .view-controls {
        display: flex;
        border: 1px solid var(--admin-gray-300, #d1d5db);
        border-radius: 6px;
        overflow: hidden;
    }

    .view-btn {
        padding: 0.625rem;
        border: none;
        background: white;
        color: var(--admin-gray-600, #6b7280);
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }

    .view-btn:hover { background: var(--admin-gray-50, #f8f9fa); }
    .view-btn.active {
        background: #667eea;
        color: white;
    }

    /* CONTENT & TABLE */
    .pages-content { min-height: 400px; }
    .view-section { display: none; }
    .view-section.active { display: block; }
    
    .table-container { padding: 0; }
    .table-wrapper { overflow-x: auto; }
    
    .table-compact {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .table-compact th {
        background: var(--admin-gray-50, #f8f9fa);
        padding: 0.75rem 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--admin-gray-700, #374151);
        border-bottom: 2px solid var(--admin-gray-200, #e5e7eb);
        white-space: nowrap;
    }

    .table-compact td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        vertical-align: middle;
    }

    .table-compact tbody tr:hover { background: var(--admin-gray-50, #f8f9fa); }

    .col-title { min-width: 200px; }
    .col-actions { width: 120px; text-align: right; }

    .page-info { display: flex; flex-direction: column; gap: 0.25rem; }
    .page-title-compact { font-weight: 600; color: var(--admin-gray-900, #1f2937); }
    .page-slug-compact { font-size: 0.75rem; color: var(--admin-gray-500, #6b7280); font-family: monospace; }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        white-space: nowrap;
    }
    .status-published { background: rgba(72, 187, 120, 0.1); color: #48bb78; }
    .status-draft { background: rgba(237, 137, 54, 0.1); color: #ed8936; }

    .actions-compact { display: flex; gap: 0.25rem; justify-content: flex-end; }
    
    .action-btn-icon {
        width: 2rem; height: 2rem;
        border: 1px solid var(--admin-gray-300, #d1d5db);
        border-radius: 6px;
        background: white; color: var(--admin-gray-600, #6b7280);
        cursor: pointer; transition: all 0.2s ease;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.75rem; text-decoration: none;
    }
    .action-btn-icon:hover { transform: translateY(-1px); }
    
    .preview-btn:hover { background: #4299e1; color: white; border-color: #4299e1; }
    .edit-btn:hover { background: #667eea; color: white; border-color: #667eea; }
    .delete-btn:hover { background: #f56565; color: white; border-color: #f56565; }

    /* Grid View */
    .grid-container { padding: 1.5rem 2rem; }
    .pages-grid-compact { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; }
    
    .page-card-compact {
        background: white; border: 1px solid var(--admin-gray-200, #e5e7eb);
        border-radius: 8px; overflow: hidden; transition: all 0.2s ease;
    }
    .page-card-compact:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
    
    .card-header-compact {
        display: flex; justify-content: space-between; align-items: center;
        padding: 0.75rem 1rem; border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: var(--admin-gray-50, #f8f9fa);
    }
    .card-content-compact { padding: 1rem; }
    .card-title-compact { font-size: 1rem; font-weight: 600; margin: 0 0 0.75rem 0; }
    .card-footer-compact { padding: 0.75rem 1rem; border-top: 1px solid var(--admin-gray-200, #e5e7eb); background: var(--admin-gray-50, #f8f9fa); }
    
    .empty-state-compact {
        display: flex; flex-direction: column; align-items: center;
        padding: 4rem 2rem; text-align: center;
    }
    .empty-state-compact i { font-size: 3rem; color: var(--admin-gray-400, #9ca3af); margin-bottom: 1rem; }
    .empty-state-compact h3 { font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; }

    /* Responsive */
    @media (max-width: 1024px) {
        .compact-header { flex-direction: column; align-items: stretch; gap: 1rem; }
        .toolbar-left { flex-direction: column; align-items: stretch; }
        .search-compact { max-width: none; }
    }
    @media (max-width: 768px) {
        .compact-stats { grid-template-columns: repeat(2, 1fr); }
        .table-compact th, .table-compact td { padding: 0.5rem; font-size: 0.75rem; }
    }
</style>