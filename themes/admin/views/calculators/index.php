<?php

/**
 * OPTIMIZED CALCULATORS MANAGEMENT INTERFACE
 * Adapted from Pages Management UI
 */

// Stats from Controller
$totalCalculators = $stats['total'];
$activeCalculators = $stats['active'];
$inactiveCalculators = $stats['inactive'];
$totalModules = $stats['modules'];
?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-calculator"></i>
                    <h1>Calculators</h1>
                </div>
                <div class="header-subtitle"><?php echo $totalCalculators; ?> calculators • <?php echo $totalModules; ?> modules • <?php echo $activeCalculators; ?> active</div>
            </div>
            <!-- Future: Add 'Scan New' button if relevant -->
        </div>

        <!-- Compact Stats Cards -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $totalCalculators; ?></div>
                    <div class="stat-label">Total Tools</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $activeCalculators; ?></div>
                    <div class="stat-label">Active</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-power-off"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $inactiveCalculators; ?></div>
                    <div class="stat-label">Inactive</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-cubes"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $totalModules; ?></div>
                    <div class="stat-label">Modules</div>
                </div>
            </div>
        </div>

        <!-- Compact Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <!-- Search Form -->
                <form action="" method="GET" class="d-flex flex-grow-1 gap-2 m-0" id="filter-form">
                    <div class="search-compact">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search calculators..." value="<?php echo htmlspecialchars($filters['search']); ?>">
                        <?php if (!empty($filters['search'])): ?>
                            <a href="<?php echo get_app_url(); ?>/admin/calculators" class="search-clear" style="display:block;">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                    <select name="module" class="filter-compact" onchange="this.form.submit()">
                        <option value="">All Modules</option>
                        <?php foreach ($modules as $modName): ?>
                            <?php $modSlug = strtolower(str_replace(' ', '-', $modName)); ?>
                            <option value="<?php echo $modSlug; ?>" <?php echo ($filters['module'] === $modSlug) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($modName); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
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
                    <?php if (empty($calculators)): ?>
                         <div class="empty-state-compact">
                            <i class="fas fa-calculator"></i>
                            <h3>No calculators found</h3>
                            <p>Try adjusting your search or filters.</p>
                            <a href="<?php echo get_app_url(); ?>/admin/calculators" class="btn btn-primary">
                                Clear Filters
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="table-compact">
                                <thead>
                                    <tr>
                                        <th class="col-title">Calculator Name</th>
                                        <th class="col-author">Module</th>
                                        <th class="col-status">Status</th>
                                        <th class="col-date">Path</th>
                                        <th class="col-actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($calculators as $calc): ?>
                                        <tr class="page-row">
                                            <td>
                                                <div class="page-info">
                                                    <div class="page-title-compact"><?php echo htmlspecialchars($calc['name']); ?></div>
                                                    <div class="page-slug-compact"><?php echo htmlspecialchars($calc['slug']); ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="author-compact">
                                                    <i class="fas fa-cube"></i>
                                                    <?php echo htmlspecialchars($calc['module_name']); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?php echo ($calc['status'] === 'active') ? 'published' : 'draft'; ?>">
                                                    <i class="fas fa-<?php echo ($calc['status'] === 'active') ? 'check-circle' : 'power-off'; ?>"></i>
                                                    <?php echo ucfirst($calc['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="date-compact">
                                                    <span class="time-compact"><?php echo htmlspecialchars($calc['path']); ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="actions-compact">
                                                     <!-- Toggle Button -->
                                                    <?php if ($calc['status'] === 'active'): ?>
                                                        <button class="action-btn-icon delete-btn" 
                                                                onclick="toggleCalculator('<?php echo $calc['unique_id']; ?>', 'deactivate')" 
                                                                title="Deactivate">
                                                            <i class="fas fa-power-off"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="action-btn-icon edit-btn" 
                                                                onclick="toggleCalculator('<?php echo $calc['unique_id']; ?>', 'activate')" 
                                                                title="Activate">
                                                            <i class="fas fa-power-off"></i>
                                                        </button>
                                                    <?php endif; ?>

                                                    <!-- Test Button -->
                                                    <a href="<?php echo get_app_url() . $calc['url']; ?>" target="_blank" class="action-btn-icon preview-btn" title="Test Calculator">
                                                        <i class="fas fa-external-link-alt"></i>
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
                    <?php if (empty($calculators)): ?>
                        <div class="empty-state-compact">
                            <i class="fas fa-calculator"></i>
                            <h3>No calculators found</h3>
                        </div>
                    <?php else: ?>
                        <div class="pages-grid-compact">
                            <?php foreach ($calculators as $calc): ?>
                                <div class="page-card-compact">
                                    <div class="card-header-compact">
                                        <div class="card-status">
                                            <span class="status-badge status-<?php echo ($calc['status'] === 'active') ? 'published' : 'draft'; ?>">
                                                <i class="fas fa-<?php echo ($calc['status'] === 'active') ? 'check-circle' : 'power-off'; ?>"></i>
                                            </span>
                                            <small class="ms-2 text-muted"><?php echo htmlspecialchars($calc['module_name']); ?></small>
                                        </div>
                                        <div class="card-actions">
                                            <a href="<?php echo get_app_url() . $calc['url']; ?>" target="_blank" class="action-btn-icon" title="Test">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-content-compact">
                                        <h3 class="card-title-compact"><?php echo htmlspecialchars($calc['name']); ?></h3>
                                        <div class="card-meta-compact">
                                            <span class="meta-item">
                                                <i class="fas fa-code-branch"></i>
                                                <?php echo htmlspecialchars($calc['path']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-footer-compact">
                                         <?php if ($calc['status'] === 'active'): ?>
                                            <button class="btn btn-sm btn-outline-danger w-100" onclick="toggleCalculator('<?php echo $calc['unique_id']; ?>', 'deactivate')">
                                                <i class="fas fa-power-off"></i> Deactivate
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-success w-100" onclick="toggleCalculator('<?php echo $calc['unique_id']; ?>', 'activate')">
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

    function toggleCalculator(id, action) {
        if (!confirm('Are you sure you want to ' + action + ' this calculator?')) return;

        const formData = new FormData();
        formData.append('id', id);
        formData.append('action', action);

        fetch('<?php echo get_app_url(); ?>/admin/calculators/toggle', {
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
            alert('Request failed');
        });
    }
</script>

<style>
    /* ========================================
       OPTIMIZED ADMIN WRAPPER CONTAINER
       (Copied from pages.php for consistency)
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
        color: white; /* Ensure text is white */
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
    
    /* Force side-by-side form */
    #filter-form {
        display: flex;
        flex-direction: row;
        align-items: center;
        width: 100%;
        gap: 1rem;
    }

    .search-compact {
        position: relative;
        flex: 1; /* Allow search to take available space */
        min-width: 200px;
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
    
    .search-clear {
        position: absolute;
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--admin-gray-400, #9ca3af);
        cursor: pointer;
        padding: 0.25rem;
    }

    .filter-compact {
        padding: 0.625rem 0.75rem;
        border: 1px solid var(--admin-gray-300, #d1d5db);
        border-radius: 6px;
        font-size: 0.875rem;
        background: white;
        min-width: 150px;
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

    /* Column Widths */
    .col-title { min-width: 200px; }
    .col-status { width: 120px; }
    .col-author { width: 150px; }
    .col-date { width: 250px; }
    .col-actions { width: 120px; text-align: right; }

    /* Elements */
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
    .status-draft { background: rgba(237, 137, 54, 0.1); color: #ed8936; } /* Used for Inactive */

    .author-compact {
        display: flex; text-align: left; align-items: center; gap: 0.375rem; 
        color: var(--admin-gray-700, #374151); font-size: 0.875rem; 
    }
    
    .date-compact { font-size: 0.75rem; color: var(--admin-gray-600, #6b7280); }

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