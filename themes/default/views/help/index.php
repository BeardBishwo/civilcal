<style>
.help-layout {
    display: flex;
    min-height: calc(100vh - 80px);
    background: #f8fafc;
}

body.dark-theme .help-layout {
    background: #0f172a;
}

.help-sidebar {
    width: 280px;
    background: white;
    border-right: 1px solid #e2e8f0;
    padding: 2rem 0;
    position: sticky;
    top: 80px;
    height: calc(100vh - 80px);
    overflow-y: auto;
}

body.dark-theme .help-sidebar {
    background: #1e293b;
    border-color: #334155;
}

.help-content {
    flex: 1;
    padding: 2rem;
    max-width: calc(100% - 280px);
}

.sidebar-section {
    margin-bottom: 2rem;
}

.sidebar-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin: 0 0 1rem 0;
    padding: 0 1.5rem;
}

body.dark-theme .sidebar-title {
    color: #9ca3af;
}

.sidebar-nav {
    list-style: none;
    margin: 0;
    padding: 0;
}

.sidebar-nav li {
    margin: 0;
}

.sidebar-nav a {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1.5rem;
    color: #4b5563;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
}

.sidebar-nav a:hover {
    background: #f3f4f6;
    color: #1f2937;
    border-left-color: #3b82f6;
}

.sidebar-nav a.active {
    background: #eff6ff;
    color: #2563eb;
    border-left-color: #3b82f6;
}

body.dark-theme .sidebar-nav a {
    color: #d1d5db;
}

body.dark-theme .sidebar-nav a:hover {
    background: #374151;
    color: #f9fafb;
}

body.dark-theme .sidebar-nav a.active {
    background: #1e3a8a;
    color: #93c5fd;
}

.help-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 3rem;
    border-radius: 16px;
    margin-bottom: 3rem;
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

.help-hero-content {
    position: relative;
    z-index: 2;
}

.help-sections {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.help-section-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
}

.help-section-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--section-color, #3b82f6);
}

.help-section-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
    color: inherit;
    text-decoration: none;
}

body.dark-theme .help-section-card {
    background: #1e293b;
    border-color: #334155;
}

.section-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
    margin-bottom: 1.5rem;
    background: var(--section-color, #3b82f6);
}

.quick-start {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    margin-bottom: 3rem;
}

body.dark-theme .quick-start {
    background: #1e293b;
    border-color: #334155;
}

.code-block {
    background: #1e293b;
    color: #e2e8f0;
    padding: 1.5rem;
    border-radius: 8px;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 0.875rem;
    line-height: 1.6;
    overflow-x: auto;
    margin: 1rem 0;
    position: relative;
}

.code-block::before {
    content: attr(data-language);
    position: absolute;
    top: 0.5rem;
    right: 1rem;
    font-size: 0.75rem;
    color: #94a3b8;
    text-transform: uppercase;
    font-weight: 500;
}

.code-tabs {
    display: flex;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 1rem;
}

body.dark-theme .code-tabs {
    border-color: #374151;
}

.code-tab {
    padding: 0.75rem 1rem;
    background: none;
    border: none;
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    border-bottom: 2px solid transparent;
}

.code-tab.active {
    color: #3b82f6;
    border-bottom-color: #3b82f6;
}

body.dark-theme .code-tab {
    color: #9ca3af;
}

body.dark-theme .code-tab.active {
    color: #60a5fa;
    border-bottom-color: #60a5fa;
}

.endpoint-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
    margin-bottom: 1rem;
    transition: all 0.2s ease;
}

.endpoint-card:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
}

body.dark-theme .endpoint-card {
    background: #1e293b;
    border-color: #334155;
}

.method-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-right: 1rem;
}

.method-post {
    background: #dcfdf7;
    color: #065f46;
}

.method-get {
    background: #dbeafe;
    color: #1e40af;
}

body.dark-theme .method-post {
    background: #064e3b;
    color: #6ee7b7;
}

body.dark-theme .method-get {
    background: #1e3a8a;
    color: #93c5fd;
}

.sdk-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin: 2rem 0;
}

.sdk-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
}

.sdk-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

