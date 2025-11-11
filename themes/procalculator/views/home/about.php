<?php
/**
 * ProCalculator About Page
 */
$user = $user ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - ProCalculator</title>
    
    <!-- ProCalculator Premium CSS -->
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('assets/css/procalculator-premium.css') ?>">
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('assets/css/header-footer.css') ?>">
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('assets/css/components.css') ?>">
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('assets/css/animations.css') ?>">
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('assets/css/responsive.css') ?>">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="procalculator-about">
    <!-- Header -->
    <?php $viewHelper->partial('partials/header', compact('user')); ?>

<div class="container-fluid px-0">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-75">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-white mb-4">
                        About <span class="text-gradient">ProCalculator</span>
                    </h1>
                    <p class="lead text-white-50 mb-4">
                        Revolutionizing engineering calculations with cutting-edge technology, 
                        precision tools, and comprehensive solutions for modern professionals.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#features" class="btn btn-primary btn-lg">Explore Features</a>
                        <a href="/contact" class="btn btn-outline-light btn-lg">Get in Touch</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-animation">
                        <div class="calculator-icon">
                            <i class="fas fa-calculator text-gradient" style="font-size: 8rem; opacity: 0.8;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Story Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="section-title">Our Story</h2>
                    <p class="section-subtitle">Built by engineers, for engineers</p>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4">
                    <div class="feature-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 4rem; height: 4rem;">
                        <i class="fas fa-lightbulb fa-2x"></i>
                    </div>
                    <h3 class="mt-4">The Vision</h3>
                    <p class="text-muted">
                        ProCalculator was born from the frustration of working with outdated calculation tools. 
                        Our founders, experienced engineers themselves, recognized the need for a modern, 
                        comprehensive platform that could handle the complex calculations required in today's 
                        engineering projects.
                    </p>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="feature-icon bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 4rem; height: 4rem;">
                        <i class="fas fa-cogs fa-2x"></i>
                    </div>
                    <h3 class="mt-4">The Innovation</h3>
                    <p class="text-muted">
                        We combined advanced algorithms, intuitive design, and real-time collaboration 
                        features to create a platform that not only calculates but also educates and 
                        streamlines workflows. Every feature is crafted with the professional engineer in mind.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="section-title">Our Core Values</h2>
                    <p class="section-subtitle">The principles that drive everything we do</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 3.5rem; height: 3.5rem;">
                                <i class="fas fa-bullseye fa-2x"></i>
                            </div>
                            <h4>Precision</h4>
                            <p class="text-muted">
                                Every calculation is verified through multiple algorithms and testing methodologies 
                                to ensure the highest level of accuracy in the industry.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 3.5rem; height: 3.5rem;">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                            <h4>Collaboration</h4>
                            <p class="text-muted">
                                Engineering is a team sport. Our platform enables seamless collaboration 
                                and knowledge sharing among professionals worldwide.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 3.5rem; height: 3.5rem;">
                                <i class="fas fa-rocket fa-2x"></i>
                            </div>
                            <h4>Innovation</h4>
                            <p class="text-muted">
                                We continuously push the boundaries of what's possible in calculation software, 
                                integrating the latest technologies and methodologies.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="section-title">ProCalculator by the Numbers</h2>
                </div>
            </div>
            <div class="row g-4 text-center">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-number text-gradient display-4 fw-bold">50K+</div>
                        <div class="stat-label text-muted">Active Engineers</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-number text-gradient display-4 fw-bold">1M+</div>
                        <div class="stat-label text-muted">Calculations Completed</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-number text-gradient display-4 fw-bold">150+</div>
                        <div class="stat-label text-muted">Calculation Modules</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-number text-gradient display-4 fw-bold">99.9%</div>
                        <div class="stat-label text-muted">Uptime Reliability</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="section-title">Meet Our Team</h2>
                    <p class="section-subtitle">The minds behind ProCalculator</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="team-avatar bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 5rem; height: 5rem; font-size: 1.5rem;">
                                <i class="fas fa-user"></i>
                            </div>
                            <h4>Dr. Sarah Chen</h4>
                            <p class="text-muted mb-3">Chief Technology Officer</p>
                            <p class="text-muted">
                                15+ years in computational engineering with a PhD from MIT. 
                                Leading our innovation in calculation algorithms.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="team-avatar bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 5rem; height: 5rem; font-size: 1.5rem;">
                                <i class="fas fa-user"></i>
                            </div>
                            <h4>Michael Rodriguez</h4>
                            <p class="text-muted mb-3">Lead Software Architect</p>
                            <p class="text-muted">
                                Former Google engineer with expertise in scalable systems. 
                                Designing the architecture of tomorrow.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="team-avatar bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 5rem; height: 5rem; font-size: 1.5rem;">
                                <i class="fas fa-user"></i>
                            </div>
                            <h4>Emma Thompson</h4>
                            <p class="text-muted mb-3">Head of Product Design</p>
                            <p class="text-muted">
                                Award-winning UX designer with a background in mechanical engineering. 
                                Making complex calculations beautifully simple.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h2 class="text-white mb-4">Ready to Transform Your Calculations?</h2>
                    <p class="text-white-50 mb-4 lead">
                        Join thousands of engineers who trust ProCalculator for their most critical calculations.
                    </p>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="/pricing" class="btn btn-primary btn-lg">Get Started Today</a>
                        <a href="/contact" class="btn btn-outline-light btn-lg">Schedule a Demo</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Footer -->
<?php $viewHelper->partial('partials/footer'); ?>

<!-- ProCalculator Core JavaScript -->
<script src="/assets/themes/procalculator/js/procalculator-core.js"></script>
</body>
</html>
