<?php
/**
 * BLOG POST FORM
 * Unified for Create and Edit
 */

$is_edit = isset($post);
$form_action = $is_edit ? app_base_url('/admin/blog/update/' . $post['id']) : app_base_url('/admin/blog/store');
?>

<div class="page-create-container">
    <div class="page-create-wrapper">

        <!-- Compact Header -->
        <div class="compact-create-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-<?php echo $is_edit ? 'edit' : 'plus'; ?>"></i>
                    <h1><?php echo $is_edit ? 'Edit Article' : 'New Article'; ?></h1>
                </div>
                <div class="header-subtitle">
                    <?php echo $is_edit ? 'Update your blog post content and SEO' : 'Write a new article for your audience'; ?>
                </div>
            </div>
            <div class="header-actions">
                <a href="<?php echo app_base_url('/admin/blog'); ?>" class="btn btn-secondary btn-compact">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Blog</span>
                </a>
            </div>
        </div>

        <form id="blog-form" method="POST" action="<?php echo $form_action; ?>" class="main-form-container">
            <div class="create-content-single-column">
                
                <!-- Main Content Card -->
                <div class="content-card">
                    <div class="card-header-clean">
                        <h3 class="card-title">Content Details</h3>
                    </div>
                    <div class="card-body-clean">
                        <div class="form-group-modern">
                            <label for="post-title" class="form-label required">Post Title</label>
                            <input type="text" id="post-title" name="title" class="form-control-modern form-control-lg" 
                                   value="<?php echo $is_edit ? htmlspecialchars($post['title']) : ''; ?>" placeholder="Enter catchy title..." required>
                        </div>

                        <div class="form-group-modern">
                            <label for="post-slug" class="form-label required">URL Slug</label>
                            <div class="input-group-modern">
                                <span class="input-addon">/blog/</span>
                                <input type="text" id="post-slug" name="slug" class="form-control-modern" 
                                       value="<?php echo $is_edit ? htmlspecialchars($post['slug']) : ''; ?>" placeholder="post-url-slug" required>
                            </div>
                            <small class="text-muted mt-2 d-block">The URL of your post. Auto-generated from title, but can be customized.</small>
                        </div>

                        <div class="form-group-modern">
                            <label for="post-content" class="form-label required">Article Content</label>
                            <textarea id="post-content" name="content" class="form-control-modern" rows="15" 
                                      placeholder="Write your article here (HTML allowed)..." required><?php echo $is_edit ? htmlspecialchars($post['content']) : ''; ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Featured Settings -->
                <div class="settings-grid">
                    <div class="content-card">
                        <div class="card-header-clean">
                            <h3 class="card-title"><i class="fas fa-image"></i> Featured Image</h3>
                        </div>
                        <div class="card-body-clean">
                            <div class="form-group-modern">
                                <label for="featured-image" class="form-label">Image URL</label>
                                <input type="text" id="featured-image" name="featured_image" class="form-control-modern" 
                                       value="<?php echo $is_edit ? htmlspecialchars($post['featured_image']) : ''; ?>" placeholder="https://...">
                            </div>
                        </div>
                    </div>

                    <div class="content-card">
                        <div class="card-header-clean">
                            <h3 class="card-title"><i class="fas fa-bullhorn"></i> Status</h3>
                        </div>
                        <div class="card-body-clean">
                            <div class="form-group-modern">
                                <label for="post-status" class="form-label">Publication Status</label>
                                <select id="post-status" name="status" class="form-control-modern">
                                    <option value="published" <?php echo ($is_edit && $post['status'] === 'published') ? 'selected' : ''; ?>>Published</option>
                                    <option value="draft" <?php echo ($is_edit && $post['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Excerpt Card -->
                <div class="content-card">
                    <div class="card-header-clean">
                        <h3 class="card-title">Excerpt (Short Summary)</h3>
                    </div>
                    <div class="card-body-clean">
                        <textarea name="excerpt" class="form-control-modern" rows="3" 
                                  placeholder="Brief summary for list views..."><?php echo $is_edit ? htmlspecialchars($post['excerpt']) : ''; ?></textarea>
                    </div>
                </div>

                <!-- SEO Settings -->
                <div class="content-card">
                    <div class="card-header-clean">
                        <h3 class="card-title"><i class="fas fa-search"></i> SEO Metadata</h3>
                    </div>
                    <div class="card-body-clean">
                        <div class="form-group-modern">
                            <label for="seo-title" class="form-label">Meta Title</label>
                            <input type="text" id="seo-title" name="seo_title" class="form-control-modern" 
                                   value="<?php echo $is_edit ? htmlspecialchars($post['seo_title']) : ''; ?>" placeholder="SEO Optimized Title">
                        </div>
                        <div class="form-group-modern">
                            <label for="seo-description" class="form-label">Meta Description</label>
                            <textarea id="seo-description" name="seo_description" class="form-control-modern" rows="2" 
                                      placeholder="Brief SEO summary..."><?php echo $is_edit ? htmlspecialchars($post['seo_description']) : ''; ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="form-actions d-flex justify-content-end gap-3 pb-5">
                    <a href="<?php echo app_base_url('/admin/blog'); ?>" class="btn btn-secondary btn-lg">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-primary">
                        <i class="fas fa-save me-2"></i> <?php echo $is_edit ? 'Update Post' : 'Publish Article'; ?>
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('post-title');
    const slugInput = document.getElementById('post-slug');

    titleInput.addEventListener('input', function() {
        if (!<?php echo $is_edit ? 'true' : 'false'; ?> || slugInput.value === '') {
            slugInput.value = generateSlug(this.value);
        }
    });

    function generateSlug(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')              // Trim - from start of text
            .replace(/-+$/, '');             // Trim - from end of text
    }

    // Initialize CKEditor
    if (document.getElementById('post-content')) {
        CKEDITOR.replace('post-content', {
            height: 400,
            removePlugins: 'resize',
            allowedContent: true // Allow all HTML
        });
    }

    // Form submission
    const form = document.getElementById('blog-form');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Update textarea from CKEditor
        if (CKEDITOR.instances['post-content']) {
            CKEDITOR.instances['post-content'].updateElement();
        }

        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.href = '<?php echo app_base_url('/admin/blog'); ?>';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('An unexpected error occurred.');
        });
    });
});
</script>

