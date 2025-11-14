<?php
require_once dirname(__DIR__, 3) . '/themes/default/views/partials/header.php';
?>

<style>
    .article-container {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 0 2rem;
    }
    
    .article-header {
        background: white;
        border-radius: 16px;
        padding: 3rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 2rem;
        font-size: 0.875rem;
        color: #718096;
    }
    
    .breadcrumb a {
        color: #667eea;
        text-decoration: none;
    }
    
    .breadcrumb a:hover {
        text-decoration: underline;
    }
    
    .article-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1a202c;
        margin: 0 0 1rem 0;
        line-height: 1.2;
    }
    
    .article-meta {
        display: flex;
        align-items: center;
        gap: 2rem;
        color: #718096;
        font-size: 0.875rem;
        margin-bottom: 2rem;
    }
    
    .article-meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .article-content {
        background: white;
        border-radius: 16px;
        padding: 3rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        border: 1px solid rgba(0,0,0,0.05);
        line-height: 1.7;
    }
    
    .article-content h2 {
        color: #1a202c;
        font-size: 1.75rem;
        font-weight: 600;
        margin: 2rem 0 1rem 0;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .article-content h3 {
        color: #2d3748;
        font-size: 1.5rem;
        font-weight: 600;
        margin: 1.5rem 0 1rem 0;
    }
    
    .article-content p {
        color: #4a5568;
        margin: 0 0 1.5rem 0;
    }
    
    .article-content ul,
    .article-content ol {
        color: #4a5568;
        margin: 0 0 1.5rem 0;
        padding-left: 2rem;
    }
    
    .article-content li {
        margin-bottom: 0.5rem;
    }
    
    .article-content strong {
        color: #2d3748;
        font-weight: 600;
    }
    
    .related-articles {
        margin-top: 3rem;
    }
    
    .related-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }
    
    .related-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    
    .related-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 25px rgba(0,0,0,0.12);
    }
    
    .article-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e2e8f0;
    }
    
    .tag {
        background: #f7fafc;
        color: #4a5568;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        text-decoration: none;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .tag:hover {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }
    
    .article-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e2e8f0;
    }
    
    .helpful-section {
        text-align: center;
    }
    
    .helpful-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 1rem;
    }
    
    .helpful-btn {
        background: #f7fafc;
        border: 1px solid #e2e8f0;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .helpful-btn:hover {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }
    
    /* Dark theme support */
    body.dark-theme .article-header,
    body.dark-theme .article-content,
    body.dark-theme .related-card {
        background: rgba(255,255,255,0.05);
        border-color: rgba(255,255,255,0.1);
    }
    
    body.dark-theme .article-title,
    body.dark-theme .article-content h2,
    body.dark-theme .article-content h3 {
        color: #e2e8f0;
    }
    
    body.dark-theme .article-content p,
    body.dark-theme .article-content li {
        color: #a0aec0;
    }
    
    body.dark-theme .tag {
        background: rgba(255,255,255,0.1);
        color: #e2e8f0;
        border-color: rgba(255,255,255,0.2);
    }
</style>

<div class="article-container">
    <!-- Article Header -->
    <div class="article-header">
        <div class="breadcrumb">
            <a href="/help">Help Center</a>
            <i class="fas fa-chevron-right"></i>
            <a href="/help/category/<?php echo $article['category']; ?>"><?php echo ucfirst(str_replace('-', ' ', $article['category'])); ?></a>
            <i class="fas fa-chevron-right"></i>
            <span><?php echo htmlspecialchars($article['title']); ?></span>
        </div>
        
        <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>
        
        <div class="article-meta">
            <div class="article-meta-item">
                <i class="fas fa-user"></i>
                <span>By <?php echo htmlspecialchars($article['author']); ?></span>
            </div>
            <div class="article-meta-item">
                <i class="fas fa-calendar"></i>
                <span>Published <?php echo date('M j, Y', strtotime($article['date'])); ?></span>
            </div>
            <?php if ($article['updated'] !== $article['date']): ?>
            <div class="article-meta-item">
                <i class="fas fa-edit"></i>
                <span>Updated <?php echo date('M j, Y', strtotime($article['updated'])); ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <p style="font-size: 1.125rem; color: #4a5568; margin: 0; line-height: 1.6;">
            <?php echo htmlspecialchars($article['excerpt']); ?>
        </p>
    </div>
    
    <!-- Article Content -->
    <div class="article-content">
        <?php echo $article['content']; ?>
        
        <!-- Tags -->
        <?php if (!empty($article['tags'])): ?>
        <div class="article-tags">
            <?php foreach ($article['tags'] as $tag): ?>
            <a href="/help/search?q=<?php echo urlencode($tag); ?>" class="tag">
                #<?php echo htmlspecialchars($tag); ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- Article Actions -->
        <div class="article-actions">
            <div class="helpful-section">
                <p style="margin: 0 0 1rem 0; color: #4a5568; font-weight: 500;">Was this article helpful?</p>
                <div class="helpful-buttons">
                    <button class="helpful-btn" onclick="submitFeedback('yes')">
                        <i class="fas fa-thumbs-up me-2"></i> Yes
                    </button>
                    <button class="helpful-btn" onclick="submitFeedback('no')">
                        <i class="fas fa-thumbs-down me-2"></i> No
                    </button>
                </div>
            </div>
            
            <div>
                <a href="/help" style="background: #667eea; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.3s ease;">
                    <i class="fas fa-arrow-left me-2"></i> Back to Help Center
                </a>
            </div>
        </div>
    </div>
    
    <!-- Related Articles -->
    <?php if (!empty($related_articles)): ?>
    <div class="related-articles">
        <h3 style="font-size: 1.75rem; font-weight: 600; color: #1a202c; margin: 0 0 2rem 0;">Related Articles</h3>
        
        <div class="related-grid">
            <?php foreach ($related_articles as $related): ?>
            <a href="/help/article/<?php echo $related['slug']; ?>" class="related-card" style="text-decoration: none;">
                <h4 style="margin: 0 0 1rem 0; color: #1a202c; font-weight: 600; font-size: 1.1rem;">
                    <?php echo htmlspecialchars($related['title']); ?>
                </h4>
                <p style="margin: 0; color: #4a5568; line-height: 1.6;">
                    <?php echo htmlspecialchars($related['excerpt']); ?>
                </p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function submitFeedback(type) {
    // In a real application, this would send feedback to the server
    const message = type === 'yes' ? 'Thank you for your feedback!' : 'Thank you for your feedback. We\'ll work to improve this article.';
    
    // Show a temporary message
    const feedbackDiv = document.createElement('div');
    feedbackDiv.innerHTML = `
        <div style="background: #10b981; color: white; padding: 1rem; border-radius: 8px; margin-top: 1rem; text-align: center;">
            <i class="fas fa-check me-2"></i> ${message}
        </div>
    `;
    
    const helpfulSection = document.querySelector('.helpful-section');
    helpfulSection.appendChild(feedbackDiv);
    
    // Hide the buttons
    document.querySelector('.helpful-buttons').style.display = 'none';
    
    // Remove the message after 3 seconds
    setTimeout(() => {
        feedbackDiv.remove();
        document.querySelector('.helpful-buttons').style.display = 'flex';
    }, 3000);
    
    console.log('Article feedback:', type, 'for article:', '<?php echo $article['slug']; ?>');
}
</script>

<?php require_once dirname(__DIR__, 3) . '/themes/default/views/partials/footer.php'; ?>
