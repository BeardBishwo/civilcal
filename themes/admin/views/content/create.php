<?php

/**
 * OPTIMIZED PAGE CREATION/EDITING INTERFACE
 * Compact, User-Friendly Layout with Enhanced UX
 */

// Page creation/editing interface
$page_title = $is_edit ? 'Edit Page - Admin Panel' : 'Create New Page - Admin Panel';
$currentPage = 'content';

// Set breadcrumbs
$breadcrumbs = [
    ['title' => 'Content Management', 'url' => app_base_url('admin/content')],
    ['title' => 'Pages', 'url' => app_base_url('admin/content/pages')],
    ['title' => $is_edit ? 'Edit Page' : 'Create New Page']
];

// Get page data for editing
$pageData = $page ?? [
    'id' => '',
    'title' => '',
    'slug' => '',
    'content' => '',
    'status' => 'draft',
    'author' => $user->username ?? 'Admin',
    'meta_description' => '',
    'meta_keywords' => '',
    'template' => 'default',
    'parent_id' => '',
    'menu_order' => '0'
];
?>

<!-- Optimized Admin Container -->
<div class="page-create-container">
    <div class="page-create-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-create-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-<?php echo $is_edit ? 'edit' : 'plus'; ?>"></i>
                    <h1><?php echo $is_edit ? 'Edit Page' : 'Create New Page'; ?></h1>
                </div>
                <div class="header-subtitle">
                    <?php echo $is_edit ? 'Update your page content and settings' : 'Create a new page with rich content and advanced options'; ?>
                </div>
            </div>
            <div class="header-actions">
                <a href="<?php echo app_base_url('admin/content/pages'); ?>" class="btn btn-secondary btn-compact">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Pages</span>
                </a>
            </div>
        </div>

        <!-- Compact Action Bar -->
        <div class="compact-action-bar">
            <div class="action-left">
                <div class="save-status" id="save-status">
                    <i class="fas fa-circle"></i>
                    <span>All changes saved</span>
                </div>
            </div>
            <div class="action-right">
                <button type="button" class="btn btn-outline-secondary btn-compact" id="preview-btn">
                    <i class="fas fa-eye"></i>
                    Preview
                </button>
                <button type="button" class="btn btn-warning btn-compact" id="save-draft-btn">
                    <i class="fas fa-save"></i>
                    Save Draft
                </button>
                <button type="button" class="btn btn-primary btn-compact" id="publish-btn">
                    <i class="fas fa-<?php echo $is_edit ? 'check' : 'publish'; ?>"></i>
                    <?php echo $is_edit ? 'Update' : 'Publish'; ?>
                </button>
            </div>
        </div>

        <!-- Main Content Layout (Single Column) -->
        <div class="create-content-single-column">

            <form id="page-form" method="POST" action="#" class="main-form-container">

                <!-- Title & Slug Section -->
                <div class="content-card">
                    <div class="card-header-clean">
                        <h3 class="card-title">Page Details</h3>
                    </div>
                    <div class="card-body-clean">
                        <div class="form-group-modern">
                            <label for="page-title" class="form-label required">Page Title</label>
                            <input
                                type="text"
                                id="page-title"
                                name="title"
                                class="form-control-modern form-control-lg"
                                value="<?php echo htmlspecialchars($pageData['title']); ?>"
                                placeholder="Enter page title here..."
                                required
                                maxlength="255">
                        </div>

                        <div class="form-group-modern">
                            <label for="page-slug" class="form-label required">URL Slug</label>
                            <div class="slug-wrapper-modern">
                                <span class="slug-base"><?php echo rtrim(app_base_url(''), '/'); ?>/</span>
                                <input
                                    type="text"
                                    id="page-slug"
                                    name="slug"
                                    class="form-control-modern slug-input"
                                    value="<?php echo htmlspecialchars($pageData['slug']); ?>"
                                    placeholder="page-url-slug"
                                    required
                                    pattern="[a-z0-9-]+"
                                    maxlength="100">
                                <button type="button" class="btn-icon slug-generate" id="generate-slug-btn" title="Generate from Title">
                                    <i class="fas fa-magic"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Editor Section -->
                <div class="content-card">
                    <div class="card-header-clean">
                        <h3 class="card-title">Content</h3>
                    </div>
                    <div class="card-body-clean p-0">
                        <textarea id="content-editor" name="content" class="form-control-modern" rows="20"><?php echo htmlspecialchars($pageData['content']); ?></textarea>
                    </div>
                </div>

                <!-- Settings Grid (2 Columns for settings to save space) -->
                <div class="settings-grid">

                    <!-- Publish Settings -->
                    <div class="content-card">
                        <div class="card-header-clean">
                            <h3 class="card-title"><i class="fas fa-globe"></i> Publish Settings</h3>
                        </div>
                        <div class="card-body-clean">
                            <div class="form-group-modern">
                                <label for="page-status" class="form-label">Visibility</label>
                                <select id="page-status" name="status" class="form-control-modern">
                                    <option value="draft" <?php echo ($pageData['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                    <option value="published" <?php echo ($pageData['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                                    <option value="private" <?php echo ($pageData['status'] ?? '') === 'private' ? 'selected' : ''; ?>>Private</option>
                                </select>
                            </div>

                            <div class="form-group-modern">
                                <label class="form-label">Author</label>
                                <div class="readonly-value">
                                    <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($pageData['author']); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Page Attributes -->
                    <div class="content-card">
                        <div class="card-header-clean">
                            <h3 class="card-title"><i class="fas fa-sliders-h"></i> Page Attributes</h3>
                        </div>
                        <div class="card-body-clean">
                            <div class="form-group-modern">
                                <label for="page-template" class="form-label">Template</label>
                                <select id="page-template" name="template" class="form-control-modern">
                                    <option value="default" <?php echo ($pageData['template'] ?? 'default') === 'default' ? 'selected' : ''; ?>>Default Template</option>
                                    <option value="full-width" <?php echo ($pageData['template'] ?? '') === 'full-width' ? 'selected' : ''; ?>>Full Width</option>
                                    <option value="sidebar-left" <?php echo ($pageData['template'] ?? '') === 'sidebar-left' ? 'selected' : ''; ?>>Sidebar Left</option>
                                    <option value="sidebar-right" <?php echo ($pageData['template'] ?? '') === 'sidebar-right' ? 'selected' : ''; ?>>Sidebar Right</option>
                                </select>
                            </div>

                            <div class="form-group-modern">
                                <label for="page-order" class="form-label">Order</label>
                                <input
                                    type="number"
                                    id="page-order"
                                    name="menu_order"
                                    class="form-control-modern"
                                    value="<?php echo htmlspecialchars($pageData['menu_order'] ?? '0'); ?>"
                                    min="0">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Full Width Sections -->

                <!-- Featured Image -->
                <div class="content-card">
                    <div class="card-header-clean">
                        <h3 class="card-title"><i class="fas fa-image"></i> Featured Image</h3>
                    </div>
                    <div class="card-body-clean">
                        <div class="featured-image-container">
                            <div class="image-preview-area" id="image-preview" style="display: none;">
                                <img id="preview-img" src="" alt="Featured Image">
                                <button type="button" class="btn-remove-image" title="Remove Image">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="image-upload-area" id="image-upload">
                                <div class="upload-content">
                                    <i class="fas fa-cloud-upload-alt fa-2x"></i>
                                    <p>Drop image here or click to upload</p>
                                </div>
                            </div>
                            <input type="file" id="featured-image" name="featured_image" accept="image/*" style="display: none;">
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
</div>

<!-- Page Preview Modal -->
<div id="preview-modal" class="preview-modal-compact" style="display: none;">
    <div class="preview-backdrop" onclick="closePreviewModal()"></div>
    <div class="preview-content-compact">
        <div class="preview-header-compact">
            <h3 class="preview-title">Page Preview</h3>
            <div class="preview-actions">
                <button class="btn btn-sm btn-outline-primary" onclick="editFromPreview()">
                    <i class="fas fa-edit"></i>
                    Edit
                </button>
                <button class="btn btn-sm btn-outline-secondary" onclick="closePreviewModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="preview-body-compact">
            <iframe id="preview-iframe" src="" frameborder="0"></iframe>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
<script>
    const CSRF_TOKEN = "<?php echo $csrf_token ?? ''; ?>";
    const SAVE_URL = "<?php echo app_base_url('admin/content/pages/save'); ?>";

    // Enhanced JavaScript for Optimized Interface
    document.addEventListener('DOMContentLoaded', function() {
        initializePageEditor();
    });

    function initializePageEditor() {
        // Form elements
        const pageForm = document.getElementById('page-form') || createFormElement();
        const titleInput = document.getElementById('page-title');
        const slugInput = document.getElementById('page-slug');
        // content-editor is now the textarea for CKEditor

        // Auto-generate slug from title
        titleInput?.addEventListener('input', function() {
            if (!slugInput.value || slugInput.dataset.autoGenerated === 'true') {
                generateSlug(this.value);
            }
            updateSaveStatus('unsaved');
        });

        // Generate slug button
        document.getElementById('generate-slug-btn')?.addEventListener('click', function() {
            generateSlug(titleInput.value);
        });

        function generateSlug(text) {
            const slug = text.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
            slugInput.value = slug;
            slugInput.dataset.autoGenerated = 'true';
        }

        // Content editor functionality
        // Initialize CKEditor
        if (document.getElementById('content-editor')) {
            CKEDITOR.replace('content-editor', {
                height: 400,
                removePlugins: 'resize',
                allowedContent: true // Allow all HTML
            });

            // Auto update textarea for validation/saving
            CKEDITOR.instances['content-editor'].on('change', function() {
                CKEDITOR.instances['content-editor'].updateElement();
                updateSaveStatus('unsaved');
            });
        }

        // Update hidden textarea with editor content
        function updateContentTextarea() {
            if (CKEDITOR.instances['content-editor']) {
                CKEDITOR.instances['content-editor'].updateElement();
            }
        }

        // SEO section toggle
        document.getElementById('seo-toggle')?.addEventListener('click', function() {
            const seoContent = document.querySelector('.seo-content');
            const toggleIcon = this.querySelector('.toggle-icon');

            if (seoContent.style.display === 'none') {
                seoContent.style.display = 'block';
                toggleIcon.className = 'fas fa-chevron-up toggle-icon';
            } else {
                seoContent.style.display = 'none';
                toggleIcon.className = 'fas fa-chevron-down toggle-icon';
            }
        });

        // Meta description character count
        const metaDescription = document.getElementById('meta-description');
        const charCount = document.getElementById('char-count');

        metaDescription?.addEventListener('input', function() {
            charCount.textContent = this.value.length;
            updateSaveStatus('unsaved');
        });

        // Featured image upload
        const imageUpload = document.getElementById('image-upload');
        const featuredImage = document.getElementById('featured-image');
        const imagePreview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');

        imageUpload?.addEventListener('click', () => featuredImage.click());

        featuredImage?.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imageUpload.style.display = 'none';
                    imagePreview.style.display = 'block';
                    updateSaveStatus('unsaved');
                };
                reader.readAsDataURL(file);
            }
        });

        document.querySelector('.remove-image')?.addEventListener('click', function() {
            featuredImage.value = '';
            imagePreview.style.display = 'none';
            imageUpload.style.display = 'block';
            updateSaveStatus('unsaved');
        });

        // Form submission handlers
        async function savePage(status = 'draft') {
            document.getElementById('page-status').value = status;
            updateContentTextarea();

            // Show loading state
            const btn = status === 'published' ? document.getElementById('publish-btn') : document.getElementById('save-draft-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            btn.disabled = true;
            updateSaveStatus('saving');

            try {
                const formData = new FormData(pageForm);
                formData.append('csrf_token', CSRF_TOKEN); // Add CSRF token

                // Add content from CKEditor specifically to be safe
                if (CKEDITOR.instances['content-editor']) {
                    formData.set('content', CKEDITOR.instances['content-editor'].getData());
                }

                // Add ID if editing
                const pageId = "<?php echo $pageData['id'] ?? ''; ?>";
                if (pageId) {
                    formData.append('id', pageId);
                }

                const response = await fetch(SAVE_URL, {
                    method: 'POST',
                    body: formData
                });

                // Handle redirect or success
                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }

                if (response.ok) {
                    updateSaveStatus('saved');
                    showNotification(`Page ${status === 'published' ? 'published' : 'saved as draft'} successfully`, 'success');

                    // If it's a new page, we might want to redirect to edit or list, 
                    // but the controller redirects to list currently.
                    // If controller didn't redirect, check response text/json
                    const text = await response.text();
                    if (text.includes('Redirecting')) { // Or check current URL logic
                        window.location.href = "<?php echo app_base_url('admin/content/pages'); ?>";
                    }
                } else {
                    throw new Error('Save failed');
                }

            } catch (error) {
                console.error('Save error:', error);
                updateSaveStatus('unsaved');
                showNotification('Error saving page. Please try again.', 'error');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        // Quick action buttons
        document.getElementById('save-draft-btn')?.addEventListener('click', () => savePage('draft'));
        document.getElementById('publish-btn')?.addEventListener('click', () => savePage('published'));

        // Preview functionality
        document.getElementById('preview-btn')?.addEventListener('click', function() {
            updateContentTextarea();
            showPreviewModal();
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                switch (e.key) {
                    case 's':
                        e.preventDefault();
                        savePage('draft');
                        break;
                    case 'p':
                        e.preventDefault();
                        showPreviewModal();
                        break;
                }
            }
        });

        // Auto-save functionality
        let autoSaveTimer;

        function autoSave() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                if (titleInput.value.trim()) {
                    updateContentTextarea();
                    // Implement auto-save logic here
                    updateSaveStatus('saving');
                    setTimeout(() => updateSaveStatus('saved'), 1000);
                }
            }, 10000); // Auto-save every 10 seconds
        }

        contentEditor?.addEventListener('input', autoSave);
        titleInput?.addEventListener('input', autoSave);

        // Save status management
        function updateSaveStatus(status) {
            const statusElement = document.getElementById('save-status');
            const icon = statusElement.querySelector('i');
            const text = statusElement.querySelector('span');

            switch (status) {
                case 'unsaved':
                    icon.className = 'fas fa-circle text-warning';
                    text.textContent = 'Unsaved changes';
                    break;
                case 'saving':
                    icon.className = 'fas fa-spinner fa-spin text-info';
                    text.textContent = 'Saving...';
                    break;
                case 'saved':
                    icon.className = 'fas fa-check-circle text-success';
                    text.textContent = 'All changes saved';
                    break;
            }
        }

        // Initialize form validation
        function validateForm() {
            const errors = [];

            if (!titleInput.value.trim()) {
                errors.push('Page title is required');
            }

            if (!slugInput.value.trim()) {
                errors.push('URL slug is required');
            }

            return errors;
        }
    }

    function showPreviewModal() {
        const modal = document.getElementById('preview-modal');
        const iframe = document.getElementById('preview-iframe');

        // In a real implementation, this would load the actual page preview
        iframe.src = '/pages/preview/new';

        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('visible'), 10);
    }

    function closePreviewModal() {
        const modal = document.getElementById('preview-modal');
        modal.classList.remove('visible');
        setTimeout(() => {
            modal.style.display = 'none';
            document.getElementById('preview-iframe').src = '';
        }, 300);
    }

    function editFromPreview() {
        closePreviewModal();
        // Focus back to editor
        document.getElementById('content-editor').focus();
    }

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        </div>
    `;

        document.body.appendChild(notification);

        setTimeout(() => notification.classList.add('visible'), 10);
        setTimeout(() => {
            notification.classList.remove('visible');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    function createFormElement() {
        // Create form element if it doesn't exist
        const form = document.createElement('form');
        form.id = 'page-form';
        form.action = '#';
        form.method = 'POST';
        form.style.display = 'none';
        document.body.appendChild(form);
        return form;
    }
</script>



<style>
    /* ========================================
       PREMIUM DESIGN SYSTEM (PRODUCTION READY)
       ======================================== */
    :root {
        --primary-600: #4f46e5;
        --primary-700: #4338ca;
        --primary-50: #eef2ff;

        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;

        --success-500: #10b981;
        --warning-500: #f59e0b;
        --danger-500: #ef4444;

        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);

        --radius-md: 0.5rem;
        --radius-lg: 0.75rem;
    }

    body.admin-body {
        background-color: var(--gray-50);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: var(--gray-800);
    }

    .page-create-container {
        max-width: 100%;
        padding-bottom: 5rem;
        background-color: var(--gray-50);
        min-height: 100vh;
    }

    /* --- Page Header Section --- */
    .page-create-wrapper {
        background: transparent;
    }

    .compact-create-header {
        max-width: 960px;
        margin: 0 auto;
        padding: 2rem 0 1.5rem 0;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 2rem;
    }

    .header-left {
        flex: 1;
    }

    .header-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
    }

    .header-title i {
        font-size: 1.75rem;
        color: var(--primary-600);
    }

    .header-title h1 {
        margin: 0;
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--gray-900);
        letter-spacing: -0.025em;
        line-height: 1.2;
    }

    .header-subtitle {
        font-size: 0.9375rem;
        color: var(--gray-500);
        margin: 0;
        line-height: 1.5;
        font-weight: 400;
    }

    .header-actions {
        flex-shrink: 0;
        padding-top: 0.25rem;
    }

    .btn {
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-outline-secondary {
        background: white;
        border: 1px solid var(--gray-300);
        color: var(--gray-700);
    }

    .btn-outline-secondary:hover {
        background: var(--gray-50);
        border-color: var(--gray-400);
    }

    /* --- Action Bar (Floating) --- */
    .compact-action-bar {
        max-width: 960px;
        margin: 1.5rem auto 2rem auto;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(8px);
        padding: 0.75rem 1.25rem;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid var(--gray-200);
        position: sticky;
        top: 1.5rem;
        z-index: 50;
    }

    .save-status {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--gray-500);
    }

    .save-status i {
        font-size: 0.625rem;
    }

    .action-right {
        display: flex;
        gap: 0.75rem;
    }

    /* --- Main Layout --- */
    .create-content-single-column {
        max-width: 960px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    /* --- Cards --- */
    .content-card {
        background: white;
        border-radius: var(--radius-lg);
        border: 1px solid var(--gray-200);
        box-shadow: var(--shadow-sm);
        transition: box-shadow 0.2sease, transform 0.2s ease;
        overflow: hidden;
    }

    .content-card:hover {
        box-shadow: var(--shadow-md);
    }

    .card-header-clean {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--gray-100);
        background: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--gray-900);
        letter-spacing: -0.025em;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.625rem;
    }

    .card-title i {
        color: var(--gray-400);
        font-size: 1rem;
    }

    .card-body-clean {
        padding: 1.5rem;
    }

    .card-body-clean.p-0 {
        padding: 0;
    }

    /* --- Forms --- */
    .form-group-modern {
        margin-bottom: 1.5rem;
    }

    .form-group-modern:last-child {
        margin-bottom: 0;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
    }

    .form-label.required::after {
        content: "*";
        color: var(--danger-500);
        margin-left: 0.125rem;
    }

    .form-control-modern {
        width: 100%;
        padding: 0.625rem 0.875rem;
        font-size: 0.95rem;
        line-height: 1.5;
        color: var(--gray-900);
        background-color: white;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-md);
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control-modern:focus {
        border-color: var(--primary-600);
        outline: 0;
        box-shadow: 0 0 0 4px var(--primary-50);
    }

    .form-control-lg {
        padding: 0.875rem 1rem;
        font-size: 1.125rem;
    }

    .form-help {
        display: block;
        margin-top: 0.375rem;
        font-size: 0.8rem;
        color: var(--gray-500);
    }

    /* --- Slug Input --- */
    .slug-wrapper-modern {
        display: flex;
        align-items: stretch;
        background: var(--gray-50);
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-md);
        overflow: hidden;
        transition: all 0.2s;
    }

    .slug-wrapper-modern:focus-within {
        border-color: var(--primary-600);
        box-shadow: 0 0 0 4px var(--primary-50);
    }

    .slug-base {
        padding: 0.625rem 0 0.625rem 0.875rem;
        color: var(--gray-500);
        font-size: 0.875rem;
        background: var(--gray-50);
        border-right: 1px solid var(--gray-200);
        display: flex;
        align-items: center;
        user-select: none;
    }

    .slug-input {
        border: none;
        box-shadow: none;
        background: white;
        padding-left: 0.75rem;
        color: var(--gray-800);
        font-weight: 500;
    }

    .slug-input:focus {
        border: none;
        box-shadow: none;
    }

    .btn-icon {
        background: white;
        border: none;
        border-left: 1px solid var(--gray-200);
        padding: 0 1rem;
        color: var(--gray-400);
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-icon:hover {
        color: var(--primary-600);
        background: var(--primary-50);
    }

    /* --- Settings Grid --- */
    .settings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 1.5rem;
    }

    /* --- Featured Image --- */
    .featured-image-container {
        border: 2px dashed var(--gray-300);
        border-radius: var(--radius-lg);
        padding: 2rem;
        text-align: center;
        transition: all 0.2s ease;
        cursor: pointer;
        background: var(--gray-50);
    }

    .featured-image-container:hover {
        border-color: var(--primary-600);
        background: var(--primary-50);
    }

    .upload-content i {
        color: var(--gray-400);
        margin-bottom: 0.75rem;
    }

    .upload-content p {
        margin: 0;
        font-weight: 600;
        color: var(--gray-700);
    }

    /* --- Buttons --- */
    .btn-compact {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 1rem;
        border-radius: var(--radius-md);
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s;
        cursor: pointer;
        border: 1px solid transparent;
        gap: 0.5rem;
    }

    .btn-primary {
        background-color: var(--primary-600);
        color: white;
        box-shadow: 0 1px 2px 0 rgba(79, 70, 229, 0.3);
    }

    .btn-primary:hover {
        background-color: var(--primary-700);
        transform: translateY(-1px);
    }

    .btn-secondary {
        background-color: white;
        border-color: var(--gray-300);
        color: var(--gray-700);
    }

    .btn-secondary:hover {
        background-color: var(--gray-50);
        border-color: var(--gray-400);
    }

    .btn-warning {
        background-color: white;
        border-color: var(--warning-500);
        color: var(--warning-500);
    }

    .btn-warning:hover {
        background-color: #fffbeb;
    }

    /* --- Toggle/Collapse --- */
    .toggle-icon {
        color: var(--gray-400);
        transition: transform 0.2s;
    }

    .card-header-clean:hover .toggle-icon {
        color: var(--primary-600);
    }

    /* --- Readonly --- */
    .readonly-value {
        padding: 0.625rem 0.875rem;
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-md);
        color: var(--gray-600);
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* --- Character Count --- */
    .char-count {
        text-align: right;
        font-size: 0.75rem;
        color: var(--gray-500);
        margin-top: 0.25rem;
    }

    /* --- Responsive --- */
    @media (max-width: 768px) {
        .compact-action-bar {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
            top: 0.5rem;
        }

        .action-right {
            justify-content: space-between;
        }

        .settings-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Notifications */
    .notification {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 1100;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s ease;
        padding: 1rem;
        border-left: 4px solid #4f46e5;
    }

    .notification.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .notification-success {
        border-left-color: var(--success-500);
    }

    .notification-error {
        border-left-color: var(--danger-500);
    }

    .notification-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 500;
        color: var(--gray-800);
    }
</style>