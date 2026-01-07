<?php
/**
 * PREMIUM BLOG POST GENERATOR
 * Auto-generate SEO-optimized blog posts from question collections
 */
$categories = $categories ?? [];
?>

<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-blog"></i>
                    <h1>Blog Post Generator</h1>
                </div>
                <div class="header-subtitle">Auto-generate SEO-optimized collection posts from your question bank</div>
            </div>
            <div class="header-actions" style="display:flex; gap:10px;">
                <a href="<?php echo app_base_url('admin/blog/posts'); ?>" class="btn-secondary-compact">
                    <i class="fas fa-list"></i> View All Posts
                </a>
            </div>
        </div>

        <!-- Single Row Creation Toolbar -->
        <div class="creation-toolbar">
            <h5 class="toolbar-title">Generate New Blog Post</h5>
            <form id="blogPostForm" class="creation-form" style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; flex: 1;">
                
                <!-- Post Type Select -->
                <div class="input-group-premium" style="flex: 2; min-width: 200px;">
                    <i class="fas fa-layer-group icon"></i>
                    <select name="type" id="postType" class="form-input-premium" required style="padding-left: 2.25rem;" onchange="updateFilters(this.value)">
                        <option value="popular">🔥 Popular Questions</option>
                        <option value="category">📁 By Category</option>
                        <option value="difficulty">📊 By Difficulty</option>
                        <option value="recent">🕐 Recent Questions</option>
                        <option value="featured">⭐ Featured Questions</option>
                    </select>
                </div>

                <!-- Title Input -->
                <div class="input-group-premium" style="flex: 4; min-width: 250px;">
                    <i class="fas fa-heading icon"></i>
                    <input type="text" name="title" class="form-input-premium" placeholder="e.g., Top 10 Civil Engineering Questions" required>
                </div>
                
                <!-- Limit Input -->
                <div class="input-group-premium" style="flex: 1; min-width: 100px;">
                    <i class="fas fa-list-ol icon"></i>
                    <input type="number" name="limit" class="form-input-premium" placeholder="Limit" value="10" min="1" max="100">
                </div>

                <!-- Category Filter (hidden by default) -->
                <div id="categoryFilter" class="input-group-premium" style="flex: 2; min-width: 200px; display:none;">
                    <i class="fas fa-folder icon"></i>
                    <select name="category_id" class="form-input-premium" style="padding-left: 2.25rem;">
                        <option value="">Select Category...</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>">
                                <?php echo htmlspecialchars($cat['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Difficulty Filter (hidden by default) -->
                <div id="difficultyFilter" class="input-group-premium" style="flex: 2; min-width: 150px; display:none;">
                    <i class="fas fa-chart-line icon"></i>
                    <select name="difficulty" class="form-input-premium" style="padding-left: 2.25rem;">
                        <option value="">Select Difficulty...</option>
                        <option value="1">Easy</option>
                        <option value="2">Easy-Mid</option>
                        <option value="3">Medium</option>
                        <option value="4">Hard</option>
                        <option value="5">Expert</option>
                    </select>
                </div>

                <button type="button" onclick="generatePost()" class="btn-create-premium">
                    <i class="fas fa-magic"></i> GENERATE
                </button>
            </form>
        </div>

        <!-- Content Section -->
        <div class="table-container" style="margin-top: 1.5rem;">
            <div class="table-wrapper" style="background: white; border-radius: 8px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                <h5 style="font-size: 0.875rem; font-weight: 700; color: #475569; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">
                    <i class="fas fa-align-left" style="color: #6366f1;"></i> Post Content
                </h5>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
                    <!-- Introduction -->
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-file-alt" style="color: #6366f1;"></i> Introduction
                        </label>
                        <textarea name="introduction" id="introduction" rows="4" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 0.875rem; font-family: inherit; resize: vertical; transition: all 0.3s ease;" 
                                  placeholder="Write an engaging introduction for your blog post..." 
                                  onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99, 102, 241, 0.1)';" 
                                  onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';"></textarea>
                        <div style="font-size: 0.75rem; color: #94a3b8; font-style: italic;">This will appear at the top of your blog post</div>
                    </div>
                    
                    <!-- Conclusion -->
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-flag-checkered" style="color: #6366f1;"></i> Conclusion
                        </label>
                        <textarea name="conclusion" id="conclusion" rows="4" style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 0.875rem; font-family: inherit; resize: vertical; transition: all 0.3s ease;" 
                                  placeholder="Write a motivating conclusion..." 
                                  onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 3px rgba(99, 102, 241, 0.1)';" 
                                  onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';"></textarea>
                        <div style="font-size: 0.75rem; color: #94a3b8; font-style: italic;">This will appear at the end of your blog post</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
/* Premium Compact Styles */
.btn-secondary-compact {
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 0.813rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.btn-secondary-compact:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}
</style>

<script>
function updateFilters(type) {
    const categoryFilter = document.getElementById('categoryFilter');
    const difficultyFilter = document.getElementById('difficultyFilter');
    
    // Hide all filters first
    categoryFilter.style.display = 'none';
    difficultyFilter.style.display = 'none';
    
    // Show relevant filter
    if (type === 'category') {
        categoryFilter.style.display = 'flex';
    } else if (type === 'difficulty') {
        difficultyFilter.style.display = 'flex';
    }
}

function generatePost() {
    const form = document.getElementById('blogPostForm');
    const formData = new FormData(form);
    
    // Build params object
    const params = {};
    const type = formData.get('type');
    
    if (type === 'category') {
        params.category_id = formData.get('category_id');
    } else if (type === 'difficulty') {
        params.difficulty = formData.get('difficulty');
    }
    params.limit = formData.get('limit') || 10;
    
    // Get introduction and conclusion
    const introduction = document.getElementById('introduction').value;
    const conclusion = document.getElementById('conclusion').value;
    
    // Prepare data
    const data = {
        title: formData.get('title'),
        type: type,
        introduction: introduction,
        conclusion: conclusion,
        params: JSON.stringify(params),
        is_published: 1
    };
    
    // Show loading
    Swal.fire({
        title: 'Generating Blog Post...',
        html: 'Please wait while we create your blog post',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Submit
    fetch('<?php echo app_base_url('admin/blog/posts/store'); ?>', {
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
                title: 'Blog Post Created!',
                html: `
                    <p>Your blog post has been generated successfully!</p>
                    <p class="mt-2"><strong>URL:</strong> <code style="background:#f1f5f9;padding:4px 8px;border-radius:4px;font-size:12px;">${data.url}</code></p>
                `,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-external-link-alt"></i> View Post',
                cancelButtonText: 'Create Another',
                confirmButtonColor: '#6366f1'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open(data.url, '_blank');
                } else {
                    form.reset();
                    document.getElementById('introduction').value = '';
                    document.getElementById('conclusion').value = '';
                    updateFilters('popular');
                }
            });
        } else {
            Swal.fire('Error', data.message || 'Failed to create blog post', 'error');
        }
    })
    .catch(err => {
        Swal.fire('Error', 'Network error: ' + err.message, 'error');
    });
}
</script>
