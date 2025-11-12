<?php
/**
 * ProCalculator Premium Home Page
 * $100K Quality Homepage with Glassmorphism Design
 */

$user = $user ?? null;
$isNepal = $isNepal ?? false;
$stats = $stats ?? [];
$featuredCalculators = $featuredCalculators ?? [];
$testimonials = $testimonials ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProCalculator - Premium Engineering Calculator Platform</title>
    <meta name="description" content="Professional engineering calculators with $100K premium quality. Advanced glassmorphism design with comprehensive calculation tools.">
    
    <!-- Immediate Dark Mode Script - Prevents Flash of Light Mode -->
    <script>
        (function() {
            const darkMode = localStorage.getItem('darkMode');
            const isDark = darkMode === null ? true : darkMode === 'true';
            if (isDark) {
                document.documentElement.classList.add('dark-mode');
            }
            console.log('🌙 Pre-load dark mode:', isDark);
        })();
    </script>
    
    <!-- ProCalculator Premium CSS -->
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('assets/css/procalculator-premium.css') ?>">
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('assets/css/header-footer.css') ?>">
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('assets/css/components.css') ?>">
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('assets/css/animations.css') ?>">
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('assets/css/responsive.css') ?>">
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('assets/css/dark-theme.css') ?>">

    
    <!-- Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="procalculator-home">
    <!-- Navigation Header -->
    <?php $viewHelper->partial('partials/header', compact('user')); ?>

    
    <!-- Hero Section -->
    <section class="hero-section" id="main-content">
        <div class="hero-background">
            <div class="floating-shapes">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
                <div class="shape shape-3"></div>
                <div class="shape shape-4"></div>
            </div>
        </div>
        
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="bi bi-stars"></i>
                    <span>Premium $100K Quality Platform</span>
                </div>
                
                <h1 class="hero-title">
                    <span class="title-line">Professional</span>
                    <span class="title-line gradient-text">Engineering</span>
                    <span class="title-line">Calculators</span>
                </h1>
                
                <p class="hero-description">
                    Advanced calculation platform with premium glassmorphism design, 
                    comprehensive engineering tools, and professional-grade features.
                </p>
                
                <div class="hero-actions">
                    <a href="<?= $viewHelper->url('calculators') ?>" class="btn btn-primary btn-lg">
                        <i class="bi bi-calculator"></i>
                        Start Calculating
                    </a>
                    <a href="<?= $viewHelper->url('features') ?>" class="btn btn-outline btn-lg">
                        <i class="bi bi-info-circle"></i>
                        Explore Features
                    </a>
                </div>
                
                <!-- Stats Display -->
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number"><?= number_format($stats['calculators'] ?? 56) ?>+</div>
                        <div class="stat-label">Calculators</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?= number_format($stats['users'] ?? 1234) ?>+</div>
                        <div class="stat-label">Professionals</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?= number_format($stats['calculations'] ?? 15420) ?>+</div>
                        <div class="stat-label">Calculations</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?= number_format($stats['countries'] ?? 25) ?>+</div>
                        <div class="stat-label">Countries</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Featured Calculators Section -->
    <section class="featured-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Featured Calculators</h2>
                <p class="section-subtitle">Professional tools trusted by engineers worldwide</p>
            </div>
            
            <div class="calculators-grid">
                <?php foreach ($featuredCalculators as $calculator): ?>
                <div class="calculator-card" data-category="<?= htmlspecialchars($calculator['category']) ?>">
                    <div class="card-icon">
                        <i class="bi bi-<?= htmlspecialchars($calculator['icon']) ?>"></i>
                    </div>
                    <div class="card-content">
                        <h3 class="card-title"><?= htmlspecialchars($calculator['name']) ?></h3>
                        <p class="card-description"><?= htmlspecialchars($calculator['description']) ?></p>
                        <a href="<?= $viewHelper->url('calculator/' . htmlspecialchars($calculator['category']) . '/' . htmlspecialchars($calculator['tool'])) ?>" 
                           class="card-link">
                            Use Calculator
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card-glow"></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- Premium Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Premium Features</h2>
                <p class="section-subtitle">Why professionals choose ProCalculator</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h3 class="feature-title">Professional Grade</h3>
                    <p class="feature-description">
                        $100K quality with industry-standard calculations and professional validation.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-palette"></i>
                    </div>
                    <h3 class="feature-title">Glassmorphism Design</h3>
                    <p class="feature-description">
                        Modern glassmorphism interface with premium animations and effects.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <h3 class="feature-title">Team Collaboration</h3>
                    <p class="feature-description">
                        Share calculations, collaborate with teams, and manage projects efficiently.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-cloud-arrow-up"></i>
                    </div>
                    <h3 class="feature-title">Cloud Sync</h3>
                    <p class="feature-description">
                        Access your calculations from anywhere with secure cloud synchronization.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </div>
                    <h3 class="feature-title">PDF Export</h3>
                    <p class="feature-description">
                        Generate professional reports and documentation in PDF format.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-lock"></i>
                    </div>
                    <h3 class="feature-title">Advanced Security</h3>
                    <p class="feature-description">
                        Enterprise-level security with encryption and secure data handling.
                    </p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">What Professionals Say</h2>
                <p class="section-subtitle">Trusted by engineers and construction professionals</p>
            </div>
            
            <div class="testimonials-slider">
                <?php foreach ($testimonials as $testimonial): ?>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <div class="testimonial-rating">
                            <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                                <i class="bi bi-star-fill"></i>
                            <?php endfor; ?>
                        </div>
                        <blockquote class="testimonial-text">
                            "<?= htmlspecialchars($testimonial['content']) ?>"
                        </blockquote>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <img src="<?= htmlspecialchars($testimonial['avatar']) ?>" 
                                 alt="<?= htmlspecialchars($testimonial['name']) ?>">
                        </div>
                        <div class="author-info">
                            <h4 class="author-name"><?= htmlspecialchars($testimonial['name']) ?></h4>
                            <p class="author-role"><?= htmlspecialchars($testimonial['role']) ?></p>
                            <p class="author-company"><?= htmlspecialchars($testimonial['company']) ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Ready to Upgrade Your Calculations?</h2>
                <p class="cta-description">
                    Join thousands of professionals who trust ProCalculator for their engineering needs.
                </p>
                <div class="cta-actions">
                    <?php if (!$user): ?>
                    <a href="<?= $viewHelper->url('register') ?>" class="btn btn-primary btn-lg">
                        <i class="bi bi-person-plus"></i>
                        Create Account
                    </a>
                    <a href="<?= $viewHelper->url('login') ?>" class="btn btn-outline btn-lg">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Sign In
                    </a>
                    <?php else: ?>
                    <a href="<?= $viewHelper->url('dashboard') ?>" class="btn btn-primary btn-lg">
                        <i class="bi bi-speedometer2"></i>
                        Go to Dashboard
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <?php $viewHelper->partial('partials/footer'); ?>
    
    <script>
        // Debug theme application
        console.log('🎨 Current theme classes:', document.documentElement.className);
        console.log('🌙 Dark mode enabled:', document.documentElement.classList.contains('dark-mode'));
    </script>
    
    <!-- ProCalculator Core JavaScript loaded globally in footer -->

    
    <!-- Initialize Premium Features -->
    <script>
        ProCalculator.init({
            theme: 'premium',
            animations: true,
            glassmorphism: true,
            notifications: true
        });
    </script>
</body>
</html>
