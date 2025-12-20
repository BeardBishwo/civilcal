<?php
// Remove the ob_start() and header inclusion since we're using the themes/admin layout
$page_title = 'Widget Management - ' . \App\Services\SettingsService::get('site_name', 'Engineering Calculator Pro');
?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-cube"></i>
                    <h1>Widget Management</h1>
                </div>
                <div class="header-subtitle">Manage widgets for the system</div>
            </div>
            <div class="header-actions">
                <a href="<?php echo app_base_url('/admin/widgets/setup'); ?>" class="btn btn-secondary btn-compact" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); margin-right: 0.5rem;">
                    <i class="fas fa-cog"></i>
                    <span>Setup DB</span>
                </a>
                <a href="<?php echo app_base_url('/admin/widgets/create'); ?>" class="btn btn-primary btn-compact" style="background: white; color: #667eea;">
                    <i class="fas fa-plus-circle"></i>
                    <span>Create Widget</span>
                </a>
            </div>
        </div>

        <!-- Compact Stats Cards -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-code"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo count($available_classes ?? []); ?></div>
                    <div class="stat-label">Widget Classes</div>
                </div>
            </div>
            
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo count(array_filter($widgets ?? [], fn($w) => $w->isEnabled())); ?></div>
                    <div class="stat-label">Active Widgets</div>
                </div>
            </div>
            
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-cubes"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo count($widgets ?? []); ?></div>
                    <div class="stat-label">Total Widgets</div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="analytics-content-body">

            <!-- Installed Widgets -->
            <div class="section-title">
                <h3><i class="fas fa-th-large text-primary"></i> Installed Widgets</h3>
            </div>

            <?php if (empty($widgets)): ?>
                <div class="empty-state-compact">
                    <i class="fas fa-cube"></i>
                    <h3>No widgets found</h3>
                    <p>Get started by creating your first widget.</p>
                    <a href="<?php echo app_base_url('/admin/widgets/create'); ?>" class="btn btn-primary btn-compact mt-2">
                        <i class="fas fa-plus"></i> Create Widget
                    </a>
                </div>
            <?php else: ?>
                <div class="pages-grid-compact mb-5">
                    <?php foreach ($widgets as $widget): ?>
                        <div class="page-card-compact <?php echo $widget->isEnabled() ? '' : 'opacity-75'; ?>">
                            <div class="card-header-compact">
                                <div class="header-title-sm text-truncate" title="<?php echo htmlspecialchars($widget->getTitle()); ?>">
                                    <?php echo htmlspecialchars($widget->getTitle()); ?>
                                </div>
                                <div class="card-actions">
                                    <span class="status-badge <?php echo $widget->isEnabled() ? 'status-published' : 'status-draft'; ?>">
                                        <?php echo $widget->isEnabled() ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-content-compact flex-grow-1">
                                <p class="text-muted small mb-3 description-truncate"><?php echo htmlspecialchars($widget->getDescription()); ?></p>
                                <div class="flex-align-center">
                                    <span class="badge-pill bg-light text-dark">
                                        <i class="fas fa-tag fa-xs mr-1"></i> <?php echo htmlspecialchars($widget->getType()); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-footer-compact">
                                <div class="flex-grow-1 flex-align-center gap-2">
                                    <form method="post" action="<?php echo app_base_url('/admin/widgets/toggle/'); ?><?php echo $widget->getId(); ?>" class="d-inline">
                                        <button type="submit" class="action-btn-icon" title="<?php echo $widget->isEnabled() ? 'Disable' : 'Enable'; ?>">
                                            <i class="fas <?php echo $widget->isEnabled() ? 'fa-pause text-warning' : 'fa-play text-success'; ?>"></i>
                                        </button>
                                    </form>
                                    <a href="<?php echo app_base_url('/admin/widgets/edit/'); ?><?php echo $widget->getId(); ?>" class="action-btn-icon" title="Edit">
                                        <i class="fas fa-edit text-primary"></i>
                                    </a>
                                    <a href="<?php echo app_base_url('/admin/widgets/preview/'); ?><?php echo $widget->getId(); ?>" class="action-btn-icon" title="Preview">
                                        <i class="fas fa-eye text-info"></i>
                                    </a>
                                </div>
                                <form method="post" action="<?php echo app_base_url('/admin/widgets/delete/'); ?><?php echo $widget->getId(); ?>" id="delete-widget-<?php echo $widget->getId(); ?>">
                                    <button type="button" class="action-btn-icon delete-btn" title="Delete" onclick="confirmDeleteWidget('<?php echo $widget->getId(); ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Available Widget Classes -->
            <div class="section-title mt-5">
                <h3><i class="fas fa-code text-info"></i> Available Widget Classes</h3>
            </div>

            <div class="page-card-compact">
                <div class="card-content-compact p-0">
                    <?php if (empty($available_classes ?? [])): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-code fa-2x mb-2"></i>
                            <p>No widget classes available</p>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <table class="table-compact">
                                <thead>
                                    <tr>
                                        <th>Class Name</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($available_classes ?? [] as $className): ?>
                                        <tr>
                                            <td>
                                                <code class="text-primary bg-light px-2 py-1 rounded"><?php echo htmlspecialchars($className); ?></code>
                                            </td>
                                            <td class="text-right">
                                                <a href="<?php echo app_base_url('/admin/widgets/create'); ?>?class=<?php echo urlencode($className); ?>" class="btn btn-sm btn-primary-soft">
                                                    <i class="fas fa-plus"></i> Create
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
    function confirmDeleteWidget(widgetId) {
        showConfirmModal('Delete Widget', 'Are you sure you want to delete this widget?', () => {
            document.getElementById('delete-widget-' + widgetId).submit();
        });
    }
