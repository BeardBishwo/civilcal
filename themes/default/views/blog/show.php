<?php
/**
 * PUBLIC SINGLE BLOG POST
 * Premium Content Layout
 */
include __DIR__ . '/../partials/header.php';
?>

<div class="blog-post-page pt-5 pb-5">
    <div class="container container-custom">
        <div class="row g-5">
            <!-- Article Body -->
            <div class="col-lg-8">
                <article class="glass-card p-4 p-md-5 rounded-4 border-0 shadow-lg">
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?php echo app_base_url('/blog'); ?>" class="text-decoration-none">Blog</a></li>
                            <li class="breadcrumb-item active text-truncate" aria-current="page"><?php echo htmlspecialchars($post['title']); ?></li>
                        </ol>
                    </nav>

                    <h1 class="display-4 fw-bold mb-4"><?php echo htmlspecialchars($post['title']); ?></h1>
                    
                    <div class="d-flex align-items-center gap-3 mb-5 text-muted small">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-person-circle fs-5 text-primary"></i>
                            <span class="fw-bold">Admin</span>
                        </div>
                        <div class="vr"></div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-calendar3"></i>
                            <span><?php echo date('M j, Y', strtotime($post['created_at'])); ?></span>
                        </div>
                        <div class="vr"></div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-clock"></i>
                            <span>5 min read</span>
                        </div>
                    </div>

                    <?php if (!empty($post['featured_image'])): ?>
                        <div class="post-featured-wrapper mb-5 rounded-4 overflow-hidden shadow-md">
                            <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-100">
                        </div>
                    <?php endif; ?>

                    <div class="post-content-body rich-text-area">
                        <?php echo $post['content']; // Outputting directly as it's expected to have HTML from admin ?>
                    </div>

                    <hr class="my-5 opacity-10">

                    <div class="post-footer-actions d-flex justify-content-between align-items-center">
                        <div class="social-share d-flex align-items-center gap-3">
                            <span class="small fw-bold text-muted text-uppercase">Share:</span>
                            <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle"><i class="bi bi-twitter"></i></a>
                            <a href="#" class="btn btn-outline-secondary btn-sm rounded-circle"><i class="bi bi-linkedin"></i></a>
                        </div>
                        <a href="<?php echo app_base_url('/blog'); ?>" class="btn btn-link text-decoration-none p-0"><i class="bi bi-arrow-left me-2"></i>Back to Knowledge Hub</a>
                    </div>
                </article>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <aside class="blog-sidebar sticky-top" style="top: 2rem;">
                    
                    <!-- Search Widget -->
                    <div class="glass-card p-4 mb-4 rounded-4 border-0 shadow-sm">
                        <h5 class="fw-bold mb-3">Search Hub</h5>
                        <div class="input-group">
                            <input type="text" class="form-control rounded-start-pill border-0 bg-light" placeholder="Keywords...">
                            <button class="btn btn-primary rounded-end-pill px-3"><i class="bi bi-search"></i></button>
                        </div>
                    </div>

                    <!-- Recent Posts -->
                    <div class="glass-card p-4 mb-4 rounded-4 border-0 shadow-sm">
                        <h5 class="fw-bold mb-4">You Might Also Like</h5>
                        <div class="recent-posts-list d-flex flex-column gap-4">
                            <?php foreach ($recentPosts as $recent): ?>
                                <?php if ($recent['id'] == $post['id']) continue; ?>
                                <a href="<?php echo app_base_url('/blog/' . $recent['slug']); ?>" class="recent-post-item d-flex gap-3 text-decoration-none">
                                    <div class="recent-post-thumb rounded-3 overflow-hidden" style="width: 80px; height: 80px; flex-shrink: 0; background: #eee;">
                                        <?php if (!empty($recent['featured_image'])): ?>
                                            <img src="<?php echo htmlspecialchars($recent['featured_image']); ?>" alt="Thumb" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="h-100 d-flex align-items-center justify-content-center text-muted fs-4">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="recent-post-info">
                                        <h6 class="fw-bold mb-1 line-clamp-2 text-dark" style="font-size: 0.95rem;"><?php echo htmlspecialchars($recent['title']); ?></h6>
                                        <span class="small text-muted"><?php echo date('M j, Y', strtotime($recent['created_at'])); ?></span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Ad Slot Placement (Native for blog) -->
                    <div class="blog-ad-widget text-center">
                        <?php echo \App\Helpers\AdHelper::show('sidebar_bottom', 'rounded-4 shadow-sm'); ?>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>

<style>
.blog-post-page { background: #f8fafc; }
.rich-text-area { 
    font-size: 1.125rem; line-height: 1.8; color: #334155; 
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
}
.rich-text-area h2 { font-weight: 800; margin-top: 2.5rem; margin-bottom: 1.5rem; color: #0f172a; }
.rich-text-area p { margin-bottom: 1.5rem; }
.rich-text-area blockquote { 
    padding: 1.5rem; border-left: 5px solid #4f46e5; background: #f1f5f9; 
    border-radius: 0 12px 12px 0; font-style: italic; margin: 2rem 0;
}
.rich-text-area img { max-width: 100%; height: auto; border-radius: 16px; margin: 2rem 0; }

.recent-post-item:hover .recent-post-info h6 { color: #4f46e5 !important; }
.recent-post-thumb img { transition: transform 0.3s ease; }
.recent-post-item:hover .recent-post-thumb img { transform: scale(1.1); }

.input-group .form-control:focus { box-shadow: none; border-color: transparent; }
</style>
