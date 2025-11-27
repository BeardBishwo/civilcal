<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><i class="fas fa-file-alt"></i> Email Templates</h1>
            <p class="page-description">Manage email templates for different types of communications</p>
        </div>
        <div class="page-header-actions">
            <a href="<?php echo app_base_url('/admin/email-manager/template/create'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Template
            </a>
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
            Filter Templates
        </h5>
    </div>
    <div class="card-content">
        <form method="GET" action="<?php echo app_base_url('/admin/email-manager/templates'); ?>" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category" class="form-select">
                    <option value="all" <?php echo (!isset($filters['category']) || $filters['category'] === 'all') ? 'selected' : ''; ?>>All Categories</option>
                    <option value="welcome" <?php echo (isset($filters['category']) && $filters['category'] === 'welcome') ? 'selected' : ''; ?>>Welcome</option>
                    <option value="notification" <?php echo (isset($filters['category']) && $filters['category'] === 'notification') ? 'selected' : ''; ?>>Notification</option>
                    <option value="password_reset" <?php echo (isset($filters['category']) && $filters['category'] === 'password_reset') ? 'selected' : ''; ?>>Password Reset</option>
                    <option value="verification" <?php echo (isset($filters['category']) && $filters['category'] === 'verification') ? 'selected' : ''; ?>>Verification</option>
                    <option value="general" <?php echo (isset($filters['category']) && $filters['category'] === 'general') ? 'selected' : ''; ?>>General</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="is_active" class="form-select">
                    <option value="" <?php echo (!isset($filters['is_active'])) ? 'selected' : ''; ?>>All Statuses</option>
                    <option value="1" <?php echo (isset($filters['is_active']) && $filters['is_active'] == 1) ? 'selected' : ''; ?>>Active</option>
                    <option value="0" <?php echo (isset($filters['is_active']) && $filters['is_active'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search templates..." value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
            </div>
            
            <div class="lg:col-span-4">
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <a href="<?php echo app_base_url('/admin/email-manager/templates'); ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Templates Table -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-list"></i>
            Email Templates
        </h5>
    </div>
    <div class="card-content p-0">
        <div class="table-container">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th width="150">Category</th>
                        <th width="100">Status</th>
                        <th width="150">Last Modified</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($templates)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-gray-500 py-8">
                                <i class="fas fa-file-alt fa-2x mb-2"></i>
                                <p>No email templates found</p>
                                <a href="<?php echo app_base_url('/admin/email-manager/template/create'); ?>" class="btn btn-primary mt-3">
                                    <i class="fas fa-plus"></i> Create Your First Template
                                </a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($templates as $template): ?>
                            <tr>
                                <td>
                                    <div class="font-medium text-gray-900">
                                        <?php echo htmlspecialchars($template['name'] ?? 'Untitled Template'); ?>
                                    </div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        Subject: <?php echo htmlspecialchars($template['subject'] ?? 'No Subject'); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-purple-100 text-purple-800">
                                        <?php echo ucfirst(htmlspecialchars($template['category'] ?? 'General')); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($template['is_active'])): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle mr-1"></i> Active
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-times-circle mr-1"></i> Inactive
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="text-sm">
                                        <?php echo date('M d, Y', strtotime($template['updated_at'] ?? $template['created_at'] ?? 'now')); ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <?php echo date('H:i', strtotime($template['updated_at'] ?? $template['created_at'] ?? 'now')); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?php echo app_base_url('/admin/email-manager/template/' . $template['id'] . '/edit'); ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteTemplate(<?php echo $template['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (!empty($templates)): ?>
            <div class="card-footer">
                <div class="flex justify-between items-center">
                    <div class="text-gray-600 text-sm">
                        Showing <?php echo count($templates); ?> of <?php echo number_format($total ?? 0); ?> templates
                    </div>
                    <div class="btn-group">
                        <?php if ($page > 1): ?>
                            <a class="btn btn-outline-secondary btn-sm" 
                               href="<?php echo app_base_url('/admin/email-manager/templates'); ?>?page=<?php echo $page - 1; ?>&per_page=<?php echo $per_page; ?><?php echo isset($filters['category']) && $filters['category'] !== 'all' ? '&category=' . urlencode($filters['category']) : ''; ?><?php echo isset($filters['is_active']) ? '&is_active=' . urlencode($filters['is_active']) : ''; ?><?php echo isset($filters['search']) ? '&search=' . urlencode($filters['search']) : ''; ?>">
                                <i class="fas fa-chevron-left"></i> Prev
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($page < ($total_pages ?? 1)): ?>
                            <a class="btn btn-outline-secondary btn-sm" 
                               href="<?php echo app_base_url('/admin/email-manager/templates'); ?>?page=<?php echo $page + 1; ?>&per_page=<?php echo $per_page; ?><?php echo isset($filters['category']) && $filters['category'] !== 'all' ? '&category=' . urlencode($filters['category']) : ''; ?><?php echo isset($filters['is_active']) ? '&is_active=' . urlencode($filters['is_active']) : ''; ?><?php echo isset($filters['search']) ? '&search=' . urlencode($filters['search']) : ''; ?>">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function deleteTemplate(templateId) {
    if (confirm('Are you sure you want to delete this template? This action cannot be undone.')) {
        fetch('<?php echo app_base_url('/admin/email-manager/template/'); ?>' + templateId + '/delete', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Template deleted successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Failed to delete template'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the template');
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Email templates page loaded');
});
</script>