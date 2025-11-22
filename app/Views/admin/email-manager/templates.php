<?php
// Start output buffering for layout
ob_start();
?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h1>
                <i class="fas fa-file-alt"></i>
                Email Templates
            </h1>
            <p>Create and manage email templates for quick responses</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-secondary" onclick="window.location.href='/admin/email-manager'">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </button>
            <button class="btn btn-primary" onclick="openTemplateModal()">
                <i class="fas fa-plus"></i>
                New Template
            </button>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="content-card">
    <div class="card-header">
        <h3><i class="fas fa-filter"></i> Filters</h3>
    </div>
    <div class="card-body">
        <div class="filters-grid">
            <div class="filter-group">
                <label for="category-filter" class="filter-label">Category:</label>
                <select id="category-filter" class="form-control">
                    <option value="">All Categories</option>
                    <option value="general">General</option>
                    <option value="support">Support</option>
                    <option value="billing">Billing</option>
                    <option value="technical">Technical</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="status-filter" class="filter-label">Status:</label>
                <select id="status-filter" class="form-control">
                    <option value="">All</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            <div class="filter-group filter-search">
                <label for="search-input" class="filter-label">Search:</label>
                <div class="search-input-group">
                    <input type="text" id="search-input" class="form-control" placeholder="Search templates...">
                    <i class="fas fa-search search-icon"></i>
                </div>
            </div>
        </div>

        <div class="filter-actions">
            <button class="btn btn-secondary" onclick="clearFilters()">
                <i class="fas fa-times"></i>
                Clear Filters
            </button>
            <button class="btn btn-primary" onclick="applyFilters()">
                <i class="fas fa-search"></i>
                Apply Filters
            </button>
        </div>
    </div>
</div>

