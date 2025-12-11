<?php
// Audit Logs View - Compact Design
?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-shield-alt"></i>
                    <h1>Audit Logs</h1>
                </div>
                <div class="header-subtitle">Security and activity trail with comprehensive filtering</div>
            </div>
            <div class="header-actions">
                <a href="<?php echo app_base_url('/admin/audit-logs/download'); ?>?date=<?php echo htmlspecialchars($selectedDate ?? date('Y-m-d')); ?>" class="btn btn-light btn-compact">
                    <i class="fas fa-download"></i>
                    <span>Download <?php echo htmlspecialchars($selectedDate ?? date('Y-m-d')); ?></span>
                </a>
            </div>
        </div>

        <div class="analytics-content-body">
            
            <!-- Filter Section -->
            <div class="page-card-compact mb-4">
                <div class="card-header-compact">
                    <div class="header-title-sm">
                        <i class="fas fa-filter text-primary"></i> Filter Options
                    </div>
                </div>
                <div class="card-content-compact">
                    <form method="GET" action="<?php echo app_base_url('/admin/audit-logs'); ?>">
                        <div class="grid-4-cols">
                            <div class="form-group">
                                <label class="form-label">Date</label>
                                <select class="form-control" name="date">
                                    <?php foreach ($dates ?? [] as $d): ?>
                                        <option value="<?php echo htmlspecialchars($d); ?>" <?php echo ($d === ($selectedDate ?? '')) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($d); ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <?php if (empty($dates ?? [])): ?>
                                        <option value="<?php echo htmlspecialchars($selectedDate ?? date('Y-m-d')); ?>" selected>
                                            <?php echo htmlspecialchars($selectedDate ?? date('Y-m-d')); ?>
                                        </option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Level</label>
                                <select class="form-control" name="level">
                                    <option value="">All Levels</option>
                                    <option value="INFO" <?php echo (($level ?? '') === 'INFO' ? 'selected' : ''); ?>>INFO</option>
                                    <option value="WARNING" <?php echo (($level ?? '') === 'WARNING' ? 'selected' : ''); ?>>WARNING</option>
                                    <option value="ERROR" <?php echo (($level ?? '') === 'ERROR' ? 'selected' : ''); ?>>ERROR</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Search Keywords</label>
                                <input type="text" class="form-control" name="q" value="<?php echo htmlspecialchars($q ?? ''); ?>" placeholder="Action, user, IP...">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Rows Per Page</label>
                                <input type="number" class="form-control" name="per_page" min="1" max="200" value="<?php echo htmlspecialchars((string)($perPage ?? 50)); ?>">
                            </div>
                        </div>
                        
                        <div class="form-actions d-flex justify-content-end gap-2 mt-3">
                             <a class="btn btn-light btn-compact" href="<?php echo app_base_url('/admin/audit-logs'); ?>">
                                <i class="fas fa-times"></i> Clear
                            </a>
                            <button class="btn btn-primary btn-compact" type="submit">
                                <i class="fas fa-search"></i> Apply Filters
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Entries Table -->
            <div class="page-card-compact">
                <div class="card-header-compact">
                    <div class="header-title-sm">
                        <i class="fas fa-list text-primary"></i> Audit Entries
                    </div>
                    <div class="text-xs text-muted">
                        Showing <?php echo htmlspecialchars((string)($total ?? 0)); ?> records
                    </div>
                </div>
                
                <div class="table-container">
                    <div class="table-wrapper">
                        <table class="table-compact">
                            <thead>
                                <tr>
                                    <th width="180">Timestamp</th>
                                    <th width="100">Level</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($entries ?? [])): ?>
                                    <tr>
                                        <td colspan="4">
                                            <div class="empty-state-compact py-5">
                                                <i class="fas fa-shield-alt text-muted fa-2x mb-3"></i>
                                                <p class="text-muted">No audit entries found matching criteria.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($entries as $e): ?>
                                        <tr>
                                            <td class="font-mono text-xs text-muted">
                                                <?php echo htmlspecialchars($e['ts'] ?? ''); ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $lvl = strtoupper($e['level'] ?? '');
                                                $badgeClass = 'bg-info text-white';
                                                if ($lvl === 'ERROR') $badgeClass = 'bg-danger text-white';
                                                elseif ($lvl === 'WARNING') $badgeClass = 'bg-warning text-dark';
                                                ?>
                                                <span class="badge-pill <?php echo $badgeClass; ?> text-xs">
                                                    <?php echo htmlspecialchars($lvl); ?>
                                                </span>
                                            </td>
                                            <td class="font-medium text-dark">
                                                <?php echo htmlspecialchars($e['action'] ?? ''); ?>
                                            </td>
                                            <td>
                                                <div class="code-snippet">
                                                    <?php echo htmlspecialchars(json_encode($e['details'] ?? [], JSON_UNESCAPED_SLASHES)); ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination -->
                <?php if (!empty($entries ?? []) && ($pages ?? 1) > 1): ?>
                <div class="card-footer-compact d-flex justify-content-between align-items-center">
                    <div class="text-sm text-muted">
                        Page <?php echo htmlspecialchars((string)($page ?? 1)); ?> of <?php echo htmlspecialchars((string)($pages ?? 1)); ?>
                    </div>
                    <div class="pagination-compact">
                         <a class="btn btn-light btn-compact btn-sm <?php echo (($page ?? 1) <= 1 ? 'disabled' : ''); ?>" 
                           href="<?php echo app_base_url('/admin/audit-logs'); ?>?date=<?php echo htmlspecialchars($selectedDate ?? date('Y-m-d')); ?>&level=<?php echo htmlspecialchars($level ?? ''); ?>&q=<?php echo htmlspecialchars($q ?? ''); ?>&per_page=<?php echo htmlspecialchars($perPage ?? 50); ?>&page=<?php echo max(1, ($page ?? 1) - 1); ?>">
                            <i class="fas fa-chevron-left"></i> Prev
                        </a>
                        <a class="btn btn-light btn-compact btn-sm <?php echo (($page ?? 1) >= ($pages ?? 1) ? 'disabled' : ''); ?>" 
                           href="<?php echo app_base_url('/admin/audit-logs'); ?>?date=<?php echo htmlspecialchars($selectedDate ?? date('Y-m-d')); ?>&level=<?php echo htmlspecialchars($level ?? ''); ?>&q=<?php echo htmlspecialchars($q ?? ''); ?>&per_page=<?php echo htmlspecialchars($perPage ?? 50); ?>&page=<?php echo min(($pages ?? 1), ($page ?? 1) + 1); ?>">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

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
    
    .btn-light { background: white; color: #374151; border: 1px solid #d1d5db; }
    .btn-light:hover { background: #f3f4f6; }
    .btn-primary { background: #667eea; color: white; }
    .btn-primary:hover { background: #5a67d8; }

    /* CONTENT BODY */
    .analytics-content-body {
        padding: 2rem;
    }

    .page-card-compact {
        background: white;
        border: 1px solid var(--admin-gray-200, #e5e7eb);
        border-radius: 10px;
        overflow: hidden;
    }
    
    .mb-4 { margin-bottom: 1.5rem; }

    .card-header-compact {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
        background: #f8f9fa;
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-height: 55px;
    }
    
    .card-footer-compact {
        padding: 0.75rem 1.25rem;
        border-top: 1px solid var(--admin-gray-200, #e5e7eb);
        background: #f8f9fa;
    }

    .header-title-sm {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-content-compact { padding: 1.5rem; }
    
    /* FORM & GRID */
    .grid-4-cols {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }
    
    .form-group { margin-bottom: 0; }
    
    .form-label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
    }
    
    .form-control {
        width: 100%;
        padding: 0.625rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.875rem;
        transition: border-color 0.15s;
    }
    
    .form-control:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .form-actions { display: flex; align-items: center; }
    .justify-content-end { justify-content: flex-end; }
    .gap-2 { gap: 0.5rem; }
    .mt-3 { margin-top: 1rem; }
    
    /* TABLE */
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
        vertical-align: top;
    }

    .table-compact tbody tr:hover { background: var(--admin-gray-50, #f8f9fa); }
    
    .text-xs { font-size: 0.75rem; }
    .text-sm { font-size: 0.875rem; }
    .text-muted { color: #6b7280 !important; }
    .text-primary { color: #667eea !important; }
    .text-dark { color: #1f2937; }
    .font-mono { font-family: monospace; }
    .font-medium { font-weight: 500; }
    
    .badge-pill {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        font-weight: 600;
        line-height: 1;
    }
    .bg-info { background: #4299e1; }
    .bg-danger { background: #f56565; }
    .bg-warning { background: #ed8936; }
    .bg-success { background: #48bb78; }
    .text-white { color: white; }
    
    .code-snippet {
        font-family: monospace;
        font-size: 0.75rem;
        background: #f9fafb;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        color: #4b5563;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 400px;
    }
    
    .empty-state-compact { text-align: center; }
    .py-5 { padding-top: 3rem; padding-bottom: 3rem; }
    .mb-3 { margin-bottom: 1rem; }
    
    .disabled { pointer-events: none; opacity: 0.6; }

    /* Responsive */
    @media (max-width: 768px) {
        .compact-header {
            flex-direction: column;
            align-items: stretch;
            gap: 1rem;
            padding: 1.25rem;
        }
        .grid-4-cols { grid-template-columns: 1fr; }
        .table-compact th, .table-compact td { padding: 0.5rem; }
    }
</style>
<?php
/* End of Audit Logs View */
?>