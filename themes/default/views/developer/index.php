<?php
$page_title = 'API Reference for Developers - Engineering Calculator API';
require_once dirname(__DIR__, 3) . '/themes/default/views/partials/header.php';
?>

<style>
    .developer-layout {
        display: flex;
        min-height: calc(100vh - 80px);
        background: #f8fafc;
    }
    
    body.dark-theme .developer-layout {
        background: #0f172a;
    }
    
    .developer-sidebar {
        width: 280px;
        background: white;
        border-right: 1px solid #e2e8f0;
        padding: 2rem 0;
        position: sticky;
        top: 80px;
        height: calc(100vh - 80px);
        overflow-y: auto;
    }
    
    body.dark-theme .developer-sidebar {
        background: #1e293b;
        border-color: #334155;
    }
    
    .developer-content {
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
    
    .api-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3rem;
        border-radius: 16px;
        margin-bottom: 3rem;
        position: relative;
        overflow: hidden;
    }
    
    .api-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }
    
    .api-hero-content {
        position: relative;
        z-index: 2;
    }
    
    .api-sections {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }
    
    .api-section-card {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .api-section-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--section-color, #3b82f6);
    }
    
    .api-section-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
    }
    
    body.dark-theme .api-section-card {
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
        .developer-layout {
            flex-direction: column;
        }
        
        .developer-sidebar {
            width: 100%;
            height: auto;
            position: relative;
            top: 0;
        }
        
        .developer-content {
            max-width: 100%;
        }
    }
</style>

