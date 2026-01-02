<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Shared Calculation') ?></title>
    <meta name="description" content="<?= htmlspecialchars($share['description'] ?? 'View this shared engineering calculation') ?>">
    <meta name="keywords" content="<?= htmlspecialchars($share['keywords'] ?? 'calculator, engineering, calculation') ?>">
    
    <!-- Open Graph meta tags for social sharing -->
    <meta property="og:title" content="<?= htmlspecialchars($title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($share['description'] ?? 'View this shared engineering calculation') ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($share_url) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($share_url) ?>/preview">
    
    <!-- Twitter Card meta tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($title) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($share['description'] ?? 'View this shared engineering calculation') ?>">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/public/assets/css/share.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-calculator me-2"></i>
                <?= APP_NAME ?>
            </a>
            <div class="navbar-nav ms-auto">
                <?php if ($is_owner): ?>
                    <a class="nav-link" href="/shares/my-shares">
                        <i class="fas fa-user me-1"></i>
                        My Shares
                    </a>
                <?php else: ?>
                    <a class="nav-link" href="/auth/login">
                        <i class="fas fa-sign-in-alt me-1"></i>
                        Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Share Header -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h1 class="h3 mb-1"><?= htmlspecialchars($share['title'] ?: $title) ?></h1>
                                <?php if ($share['description']): ?>
                                    <p class="text-muted mb-0"><?= htmlspecialchars($share['description']) ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="text-end">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="shareBtn">
                                        <i class="fas fa-share-alt me-1"></i>
                                        Share
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="embedBtn">
                                        <i class="fas fa-code me-1"></i>
                                        Embed
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row text-center">
                            <div class="col">
                                <div class="d-flex align-items-center justify-content-center text-muted">
                                    <i class="fas fa-eye me-1"></i>
                                    <span><?= number_format($view_count) ?> views</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="d-flex align-items-center justify-content-center text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    <span>Created <?= date('M j, Y', strtotime($share['created_at'])) ?></span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="d-flex align-items-center justify-content-center text-muted">
                                    <i class="fas fa-calculator me-1"></i>
                                    <span><?= ucfirst(str_replace('_', ' ', $calculation['calculator_type'])) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calculation Results -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Calculation Results
                        </h5>
                        <span class="badge bg-primary"><?= ucfirst($calculation['calculator_type']) ?></span>
                    </div>
                    <div class="card-body">
                        <div class="calculation-results">
                            <?php if (is_array($calculation['results'])): ?>
                                <?php foreach ($calculation['results'] as $key => $value): ?>
                                    <div class="result-item mb-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold text-uppercase small">
                                                    <?= htmlspecialchars(ucwords(str_replace('_', ' ', $key))) ?>:
                                                </label>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="result-value">
                                                    <?php if (is_numeric($value)): ?>
                                                        <span class="h5 text-primary">
                                                            <?= is_float($value) ? number_format($value, 2) : number_format($value) ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="h5 text-dark">
                                                            <?= htmlspecialchars($value) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="result-item">
                                    <div class="result-value">
                                        <span class="h5 text-primary"><?= htmlspecialchars($calculation['results']) ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-4 pt-3 border-top">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Calculated on <?= date('M j, Y \a\t g:i A', strtotime($calculation['created_at'])) ?>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Comments Section -->
                <?php if ($show_comments): ?>
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-comments me-2"></i>
                                Comments
                                <span class="badge bg-secondary ms-2" id="commentCount">0</span>
                            </h5>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary sort-btn active" data-sort="best">
                                    <i class="fas fa-fire me-1"></i>
                                    Best
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary sort-btn" data-sort="new">
                                    <i class="fas fa-clock me-1"></i>
                                    New
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary sort-btn" data-sort="top">
                                    <i class="fas fa-arrow-up me-1"></i>
                                    Top
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Comment Form -->
                            <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']): ?>
                                <div class="comment-form mb-4">
                                    <form id="commentForm">
                                        <div class="mb-3">
                                            <textarea class="form-control" id="commentContent" rows="3" 
                                                      placeholder="Share your thoughts about this calculation..." 
                                                      maxlength="5000" required></textarea>
                                            <div class="form-text">
                                                <span id="charCount">0</span>/5000 characters
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane me-1"></i>
                                            Post Comment
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Please <a href="/auth/login" class="alert-link">login</a> to post comments.
                                </div>
                            <?php endif; ?>
                            
                            <!-- Comments Container -->
                            <div id="commentsContainer">
                                <div class="text-center py-4">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Loading comments...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Share Modal -->
    <div class="modal fade" id="shareModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-share-alt me-2"></i>
                        Share This Calculation
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Share Link</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="shareLink" value="<?= htmlspecialchars($share_url) ?>" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('shareLink')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="row g-2">
                        <div class="col">
                            <button class="btn btn-primary w-100" onclick="shareToSocial('twitter', '<?= htmlspecialchars($share_url) ?>', '<?= htmlspecialchars($title) ?>')">
                                <i class="fab fa-twitter me-1"></i>
                                Twitter
                            </button>
                        </div>
                        <div class="col">
                            <button class="btn btn-primary w-100" onclick="shareToSocial('facebook', '<?= htmlspecialchars($share_url) ?>', '<?= htmlspecialchars($title) ?>')">
                                <i class="fab fa-facebook me-1"></i>
                                Facebook
                            </button>
                        </div>
                        <div class="col">
                            <button class="btn btn-primary w-100" onclick="shareToSocial('linkedin', '<?= htmlspecialchars($share_url) ?>', '<?= htmlspecialchars($title) ?>')">
                                <i class="fab fa-linkedin me-1"></i>
                                LinkedIn
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Embed Modal -->
    <div class="modal fade" id="embedModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-code me-2"></i>
                        Embed This Calculation
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Embed Code</label>
                        <textarea class="form-control" id="embedCode" rows="4" readonly><?= htmlspecialchars($embed_code) ?></textarea>
                        <div class="form-text">
                            Copy and paste this code into your website or blog.
                        </div>
                    </div>
                    
                    <button class="btn btn-outline-secondary mb-3" onclick="copyToClipboard('embedCode')">
                        <i class="fas fa-copy me-1"></i>
                        Copy Embed Code
                    </button>
                    
                    <div class="embed-preview">
                        <label class="form-label">Preview</label>
                        <div class="border rounded p-3" style="height: 400px; overflow: hidden;">
                            <div class="embed-preview-content">
                                <iframe src="<?= htmlspecialchars($share_url) ?>" 
                                        width="100%" height="100%" frameborder="0"
                                        title="<?= APP_NAME ?> - Shared Calculation"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light py-4 mt-5">
        <div class="container text-center">
            <p class="text-muted mb-0">
                &copy; <?= date('Y') ?> <?= APP_NAME ?>. 
                <a href="/privacy" class="text-decoration-none">Privacy Policy</a> | 
                <a href="/terms" class="text-decoration-none">Terms of Service</a>
            </p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/assets/js/share.js"></script>
    
    <script>
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Share modal
            document.getElementById('shareBtn').addEventListener('click', function() {
                new bootstrap.Modal(document.getElementById('shareModal')).show();
            });
            
            // Embed modal
            document.getElementById('embedBtn').addEventListener('click', function() {
                new bootstrap.Modal(document.getElementById('embedModal')).show();
            });
            
            // Comment form
            const commentForm = document.getElementById('commentForm');
            if (commentForm) {
                commentForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    submitComment();
                });
                
                // Character count
                const commentContent = document.getElementById('commentContent');
                const charCount = document.getElementById('charCount');
                commentContent.addEventListener('input', function() {
                    charCount.textContent = this.value.length;
                });
            }
            
            // Sort buttons
            document.querySelectorAll('.sort-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    loadComments(this.dataset.sort);
                });
            });
            
            // Load comments
            loadComments('best');
        });
        
        // Global variables
        const shareId = <?= json_encode($share['id']) ?>;
        const shareToken = '<?= htmlspecialchars($share['token']) ?>';
    </script>
</body>
</html>