</script>

<style>
    /* ========================================
       SHARED STYLES (Compact Admin Theme)
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

    /* HEADER */
    .compact-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .header-left { flex: 1; }
    
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
    
    .header-title i { font-size: 1.5rem; opacity: 0.9; }
    
    .header-subtitle {
        font-size: 0.875rem;
        opacity: 0.85;
        margin: 0;
        color: rgba(255,255,255,0.9);
    }

    .btn-compact {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        border-radius: 6px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    
    .btn-compact:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* STATS */
    .compact-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: #fff;
    }

    .stat-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem;
        background: var(--admin-gray-50, #f8f9fa);
        border-radius: 8px;
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        transition: all 0.2s ease;
    }
    
    .stat-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
    }

    .stat-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        flex-shrink: 0;
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
        line-height: 1.1;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.75rem;
        color: var(--admin-gray-600, #6b7280);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    /* CONTENT BODY */
    .analytics-content-body {
        padding: 2rem;
    }
    
    .section-title h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 1.5rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .pages-grid-compact {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .page-card-compact {
        background: white;
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    .page-card-compact:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1;
    }

    .card-header-compact {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: #f8f9fa;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-title-sm {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
    }

    .card-content-compact {
        padding: 1.25rem;
    }
    
    .card-footer-compact {
        padding: 0.75rem 1.25rem;
        border-top: 1px solid var(--admin-gray-200, #e5e7eb);
        background: #f8f9fa;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .action-btn-icon {
        width: 2rem;
        height: 2rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: white;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .action-btn-icon:hover {
        background: #f3f4f6;
        transform: translateY(-1px);
    }
    
    .delete-btn:hover {
        background: #fee2e2;
        color: #ef4444;
        border-color: #fca5a5;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.625rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .status-published { background: rgba(72, 187, 120, 0.1); color: #48bb78; }
    .status-draft { background: rgba(107, 114, 128, 0.1); color: #6b7280; }
    
    .badge-pill {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        font-size: 0.75rem;
        font-weight: 500;
        line-height: 1;
        border-radius: 10rem;
    }
    
    .bg-light { background-color: #f3f4f6 !important; }
    
    .flex-grow-1 { flex-grow: 1; }
    .flex-align-center { display: flex; align-items: center; }
    .gap-2 { gap: 0.5rem; }
    
    .description-truncate {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.4em;
    }
    
    .opacity-75 { opacity: 0.75; }
    
    /* TABLE */
    .table-container { padding: 0; }
    .table-compact { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
    .table-compact th {
        background: #f9fafb;
        padding: 0.75rem  1.25rem;
        text-align: left;
        font-weight: 600;
        color: #374151;
        border-bottom: 2px solid #e5e7eb;
    }
    .table-compact td {
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid #e5e7eb;
        color: #4b5563;
    }
    .table-compact tr:last-child td { border-bottom: none; }
    
    .btn-primary-soft {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
        padding: 0.35rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 6px;
        font-weight: 600;
        text-decoration: none;
    }
    
    .btn-primary-soft:hover {
        background: #667eea;
        color: white;
    }
    .p-0 { padding: 0 !important; }
    .mt-5 { margin-top: 2rem; }
    .mb-5 { margin-bottom: 2rem; }
    .text-right { text-align: right; }
    
    .empty-state-compact {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 3rem;
        text-align: center;
        color: #9ca3af;
    }
    
    .empty-state-compact i { font-size: 3rem; margin-bottom: 1rem; color: #cbd5e1; }
    .empty-state-compact h3 { margin-bottom: 0.5rem; color: #374151; font-weight: 600; }
    
    /* Responsive */
    @media (max-width: 768px) {
        .compact-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
            padding: 1.25rem;
        }
        .compact-stats {
            grid-template-columns: 1fr;
            padding: 1.25rem;
        }
    }
</style>