body.dark-theme .sdk-card {
    background: #1e293b;
    border-color: #334155;
}

.section-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 1rem 0;
}

body.dark-theme .section-title {
    color: #f9fafb;
}

@media (max-width: 1024px) {
    .help-layout {
        flex-direction: column;
    }
    
    .help-sidebar {
        width: 100%;
        height: auto;
        position: relative;
        top: 0;
    }
    
    .help-content {
        max-width: 100%;
    }
}

/* Search box styling */
.help-search-box {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    margin-bottom: 2rem;
}

body.dark-theme .help-search-box {
    background: #1e293b;
    border-color: #334155;
}

.help-search-input {
    width: 100%;
    padding: 1rem 1.5rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.2s ease;
}

body.dark-theme .help-search-input {
    background: #374151;
    border-color: #4b5563;
    color: #f9fafb;
}

.help-search-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.help-search-button {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 1rem;
    width: 100%;
}

.help-search-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

/* Popular articles styling */
.popular-articles {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    margin-bottom: 3rem;
}

body.dark-theme .popular-articles {
    background: #1e293b;
    border-color: #334155;
}

.article-card {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    border: 1px solid #e5e7eb;
    margin-bottom: 1rem;
    transition: all 0.2s ease;
    text-decoration: none;
    color: inherit;
}

.article-card:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
    color: inherit;
    text-decoration: none;
}

body.dark-theme .article-card {
    background: #1e293b;
    border-color: #334155;
}

