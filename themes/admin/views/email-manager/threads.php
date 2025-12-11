<?php
// Email Threads View - Compact Design
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-comments"></i>
                    <h1>Email Threads</h1>
                </div>
                <div class="header-subtitle">Manage and respond to email conversations</div>
            </div>
            <div class="header-actions">
                <a href="<?php echo app_base_url('/admin/email-manager'); ?>" class="btn btn-light btn-compact">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Dashboard</span>
                </a>
            </div>
        </div>

        <div class="analytics-content-body">
            
            <!-- Filter Section -->
            <div class="page-card-compact mb-4">
                <div class="card-header-compact">
                    <div class="header-title-sm">
                        <i class="fas fa-filter text-primary"></i> Filter Threads
                    </div>
                </div>
                <div class="card-content-compact">
                    <form method="GET" action="<?php echo app_base_url('/admin/email-manager/threads'); ?>">
                        <div class="grid-4-cols">
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control">
                                    <option value="all" <?php echo (!isset($filters['status']) || $filters['status'] === 'all') ? 'selected' : ''; ?>>All Statuses</option>
                                    <option value="new" <?php echo (isset($filters['status']) && $filters['status'] === 'new') ? 'selected' : ''; ?>>New</option>
                                    <option value="in_progress" <?php echo (isset($filters['status']) && $filters['status'] === 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="resolved" <?php echo (isset($filters['status']) && $filters['status'] === 'resolved') ? 'selected' : ''; ?>>Resolved</option>
                                    <option value="closed" <?php echo (isset($filters['status']) && $filters['status'] === 'closed') ? 'selected' : ''; ?>>Closed</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-control">
                                    <option value="all" <?php echo (!isset($filters['category']) || $filters['category'] === 'all') ? 'selected' : ''; ?>>All Categories</option>
                                    <option value="support" <?php echo (isset($filters['category']) && $filters['category'] === 'support') ? 'selected' : ''; ?>>Support</option>
                                    <option value="billing" <?php echo (isset($filters['category']) && $filters['category'] === 'billing') ? 'selected' : ''; ?>>Billing</option>
                                    <option value="feedback" <?php echo (isset($filters['category']) && $filters['category'] === 'feedback') ? 'selected' : ''; ?>>Feedback</option>
                                    <option value="other" <?php echo (isset($filters['category']) && $filters['category'] === 'other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Priority</label>
                                <select name="priority" class="form-control">
                                    <option value="all" <?php echo (!isset($filters['priority']) || $filters['priority'] === 'all') ? 'selected' : ''; ?>>All Priorities</option>
                                    <option value="low" <?php echo (isset($filters['priority']) && $filters['priority'] === 'low') ? 'selected' : ''; ?>>Low</option>
                                    <option value="medium" <?php echo (isset($filters['priority']) && $filters['priority'] === 'medium') ? 'selected' : ''; ?>>Medium</option>
                                    <option value="high" <?php echo (isset($filters['priority']) && $filters['priority'] === 'high') ? 'selected' : ''; ?>>High</option>
                                    <option value="urgent" <?php echo (isset($filters['priority']) && $filters['priority'] === 'urgent') ? 'selected' : ''; ?>>Urgent</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Search threads..." value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-actions d-flex justify-content-end gap-2 mt-3">
                             <a class="btn btn-light btn-compact" href="<?php echo app_base_url('/admin/email-manager/threads'); ?>">
                                <i class="fas fa-times"></i> Clear
                            </a>
                            <button class="btn btn-primary btn-compact" type="submit">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Threads Table -->
            <div class="page-card-compact">
                <div class="card-header-compact">
                    <div class="header-title-sm">
                        <i class="fas fa-list text-primary"></i> Thread List
                    </div>
                    <div class="text-xs text-muted">
                        Showing <?php echo number_format($total ?? 0); ?> threads
                    </div>
                </div>
                
                <div class="table-container">
                    <div class="table-wrapper">
                        <table class="table-compact">
                            <thead>
                                <tr>
                                    <th width="120">Status</th>
                                    <th>Subject</th>
                                    <th width="150">Category</th>
                                    <th width="120">Priority</th>
                                    <th width="150">Last Updated</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($threads)): ?>
                                    <tr>
                                        <td colspan="6">
                                            <div class="empty-state-compact py-5">
                                                <i class="fas fa-inbox text-muted fa-2x mb-3"></i>
                                                <p class="text-muted">No email threads found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($threads as $thread): ?>
                                        <tr>
                                            <td>
                                                <?php 
                                                $status = $thread['status'] ?? 'new';
                                                $statusClass = '';
                                                $statusIcon = '';
                                                switch ($status) {
                                                    case 'new':
                                                        $statusClass = 'bg-blue-100 text-blue-800';
                                                        $statusIcon = 'fa-envelope';
                                                        break;
                                                    case 'in_progress':
                                                        $statusClass = 'bg-yellow-100 text-yellow-800';
                                                        $statusIcon = 'fa-sync';
                                                        break;
                                                    case 'resolved':
                                                        $statusClass = 'bg-green-100 text-green-800';
                                                        $statusIcon = 'fa-check-circle';
                                                        break;
                                                    case 'closed':
                                                        $statusClass = 'bg-gray-100 text-gray-800';
                                                        $statusIcon = 'fa-times-circle';
                                                        break;
                                                    default:
                                                        $statusClass = 'bg-gray-100 text-gray-800';
                                                        $statusIcon = 'fa-question-circle';
                                                }
                                                ?>
                                                <span class="badge-pill <?php echo $statusClass; ?> text-xs">
                                                    <i class="fas <?php echo $statusIcon; ?> mr-1"></i> <?php echo ucfirst(str_replace('_', ' ', $status)); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?php echo app_base_url('/admin/email-manager/thread/' . $thread['id']); ?>" class="font-medium text-primary hover:underline">
                                                    <?php echo htmlspecialchars($thread['subject'] ?? 'No Subject'); ?>
                                                </a>
                                                <div class="text-sm text-muted mt-1">
                                                    <?php echo htmlspecialchars(substr($thread['last_message'] ?? '', 0, 80)) . (strlen($thread['last_message'] ?? '') > 80 ? '...' : ''); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge-pill bg-purple-100 text-purple-800 text-xs">
                                                    <?php echo ucfirst(htmlspecialchars($thread['category'] ?? 'General')); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php 
                                                $priority = $thread['priority'] ?? 'medium';
                                                $priorityClass = '';
                                                switch ($priority) {
                                                    case 'low': $priorityClass = 'bg-gray-100 text-gray-800'; break;
                                                    case 'medium': $priorityClass = 'bg-blue-100 text-blue-800'; break;
                                                    case 'high': $priorityClass = 'bg-orange-100 text-orange-800'; break;
                                                    case 'urgent': $priorityClass = 'bg-red-100 text-red-800'; break;
                                                    default: $priorityClass = 'bg-gray-100 text-gray-800';
                                                }
                                                ?>
                                                <span class="badge-pill <?php echo $priorityClass; ?> text-xs">
                                                    <?php echo ucfirst(htmlspecialchars($priority)); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="text-sm">
                                                    <?php echo date('M d, Y', strtotime($thread['updated_at'] ?? $thread['created_at'] ?? 'now')); ?>
                                                </div>
                                                <div class="text-xs text-muted">
                                                    <?php echo date('H:i', strtotime($thread['updated_at'] ?? $thread['created_at'] ?? 'now')); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="<?php echo app_base_url('/admin/email-manager/thread/' . $thread['id']); ?>" class="btn btn-primary btn-compact btn-sm">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination -->
                <?php if (!empty($threads)): ?>
                <div class="card-footer-compact d-flex justify-content-between align-items-center">
                    <div class="text-sm text-muted">
                         Page <?php echo ($page ?? 1); ?> of <?php echo ($total_pages ?? 1); ?>
                    </div>
                    <div class="pagination-compact">
                        <?php if (($page ?? 1) > 1): ?>
                            <a class="btn btn-light btn-compact btn-sm" 
                               href="<?php echo app_base_url('/admin/email-manager/threads'); ?>?page=<?php echo ($page ?? 1) - 1; ?>&limit=<?php echo $limit ?? 25; ?><?php echo isset($filters['status']) && $filters['status'] !== 'all' ? '&status=' . urlencode($filters['status']) : ''; ?><?php echo isset($filters['category']) && $filters['category'] !== 'all' ? '&category=' . urlencode($filters['category']) : ''; ?><?php echo isset($filters['priority']) && $filters['priority'] !== 'all' ? '&priority=' . urlencode($filters['priority']) : ''; ?><?php echo isset($filters['search']) ? '&search=' . urlencode($filters['search']) : ''; ?>">
                                <i class="fas fa-chevron-left"></i> Prev
                            </a>
                        <?php else: ?>
                             <button class="btn btn-light btn-compact btn-sm disabled" disabled>
                                <i class="fas fa-chevron-left"></i> Prev
                            </button>
                        <?php endif; ?>
                        
                        <?php if (($page ?? 1) < ($total_pages ?? 1)): ?>
                            <a class="btn btn-light btn-compact btn-sm" 
                               href="<?php echo app_base_url('/admin/email-manager/threads'); ?>?page=<?php echo ($page ?? 1) + 1; ?>&limit=<?php echo $limit ?? 25; ?><?php echo isset($filters['status']) && $filters['status'] !== 'all' ? '&status=' . urlencode($filters['status']) : ''; ?><?php echo isset($filters['category']) && $filters['category'] !== 'all' ? '&category=' . urlencode($filters['category']) : ''; ?><?php echo isset($filters['priority']) && $filters['priority'] !== 'all' ? '&priority=' . urlencode($filters['priority']) : ''; ?><?php echo isset($filters['search']) ? '&search=' . urlencode($filters['search']) : ''; ?>">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php else: ?>
                            <button class="btn btn-light btn-compact btn-sm disabled" disabled>
                                Next <i class="fas fa-chevron-right"></i>
                            </button>
                        <?php endif; ?>
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
    .btn-danger { background: #f56565; color: white; }
    .btn-danger:hover { background: #e53e3e; }
    .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.75rem; }

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
    .font-medium { font-weight: 500; }
    
    .badge-pill {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 9999px;
        font-weight: 600;
        line-height: 1;
    }
    .bg-info { background: #4299e1; }
    .bg-danger { background: #f56565; }
    .bg-warning { background: #ed8936; }
    .bg-orange-100 { background: #fffaf0; color: #dd6b20; }
    .bg-red-100 { background: #fff5f5; color: #e53e3e; }
    .bg-success { background: #48bb78; }
    .bg-secondary { background: #718096; }
    .bg-purple-100 { background: #ebf4ff; color: #5a67d8; }
    .bg-blue-100 { background: #ebf8ff; color: #3182ce; }
    .bg-yellow-100 { background: #fffff0; color: #d69e2e; }
    .bg-gray-100 { background: #f7fafc; color: #4a5568; }
    .bg-green-100 { background: #f0fff4; color: #38a169; }

    .text-white { color: white; }
    
    .empty-state-compact { text-align: center; }
    .py-5 { padding-top: 3rem; padding-bottom: 3rem; }
    .mb-3 { margin-bottom: 1rem; }
    
    .disabled { pointer-events: none; opacity: 0.6; }
    .mt-2 { margin-top: 0.5rem; }

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
/* End of Email Threads View */
?>