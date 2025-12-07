<?php
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
    'author' => $user->username ?? 'Admin'
];
?>

<!-- Enhanced Page Header -->
<div class="page-header">
    <div class="page-header-content">
        <div class="page-header-main">
            <h1 class="page-title">
                <i class="fas fa-<?php echo $is_edit ? 'edit' : 'plus'; ?>" aria-hidden="true"></i>
                <?php echo $is_edit ? 'Edit Page' : 'Create New Page'; ?>
            </h1>
            <p class="page-description">
                <?php echo $is_edit ? 'Update your page content and settings' : 'Create a new page with rich content and advanced options'; ?>
            </p>
        </div>
        <div class="page-header-actions">
            <a href="<?php echo app_base_url('admin/content/pages'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left" aria-hidden="true"></i>
                <span>Back to Pages</span>
            </a>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions-container">
    <div class="quick-actions-grid">
        <button type="button" class="quick-action-item" id="save-draft-btn">
            <div class="action-icon warning">
                <i class="fas fa-save" aria-hidden="true"></i>
            </div>
            <div class="action-label">Save Draft</div>
        </button>

        <button type="button" class="quick-action-item" id="preview-btn">
            <div class="action-icon info">
                <i class="fas fa-eye" aria-hidden="true"></i>
            </div>
            <div class="action-label">Preview</div>
        </button>

        <button type="button" class="quick-action-item" id="publish-btn">
            <div class="action-icon success">
                <i class="fas fa-check-circle" aria-hidden="true"></i>
            </div>
            <div class="action-label"><?php echo $is_edit ? 'Update' : 'Publish'; ?></div>
        </button>
    </div>
</div>