<!-- Templates List -->
<div class="content-card">
    <div class="card-header">
        <div class="header-content">
            <h3><i class="fas fa-list"></i> Templates</h3>
            <div class="header-stats">
                <span class="stat-badge">
                    <i class="fas fa-file-alt"></i>
                    <span id="total-count"><?php echo $total ?? 0; ?></span> Total
                </span>
                <span class="stat-badge success">
                    <i class="fas fa-check-circle"></i>
                    <span id="active-count">0</span> Active
                </span>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Loading State -->
        <div id="loading-state" class="loading-container" style="display: none;">
            <div class="spinner"></div>
            <p>Loading templates...</p>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="empty-state" style="display: <?php echo empty($templates) ? 'block' : 'none'; ?>;">
            <div class="empty-state-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <h3>No Templates Found</h3>
            <p>Create your first email template to speed up your responses.</p>
            <button class="btn btn-primary" onclick="openTemplateModal()">
                <i class="fas fa-plus"></i>
                Create Template
            </button>
        </div>

        <!-- Templates Grid -->
        <div id="templates-container" class="templates-grid" style="display: <?php echo empty($templates) ? 'none' : 'grid'; ?>;">
            <?php if (!empty($templates)): ?>
                <?php foreach ($templates as $template): ?>
                    <div class="template-card" data-template-id="<?php echo $template['id']; ?>">
                        <div class="template-header">
                            <div>
                                <h4 class="template-name"><?php echo htmlspecialchars($template['name']); ?></h4>
                                <span class="template-category badge badge-<?php echo $template['category']; ?>">
                                    <?php echo ucfirst($template['category']); ?>
                                </span>
                            </div>
                            <div class="template-status">
                                <?php if ($template['is_active']): ?>
                                    <span class="status-badge active">
                                        <i class="fas fa-check-circle"></i> Active
                                    </span>
                                <?php else: ?>
                                    <span class="status-badge inactive">
                                        <i class="fas fa-times-circle"></i> Inactive
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="template-subject">
                            <strong>Subject:</strong> <?php echo htmlspecialchars($template['subject']); ?>
                        </div>

                        <?php if (!empty($template['description'])): ?>
                            <div class="template-description">
                                <?php echo htmlspecialchars($template['description']); ?>
                            </div>
                        <?php endif; ?>

                        <div class="template-preview">
                            <?php
                            $preview = strip_tags($template['content']);
                            echo htmlspecialchars(substr($preview, 0, 150)) . (strlen($preview) > 150 ? '...' : '');
                            ?>
                        </div>

                        <?php if (!empty($template['variables'])): ?>
                            <div class="template-variables">
                                <strong>Variables:</strong>
                                <?php
                                $vars = json_decode($template['variables'], true);
                                if (is_array($vars)) {
                                    echo implode(', ', array_map(function ($v) {
                                        return '<code>{{' . htmlspecialchars($v) . '}}</code>';
                                    }, $vars));
                                }
                                ?>
                            </div>
                        <?php endif; ?>

                        <div class="template-actions">
                            <button class="btn btn-sm btn-primary" onclick="useTemplate(<?php echo $template['id']; ?>)">
                                <i class="fas fa-check"></i>
                                Use
                            </button>
                            <button class="btn btn-sm btn-secondary" onclick="editTemplate(<?php echo $template['id']; ?>)">
                                <i class="fas fa-edit"></i>
                                Edit
                            </button>
                            <button class="btn btn-sm btn-<?php echo $template['is_active'] ? 'warning' : 'success'; ?>"
                                onclick="toggleTemplateStatus(<?php echo $template['id']; ?>, <?php echo $template['is_active'] ? 0 : 1; ?>)">
                                <i class="fas fa-<?php echo $template['is_active'] ? 'eye-slash' : 'eye'; ?>"></i>
                                <?php echo $template['is_active'] ? 'Deactivate' : 'Activate'; ?>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteTemplate(<?php echo $template['id']; ?>)">
                                <i class="fas fa-trash"></i>
                                Delete
                            </button>
                        </div>

                        <div class="template-meta">
                            <small class="text-muted">
                                Created: <?php echo date('M d, Y', strtotime($template['created_at'])); ?>
                                <?php if (isset($template['first_name'])): ?>
                                    by <?php echo htmlspecialchars($template['first_name'] . ' ' . $template['last_name']); ?>
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if (isset($total_pages) && $total_pages > 1): ?>
            <div class="pagination-container">
                <div class="pagination-info">
                    Showing <?php echo (($page - 1) * $per_page) + 1; ?> to
                    <?php echo min($page * $per_page, $total); ?> of
                    <?php echo $total; ?> templates
                </div>
                <div class="pagination-controls">
                    <button class="btn btn-secondary btn-sm" <?php echo $page <= 1 ? 'disabled' : ''; ?>
                        onclick="changePage(<?php echo $page - 1; ?>)">
                        <i class="fas fa-chevron-left"></i>
                        Previous
                    </button>
                    <div class="page-numbers">
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <button class="btn btn-sm <?php echo $i === $page ? 'btn-primary' : 'btn-secondary'; ?>"
                                onclick="changePage(<?php echo $i; ?>)">
                                <?php echo $i; ?>
                            </button>
                        <?php endfor; ?>
                    </div>
                    <button class="btn btn-secondary btn-sm" <?php echo $page >= $total_pages ? 'disabled' : ''; ?>
                        onclick="changePage(<?php echo $page + 1; ?>)">
                        Next
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Template Modal -->
<div id="template-modal" class="modal" style="display: none;">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h3 id="modal-title"><i class="fas fa-plus"></i> Create Email Template</h3>
            <button class="modal-close" onclick="closeTemplateModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="template-form">
                <input type="hidden" id="template-id" name="id">

                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label for="template-name" class="form-label">Template Name *</label>
                        <input type="text" id="template-name" name="name" class="form-control" required>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="template-category" class="form-label">Category *</label>
                        <select id="template-category" name="category" class="form-control" required>
                            <option value="general">General</option>
                            <option value="support">Support</option>
                            <option value="billing">Billing</option>
                            <option value="technical">Technical</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="template-subject" class="form-label">Email Subject *</label>
                    <input type="text" id="template-subject" name="subject" class="form-control" required
                        placeholder="Use {{variable_name}} for placeholders">
                </div>

                <div class="form-group">
                    <label for="template-description" class="form-label">Description</label>
                    <textarea id="template-description" name="description" rows="2" class="form-control"
                        placeholder="Brief description of this template's purpose"></textarea>
                </div>

                <div class="form-group">
                    <label for="template-content" class="form-label">Email Content *</label>
                    <textarea id="template-content" name="content" rows="10" class="form-control" required
                        placeholder="Use {{variable_name}} for placeholders (e.g., {{user_name}}, {{ticket_id}})"></textarea>
                    <small class="form-text text-muted">
                        HTML is supported. Use {{variable_name}} syntax for placeholders.
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-checkbox">
                        <input type="checkbox" id="template-active" name="is_active" checked>
                        <span>Active (template can be used immediately)</span>
                    </label>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeTemplateModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Save Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .templates-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .template-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-color, #ddd);
        border-radius: 8px;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }

    .template-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .template-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .template-name {
        margin: 0 0 0.5rem 0;
        font-size: 1.1rem;
        color: var(--text-primary, #333);
    }

    .template-category {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-general {
        background: #6c757d;
        color: white;
    }

    .badge-support {
        background: #007bff;
        color: white;
    }

    .badge-billing {
        background: #ffc107;
        color: #333;
    }

    .badge-technical {
        background: #28a745;
        color: white;
    }

    .template-subject {
        margin: 0.75rem 0;
        padding: 0.75rem;
        background: var(--bg-secondary, #f8f9fa);
        border-left: 3px solid var(--primary-color, #007bff);
        border-radius: 4px;
        font-size: 0.9rem;
    }

    .template-description {
        margin: 0.75rem 0;
        color: var(--text-secondary, #666);
        font-size: 0.9rem;
    }

    .template-preview {
        margin: 0.75rem 0;
        padding: 0.75rem;
        background: var(--bg-light, #f8f9fa);
        border-radius: 4px;
        font-size: 0.85rem;
        color: var(--text-secondary, #666);
        line-height: 1.5;
    }

    .template-variables {
        margin: 0.75rem 0;
        font-size: 0.85rem;
    }

    .template-variables code {
        background: #e9ecef;
        padding: 0.2rem 0.4rem;
        border-radius: 3px;
        font-size: 0.8rem;
        color: #d63384;
        margin-right: 0.25rem;
    }

    .template-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color, #ddd);
    }

    .template-meta {
        margin-top: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1px solid var(--border-color, #eee);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-badge.active {
        background: #d4edda;
        color: #155724;
    }

    .status-badge.inactive {
        background: #f8d7da;
        color: #721c24;
    }

    .modal-lg {
        max-width: 800px;
    }

    .form-row {
        display: flex;
        gap: 1rem;
    }

    .col-md-8 {
        flex: 0 0 66.666%;
    }

    <option value="support">Support</option><option value="billing">Billing</option><option value="technical">Technical</option></select></div></div><div class="form-group"><label for="template-subject" class="form-label">Email Subject *</label><input type="text" id="template-subject" name="subject" class="form-control" required placeholder="Use {{variable_name}} for placeholders"></div><div class="form-group"><label for="template-description" class="form-label">Description</label><textarea id="template-description" name="description" rows="2" class="form-control"

    placeholder="Brief description of this template's purpose"></textarea></div><div class="form-group"><label for="template-content" class="form-label">Email Content *</label><textarea id="template-content" name="content" rows="10" class="form-control" required placeholder="Use {{variable_name}} for placeholders (e.g., {{user_name}}, {{ticket_id}})"></textarea><small class="form-text text-muted">HTML is supported. Use {
            {
            variable_name
        }
    }

    syntax for placeholders. </small></div><div class="form-group"><label class="form-checkbox"><input type="checkbox" id="template-active" name="is_active" checked><span>Active (template can be used immediately)</span></label></div><div class="form-actions"><button type="button" class="btn btn-secondary" onclick="closeTemplateModal()">Cancel</button><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>Save Template </button></div></form></div></div></div><style>.templates-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .template-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-color, #ddd);
        border-radius: 8px;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }

    .template-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .template-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .template-name {
        margin: 0 0 0.5rem 0;
        font-size: 1.1rem;
        color: var(--text-primary, #333);
    }

    .template-category {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-general {
        background: #6c757d;
        color: white;
    }

    .badge-support {
        background: #007bff;
        color: white;
    }

    .badge-billing {
        background: #ffc107;
        color: #333;
    }

    .badge-technical {
        background: #28a745;
        color: white;
    }

    .template-subject {
        margin: 0.75rem 0;
        padding: 0.75rem;
        background: var(--bg-secondary, #f8f9fa);
        border-left: 3px solid var(--primary-color, #007bff);
        border-radius: 4px;
        font-size: 0.9rem;
    }

    .template-description {
        margin: 0.75rem 0;
        color: var(--text-secondary, #666);
        font-size: 0.9rem;
    }

    .template-preview {
        margin: 0.75rem 0;
        padding: 0.75rem;
        background: var(--bg-light, #f8f9fa);
        border-radius: 4px;
        font-size: 0.85rem;
        color: var(--text-secondary, #666);
        line-height: 1.5;
    }

    .template-variables {
        margin: 0.75rem 0;
        font-size: 0.85rem;
    }

    .template-variables code {
        background: #e9ecef;
        padding: 0.2rem 0.4rem;
        border-radius: 3px;
        font-size: 0.8rem;
        color: #d63384;
        margin-right: 0.25rem;
    }

    .template-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color, #ddd);
    }

    .template-meta {
        margin-top: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1px solid var(--border-color, #eee);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-badge.active {
        background: #d4edda;
        color: #155724;
    }

    .status-badge.inactive {
        background: #f8d7da;
        color: #721c24;
    }

    .modal-lg {
        max-width: 800px;
    }

    .form-row {
        display: flex;
        gap: 1rem;
    }

    .col-md-8 {
        flex: 0 0 66.666%;
    }

    .col-md-4 {
        flex: 0 0 33.333%;
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        updateStats();

        // Initialize TinyMCE
        tinymce.init({
            selector: '#template-content',
            height: 400,
            menubar: false,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic backcolor | alignleft aligncenter ' + +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
        });
    });

    let currentFilters = {};
    let currentPage = <?php echo $page ?? 1; ?>;

    function applyFilters() {
        currentFilters = {
            category: document.getElementById('category-filter').value,
            is_active: document.getElementById('status-filter').value,
            search: document.getElementById('search-input').value
        };
        currentPage = 1;
        reloadTemplates();
    }

    function clearFilters() {
        document.getElementById('category-filter').value = '';
        document.getElementById('status-filter').value = '';
        document.getElementById('search-input').value = '';
        currentFilters = {};
        currentPage = 1;
        reloadTemplates();
    }

    function changePage(page) {
        currentPage = page;
        reloadTemplates();
    }

    function reloadTemplates() {
        const params = new URLSearchParams({
            ...currentFilters,
            page: currentPage
        });
        window.location.href = `/admin/email-manager/templates?${params}`;
    }

    function openTemplateModal(templateData = null) {
        const modal = document.getElementById('template-modal');
        const form = document.getElementById('template-form');
        const title = document.getElementById('modal-title');

        form.reset();

        if (templateData) {
            title.innerHTML = '<i class="fas fa-edit"></i> Edit Email Template';
            document.getElementById('template-id').value = templateData.id;
            document.getElementById('template-name').value = templateData.name;
            document.getElementById('template-subject').value = templateData.subject;
            document.getElementById('template-content').value = templateData.content;
            document.getElementById('template-category').value = templateData.category;
            document.getElementById('template-description').value = templateData.description || '';
            document.getElementById('template-active').checked = templateData.is_active == 1;

            // Set content in TinyMCE
            if (tinymce.get('template-content')) {
                tinymce.get('template-content').setContent(templateData.content);
            }
        } else {
            title.innerHTML = '<i class="fas fa-plus"></i> Create Email Template';
            document.getElementById('template-id').value = '';

            // Clear TinyMCE
            if (tinymce.get('template-content')) {
                tinymce.get('template-content').setContent('');
            }
        }

        modal.style.display = 'block';
    }

    function closeTemplateModal() {
        document.getElementById('template-modal').style.display = 'none';
    }

    document.getElementById('template-form').addEventListener('submit', function(e) {
        e.preventDefault();

        // Trigger save to update textarea
        tinymce.triggerSave();

        const formData = new FormData(e.target);
        const data = {
            name: formData.get('name'),
            subject: formData.get('subject'),
            content: formData.get('content'),
            category: formData.get('category'),
            description: formData.get('description'),
            is_active: formData.get('is_active') ? 1 : 0
        };

        const templateId = document.getElementById('template-id').value;
        const url = templateId ?
            `/admin/email-manager/template/${templateId}` :
            '/admin/email-manager/templates';
        const method = templateId ? 'PUT' : 'POST';

        fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showNotification(templateId ? 'Template updated successfully!' : 'Template created successfully!', 'success');
                    closeTemplateModal();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showNotification(result.error || 'Failed to save template', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Failed to save template. Please try again.', 'error');
            });
    });

    function editTemplate(id) {
        fetch(`/admin/email-manager/template/${id}?ajax=1`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    openTemplateModal(result.template);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function useTemplate(id) {
        showNotification('Template loaded! (Feature coming soon)', 'info');
    }

    function toggleTemplateStatus(id, newStatus) {
        if (!confirm(`Are you sure you want to ${newStatus ? 'activate' : 'deactivate'} this template?`)) {
            return;
        }

        fetch(`/admin/email-manager/template/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    is_active: newStatus
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showNotification('Template status updated!', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function deleteTemplate(id) {
        if (!confirm('Are you sure you want to delete this template? This action cannot be undone.')) {
            return;
        }

        fetch(`/admin/email-manager/template/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showNotification('Template deleted successfully!', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function updateStats() {
        const activeCount = document.querySelectorAll('.status-badge.active').length;
        const totalCount = document.querySelectorAll('.template-card').length;

        if (document.getElementById('active-count')) {
            document.getElementById('active-count').textContent = activeCount;
        }
        if (document.getElementById('total-count')) {
            document.getElementById('total-count').textContent = totalCount;
        }
    }

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type}`;
        notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        ${message}
        <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
    `;

        const content = document.querySelector('.admin-content');
        if (content) {
            content.insertBefore(notification, content.firstChild);

            setTimeout(() => {
                notification.remove();
            }, 5000);
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('template-modal');
        if (event.target === modal) {
            closeTemplateModal();
        }
    }
</script>

<?php
// Capture content and pass to layout
$content = ob_get_clean();
$this->layout('admin/layout', array_merge(get_defined_vars(), ['content' => $content]));
?>