<div class="developer-layout">
    <!-- Sidebar Navigation -->
    <nav class="developer-sidebar">
        <div class="sidebar-section">
            <h3 class="sidebar-title">Getting Started</h3>
            <ul class="sidebar-nav">
                <li><a href="#overview" class="active"><i class="fas fa-home"></i> Overview</a></li>
                <li><a href="#authentication"><i class="fas fa-key"></i> Authentication</a></li>
                <li><a href="#rate-limits"><i class="fas fa-tachometer-alt"></i> Rate Limits</a></li>
                <li><a href="#errors"><i class="fas fa-exclamation-triangle"></i> Error Handling</a></li>
            </ul>
        </div>
        
        <div class="sidebar-section">
            <h3 class="sidebar-title">API Reference</h3>
            <ul class="sidebar-nav">
                <?php foreach ($api_sections as $section): ?>
                <li>
                    <a href="/developers/<?php echo $section['slug']; ?>">
                        <i class="<?php echo $section['icon']; ?>"></i>
                        <?php echo $section['name']; ?>
                        <span style="margin-left: auto; font-size: 0.75rem; color: #9ca3af;"><?php echo $section['endpoints']; ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <div class="sidebar-section">
            <h3 class="sidebar-title">Resources</h3>
            <ul class="sidebar-nav">
                <li><a href="/developers/sdk"><i class="fas fa-code"></i> SDKs & Libraries</a></li>
                <li><a href="/developers/playground"><i class="fas fa-play"></i> API Playground</a></li>
                <li><a href="/developers/changelog"><i class="fas fa-history"></i> Changelog</a></li>
                <li><a href="/developers/support"><i class="fas fa-life-ring"></i> Support</a></li>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="developer-content">
        <!-- Hero Section -->
        <div class="api-hero">
            <div class="api-hero-content">
                <h1 style="font-size: 2.5rem; font-weight: 700; margin: 0 0 1rem 0;">
                    API Reference for Developers
                </h1>
                <p style="font-size: 1.25rem; opacity: 0.9; margin: 0 0 2rem 0;">
                    Integrate powerful engineering calculations into your applications with our comprehensive REST API
                </p>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="#quick-start" style="background: rgba(255,255,255,0.2); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; backdrop-filter: blur(10px);">
                        <i class="fas fa-rocket me-2"></i> Quick Start
                    </a>
                    <a href="/developers/playground" style="background: rgba(255,255,255,0.1); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                        <i class="fas fa-play me-2"></i> Try API
                    </a>
                </div>
            </div>
        </div>
        
        <!-- API Sections -->
        <section id="api-sections">
            <h2 class="section-title">Browse API Documentation</h2>
            <div class="api-sections">
                <?php foreach ($api_sections as $section): ?>
                <a href="/developers/<?php echo $section['slug']; ?>" class="api-section-card" style="text-decoration: none; --section-color: <?php echo $section['color']; ?>">
                    <div class="section-icon">
                        <i class="<?php echo $section['icon']; ?>"></i>
                    </div>
                    <h3 style="margin: 0 0 1rem 0; color: #1f2937; font-size: 1.25rem; font-weight: 600;">
                        <?php echo htmlspecialchars($section['name']); ?>
                    </h3>
                    <p style="color: #6b7280; margin: 0 0 1.5rem 0; line-height: 1.6;">
                        <?php echo htmlspecialchars($section['description']); ?>
                    </p>
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.875rem; color: #9ca3af;">
                        <span><?php echo $section['endpoints']; ?> endpoints</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        
        <!-- Quick Start Guide -->
        <section id="quick-start" class="quick-start">
            <h2 class="section-title">Quick Start Guide</h2>
            <p style="color: #6b7280; margin: 0 0 2rem 0; font-size: 1.125rem;">
                Get up and running with the Engineering Calculator API in minutes
            </p>
            
            <?php foreach ($quick_start['steps'] as $index => $step): ?>
            <div style="margin-bottom: 3rem;">
                <h3 style="display: flex; align-items: center; gap: 1rem; margin: 0 0 1rem 0; color: #1f2937;">
                    <span style="width: 32px; height: 32px; background: #3b82f6; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; font-weight: 600;">
                        <?php echo $index + 1; ?>
                    </span>
                    <?php echo htmlspecialchars($step['title']); ?>
                </h3>
                <p style="color: #6b7280; margin: 0 0 1rem 0; padding-left: 3rem;">
                    <?php echo htmlspecialchars($step['description']); ?>
                </p>
                <div style="padding-left: 3rem;">
                    <div class="code-block" data-language="bash">
                        <?php echo htmlspecialchars($step['code']); ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </section>
        
        <!-- Featured Endpoints -->
        <section id="featured-endpoints">
            <h2 class="section-title">Popular Endpoints</h2>
            <p style="color: #6b7280; margin: 0 0 2rem 0;">
                Most commonly used API endpoints to get you started
            </p>
            
            <?php foreach ($featured_endpoints as $endpoint): ?>
            <div class="endpoint-card">
                <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                    <span class="method-badge method-<?php echo strtolower($endpoint['method']); ?>">
                        <?php echo $endpoint['method']; ?>
                    </span>
                    <code style="font-family: 'Monaco', 'Menlo', monospace; color: #1f2937; font-size: 0.875rem;">
                        <?php echo htmlspecialchars($endpoint['endpoint']); ?>
                    </code>
                </div>
                <h4 style="margin: 0 0 0.5rem 0; color: #1f2937; font-weight: 600;">
                    <?php echo htmlspecialchars($endpoint['name']); ?>
                </h4>
                <p style="color: #6b7280; margin: 0 0 1rem 0;">
                    <?php echo htmlspecialchars($endpoint['description']); ?>
                </p>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.875rem; color: #9ca3af;">
                        <?php echo htmlspecialchars($endpoint['category']); ?>
                    </span>
                    <a href="/developers/endpoint<?php echo $endpoint['endpoint']; ?>" style="color: #3b82f6; text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                        View Documentation <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </section>
        
        <!-- Code Examples -->
        <section id="code-examples">
            <h2 class="section-title">Code Examples</h2>
            <p style="color: #6b7280; margin: 0 0 2rem 0;">
                Ready-to-use code examples in popular programming languages
            </p>
            
            <div class="code-tabs">
                <?php $first = true; foreach ($code_examples as $lang => $example): ?>
                <button class="code-tab <?php echo $first ? 'active' : ''; ?>" data-tab="<?php echo $lang; ?>">
                    <?php echo $example['name']; ?>
                </button>
                <?php $first = false; endforeach; ?>
            </div>
            
            <?php $first = true; foreach ($code_examples as $lang => $example): ?>
            <div class="code-content <?php echo $first ? 'active' : ''; ?>" data-content="<?php echo $lang; ?>">
                <div class="code-block" data-language="<?php echo $lang; ?>">
                    <?php echo htmlspecialchars($example['code']); ?>
                </div>
            </div>
            <?php $first = false; endforeach; ?>
        </section>
        
        <!-- SDKs -->
        <section id="sdks">
            <h2 class="section-title">Official SDKs</h2>
            <p style="color: #6b7280; margin: 0 0 2rem 0;">
                Use our official SDKs to integrate faster with your preferred programming language
            </p>
            
            <div class="sdk-grid">
                <?php foreach ($sdk_info as $sdk): ?>
                <div class="sdk-card">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div style="width: 48px; height: 48px; background: #f3f4f6; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fab fa-<?php echo $sdk['language']; ?>" style="font-size: 1.5rem; color: #6b7280;"></i>
                        </div>
                        <div>
                            <h4 style="margin: 0; color: #1f2937; font-weight: 600;">
                                <?php echo htmlspecialchars($sdk['name']); ?>
                            </h4>
                            <p style="margin: 0; font-size: 0.875rem; color: #9ca3af;">
                                v<?php echo $sdk['version']; ?>
                            </p>
                        </div>
                    </div>
                    <p style="color: #6b7280; margin: 0 0 1.5rem 0; line-height: 1.6;">
                        <?php echo htmlspecialchars($sdk['description']); ?>
                    </p>
                    <div class="code-block" data-language="bash" style="margin-bottom: 1rem;">
                        <?php echo htmlspecialchars($sdk['install']); ?>
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <a href="/developers/sdk/<?php echo $sdk['language']; ?>" style="color: #3b82f6; text-decoration: none; font-size: 0.875rem; font-weight: 500;">
                            Documentation
                        </a>
                        <a href="<?php echo $sdk['github']; ?>" style="color: #6b7280; text-decoration: none; font-size: 0.875rem;">
                            <i class="fab fa-github me-1"></i> GitHub
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</div>

<script>
// Code tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.code-tab');
    const contents = document.querySelectorAll('.code-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const target = this.dataset.tab;
            
            // Remove active class from all tabs and contents
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            document.querySelector(`[data-content="${target}"]`).classList.add('active');
        });
    });
    
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
});

// Hide code content by default, show active
document.addEventListener('DOMContentLoaded', function() {
    const contents = document.querySelectorAll('.code-content');
    contents.forEach(content => {
        if (!content.classList.contains('active')) {
            content.style.display = 'none';
        }
    });
    
    // Update tab click handler to show/hide content
    const tabs = document.querySelectorAll('.code-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const target = this.dataset.tab;
            
            // Hide all contents
            contents.forEach(c => c.style.display = 'none');
            
            // Show target content
            const targetContent = document.querySelector(`[data-content="${target}"]`);
            if (targetContent) {
                targetContent.style.display = 'block';
            }
        });
    });
});
</script>

<?php require_once dirname(__DIR__, 3) . '/themes/default/views/partials/footer.php'; ?>
