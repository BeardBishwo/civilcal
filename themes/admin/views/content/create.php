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

        <!-- Main Content Layout -->
        <div class="create-content-layout">
            
            <!-- Main Editor Section -->
            <div class="main-editor-section">
                
                <!-- Title & Slug Section -->
                <div class="compact-card">
                    <div class="card-header-compact">
                        <h3 class="card-title">
                            <i class="fas fa-heading"></i>
                            Page Information
                        </h3>
                    </div>
                    <div class="card-content-compact">
                        <div class="form-row">
                            <div class="form-group-compact">
                                <label for="page-title" class="form-label required">Page Title</label>
                                <input
                                    type="text"
                                    id="page-title"
                                    name="title"
                                    class="form-control-compact"
                                    value="<?php echo htmlspecialchars($pageData['title']); ?>"
                                    placeholder="Enter page title..."
                                    required
                                    maxlength="255"
                                >
                                <small class="form-help">This will be the main heading of your page and used in SEO</small>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group-compact">
                                <label for="page-slug" class="form-label required">URL Slug</label>
                                <div class="slug-wrapper">
                                    <span class="slug-base"><?php echo rtrim(app_base_url(''), '/'); ?>/</span>
                                    <input
                                        type="text"
                                        id="page-slug"
                                        name="slug"
                                        class="form-control-compact slug-input"
                                        value="<?php echo htmlspecialchars($pageData['slug']); ?>"
                                        placeholder="page-url-slug"
                                        required
                                        pattern="[a-z0-9-]+"
                                        maxlength="100"
                                    >
                                    <button type="button" class="btn btn-sm btn-outline-secondary slug-generate" id="generate-slug-btn">
                                        <i class="fas fa-magic"></i>
                                    </button>
                                </div>
                                <small class="form-help">URL-friendly version (lowercase, dashes only)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Editor Section -->
                <div class="compact-card">
                    <div class="card-header-compact">
                        <h3 class="card-title">
                            <i class="fas fa-edit"></i>
                            Content Editor
                        </h3>
                        <div class="editor-toolbar-compact">
                            <button type="button" class="toolbar-btn" data-command="bold" title="Bold">
                                <i class="fas fa-bold"></i>
                            </button>
                            <button type="button" class="toolbar-btn" data-command="italic" title="Italic">
                                <i class="fas fa-italic"></i>
                            </button>
                            <button type="button" class="toolbar-btn" data-command="underline" title="Underline">
                                <i class="fas fa-underline"></i>
                            </button>
                            <div class="toolbar-divider"></div>
                            <button type="button" class="toolbar-btn" data-command="insertUnorderedList" title="Bullet List">
                                <i class="fas fa-list-ul"></i>
                            </button>
                            <button type="button" class="toolbar-btn" data-command="insertOrderedList" title="Numbered List">
                                <i class="fas fa-list-ol"></i>
                            </button>
                            <div class="toolbar-divider"></div>
                            <button type="button" class="toolbar-btn" data-command="createLink" title="Insert Link">
                                <i class="fas fa-link"></i>
                            </button>
                            <button type="button" class="toolbar-btn" data-command="insertImage" title="Insert Image">
                                <i class="fas fa-image"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-content-compact">
                        <div class="content-editor-compact">
                            <div id="content-editor" class="content-editable" contenteditable="true">
                                <?php echo $pageData['content'] ?: '<p>Start writing your content here...</p>'; ?>
                            </div>
                            <textarea id="page-content" name="content" style="display: none;"><?php echo htmlspecialchars($pageData['content']); ?></textarea>
                        </div>
                        <small class="form-help">Use the toolbar above to format your content</small>
                    </div>
                </div>

                <!-- SEO Section (Collapsible) -->
                <div class="compact-card">
                    <div class="card-header-compact collapsible-header" id="seo-toggle">
                        <h3 class="card-title">
                            <i class="fas fa-search"></i>
                            SEO Settings
                        </h3>
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                    <div class="card-content-compact seo-content" style="display: none;">
                        <div class="form-row">
                            <div class="form-group-compact">
                                <label for="meta-description" class="form-label">Meta Description</label>
                                <textarea
                                    id="meta-description"
                                    name="meta_description"
                                    class="form-control-compact"
                                    rows="3"
                                    maxlength="160"
                                    placeholder="Brief description for search engines..."
                                ><?php echo htmlspecialchars($pageData['meta_description']); ?></textarea>
                                <small class="form-help">
                                    <span id="char-count">0</span>/160 characters
                                </small>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group-compact">
                                <label for="meta-keywords" class="form-label">Meta Keywords</label>
                                <input
                                    type="text"
                                    id="meta-keywords"
                                    name="meta_keywords"
                                    class="form-control-compact"
                                    value="<?php echo htmlspecialchars($pageData['meta_keywords']); ?>"
                                    placeholder="keyword1, keyword2, keyword3"
                                >
                                <small class="form-help">Comma-separated keywords</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Section -->
            <div class="sidebar-section">
                
                <!-- Publish Settings -->
                <div class="compact-card">
                    <div class="card-header-compact">
                        <h3 class="card-title">
                            <i class="fas fa-cog"></i>
                            Publish Settings
                        </h3>
                    </div>
                    <div class="card-content-compact">
                        <div class="form-group-compact">
                            <label for="page-status" class="form-label">Status</label>
                            <select id="page-status" name="status" class="form-control-compact">
                                <option value="draft" <?php echo ($pageData['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                <option value="published" <?php echo ($pageData['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                                <option value="private" <?php echo ($pageData['status'] ?? '') === 'private' ? 'selected' : ''; ?>>Private</option>
                            </select>
                        </div>

                        <div class="form-group-compact">
                            <label for="page-author" class="form-label">Author</label>
                            <input
                                type="text"
                                id="page-author"
                                name="author"
                                class="form-control-compact"
                                value="<?php echo htmlspecialchars($pageData['author']); ?>"
                                readonly
                            >
                        </div>

                        <?php if ($is_edit): ?>
                        <div class="form-group-compact">
                            <label class="form-label">Last Modified</label>
                            <div class="readonly-compact">
                                <?php echo date('M j, Y \a\t H:i', strtotime($pageData['updated_at'] ?? 'now')); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Page Attributes -->
                <div class="compact-card">
                    <div class="card-header-compact">
                        <h3 class="card-title">
                            <i class="fas fa-sliders-h"></i>
                            Page Attributes
                        </h3>
                    </div>
                    <div class="card-content-compact">
                        <div class="form-group-compact">
                            <label for="page-template" class="form-label">Template</label>
                            <select id="page-template" name="template" class="form-control-compact">
                                <option value="default" <?php echo ($pageData['template'] ?? 'default') === 'default' ? 'selected' : ''; ?>>Default Template</option>
                                <option value="full-width" <?php echo ($pageData['template'] ?? '') === 'full-width' ? 'selected' : ''; ?>>Full Width</option>
                                <option value="sidebar-left" <?php echo ($pageData['template'] ?? '') === 'sidebar-left' ? 'selected' : ''; ?>>Sidebar Left</option>
                                <option value="sidebar-right" <?php echo ($pageData['template'] ?? '') === 'sidebar-right' ? 'selected' : ''; ?>>Sidebar Right</option>
                            </select>
                        </div>

                        <div class="form-group-compact">
                            <label for="page-parent" class="form-label">Parent Page</label>
                            <select id="page-parent" name="parent_id" class="form-control-compact">
                                <option value="">No Parent</option>
                                <!-- Populate with existing pages -->
                            </select>
                        </div>

                        <div class="form-group-compact">
                            <label for="page-order" class="form-label">Order</label>
                            <input
                                type="number"
                                id="page-order"
                                name="menu_order"
                                class="form-control-compact"
                                value="<?php echo htmlspecialchars($pageData['menu_order'] ?? '0'); ?>"
                                min="0"
                            >
                            <small class="form-help">Higher numbers appear first</small>
                        </div>
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="compact-card">
                    <div class="card-header-compact">
                        <h3 class="card-title">
                            <i class="fas fa-image"></i>
                            Featured Image
                        </h3>
                    </div>
                    <div class="card-content-compact">
                        <div class="featured-image-compact">
                            <div class="image-preview-compact" id="image-preview" style="display: none;">
                                <img id="preview-img" src="" alt="Featured Image">
                                <button type="button" class="btn btn-sm btn-danger remove-image">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="image-upload-compact" id="image-upload">
                                <div class="upload-placeholder-compact">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Click to upload</p>
                                    <small>PNG, JPG, GIF up to 2MB</small>
                                </div>
                                <input type="file" id="featured-image" name="featured_image" accept="image/*" style="display: none;">
                            </div>
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

<script>
// Enhanced JavaScript for Optimized Interface
document.addEventListener('DOMContentLoaded', function() {
    initializePageEditor();
});

function initializePageEditor() {
    // Form elements
    const pageForm = document.getElementById('page-form') || createFormElement();
    const titleInput = document.getElementById('page-title');
    const slugInput = document.getElementById('page-slug');
    const contentEditor = document.getElementById('content-editor');
    const contentTextarea = document.getElementById('page-content');

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
    const toolbarBtns = document.querySelectorAll('.toolbar-btn');
    toolbarBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const command = this.dataset.command;
            document.execCommand(command, false, null);
            contentEditor.focus();
            updateContentTextarea();
            updateSaveStatus('unsaved');
        });
    });

    // Update hidden textarea with editor content
    function updateContentTextarea() {
        contentTextarea.value = contentEditor.innerHTML;
    }

    contentEditor?.addEventListener('input', function() {
        updateContentTextarea();
        updateSaveStatus('unsaved');
    });

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
    function savePage(status = 'draft') {
        document.getElementById('page-status').value = status;
        updateContentTextarea();
        
        // Show loading state
        const btn = status === 'published' ? document.getElementById('publish-btn') : document.getElementById('save-draft-btn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        btn.disabled = true;
        
        // Simulate form submission (replace with actual submission)
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            updateSaveStatus('saved');
            showNotification(`Page ${status === 'published' ? 'published' : 'saved as draft'} successfully`, 'success');
        }, 1500);
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
            switch(e.key) {
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
        
        switch(status) {
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
   OPTIMIZED PAGE CREATE CONTAINER
   ======================================== */

.page-create-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 1rem;
    background: var(--admin-gray-50, #f8f9fa);
    min-height: calc(100vh - 70px);
}

.page-create-wrapper {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

/* ========================================
   COMPACT CREATE HEADER
   ======================================== */

.compact-create-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.header-left {
    flex: 1;
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

.header-actions {
    flex-shrink: 0;
}

.btn-compact {
    padding: 0.625rem 1.25rem;
    font-size: 0.875rem;
    border-radius: 8px;
    font-weight: 500;
}

/* ========================================
   COMPACT ACTION BAR
   ======================================== */

.compact-action-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
    border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
    background: var(--admin-gray-50, #f8f9fa);
}

.action-left {
    flex: 1;
}

.save-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--admin-gray-600, #6b7280);
}

.save-status i {
    font-size: 0.5rem;
}

.text-success { color: #48bb78; }
.text-warning { color: #ed8936; }
.text-info { color: #4299e1; }

.action-right {
    display: flex;
    gap: 0.75rem;
}

/* ========================================
   CREATE CONTENT LAYOUT
   ======================================== */

.create-content-layout {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 2rem;
    padding: 2rem;
}

.main-editor-section {
    min-width: 0;
}

.sidebar-section {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    position: sticky;
    top: 2rem;
    height: fit-content;
}

/* ========================================
   COMPACT CARDS
   ======================================== */

.compact-card {
    background: white;
    border: 1px solid var(--admin-gray-200, #e5e7eb);
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.2s ease;
}

.compact-card:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.card-header-compact {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
    background: var(--admin-gray-50, #f8f9fa);
}

.card-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: var(--admin-gray-900, #1f2937);
}

.card-content-compact {
    padding: 1.5rem;
}

/* ========================================
   FORM ELEMENTS
   ======================================== */

.form-row {
    margin-bottom: 1.5rem;
}

.form-row:last-child {
    margin-bottom: 0;
}

.form-group-compact {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-label {
    font-weight: 600;
    color: var(--admin-gray-700, #374151);
    font-size: 0.875rem;
}

.form-label.required::after {
    content: " *";
    color: #f56565;
}

.form-help {
    color: var(--admin-gray-500, #6b7280);
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

.form-control-compact {
    padding: 0.75rem 1rem;
    border: 1px solid var(--admin-gray-300, #d1d5db);
    border-radius: 6px;
    font-size: 0.875rem;
    background: white;
    transition: all 0.2s ease;
}

.form-control-compact:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Slug Wrapper */
.slug-wrapper {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.slug-base {
    padding: 0.75rem 0;
    color: var(--admin-gray-500, #6b7280);
    font-family: 'Monaco', 'Menlo', monospace;
    font-size: 0.875rem;
    white-space: nowrap;
    background: var(--admin-gray-50, #f8f9fa);
    border: 1px solid var(--admin-gray-300, #d1d5db);
    border-radius: 6px;
    padding: 0.75rem 1rem;
}

.slug-input {
    flex: 1;
}

.slug-generate {
    padding: 0.5rem;
    border-radius: 6px;
}

/* ========================================
   CONTENT EDITOR
   ======================================== */

.editor-toolbar-compact {
    display: flex;
    gap: 0.25rem;
    align-items: center;
}

.toolbar-btn {
    padding: 0.5rem;
    border: 1px solid var(--admin-gray-300, #d1d5db);
    border-radius: 4px;
    background: white;
    color: var(--admin-gray-600, #6b7280);
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.875rem;
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toolbar-btn:hover {
    background: var(--admin-gray-50, #f8f9fa);
    color: var(--admin-gray-800, #1f2937);
}

.toolbar-divider {
    width: 1px;
    height: 1.5rem;
    background: var(--admin-gray-300, #d1d5db);
    margin: 0 0.25rem;
}

.content-editor-compact {
    border: 1px solid var(--admin-gray-300, #d1d5db);
    border-radius: 6px;
    overflow: hidden;
}

.content-editable {
    min-height: 300px;
    padding: 1rem;
    outline: none;
    line-height: 1.6;
    font-size: 0.875rem;
}

.content-editable:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Collapsible Header */
.collapsible-header {
    cursor: pointer;
    transition: background 0.2s ease;
}

.collapsible-header:hover {
    background: var(--admin-gray-100, #f3f4f6);
}

.toggle-icon {
    transition: transform 0.2s ease;
}

/* Readonly Field */
.readonly-compact {
    padding: 0.75rem 1rem;
    background: var(--admin-gray-50, #f8f9fa);
    border: 1px solid var(--admin-gray-200, #e5e7eb);
    border-radius: 6px;
    color: var(--admin-gray-600, #6b7280);
    font-size: 0.875rem;
}

/* ========================================
   FEATURED IMAGE
   ======================================== */

.featured-image-compact {
    position: relative;
}

.image-preview-compact {
    position: relative;
    margin-bottom: 1rem;
}

.image-preview-compact img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 6px;
}

.remove-image {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    border: none;
    border-radius: 50%;
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.remove-image:hover {
    background: #f56565;
}

.upload-placeholder-compact {
    text-align: center;
    padding: 2rem 1rem;
    border: 2px dashed var(--admin-gray-300, #d1d5db);
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.upload-placeholder-compact:hover {
    border-color: #667eea;
    background: rgba(102, 126, 234, 0.05);
}

.upload-placeholder-compact i {
    font-size: 2rem;
    color: var(--admin-gray-400, #9ca3af);
    margin-bottom: 0.75rem;
}

.upload-placeholder-compact p {
    margin: 0 0 0.5rem 0;
    font-weight: 500;
    color: var(--admin-gray-700, #374151);
}

.upload-placeholder-compact small {
    color: var(--admin-gray-500, #6b7280);
    font-size: 0.75rem;
}

/* ========================================
   PREVIEW MODAL
   ======================================== */

.preview-modal-compact {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1050;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.preview-modal-compact.visible {
    opacity: 1;
    visibility: visible;
}

.preview-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

.preview-content-compact {
    position: relative;
    background: white;
    border-radius: 12px;
    width: 90%;
    max-width: 1200px;
    height: 80%;
    max-height: 800px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.preview-header-compact {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--admin-gray-200, #e5e7eb);
    background: var(--admin-gray-50, #f8f9fa);
}

.preview-title {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--admin-gray-900, #1f2937);
}

.preview-actions {
    display: flex;
    gap: 0.5rem;
}

.preview-body-compact {
    flex: 1;
    overflow: hidden;
}

#preview-iframe {
    width: 100%;
    height: 100%;
    border: none;
}

/* ========================================
   NOTIFICATIONS
   ======================================== */

.notification {
    position: fixed;
    top: 2rem;
    right: 2rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    border: 1px solid var(--admin-gray-200, #e5e7eb);
    z-index: 1100;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.3s ease;
}

.notification.visible {
    opacity: 1;
    transform: translateX(0);
}

.notification-content {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
}

.notification-success {
    border-left: 4px solid #48bb78;
}

.notification-info {
    border-left: 4px solid #4299e1;
}

/* ========================================
   RESPONSIVE DESIGN
   ======================================== */

@media (max-width: 1024px) {
    .page-create-container {
        padding: 0.5rem;
    }
    
    .create-content-layout {
        grid-template-columns: 1fr;
        gap: 1.5rem;
        padding: 1.5rem;
    }
    
    .sidebar-section {
        position: static;
        order: -1;
    }
    
    .compact-create-header {
        padding: 1rem 1.5rem;
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    
    .compact-action-bar {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
}

@media (max-width: 768px) {
    .compact-action-bar {
        padding: 0.75rem 1.5rem;
    }
    
    .action-right {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .card-content-compact {
        padding: 1rem;
    }
    
    .editor-toolbar-compact {
        flex-wrap: wrap;
    }
    
    .slug-wrapper {
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
    }
    
    .slug-base {
        text-align: center;
    }
    
    .preview-content-compact {
        width: 95%;
        height: 90%;
    }
}

@media (max-width: 480px) {
    .compact-create-header {
        padding: 1rem;
    }
    
    .create-content-layout {
        padding: 1rem;
    }
    
    .btn-compact {
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
    }
    
    .toolbar-btn {
        width: 1.75rem;
        height: 1.75rem;
        font-size: 0.75rem;
    }
}

/* ========================================
   ACCESSIBILITY
   ======================================== */

@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Focus styles */
.form-control-compact:focus,
.toolbar-btn:focus,
.btn-compact:focus {
    outline: 2px solid #667eea;
    outline-offset: 2px;
}

/* High contrast mode */
@media (prefers-contrast: high) {
    .compact-card {
        border-width: 2px;
    }
    
    .form-control-compact {
        border-width: 2px;
    }
}
</style>