/* Contact support styling */
.contact-support {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

body.dark-theme .contact-support {
    background: #1e293b;
    border-color: #334155;
}

.contact-support h5 {
    color: #1f2937;
    margin-bottom: 1rem;
}

body.dark-theme .contact-support h5 {
    color: #f9fafb;
}

.contact-support p {
    color: #6b7280;
    margin-bottom: 1.5rem;
}

body.dark-theme .contact-support p {
    color: #9ca3af;
}

.contact-support .btn {
    margin-right: 1rem;
}

.contact-support .btn-info {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
}

.contact-support .btn-info:hover {
    background: #1d4ed8;
    color: white;
}

.contact-support .btn-outline-info {
    background: transparent;
    color: #3b82f6;
    border: 1px solid #3b82f6;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
}

.contact-support .btn-outline-info:hover {
    background: #3b82f6;
    color: white;
}

body.dark-theme .contact-support .btn-outline-info {
    color: #60a5fa;
    border-color: #60a5fa;
}

body.dark-theme .contact-support .btn-outline-info:hover {
    background: #60a5fa;
    color: white;
}
</style>

<div class="help-layout">
    <!-- Sidebar Navigation -->
    <nav class="help-sidebar">
        <div class="sidebar-section">
            <h3 class="sidebar-title">Getting Started</h3>
            <ul class="sidebar-nav">
                <li><a href="#overview" class="active"><i class="fas fa-home"></i> Overview</a></li>
                <li><a href="#search"><i class="fas fa-search"></i> Search</a></li>
                <li><a href="#categories"><i class="fas fa-th-large"></i> Categories</a></li>
                <li><a href="#popular"><i class="fas fa-star"></i> Popular Articles</a></li>
            </ul>
        </div>
        
        <div class="sidebar-section">
            <h3 class="sidebar-title">Resources</h3>
            <ul class="sidebar-nav">
                <li><a href="/developers"><i class="fas fa-code"></i> API Documentation</a></li>
                <li><a href="/help/contact"><i class="fas fa-envelope"></i> Contact Support</a></li>
                <li><a href="/help/faq"><i class="fas fa-question-circle"></i> FAQ</a></li>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="help-content">
        <!-- Hero Section -->
        <div class="help-hero">
            <div class="help-hero-content">
                <h1 style="font-size: 2.5rem; font-weight: 700; margin: 0 0 1rem 0;">
                    Help Center
                </h1>
                <p style="font-size: 1.25rem; opacity: 0.9; margin: 0 0 2rem 0;">
                    Find answers to your engineering calculation questions
                </p>
                
                <!-- Search Box -->
                <div class="help-search-box">
                    <div class="input-group">
                        <input type="text" class="help-search-input" placeholder="Search for help articles...">
                        <button class="help-search-button" type="button">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Help Categories -->
        <section id="categories">
            <h2 class="section-title">Browse Help Categories</h2>
            <div class="help-sections">
                <?php foreach ($categories as $category): ?>
                <a href="/help/category/<?php echo $category['slug']; ?>" class="help-section-card" style="text-decoration: none; --section-color: <?php echo $category['color']; ?>">
                    <div class="section-icon">
                        <i class="<?php echo $category['icon']; ?>"></i>
                    </div>
                    <h3 style="margin: 0 0 1rem 0; color: #1f2937; font-size: 1.25rem; font-weight: 600;">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </h3>
                    <p style="color: #6b7280; margin: 0 0 1.5rem 0; line-height: 1.6;">
                        <?php echo htmlspecialchars($category['description']); ?>
                    </p>
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.875rem; color: #9ca3af;">
                        <span><?php echo $category['article_count']; ?> articles</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        
        <!-- Featured Articles -->
        <section id="featured">
            <h2 class="section-title">Featured Articles</h2>
            <div class="help-sections">
                <?php foreach ($featured_articles as $article): ?>
                <a href="/help/article/<?php echo $article['slug']; ?>" class="help-section-card" style="text-decoration: none; --section-color: <?php echo $article['color']; ?>">
                    <div class="section-icon">
                        <i class="<?php echo $article['icon']; ?>"></i>
                    </div>
                    <h3 style="margin: 0 0 1rem 0; color: #1f2937; font-size: 1.25rem; font-weight: 600;">
                        <?php echo htmlspecialchars($article['title']); ?>
                    </h3>
                    <p style="color: #6b7280; margin: 0 0 1.5rem 0; line-height: 1.6;">
                        <?php echo htmlspecialchars($article['excerpt']); ?>
                    </p>
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.875rem; color: #9ca3af;">
                        <span>Featured</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        
        <!-- Popular Articles -->
        <section id="popular">
            <h2 class="section-title">Popular Articles</h2>
            <div class="popular-articles">
                <?php foreach ($recent_articles as $article): ?>
                <a href="/help/article/<?php echo $article['slug']; ?>" class="article-card">
                    <div style="display: flex; align-items: start; gap: 1rem;">
                        <div style="flex: 1;">
                            <h4 style="margin: 0 0 0.5rem 0; color: #1f2937; font-weight: 600;">
                                <?php echo htmlspecialchars($article['title']); ?>
                            </h4>
                            <p style="color: #6b7280; margin: 0; font-size: 0.875rem;">
                                <?php echo htmlspecialchars($article['category']); ?> • <?php echo $article['date']; ?>
                            </p>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        
        <!-- Contact Support -->
        <section id="contact">
            <div class="contact-support">
                <h5><i class="fas fa-headset me-2"></i>Need More Help?</h5>
                <p>Can't find what you're looking for? Our support team is here to help!</p>
                <div>
                    <a href="mailto:support@bishwocalculator.com" class="btn-info">
                        <i class="fas fa-envelope me-1"></i> Contact Support
                    </a>
                    <a href="/developers" class="btn-outline-info">
                        <i class="fas fa-code me-1"></i> API Documentation
                    </a>
                </div>
            </div>
        </section>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarLinks = document.querySelectorAll('.sidebar-nav a');
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Update active sidebar link based on scroll position
    window.addEventListener('scroll', function() {
        let current = '';
        const sections = document.querySelectorAll('section[id]');
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (pageYOffset >= sectionTop - 100) {
                current = section.getAttribute('id');
            }
        });
        
        sidebarLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
            }
        });
    });
    
    // Search functionality
    const searchInput = document.querySelector('.help-search-input');
    const searchButton = document.querySelector('.help-search-button');
    
    if (searchButton && searchInput) {
        searchButton.addEventListener('click', function() {
            const query = searchInput.value.trim();
            if (query) {
                window.location.href = '/help/search?q=' + encodeURIComponent(query);
            }
        });
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchButton.click();
            }
        });
    }
});
</script>
