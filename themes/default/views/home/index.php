<?php
/**
 * Premium Home Page Template - Theme Default
 * $10,000 Quality Design Integration
 * Used by HomeController@index
 */
?>

<!-- Hero Section - Premium Design -->
<div class="hero-section-premium">
    <div class="container">
        <div class="hero-content-premium text-center">
            <div class="premium-badge-premium">
                ✨ Professional Engineering Solutions
            </div>
            <h1 class="hero-title-premium">Bishwo Calculator</h1>
            <p class="hero-subtitle-premium">Advanced Engineering Calculations & Design Tools for Modern Professionals</p>
            <div class="premium-buttons-premium">
                <?php if (!isset($user) || !$user): ?>
                    <a href="/login" class="premium-btn btn-primary">🔐 Login to Dashboard</a>
                    <a href="/register" class="premium-btn btn-outline">🚀 Get Started Free</a>
                <?php else: ?>
                    <a href="/dashboard" class="premium-btn btn-primary">🏠 Dashboard</a>
                    <a href="/calculators" class="premium-btn btn-outline">🛠️ Browse Tools</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Engineering Categories Section -->
<div class="categories-section-premium">
    <div class="container">
        <div class="section-header-premium text-center">
            <h2 class="section-title-premium">Engineering Calculation Suites</h2>
            <p class="section-subtitle-premium">Comprehensive tools for civil, electrical, structural, HVAC, plumbing, and estimation engineering</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="premium-card-premium">
                    <div class="category-icon-premium">🏗️</div>
                    <h3 class="category-title-premium">Civil Engineering</h3>
                    <p class="category-description-premium">Structural analysis, concrete design, masonry calculations, and construction planning tools</p>
                    <div class="category-badge-premium">15 Professional Tools</div>
                    <a href="/calculator/civil" class="btn btn-primary mt-3">Explore Tools</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="premium-card-premium">
                    <div class="category-icon-premium">⚡</div>
                    <h3 class="category-title-premium">Electrical Engineering</h3>
                    <p class="category-description-premium">Load calculations, circuit design, power distribution, and electrical safety analysis</p>
                    <div class="category-badge-premium">12 Advanced Tools</div>
                    <a href="/calculator/electrical" class="btn btn-primary mt-3">Explore Tools</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="premium-card-premium">
                    <div class="category-icon-premium">🏢</div>
                    <h3 class="category-title-premium">Structural Engineering</h3>
                    <p class="category-description-premium">Beam analysis, column design, foundation calculations, and structural integrity assessment</p>
                    <div class="category-badge-premium">10 Specialized Tools</div>
                    <a href="/calculator/structural" class="btn btn-primary mt-3">Explore Tools</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="premium-card-premium">
                    <div class="category-icon-premium">🌡️</div>
                    <h3 class="category-title-premium">HVAC Systems</h3>
                    <p class="category-description-premium">Heating load, cooling load, ventilation design, and energy efficiency calculations</p>
                    <div class="category-badge-premium">8 Essential Tools</div>
                    <a href="/calculator/hvac" class="btn btn-primary mt-3">Explore Tools</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="premium-card-premium">
                    <div class="category-icon-premium">🚰</div>
                    <h3 class="category-title-premium">Plumbing Design</h3>
                    <p class="category-description-premium">Pipe sizing, drainage calculations, water supply design, and hydraulic analysis</p>
                    <div class="category-badge-premium">6 Core Tools</div>
                    <a href="/calculator/plumbing" class="btn btn-primary mt-3">Explore Tools</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="premium-card-premium">
                    <div class="category-icon-premium">💰</div>
                    <h3 class="category-title-premium">Project Estimation</h3>
                    <p class="category-description-premium">Cost estimation, material takeoffs, project budgeting, and financial analysis</p>
                    <div class="category-badge-premium">5 Smart Tools</div>
                    <a href="/calculator/estimation" class="btn btn-primary mt-3">Explore Tools</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="stats-section-premium">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="stat-card-premium">
                    <div class="stat-number-premium" id="stat-tools"><?= $stats['calculators'] ?? '56' ?>+</div>
                    <div class="stat-label-premium">Professional Tools</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card-premium">
                    <div class="stat-number-premium" id="stat-accuracy">99.9%</div>
                    <div class="stat-label-premium">Accuracy Rate</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card-premium">
                    <div class="stat-number-premium" id="stat-availability">24/7</div>
                    <div class="stat-label-premium">Availability</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card-premium">
                    <div class="stat-number-premium" id="stat-free">100%</div>
                    <div class="stat-label-premium">Free to Use</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Premium Footer Section -->
<div class="premium-footer-premium">
    <div class="container">
        <div class="footer-content-premium text-center">
            <h3 class="footer-title-premium">Trusted by Engineering Professionals Worldwide</h3>
            <p class="footer-description-premium">Bishwo Calculator provides accurate, reliable engineering calculations backed by industry standards and best practices. Our platform serves civil, electrical, structural, HVAC, plumbing, and estimation engineers globally.</p>
            <div class="footer-badges-premium">
                <div class="footer-badge-premium">✅ ISO Standards Compliant</div>
                <div class="footer-badge-premium">🗄️ Secure Database</div>
                <div class="footer-badge-premium">🌐 Real-time Calculations</div>
                <div class="footer-badge-premium">📱 Mobile Optimized</div>
            </div>
        </div>
    </div>
</div>

<script>
// Simple counter animation for stats (if needed)
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.stat-number-premium');
    
    counters.forEach(counter => {
        const target = counter.textContent;
        if (target.includes('+')) {
            // Animate numbers like "56+"
            const num = parseInt(target.replace('+', ''));
            let current = 0;
            const increment = num / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= num) {
                    counter.textContent = num + '+';
                    clearInterval(timer);
                } else {
                    counter.textContent = Math.floor(current) + '+';
                }
            }, 30);
        }
    });
});
</script>
