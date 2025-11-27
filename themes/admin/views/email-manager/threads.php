<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><i class="fas fa-comments"></i> Email Threads</h1>
            <p class="page-description">Manage and respond to email conversations</p>
        </div>
        <div class="page-header-actions">
            <a href="<?php echo app_base_url('/admin/email-manager'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-filter"></i>
            Filter Threads
        </h5>
    </div>
    <div class="card-content">
        <form method="GET" action="<?php echo app_base_url('/admin/email-manager/threads'); ?>" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="all" <?php echo (!isset($filters['status']) || $filters['status'] === 'all') ? 'selected' : ''; ?>>All Statuses</option>
                    <option value="new" <?php echo (isset($filters['status']) && $filters['status'] === 'new') ? 'selected' : ''; ?>>New</option>
                    <option value="in_progress" <?php echo (isset($filters['status']) && $filters['status'] === 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                    <option value="resolved" <?php echo (isset($filters['status']) && $filters['status'] === 'resolved') ? 'selected' : ''; ?>>Resolved</option>
                    <option value="closed" <?php echo (isset($filters['status']) && $filters['status'] === 'closed') ? 'selected' : ''; ?>>Closed</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category" class="form-select">
                    <option value="all" <?php echo (!isset($filters['category']) || $filters['category'] === 'all') ? 'selected' : ''; ?>>All Categories</option>
                    <option value="support" <?php echo (isset($filters['category']) && $filters['category'] === 'support') ? 'selected' : ''; ?>>Support</option>
                    <option value="billing" <?php echo (isset($filters['category']) && $filters['category'] === 'billing') ? 'selected' : ''; ?>>Billing</option>
                    <option value="feedback" <?php echo (isset($filters['category']) && $filters['category'] === 'feedback') ? 'selected' : ''; ?>>Feedback</option>
                    <option value="other" <?php echo (isset($filters['category']) && $filters['category'] === 'other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Priority</label>
                <select name="priority" class="form-select">
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
            
            <div class="lg:col-span-4">
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <a href="<?php echo app_base_url('/admin/email-manager/threads'); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Threads Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-list"></i>
            Email Threads
        </h5>
    </div>
    <div class="card-content p-0">
        <div class="table-container">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="50">Status</th>
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
                            <td colspan="6" class="text-center text-gray-500 py-8">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>No email threads found</p>
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
                                    <span class="badge <?php echo $statusClass; ?>">
                                        <i class="fas <?php echo $statusIcon; ?>"></i>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo app_base_url('/admin/email-manager/thread/' . $thread['id']); ?>" class="font-medium text-blue-600 hover:underline">
                                        <?php echo htmlspecialchars($thread['subject'] ?? 'No Subject'); ?>
                                    </a>
                                    <div class="text-sm text-gray-500 mt-1">
                                        <?php echo htmlspecialchars(substr($thread['last_message'] ?? '', 0, 80)) . (strlen($thread['last_message'] ?? '') > 80 ? '...' : ''); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-purple-100 text-purple-800">
                                        <?php echo ucfirst(htmlspecialchars($thread['category'] ?? 'General')); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $priority = $thread['priority'] ?? 'medium';
                                    $priorityClass = '';
                                    switch ($priority) {
                                        case 'low':
                                            $priorityClass = 'bg-gray-100 text-gray-800';
                                            break;
                                        case 'medium':
                                            $priorityClass = 'bg-blue-100 text-blue-800';
                                            break;
                                        case 'high':
                                            $priorityClass = 'bg-orange-100 text-orange-800';
                                            break;
                                        case 'urgent':
                                            $priorityClass = 'bg-red-100 text-red-800';
                                            break;
                                        default:
                                            $priorityClass = 'bg-gray-100 text-gray-800';
                                    }
                                    ?>
                                    <span class="badge <?php echo $priorityClass; ?>">
                                        <?php echo ucfirst(htmlspecialchars($priority)); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="text-sm">
                                        <?php echo date('M d, Y', strtotime($thread['updated_at'] ?? $thread['created_at'] ?? 'now')); ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <?php echo date('H:i', strtotime($thread['updated_at'] ?? $thread['created_at'] ?? 'now')); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?php echo app_base_url('/admin/email-manager/thread/' . $thread['id']); ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (!empty($threads)): ?>
            <div class="card-footer">
                <div class="flex justify-between items-center">
                    <div class="text-gray-600 text-sm">
                        Showing <?php echo count($threads); ?> of <?php echo number_format($total ?? 0); ?> threads
                    </div>
                    <div class="btn-group">
                        <?php if ($page > 1): ?>
                            <a class="btn btn-outline-secondary btn-sm" 
                               href="<?php echo app_base_url('/admin/email-manager/threads'); ?>?page=<?php echo $page - 1; ?>&limit=<?php echo $limit; ?><?php echo isset($filters['status']) && $filters['status'] !== 'all' ? '&status=' . urlencode($filters['status']) : ''; ?><?php echo isset($filters['category']) && $filters['category'] !== 'all' ? '&category=' . urlencode($filters['category']) : ''; ?><?php echo isset($filters['priority']) && $filters['priority'] !== 'all' ? '&priority=' . urlencode($filters['priority']) : ''; ?><?php echo isset($filters['search']) ? '&search=' . urlencode($filters['search']) : ''; ?>">
                                <i class="fas fa-chevron-left"></i> Prev
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($page < ($total_pages ?? 1)): ?>
                            <a class="btn btn-outline-secondary btn-sm" 
                               href="<?php echo app_base_url('/admin/email-manager/threads'); ?>?page=<?php echo $page + 1; ?>&limit=<?php echo $limit; ?><?php echo isset($filters['status']) && $filters['status'] !== 'all' ? '&status=' . urlencode($filters['status']) : ''; ?><?php echo isset($filters['category']) && $filters['category'] !== 'all' ? '&category=' . urlencode($filters['category']) : ''; ?><?php echo isset($filters['priority']) && $filters['priority'] !== 'all' ? '&priority=' . urlencode($filters['priority']) : ''; ?><?php echo isset($filters['search']) ? '&search=' . urlencode($filters['search']) : ''; ?>">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>