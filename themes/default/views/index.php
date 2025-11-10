<?php
/**
 * Premium Home Page Template - Theme Default
 * Fixed version with proper CSS classes and structure
 */
?>

<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <div class="hero-content text-center">
            <div class="premium-badge">
                ✨ Professional Engineering Solutions
            </div>
            <h1 class="hero-title">Bishwo Calculator</h1>
            <p class="hero-subtitle">Advanced Engineering Calculations & Design Tools for Modern Professionals</p>
            <div class="hero-buttons">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="/login" class="btn btn-primary">🔐 Login to Dashboard</a>
                    <a href="/register" class="btn btn-outline">🚀 Get Started Free</a>
                <?php else: ?>
                    <a href="/dashboard" class="btn btn-primary">🏠 Dashboard</a>
                    <a href="/calculators" class="btn btn-outline">🛠️ Browse Tools</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Engineering Categories Section -->
<div class="categories-section">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title">Engineering Calculation Suites</h2>
            <p class="section-subtitle">Comprehensive tools for civil, electrical, structural, HVAC, plumbing, and estimation engineering</p>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="category-card">
                    <div class="category-icon">🏗️</div>
                    <h3 class="category-title">Civil Engineering</h3>
                    <p class="category-description">Structural analysis, concrete design, masonry calculations, and construction planning tools</p>
                    <div class="category-badge">15 Professional Tools</div>
                    <a href="/modules/civil/" class="btn btn-primary mt-3">Explore Tools</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="category-card">
                    <div class="category-icon">⚡</div>
                    <h3 class="category-title">Electrical Engineering</h3>
                    <p class="category-description">Load calculations, circuit design, power distribution, and electrical safety analysis</p>
                    <div class="category-badge">12 Advanced Tools</div>
                    <a href="/modules/electrical/" class="btn btn-primary mt-3">Explore Tools</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="category-card">
                    <div class="category-icon">🏢</div>
                    <h3 class="category-title">Structural Engineering</h3>
                    <p class="category-description">Beam analysis, column design, foundation calculations, and structural integrity assessment</p>
                    <div class="category-badge">10 Specialized Tools</div>
                    <a href="/modules/structural/" class="btn btn-primary mt-3">Explore Tools</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="category-card">
                    <div class="category-icon">🌡️</div>
                    <h3 class="category-title">HVAC Systems</h3>
                    <p class="category-description">Heating load, cooling load, ventilation design, and energy efficiency calculations</p>
                    <div class="category-badge">8 Essential Tools</div>
                    <a href="/modules/hvac/" class="btn btn-primary mt-3">Explore Tools</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="category-card">
                    <div class="category-icon">🚰</div>
                    <h3 class="category-title">Plumbing Design</h3>
                    <p class="category-description">Pipe sizing, drainage calculations, water supply design, and hydraulic analysis</p>
                    <div class="category-badge">6 Core Tools</div>
                    <a href="/modules/plumbing/" class="btn btn-primary mt-3">Explore Tools</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="category-card">
                    <div class="category-icon">💰</div>
                    <h3 class="category-title">Project Estimation</h3>
                    <p class="category-description">Cost estimation, material takeoffs, project budgeting, and financial analysis</p>
                    <div class="category-badge">5 Smart Tools</div>
                    <a href="/modules/estimation/" class="btn btn-primary mt-3">Explore Tools</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-number" id="stat-tools">56+</div>
                    <div class="stat-label">Professional Tools</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-number" id="stat-accuracy">99.9%</div>
                    <div class="stat-label">Accuracy Rate</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-number" id="stat-availability">24/7</div>
                    <div class="stat-label">Availability</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-number" id="stat-free">100%</div>
                    <div class="stat-label">Free to Use</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Premium Footer Section -->
<div class="premium-footer">
    <div class="container">
        <div class="footer-content text-center">
            <h3 class="footer-title">Trusted by Engineering Professionals Worldwide</h3>
            <p class="footer-description">Bishwo Calculator provides accurate, reliable engineering calculations backed by industry standards and best practices. Our platform serves civil, electrical, structural, HVAC, plumbing, and estimation engineers globally.</p>
            <div class="footer-badges">
                <div class="footer-badge">✅ ISO Standards Compliant</div>
                <div class="footer-badge">🗄️ Secure Database</div>
                <div class="footer-badge">🌐 Real-time Calculations</div>
                <div class="footer-badge">📱 Mobile Optimized</div>
            </div>
        </div>
    </div>
</div>
