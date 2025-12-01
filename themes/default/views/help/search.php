<?php
require_once dirname(__DIR__, 3) . '/themes/default/views/partials/header.php';
?>

<style>
    .search-container {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 0 2rem;
    }
    
    .search-header {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        border: 1px solid rgba(0,0,0,0.05);
        text-align: center;
    }
    
    .search-form {
        max-width: 600px;
        margin: 2rem auto 0;
        position: relative;
    }
    
    .search-input {
        width: 100%;
        padding: 1rem 1.5rem;
        border: 2px solid #e2e8f0;
        border-radius: 50px;
        font-size: 1.1rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .search-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 4px 20px rgba(102,126,234,0.2);
    }
    
    .search-btn {
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
    
    .search-btn:hover {
        transform: translateY(-50%) scale(1.05);
    }
    
    .search-results {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .result-item {
        padding: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .result-item:last-child {
        border-bottom: none;
    }
    
    .result-item:hover {
        background: #f8fafc;
        border-radius: 12px;
    }
    
    .result-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1a202c;
        margin: 0 0 0.5rem 0;
    }
    
    .result-title a {
        color: inherit;
        text-decoration: none;
    }
    
    .result-title a:hover {
        color: #667eea;
    }
    
    .result-excerpt {
        color: #4a5568;
        line-height: 1.6;
        margin: 0 0 1rem 0;
    }
    
    .result-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 0.875rem;
        color: #718096;
    }
    
    .category-badge {
        background: #f7fafc;
        color: #4a5568;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        border: 1px solid #e2e8f0;
    }
    
    .no-results {
        text-align: center;
        padding: 4rem 2rem;
        color: #718096;
    }
    
    .no-results i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    /* Dark theme support */
    body.dark-theme .search-header,
    body.dark-theme .search-results {
        background: rgba(255,255,255,0.05);
        border-color: rgba(255,255,255,0.1);
    }
    
    body.dark-theme .result-title {
        color: #e2e8f0;
    }
    
    body.dark-theme .result-excerpt {
        color: #a0aec0;
    }
    
    body.dark-theme .search-input {
        background: rgba(255,255,255,0.05);
        border-color: rgba(255,255,255,0.1);
        color: #e2e8f0;
    }
</style>

<div class="search-container">
    <!-- Search Header -->
    <div class="search-header">
        <h1 style="font-size: 2.5rem; font-weight: 700; color: #1a202c; margin: 0 0 1rem 0;">
            <?php if (!empty($query)): ?>
                Search Results
            <?php else: ?>
                Search Help Center
            <?php endif; ?>
        </h1>
        
        <?php if (!empty($query)): ?>
            <p style="font-size: 1.125rem; color: #4a5568; margin: 0;">
                Found <?php echo $total_results; ?> result<?php echo $total_results !== 1 ? 's' : ''; ?> for "<?php echo htmlspecialchars($query); ?>"
            </p>
        <?php else: ?>
            <p style="font-size: 1.125rem; color: #4a5568; margin: 0;">
                Search our help articles, tutorials, and FAQs
            </p>
        <?php endif; ?>
        
        <div class="search-form">
            <form action="/help/search" method="GET">
                <input type="text" name="q" class="search-input" 
                       placeholder="Search for help articles, tutorials, or FAQs..." 
                       value="<?php echo htmlspecialchars($query); ?>" autofocus>
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>
    </div>
    
    <!-- Search Results -->
    <?php if (!empty($query)): ?>
        <div class="search-results">
            <?php if (empty($results)): ?>
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3 style="margin: 0 0 1rem 0; color: #4a5568;">No results found</h3>
                    <p style="margin: 0 0 2rem 0;">
                        We couldn't find any articles matching "<?php echo htmlspecialchars($query); ?>".
                    </p>
                    <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
                        <a href="/help" style="background: #667eea; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 500;">
                            Browse All Topics
                        </a>
                        <a href="/help/category/getting-started" style="background: #f7fafc; color: #4a5568; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 500; border: 1px solid #e2e8f0;">
                            Getting Started Guide
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($results as $result): ?>
                <div class="result-item">
                    <h3 class="result-title">
                        <a href="/help/article/<?php echo $result['slug']; ?>">
                            <?php echo htmlspecialchars($result['title']); ?>
                        </a>
                    </h3>
                    <p class="result-excerpt">
                        <?php echo htmlspecialchars($result['excerpt']); ?>
                    </p>
                    <div class="result-meta">
                        <span class="category-badge">
                            <?php echo htmlspecialchars($result['category']); ?>
                        </span>
                        <span>
                            <i class="fas fa-arrow-right"></i> Read Article
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Popular Searches or Categories when no query -->
        <div class="search-results">
            <h3 style="margin: 0 0 2rem 0; color: #1a202c; font-size: 1.5rem; font-weight: 600;">
                Popular Help Topics
            </h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                <a href="/help/category/getting-started" style="display: block; padding: 1.5rem; background: #f8fafc; border-radius: 12px; text-decoration: none; border: 1px solid #e2e8f0; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 48px; height: 48px; background: #10b981; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <div>
                            <h4 style="margin: 0 0 0.5rem 0; color: #1a202c; font-weight: 600;">Getting Started</h4>
                            <p style="margin: 0; color: #4a5568; font-size: 0.875rem;">Basic tutorials and setup</p>
                        </div>
                    </div>
                </a>
                
                <a href="/help/category/civil-engineering" style="display: block; padding: 1.5rem; background: #f8fafc; border-radius: 12px; text-decoration: none; border: 1px solid #e2e8f0; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 48px; height: 48px; background: #3b82f6; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-hard-hat"></i>
                        </div>
                        <div>
                            <h4 style="margin: 0 0 0.5rem 0; color: #1a202c; font-weight: 600;">Civil Engineering</h4>
                            <p style="margin: 0; color: #4a5568; font-size: 0.875rem;">Concrete, steel, foundations</p>
                        </div>
                    </div>
                </a>
                
                <a href="/help/category/troubleshooting" style="display: block; padding: 1.5rem; background: #f8fafc; border-radius: 12px; text-decoration: none; border: 1px solid #e2e8f0; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 48px; height: 48px; background: #6b7280; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div>
                            <h4 style="margin: 0 0 0.5rem 0; color: #1a202c; font-weight: 600;">Troubleshooting</h4>
                            <p style="margin: 0; color: #4a5568; font-size: 0.875rem;">Common issues & solutions</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__, 3) . '/themes/default/views/partials/footer.php'; ?>
