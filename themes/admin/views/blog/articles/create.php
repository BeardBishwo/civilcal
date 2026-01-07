<?php
/**
 * PREMIUM BLOG ARTICLE CREATE/EDIT FORM
 * WordPress-style interface with rich text editor
 */
$categories = $categories ?? [];
$article = $article ?? null;
$isEdit = isset($article);
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-pen"></i>
                    <h1><?php echo $isEdit ? 'Edit Article' : 'Create New Article'; ?></h1>
                </div>
                <div class="header-subtitle">Write and publish your blog article</div>
            </div>
            <div class="header-actions" style="display:flex; gap:10px;">
                <button onclick="saveDraft()" class="btn-secondary-compact">
                    <i class="fas fa-save"></i> Save Draft
                </button>
                <button onclick="publishArticle()" class="btn-create-premium">
                    <i class="fas fa-paper-plane"></i> PUBLISH
                </button>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div style="display: grid; grid-template-columns: 1fr 350px; gap: 1.5rem; margin-top: 1.5rem;">
            
            <!-- Main Content Column -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                <!-- Title Input -->
                <div class="content-card-premium">
                    <input type="text" 
                           id="articleTitle" 
                           placeholder="Enter article title..." 
                           value="<?php echo $isEdit ? htmlspecialchars($article['title']) : ''; ?>"
                           style="width: 100%; padding: 1rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1.5rem; font-weight: 600; transition: all 0.3s ease;"
                           onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99, 102, 241, 0.1)';" 
                           onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';"
                           onkeyup="updateSlugPreview()">
                    <div id="slugPreview" style="margin-top: 0.5rem; font-size: 0.75rem; color: #64748b;">
                        <strong>URL:</strong> <span id="slugText"><?php echo app_base_url('blog/your-article-slug'); ?></span>
                    </div>
                </div>

                <!-- Rich Text Editor -->
                <div class="content-card-premium">
                    <h5 class="card-title-premium">
                        <i class="fas fa-align-left"></i> Content
                    </h5>
                    <textarea id="articleContent" class="rich-editor"><?php echo $isEdit ? $article['content'] : ''; ?></textarea>
                </div>

                <!-- Excerpt -->
                <div class="content-card-premium">
                    <h5 class="card-title-premium">
                        <i class="fas fa-file-alt"></i> Excerpt
                    </h5>
                    <textarea id="articleExcerpt" 
                              rows="3" 
                              placeholder="Write a short excerpt (optional - will auto-generate from content if left empty)"
                              style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 0.875rem; resize: vertical; transition: all 0.3s ease;"
                              onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99, 102, 241, 0.1)';" 
                              onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';"><?php echo $isEdit ? htmlspecialchars($article['excerpt']) : ''; ?></textarea>
                </div>

            </div>

            <!-- Sidebar Column -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                <!-- Publish Card -->
                <div class="sidebar-card-premium">
                    <h5 class="sidebar-card-title">
                        <i class="fas fa-paper-plane"></i> Publish
                    </h5>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 600; color: #64748b; display: block; margin-bottom: 0.5rem;">Status</label>
                            <select id="articleStatus" style="width: 100%; padding: 0.5rem; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 0.875rem;">
                                <option value="draft" <?php echo ($isEdit && $article['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                                <option value="published" <?php echo ($isEdit && $article['status'] === 'published') ? 'selected' : ''; ?>>Published</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Featured Image Card -->
                <div class="sidebar-card-premium">
                    <h5 class="sidebar-card-title">
                        <i class="fas fa-image"></i> Featured Image
                    </h5>
                    <div id="featuredImagePreview" style="margin-bottom: 0.75rem;">
                        <?php if ($isEdit && $article['featured_image']): ?>
                            <img src="<?php echo htmlspecialchars($article['featured_image']); ?>" 
                                 style="width: 100%; height: 150px; object-fit: cover; border-radius: 6px;">
                        <?php else: ?>
                            <div style="width: 100%; height: 150px; background: #f1f5f9; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 0.5rem;">
                                <i class="fas fa-image" style="font-size: 2rem; color: #cbd5e1;"></i>
                                <span style="font-size: 0.75rem; color: #94a3b8;">No image selected</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <input type="text" id="featuredImage" placeholder="Image URL" value="<?php echo $isEdit ? htmlspecialchars($article['featured_image']) : ''; ?>" style="width: 100%; padding: 0.5rem; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 0.875rem; margin-bottom: 0.5rem;" onchange="updateImagePreview(this.value)">
                    <button onclick="openMediaSelector()" class="btn-secondary-compact" style="width: 100%;">
                        <i class="fas fa-upload"></i> Select Image
                    </button>
                </div>

                <!-- Category Card -->
                <div class="sidebar-card-premium">
                    <h5 class="sidebar-card-title">
                        <i class="fas fa-folder"></i> Category
                    </h5>
                    <select id="articleCategory" style="width: 100%; padding: 0.5rem; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 0.875rem;">
                        <option value="">Uncategorized</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($isEdit && $article['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <a href="<?php echo app_base_url('admin/blog/categories'); ?>" style="font-size: 0.75rem; color: #6366f1; margin-top: 0.5rem; display: inline-block;">
                        + Add New Category
                    </a>
                </div>

                <!-- Tags Card -->
                <div class="sidebar-card-premium">
                    <h5 class="sidebar-card-title">
                        <i class="fas fa-tags"></i> Tags
                    </h5>
                    <input type="text" 
                           id="articleTags" 
                           placeholder="Comma-separated tags" 
                           value="<?php echo $isEdit ? htmlspecialchars($article['tags']) : ''; ?>"
                           style="width: 100%; padding: 0.5rem; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 0.875rem;">
                    <div style="font-size: 0.688rem; color: #94a3b8; margin-top: 0.5rem;">Separate tags with commas</div>
                </div>

                <!-- SEO Card -->
                <div class="sidebar-card-premium">
                    <h5 class="sidebar-card-title">
                        <i class="fas fa-search"></i> SEO
                    </h5>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 600; color: #64748b; display: block; margin-bottom: 0.5rem;">Meta Title</label>
                            <input type="text" id="metaTitle" placeholder="Auto-filled from title" value="<?php echo $isEdit ? htmlspecialchars($article['meta_title']) : ''; ?>" style="width: 100%; padding: 0.5rem; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 0.875rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.75rem; font-weight: 600; color: #64748b; display: block; margin-bottom: 0.5rem;">Meta Description</label>
                            <textarea id="metaDescription" rows="2" placeholder="Auto-filled from excerpt" style="width: 100%; padding: 0.5rem; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 0.875rem; resize: vertical;"><?php echo $isEdit ? htmlspecialchars($article['meta_description']) : ''; ?></textarea>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

<style>
.sidebar-card-premium {
    background: white;
    border-radius: 8px;
    padding: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.sidebar-card-title {
    font-size: 0.813rem;
    font-weight: 700;
    color: #475569;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.sidebar-card-title i {
    color: #6366f1;
}
</style>

<script>
function openMediaSelector() {
    if (typeof MediaModal === 'undefined') {
        alert('Media Manager not loaded');
        return;
    }
    MediaModal.open(function(url) {
        document.getElementById('featuredImage').value = url;
        updateImagePreview(url);
    });
}

function updateSlugPreview() {
    const title = document.getElementById('articleTitle').value;
    const slug = title.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .substring(0, 100);
    document.getElementById('slugText').textContent = '<?php echo app_base_url('blog/'); ?>' + (slug || 'your-article-slug');
}

function updateImagePreview(url) {
    const preview = document.getElementById('featuredImagePreview');
    if (url) {
        preview.innerHTML = `<img src="${url}" style="width: 100%; height: 150px; object-fit: cover; border-radius: 6px;">`;
    } else {
        preview.innerHTML = `<div style="width: 100%; height: 150px; background: #f1f5f9; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 0.5rem;">
            <i class="fas fa-image" style="font-size: 2rem; color: #cbd5e1;"></i>
            <span style="font-size: 0.75rem; color: #94a3b8;">No image selected</span>
        </div>`;
    }
}

function saveDraft() {
    saveArticle('draft');
}

function publishArticle() {
    saveArticle('published');
}

function saveArticle(status) {
    const data = {
        title: document.getElementById('articleTitle').value,
        content: tinymce.get('articleContent').getContent(),
        excerpt: document.getElementById('articleExcerpt').value,
        featured_image: document.getElementById('featuredImage').value,
        category_id: document.getElementById('articleCategory').value,
        tags: document.getElementById('articleTags').value,
        meta_title: document.getElementById('metaTitle').value || document.getElementById('articleTitle').value,
        meta_description: document.getElementById('metaDescription').value,
        status: status
    };

    if (!data.title) {
        Swal.fire('Error', 'Please enter a title', 'error');
        return;
    }

    Swal.fire({
        title: status === 'published' ? 'Publishing...' : 'Saving...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const url = <?php echo $isEdit ? "'" . app_base_url('admin/blog/articles/update/' . $article['id']) . "'" : "'" . app_base_url('admin/blog/articles/store') . "'"; ?>;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(data)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: status === 'published' ? 'Published!' : 'Saved!',
                html: status === 'published' ? `
                    <p>Your article is now live!</p>
                    <p class="mt-2"><strong>URL:</strong> <code style="background:#f1f5f9;padding:4px 8px;border-radius:4px;font-size:12px;">${data.url}</code></p>
                ` : 'Your draft has been saved',
                showCancelButton: status === 'published',
                confirmButtonText: status === 'published' ? '<i class="fas fa-external-link-alt"></i> View Article' : 'OK',
                cancelButtonText: 'Stay Here',
                confirmButtonColor: '#6366f1'
            }).then((result) => {
                if (result.isConfirmed && status === 'published') {
                    window.open(data.url, '_blank');
                } else if (!result.isConfirmed && status === 'draft') {
                    window.location.href = '<?php echo app_base_url('admin/blog/articles'); ?>';
                }
            });
        } else {
            Swal.fire('Error', 'Failed to save article', 'error');
        }
    })
    .catch(err => {
        Swal.fire('Error', 'Network error: ' + err.message, 'error');
    });
}
</script>