<!-- Main Content Form -->
<div class="page-editor-container">
    <div class="editor-layout">
        <!-- Main Editor -->
        <div class="editor-main">
            <form id="page-form" action="<?php echo app_base_url('admin/content/pages/save'); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="page_id" value="<?php echo htmlspecialchars($pageData['id']); ?>">
                
                <!-- Title and Slug Section -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-heading" aria-hidden="true"></i>
                            Page Information
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="form-section">
                            <div class="form-group">
                                <label for="page-title" class="form-label required">Page Title</label>
                                <input
                                    type="text"
                                    id="page-title"
                                    name="title"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($pageData['title']); ?>"
                                    placeholder="Enter page title..."
                                    required
                                    maxlength="255"
                                >
                                <small class="form-help">This will be the main heading of your page and used in SEO</small>
                            </div>

                            <div class="form-group">
                                <label for="page-slug" class="form-label required">URL Slug</label>
                                <div class="slug-input-wrapper">
                                    <span class="slug-prefix"><?php echo app_base_url(''); ?></span>
                                    <input
                                        type="text"
                                        id="page-slug"
                                        name="slug"
                                        class="form-control slug-input"
                                        value="<?php echo htmlspecialchars($pageData['slug']); ?>"
                                        placeholder="page-url-slug"
                                        required
                                        pattern="[a-z0-9-]+"
                                        maxlength="100"
                                    >
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="generate-slug-btn">
                                        <i class="fas fa-magic" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <small class="form-help">URL-friendly version of the title (lowercase, dashes only)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Editor -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-edit" aria-hidden="true"></i>
                            Content Editor
                        </div>
                        <div class="card-actions">
                            <div class="editor-toolbar">
                                <button type="button" class="btn btn-sm btn-outline-secondary editor-btn" data-command="bold">
                                    <i class="fas fa-bold" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary editor-btn" data-command="italic">
                                    <i class="fas fa-italic" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary editor-btn" data-command="underline">
                                    <i class="fas fa-underline" aria-hidden="true"></i>
                                </button>
                                <div class="toolbar-divider"></div>
                                <button type="button" class="btn btn-sm btn-outline-secondary editor-btn" data-command="insertUnorderedList">
                                    <i class="fas fa-list-ul" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary editor-btn" data-command="insertOrderedList">
                                    <i class="fas fa-list-ol" aria-hidden="true"></i>
                                </button>
                                <div class="toolbar-divider"></div>
                                <button type="button" class="btn btn-sm btn-outline-secondary editor-btn" data-command="createLink">
                                    <i class="fas fa-link" aria-hidden="true"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary editor-btn" data-command="insertImage">
                                    <i class="fas fa-image" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="form-group">
                            <label for="page-content" class="form-label">Page Content</label>
                            <div class="content-editor-wrapper">
                                <div id="content-editor" class="content-editor" contenteditable="true">
                                    <?php echo $pageData['content'] ?: '<p>Start writing your content here...</p>'; ?>
                                </div>
                                <textarea id="page-content" name="content" style="display: none;"><?php echo htmlspecialchars($pageData['content']); ?></textarea>
                            </div>
                            <small class="form-help">Use the toolbar above to format your content. You can also insert images and links.</small>
                        </div>
                    </div>
                </div>

                <!-- SEO Settings -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-search" aria-hidden="true"></i>
                            SEO Settings
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="toggle-seo">
                            <i class="fas fa-chevron-down" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="card-content seo-section" style="display: none;">
                        <div class="form-section">
                            <div class="form-group">
                                <label for="meta-description" class="form-label">Meta Description</label>
                                <textarea
                                    id="meta-description"
                                    name="meta_description"
                                    class="form-control"
                                    rows="3"
                                    maxlength="160"
                                    placeholder="Brief description for search engines..."
                                ><?php echo htmlspecialchars($pageData['meta_description'] ?? ''); ?></textarea>
                                <small class="form-help"><span id="char-count">0</span>/160 characters - Keep it concise and descriptive</small>
                            </div>

                            <div class="form-group">
                                <label for="meta-keywords" class="form-label">Meta Keywords</label>
                                <input
                                    type="text"
                                    id="meta-keywords"
                                    name="meta_keywords"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($pageData['meta_keywords'] ?? ''); ?>"
                                    placeholder="keyword1, keyword2, keyword3"
                                >
                                <small class="form-help">Comma-separated keywords relevant to this page</small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="editor-sidebar">
            <!-- Publish Settings -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fas fa-cog" aria-hidden="true"></i>
                        Publish Settings
                    </div>
                </div>
                <div class="card-content">
                    <div class="form-section">
                        <div class="form-group">
                            <label for="page-status" class="form-label">Status</label>
                            <select id="page-status" name="status" class="form-control">
                                <option value="draft" <?php echo ($pageData['status'] ?? 'draft') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                <option value="published" <?php echo ($pageData['status'] ?? '') === 'published' ? 'selected' : ''; ?>>Published</option>
                                <option value="private" <?php echo ($pageData['status'] ?? '') === 'private' ? 'selected' : ''; ?>>Private</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="page-author" class="form-label">Author</label>
                            <input
                                type="text"
                                id="page-author"
                                name="author"
                                class="form-control"
                                value="<?php echo htmlspecialchars($pageData['author']); ?>"
                                readonly
                            >
                        </div>

                        <?php if ($is_edit): ?>
                        <div class="form-group">
                            <label class="form-label">Last Modified</label>
                            <div class="readonly-field">
                                <?php echo date('M j, Y \a\t H:i', strtotime($pageData['updated_at'] ?? 'now')); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-footer">
                        <div class="footer-actions">
                            <button type="button" class="btn btn-secondary" id="cancel-btn">
                                Cancel
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="save-draft-sidebar">
                                Save Draft
                            </button>
                            <button type="button" class="btn btn-primary" id="publish-sidebar">
                                <?php echo $is_edit ? 'Update' : 'Publish'; ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Attributes -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fas fa-sliders-h" aria-hidden="true"></i>
                        Page Attributes
                    </div>
                </div>
                <div class="card-content">
                    <div class="form-section">
                        <div class="form-group">
                            <label for="page-template" class="form-label">Template</label>
                            <select id="page-template" name="template" class="form-control">
                                <option value="default">Default Template</option>
                                <option value="full-width">Full Width</option>
                                <option value="sidebar-left">Sidebar Left</option>
                                <option value="sidebar-right">Sidebar Right</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="page-parent" class="form-label">Parent Page</label>
                            <select id="page-parent" name="parent_id" class="form-control">
                                <option value="">No Parent</option>
                                <!-- Populate with existing pages -->
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="page-order" class="form-label">Order</label>
                            <input
                                type="number"
                                id="page-order"
                                name="menu_order"
                                class="form-control"
                                value="<?php echo htmlspecialchars($pageData['menu_order'] ?? '0'); ?>"
                                min="0"
                            >
                            <small class="form-help">Higher numbers appear first in menus</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Featured Image -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fas fa-image" aria-hidden="true"></i>
                        Featured Image
                    </div>
                </div>
                <div class="card-content">
                    <div class="featured-image-section">
                        <div class="image-preview" id="image-preview" style="display: none;">
                            <img id="preview-img" src="" alt="Featured Image">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-image">
                                <i class="fas fa-times" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div class="image-upload" id="image-upload">
                            <div class="upload-placeholder">
                                <i class="fas fa-cloud-upload-alt" aria-hidden="true"></i>
                                <p>Click to upload or drag and drop</p>
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

