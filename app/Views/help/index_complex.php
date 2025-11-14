<?php
$page_title = 'Help Center - Engineering Calculator Support';
require_once dirname(__DIR__, 3) . '/themes/default/views/partials/header.php';
?>

<style>
    .help-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 4rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .help-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }
    
    .help-search {
        max-width: 600px;
        margin: 2rem auto 0;
        position: relative;
        z-index: 2;
    }
    
    .help-search .search-input {
        width: 100%;
        padding: 1rem 1.5rem;
        border: none;
        border-radius: 50px;
        font-size: 1.1rem;
        box-shadow: 0 8px 30px rgba(0,0,0,0.2);
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(10px);
    }
    
    .help-search .search-btn {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        background: linear-gradient(135deg, #667eea, #764ba2);
        border: none;
        border-radius: 50px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .help-search .search-btn:hover {
        transform: translateY(-50%) scale(1.05);
        box-shadow: 0 4px 15px rgba(102,126,234,0.4);
    }
    
    .help-section {
        padding: 4rem 0;
    }
    
    .help-categories {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }
    
    .category-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
    }
    
    .category-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--category-color, #667eea);
    }
    
    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }
    
    .category-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
        background: var(--category-color, #667eea);
    }
    
    .category-card h3 {
        margin: 0 0 1rem 0;
        color: #1a202c;
        font-size: 1.25rem;
        font-weight: 700;
    }
    
    .category-card p {
        color: #4a5568;
        margin: 0 0 1.5rem 0;
        line-height: 1.6;
    }
    
    .category-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.875rem;
        color: #718096;
    }
    
    .featured-articles {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }
    
    .article-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .article-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 25px rgba(0,0,0,0.12);
    }
    
    .article-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        margin-bottom: 1rem;
        background: var(--article-color, #667eea);
    }
    
    .faq-section {
        background: #f7fafc;
        padding: 4rem 0;
    }
    
    .faq-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }
    
    .faq-item {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .faq-question {
        font-weight: 600;
        color: #1a202c;
        margin: 0 0 1rem 0;
        font-size: 1.1rem;
    }
    
    .faq-answer {
        color: #4a5568;
        line-height: 1.6;
        margin: 0;
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }
    
    .section-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1a202c;
        margin: 0 0 1rem 0;
    }
    
    .section-subtitle {
        font-size: 1.25rem;
        color: #4a5568;
        margin: 0;
    }
    
    /* Dark theme support */
    body.dark-theme .category-card,
    body.dark-theme .article-card,
    body.dark-theme .faq-item {
        background: rgba(255,255,255,0.05);
        border-color: rgba(255,255,255,0.1);
    }
    
    body.dark-theme .category-card h3,
    body.dark-theme .section-title,
    body.dark-theme .faq-question {
        color: #e2e8f0;
    }
    
    body.dark-theme .category-card p,
    body.dark-theme .section-subtitle,
    body.dark-theme .faq-answer {
        color: #a0aec0;
    }
    
    body.dark-theme .faq-section {
        background: rgba(0,0,0,0.2);
    }
</style>

<!-- Hero Section -->
<div class="help-hero">
    <div class="container">
        <h1 style="font-size: 3rem; font-weight: 700; margin: 0 0 1rem 0;">Help Center</h1>
        <p style="font-size: 1.25rem; opacity: 0.9; margin: 0;">Get help with engineering calculations, tutorials, and support</p>
        
        <div class="help-search">
            <form action="/help/search" method="GET">
                <input type="text" name="q" class="search-input" placeholder="Search for help articles, tutorials, or FAQs..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Browse Topics Section -->
<div class="help-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Browse Topics</h2>
            <p class="section-subtitle">Find help organized by engineering discipline and topic</p>
        </div>
        
        <div class="help-categories">
            <?php foreach ($categories as $category): ?>
            <a href="/help/category/<?php echo $category['slug']; ?>" class="category-card" style="text-decoration: none; --category-color: <?php echo $category['color']; ?>">
                <div class="category-icon">
                    <i class="<?php echo $category['icon']; ?>"></i>
                </div>
                <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                <p><?php echo htmlspecialchars($category['description']); ?></p>
                <div class="category-meta">
                    <span><?php echo $category['article_count']; ?> articles</span>
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Featured Articles Section -->
<div class="help-section" style="background: #f7fafc;">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Featured Articles</h2>
            <p class="section-subtitle">Popular and essential guides to get you started</p>
        </div>
        
        <div class="featured-articles">
            <?php foreach ($featured_articles as $article): ?>
            <a href="/help/article/<?php echo $article['slug']; ?>" class="article-card" style="text-decoration: none; --article-color: <?php echo $article['color']; ?>">
                <div class="article-icon">
                    <i class="<?php echo $article['icon']; ?>"></i>
                </div>
                <h3 style="margin: 0 0 1rem 0; color: #1a202c; font-size: 1.2rem; font-weight: 600;">
                    <?php echo htmlspecialchars($article['title']); ?>
                </h3>
                <p style="color: #4a5568; margin: 0; line-height: 1.6;">
                    <?php echo htmlspecialchars($article['excerpt']); ?>
                </p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Common Questions Section -->
<div class="faq-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Common Questions</h2>
            <p class="section-subtitle">Quick answers to frequently asked questions</p>
        </div>
        
        <div class="faq-grid">
            <?php foreach ($common_questions as $faq): ?>
            <div class="faq-item">
                <h4 class="faq-question"><?php echo htmlspecialchars($faq['question']); ?></h4>
                <p class="faq-answer"><?php echo htmlspecialchars($faq['answer']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Recent Articles Section -->
<div class="help-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Recent Updates</h2>
            <p class="section-subtitle">Latest help articles and updates</p>
        </div>
        
        <div style="max-width: 800px; margin: 3rem auto 0;">
            <?php foreach ($recent_articles as $article): ?>
            <div style="display: flex; align-items: center; padding: 1.5rem; background: white; border-radius: 12px; margin-bottom: 1rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05);">
                <div style="flex: 1;">
                    <h4 style="margin: 0 0 0.5rem 0; color: #1a202c; font-weight: 600;">
                        <a href="/help/article/<?php echo $article['slug']; ?>" style="text-decoration: none; color: inherit;">
                            <?php echo htmlspecialchars($article['title']); ?>
                        </a>
                    </h4>
                    <p style="margin: 0; color: #718096; font-size: 0.875rem;">
                        <?php echo ucfirst($article['category']); ?> • <?php echo date('M j, Y', strtotime($article['date'])); ?>
                    </p>
                </div>
                <div>
                    <i class="fas fa-arrow-right" style="color: #a0aec0;"></i>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Contact Support Section -->
<div class="help-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
    <div class="container" style="text-align: center;">
        <h2 style="font-size: 2.5rem; font-weight: 700; margin: 0 0 1rem 0;">Still Need Help?</h2>
        <p style="font-size: 1.25rem; opacity: 0.9; margin: 0 0 2rem 0;">Our support team is here to assist you with any questions</p>
        
        <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
            <a href="/contact" style="background: rgba(255,255,255,0.2); color: white; padding: 1rem 2rem; border-radius: 50px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; backdrop-filter: blur(10px);">
                <i class="fas fa-envelope me-2"></i> Contact Support
            </a>
            <a href="/feedback" style="background: rgba(255,255,255,0.2); color: white; padding: 1rem 2rem; border-radius: 50px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; backdrop-filter: blur(10px);">
                <i class="fas fa-comment me-2"></i> Send Feedback
            </a>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__, 3) . '/themes/default/views/partials/footer.php'; ?>
