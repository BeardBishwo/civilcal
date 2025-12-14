<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title">
                <i class="fas fa-file-alt"></i>
                <?php echo !empty($template) ? 'Edit Email Template' : 'Create Email Template'; ?>
            </h1>
            <p class="page-description">
                <?php echo !empty($template) ? 'Modify an existing email template' : 'Create a new email template'; ?>
            </p>
        </div>
        <div class="page-header-actions">
            <a href="<?php echo app_base_url('/admin/email-manager/templates'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Templates
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-edit"></i>
                    Template Content
                </h5>
            </div>
            <div class="card-content">
                <form method="POST" action="<?php echo !empty($template) ? app_base_url('/admin/email-manager/template/' . $template['id'] . '/update') : app_base_url('/admin/email-manager/template/create'); ?>" id="templateForm">
                    <div class="form-group mb-4">
                        <label class="form-label">Template Name *</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($template['name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="form-label">Subject *</label>
                        <input type="text" name="subject" class="form-control" value="<?php echo htmlspecialchars($template['subject'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="form-label">Content *</label>
                        <textarea name="content" class="form-control" rows="12" required><?php echo htmlspecialchars($template['content'] ?? ''); ?></textarea>
                        <div class="form-help">
                            You can use variables like {{username}}, {{email}}, etc. in your template content.
                        </div>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($template['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo !empty($template) ? 'Update Template' : 'Create Template'; ?>
                        </button>
                        <a href="<?php echo app_base_url('/admin/email-manager/templates'); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="space-y-6">
        <!-- Template Settings -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-cog"></i>
                    Settings
                </h5>
            </div>
            <div class="card-content">
                <div class="space-y-4">
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select" form="templateForm">
                            <option value="welcome" <?php echo (isset($template['category']) && $template['category'] === 'welcome') ? 'selected' : ''; ?>>Welcome</option>
                            <option value="notification" <?php echo (isset($template['category']) && $template['category'] === 'notification') ? 'selected' : ''; ?>>Notification</option>
                            <option value="password_reset" <?php echo (isset($template['category']) && $template['category'] === 'password_reset') ? 'selected' : ''; ?>>Password Reset</option>
                            <option value="verification" <?php echo (isset($template['category']) && $template['category'] === 'verification') ? 'selected' : ''; ?>>Verification</option>
                            <option value="general" <?php echo (!isset($template['category']) || $template['category'] === 'general') ? 'selected' : ''; ?>>General</option>
                        </select>
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="isActive" class="form-check-input" value="1" form="templateForm" <?php echo (!isset($template) || !empty($template['is_active'])) ? 'checked' : ''; ?>>
                        <label for="isActive" class="form-check-label">Active</label>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Available Variables -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-code"></i>
                    Available Variables
                </h5>
            </div>
            <div class="card-content">
                <div class="text-sm space-y-2">
                    <p class="text-gray-600">You can use these variables in your template:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li><code>{{username}}</code> - User's username</li>
                        <li><code>{{email}}</code> - User's email address</li>
                        <li><code>{{first_name}}</code> - User's first name</li>
                        <li><code>{{last_name}}</code> - User's last name</li>
                        <li><code>{{site_name}}</code> - Site name</li>
                        <li><code>{{site_url}}</code> - Site URL</li>
                    </ul>
                    <p class="mt-3 text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Wrap variables in double curly braces.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle form submission
    document.getElementById('templateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const action = this.action;
        
        fetch(action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('<?php echo !empty($template) ? 'Template updated' : 'Template created'; ?> successfully!', 'success');
                window.location.href = '<?php echo app_base_url('/admin/email-manager/templates'); ?>';
            } else {
                const errorMessage = data.errors ? Object.values(data.errors).join('\n') : (data.error || 'An error occurred');
                showNotification('Error: ' + errorMessage, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while saving the template', 'error');
        });
    });
    
    console.log('Template form loaded');
});
</script>