<style>
/* Enhanced Page Editor Styles */
.page-header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 24px;
}

.page-header-main {
    flex: 1;
}

.page-header-actions {
    flex-shrink: 0;
}

.page-title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
}

.page-description {
    color: var(--admin-gray-600);
    font-size: 16px;
    max-width: 600px;
}

/* Quick Actions */
.quick-actions-container {
    margin-bottom: 24px;
}

.quick-actions-grid {
    display: flex;
    gap: 16px;
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: var(--admin-shadow);
    border: 1px solid var(--admin-gray-200);
}

.quick-action-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 16px 20px;
    background: transparent;
    border: 2px solid transparent;
    border-radius: 8px;
    cursor: pointer;
    transition: var(--transition);
    text-decoration: none;
    color: inherit;
    min-width: 100px;
}

.quick-action-item:hover {
    background: var(--admin-gray-50);
    border-color: var(--admin-primary);
    color: var(--admin-primary);
}

.action-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
}

.action-icon.primary {
    background: linear-gradient(135deg, var(--admin-primary), var(--admin-primary-dark));
}

.action-icon.success {
    background: linear-gradient(135deg, var(--admin-success), #059669);
}

.action-icon.warning {
    background: linear-gradient(135deg, var(--admin-warning), #d97706);
}

.action-icon.info {
    background: linear-gradient(135deg, var(--admin-info), #2563eb);
}

.action-label {
    font-weight: 500;
    font-size: 14px;
}

/* Editor Layout */
.page-editor-container {
    margin-bottom: 32px;
}

.editor-layout {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 24px;
    align-items: start;
}

.editor-main {
    min-width: 0;
}

.editor-sidebar {
    position: sticky;
    top: 100px;
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* Form Elements */
.form-section {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-label {
    font-weight: 600;
    color: var(--admin-gray-700);
    font-size: 14px;
}

.form-label.required::after {
    content: " *";
    color: var(--admin-danger);
}

.form-help {
    color: var(--admin-gray-500);
    font-size: 12px;
}

.form-control {
    padding: 12px 16px;
    border: 2px solid var(--admin-gray-200);
    border-radius: 8px;
    font-size: 14px;
    transition: var(--transition);
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: var(--admin-primary);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.slug-input-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
}

.slug-prefix {
    padding: 12px 0;
    color: var(--admin-gray-500);
    font-family: monospace;
    white-space: nowrap;
    border: 2px solid var(--admin-gray-200);
    border-radius: 8px;
    background: var(--admin-gray-50);
    padding: 12px 16px;
}

.slug-input {
    flex: 1;
}

/* Content Editor */
.content-editor-wrapper {
    border: 2px solid var(--admin-gray-200);
    border-radius: 8px;
    overflow: hidden;
}

.content-editor {
    min-height: 400px;
    padding: 16px;
    outline: none;
    line-height: 1.6;
}

.content-editor:focus {
    border-color: var(--admin-primary);
}

.editor-toolbar {
    display: flex;
    gap: 4px;
    padding: 8px;
    background: var(--admin-gray-50);
    border-bottom: 1px solid var(--admin-gray-200);
}

.editor-btn {
    padding: 6px 8px;
    border: 1px solid var(--admin-gray-200);
    background: white;
    border-radius: 4px;
    cursor: pointer;
    transition: var(--transition);
}

.editor-btn:hover {
    background: var(--admin-gray-100);
}

.toolbar-divider {
    width: 1px;
    background: var(--admin-gray-200);
    margin: 0 4px;
}

/* Featured Image */
.featured-image-section {
    position: relative;
}

.image-preview {
    position: relative;
    margin-bottom: 12px;
}

.image-preview img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
}

.remove-image {
    position: absolute;
    top: 8px;
    right: 8px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.upload-placeholder {
    text-align: center;
    padding: 40px 20px;
    border: 2px dashed var(--admin-gray-300);
    border-radius: 8px;
    cursor: pointer;
    transition: var(--transition);
}

.upload-placeholder:hover {
    border-color: var(--admin-primary);
    background: rgba(79, 70, 229, 0.05);
}

.upload-placeholder i {
    font-size: 32px;
    color: var(--admin-gray-400);
    margin-bottom: 12px;
}

/* Readonly Field */
.readonly-field {
    padding: 12px 16px;
    background: var(--admin-gray-50);
    border: 1px solid var(--admin-gray-200);
    border-radius: 8px;
    color: var(--admin-gray-600);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .editor-layout {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .editor-sidebar {
        position: static;
        order: -1;
    }

    .quick-actions-grid {
        flex-wrap: wrap;
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .page-header-content {
        flex-direction: column;
        align-items: stretch;
        gap: 16px;
    }

    .quick-actions-grid {
        flex-direction: column;
        align-items: stretch;
    }

    .editor-toolbar {
        flex-wrap: wrap;
    }

    .slug-input-wrapper {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
    }

    .slug-prefix {
        text-align: center;
    }
}

@media (max-width: 480px) {
    .page-header-content {
        padding: 0 16px;
    }

    .quick-actions-grid {
        padding: 16px;
    }

    .action-icon {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form elements
    const pageForm = document.getElementById('page-form');
    const titleInput = document.getElementById('page-title');
    const slugInput = document.getElementById('page-slug');
    const contentEditor = document.getElementById('content-editor');
    const contentTextarea = document.getElementById('page-content');

    // Auto-generate slug from title
    titleInput.addEventListener('input', function() {
        if (!slugInput.value || slugInput.dataset.autoGenerated === 'true') {
            generateSlug(this.value);
        }
    });

    // Generate slug button
    document.getElementById('generate-slug-btn').addEventListener('click', function() {
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
    const editorBtns = document.querySelectorAll('.editor-btn');
    editorBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const command = this.dataset.command;
            document.execCommand(command, false, null);
            contentEditor.focus();
            updateContentTextarea();
        });
    });

    // Update hidden textarea with editor content
    function updateContentTextarea() {
        contentTextarea.value = contentEditor.innerHTML;
    }

    contentEditor.addEventListener('input', updateContentTextarea);

    // SEO section toggle
    document.getElementById('toggle-seo').addEventListener('click', function() {
        const seoSection = document.querySelector('.seo-section');
        const icon = this.querySelector('i');
        
        if (seoSection.style.display === 'none') {
            seoSection.style.display = 'block';
            icon.className = 'fas fa-chevron-up';
        } else {
            seoSection.style.display = 'none';
            icon.className = 'fas fa-chevron-down';
        }
    });

    // Meta description character count
    const metaDescription = document.getElementById('meta-description');
    const charCount = document.getElementById('char-count');
    
    metaDescription.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });

    // Featured image upload
    const imageUpload = document.getElementById('image-upload');
    const featuredImage = document.getElementById('featured-image');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');

    imageUpload.addEventListener('click', () => featuredImage.click());

    featuredImage.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imageUpload.style.display = 'none';
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    document.querySelector('.remove-image').addEventListener('click', function() {
        featuredImage.value = '';
        imagePreview.style.display = 'none';
        imageUpload.style.display = 'block';
    });

    // Form submission handlers
    function savePage(status = 'draft') {
        document.getElementById('page-status').value = status;
        updateContentTextarea();
        pageForm.submit();
    }

    // Quick action buttons
    document.getElementById('save-draft-btn').addEventListener('click', () => savePage('draft'));
    document.getElementById('save-draft-sidebar').addEventListener('click', () => savePage('draft'));
    document.getElementById('publish-btn').addEventListener('click', () => savePage('published'));
    document.getElementById('publish-sidebar').addEventListener('click', () => savePage('published'));

    // Preview functionality
    document.getElementById('preview-btn').addEventListener('click', function() {
        updateContentTextarea();
        const formData = new FormData(pageForm);
        // Open preview in new window (placeholder)
        window.open('/preview-page', '_blank');
    });

    // Cancel button
    document.getElementById('cancel-btn').addEventListener('click', function() {
        if (confirm('Are you sure you want to leave? Any unsaved changes will be lost.')) {
            window.location.href = '<?php echo app_base_url('admin/content/pages'); ?>';
        }
    });

    // Auto-save functionality (optional)
    let autoSaveTimer;
    function autoSave() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(() => {
            if (titleInput.value.trim()) {
                updateContentTextarea();
                // Implement auto-save logic here
                console.log('Auto-saving...');
            }
        }, 30000); // Auto-save every 30 seconds
    }

    contentEditor.addEventListener('input', autoSave);
    titleInput.addEventListener('input', autoSave);

    // Form validation
    pageForm.addEventListener('submit', function(e) {
        if (!titleInput.value.trim()) {
            e.preventDefault();
            alert('Page title is required');
            titleInput.focus();
            return false;
        }

        if (!slugInput.value.trim()) {
            e.preventDefault();
            alert('URL slug is required');
            slugInput.focus();
            return false;
        }

        return true;
    });
});
</script>