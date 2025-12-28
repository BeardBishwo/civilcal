<?php
/**
 * PUBLIC BLOG INDEX
 * Premium Glassmorphism Design
 */
include __DIR__ . '/../partials/header.php';
?>

<div class="blog-hero-section">
    <div class="container container-custom text-center py-5">
        <h1 class="display-4 fw-bold mb-3">Knowledge Hub</h1>
        <p class="lead text-muted mx-auto" style="max-width: 700px;">
            Insights, tutorials, and deep dives into the world of engineering, finance, and mathematical precision.
        </p>
    </div>
</div>

<div class="container container-custom pb-5">
    <div class="row g-4">
        <?php if (empty($posts)): ?>
            <div class="col-12 text-center py-5">
                <div class="glass-card p-5">
                    <i class="bi bi-journal-x fs-1 text-muted mb-3 d-block"></i>
                    <h3 class="fw-bold">No articles yet</h3>
                    <p class="text-muted">Our experts are busy writing some amazing content. Check back soon!</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="col-md-6 col-lg-4">
                    <article class="blog-card glass-card h-100 transition-hover">
                        <div class="blog-thumb-wrapper">
                            <?php if (!empty($post['featured_image'])): ?>
                                <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="blog-thumb">
                            <?php else: ?>
                                <div class="blog-thumb-placeholder">
                                    <i class="bi bi-image"></i>
                                </div>
                            <?php endif; ?>
                            <div class="blog-date-badge">
                                <?php echo date('M j', strtotime($post['created_at'])); ?>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="fw-bold fs-5 mb-3 line-clamp-2">
                                <a href="<?php echo app_base_url('/blog/' . $post['slug']); ?>" class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </h3>
                            <p class="text-muted small mb-4 line-clamp-3">
                                <?php echo htmlspecialchars($post['excerpt'] ?: substr(strip_tags($post['content']), 0, 150) . '...'); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-secondary"><i class="bi bi-person me-2"></i>Admin</span>
                                <a href="<?php echo app_base_url('/blog/' . $post['slug']); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-4">Read Article</a>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>

<style>
.blog-hero-section { background: rgba(255,255,255,0.4); padding: 4rem 0; margin-bottom: 2rem; }
.blog-card { border: none; border-radius: 20px; overflow: hidden; display: flex; flex-direction: column; }
.blog-thumb-wrapper { position: relative; height: 220px; overflow: hidden; background: #f0f2f5; }
.blog-thumb { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
.blog-card:hover .blog-thumb { transform: scale(1.1); }
.blog-thumb-placeholder { height: 100%; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: #d1d5db; }

.blog-date-badge { 
    position: absolute; top: 1rem; left: 1rem; background: rgba(255,255,255,0.9); 
    backdrop-filter: blur(10px); padding: 0.4rem 0.8rem; border-radius: 12px; 
    font-size: 0.75rem; font-weight: 700; color: #4f46e5; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }

.transition-hover:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important; }

/* Breadcrumb override if needed */
.breadcrumb-container { display: none; } 
</style>