<style>
/* Design System Bridge */
.page-create-container { background-color: #f9fafb; min-height: 100vh; padding-top: 2rem; }
.create-content-single-column { max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 2rem; }

.compact-create-header { max-width: 900px; margin: 0 auto 2rem auto; display: flex; justify-content: space-between; align-items: flex-start; }
.header-title { display: flex; align-items: center; gap: 1rem; }
.header-title h1 { margin: 0; font-size: 1.875rem; font-weight: 800; color: #111827; }
.header-title i { color: #4f46e5; font-size: 1.5rem; }
.header-subtitle { color: #6b7280; margin-top: 0.25rem; }

.content-card { background: white; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
.card-header-clean { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f3f4f6; }
.card-title { font-size: 1.1rem; font-weight: 700; color: #111827; margin: 0; display: flex; align-items: center; gap: 0.75rem; }
.card-body-clean { padding: 1.5rem; }

.form-group-modern { margin-bottom: 1.5rem; }
.form-label { display: block; font-size: 0.875rem; font-weight: 700; color: #374151; margin-bottom: 0.5rem; }
.required:after { content: ' *'; color: #ef4444; }

.form-control-modern { 
    width: 100%; padding: 0.75rem 1rem; border: 1px solid #d1d5db; border-radius: 8px; 
    font-size: 0.95rem; transition: all 0.2s; 
}
.form-control-modern:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); outline: none; }
.form-control-lg { font-size: 1.25rem; font-weight: 600; }

.input-group-modern { display: flex; align-items: center; }
.input-addon { background: #f3f4f6; border: 1px solid #d1d5db; border-right: none; padding: 0.75rem 1rem; border-radius: 8px 0 0 8px; color: #6b7280; font-size: 0.9rem; }
.input-group-modern .form-control-modern { border-radius: 0 8px 8px 0; }

.settings-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }

.shadow-primary { box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4); }
</style>
