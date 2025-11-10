<?php
/**
 * ProCalculator Features Page
 * Premium $100K Quality Features Showcase
 */

$user = $user ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Features - ProCalculator Premium Platform</title>
    <meta name="description" content="Discover premium features of ProCalculator: $100K quality calculations, glassmorphism design, and professional tools.">
    
    <!-- ProCalculator Premium CSS -->
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('css/procalculator-premium.css') ?>">
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('css/components.css') ?>">
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('css/animations.css') ?>">
    <link rel="stylesheet" href="<?= $viewHelper->themeUrl('css/responsive.css') ?>">
    
    <!-- Premium Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="procalculator-features">
    <!-- Navigation Header -->
    <?php $viewHelper->partial('partials/header', compact('user')); ?>
    
    <!-- Page Hero -->
    <section class="page-hero" id="main-content">
        <div class="container">
            <div class="hero-content">
                <nav class="breadcrumb">
                    <a href="/">Home</a>
                    <i class="bi bi-chevron-right"></i>
                    <span>Features</span>
                </nav>
                
                <h1 class="page-title">
                    <span class="title-line">Premium</span>
                    <span class="title-line gradient-text">Features</span>
                </h1>
                
                <p class="page-description">
                    Explore the comprehensive features that make ProCalculator the preferred choice 
                    for engineering professionals worldwide.
                </p>
            </div>
        </div>
    </section>
    
    <!-- Core Features -->
    <section class="core-features">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Core Features</h2>
                <p class="section-subtitle">Essential tools for professional engineering</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-showcase">
                    <div class="feature-icon">
                        <i class="bi bi-calculator"></i>
                    </div>
                    <h3 class="feature-title">Professional Calculators</h3>
                    <p class="feature-description">
                        Over 50 professional-grade calculators covering civil, electrical, 
                        mechanical, and structural engineering disciplines.
                    </p>
                    <ul class="feature-list">
                        <li>Civil Engineering Tools</li>
                        <li>Electrical Load Calculations</li>
                        <li>Structural Analysis</li>
                        <li>HVAC Design Tools</li>
                        <li>Plumbing Systems</li>
                    </ul>
                </div>
                
                <div class="feature-showcase">
                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h3 class="feature-title">Accuracy & Standards</h3>
                    <p class="feature-description">
                        All calculations based on industry standards (ACI, AISC, NEC, etc.) 
                        with professional validation and verification.
                    </p>
                    <ul class="feature-list">
                        <li>Industry Standard Formulas</li>
                        <li>Professional Validation</li>
                        <li>Regulatory Compliance</li>
                        <li>Quality Assurance</li>
                        <li>Error Checking</li>
                    </ul>
                </div>
                
                <div class="feature-showcase">
                    <div class="feature-icon">
                        <i class="bi bi-cloud-arrow-up"></i>
                    </div>
                    <h3 class="feature-title">Cloud Integration</h3>
                    <p class="feature-description">
                        Access your calculations from anywhere with secure cloud storage 
                        and real-time synchronization across devices.
                    </p>
                    <ul class="feature-list">
                        <li>Cloud Storage</li>
                        <li>Real-time Sync</li>
                        <li>Cross-device Access</li>
                        <li>Backup & Recovery</li>
                        <li>Version Control</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Premium Features -->
    <section class="premium-features">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Premium Features</h2>
                <p class="section-subtitle">Advanced capabilities for professional teams</p>
            </div>
            
            <div class="premium-grid">
                <div class="premium-feature">
                    <div class="feature-header">
                        <i class="bi bi-people"></i>
                        <h3>Team Collaboration</h3>
                        <span class="premium-badge">Pro</span>
                    </div>
                    <p>Work together on calculations, share projects, and manage team workflows efficiently.</p>
                    <ul>
                        <li>Shared Workspaces</li>
                        <li>Team Permissions</li>
                        <li>Real-time Collaboration</li>
                        <li>Project Management</li>
                    </ul>
                </div>
                
                <div class="premium-feature">
                    <div class="feature-header">
                        <i class="bi bi-file-earmark-pdf"></i>
                        <h3>Advanced Reporting</h3>
                        <span class="premium-badge">Pro</span>
                    </div>
                    <p>Generate comprehensive reports and documentation with professional formatting.</p>
                    <ul>
                        <li>PDF Export</li>
                        <li>Custom Templates</li>
                        <li>Professional Layouts</li>
                        <li>Branding Options</li>
                    </ul>
                </div>
                
                <div class="premium-feature">
                    <div class="feature-header">
                        <i class="bi bi-graph-up"></i>
                        <h3>Analytics Dashboard</h3>
                        <span class="premium-badge">Pro</span>
                    </div>
                    <p>Track calculation usage, team productivity, and project performance metrics.</p>
                    <ul>
                        <li>Usage Analytics</li>
                        <li>Performance Metrics</li>
                        <li>Team Insights</li>
                        <li>Custom Reports</li>
                    </ul>
                </div>
                
                <div class="premium-feature">
                    <div class="feature-header">
                        <i class="bi bi-lock"></i>
                        <h3>Enterprise Security</h3>
                        <span class="premium-badge">Enterprise</span>
                    </div>
                    <p>Advanced security features for enterprise and government applications.</p>
                    <ul>
                        <li>SSO Integration</li>
                        <li>Advanced Encryption</li>
                        <li>Audit Logs</li>
                        <li>Compliance Tools</li>
                    </ul>
                </div>
                
                <div class="premium-feature">
                    <div class="feature-header">
                        <i class="bi bi-code-slash"></i>
                        <h3>API Access</h3>
                        <span class="premium-badge">Enterprise</span>
                    </div>
                    <p>Integrate ProCalculator with your existing systems and workflows.</p>
                    <ul>
                        <li>RESTful API</li>
                        <li>Webhooks</li>
                        <li>Custom Integrations</li>
                        <li>Developer Tools</li>
                    </ul>
                </div>
                
                <div class="premium-feature">
                    <div class="feature-header">
                        <i class="bi bi-headset"></i>
                        <h3>Priority Support</h3>
                        <span class="premium-badge">Enterprise</span>
                    </div>
                    <p>Dedicated support team with direct access to our engineering experts.</p>
                    <ul>
                        <li>24/7 Support</li>
                        <li>Dedicated Manager</li>
                        <li>Training Sessions</li>
                        <li>Custom Development</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Technical Specifications -->
    <section class="tech-specs">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Technical Specifications</h2>
                <p class="section-subtitle">Built for reliability and performance</p>
            </div>
            
            <div class="specs-grid">
                <div class="spec-category">
                    <h3>Calculation Engine</h3>
                    <ul>
                        <li>IEEE 754 Double Precision</li>
                        <li>Multiple Precision Arithmetic</li>
                        <li>Error Handling & Validation</li>
                        <li>Unit Conversion Support</li>
                        <li>Custom Formula Builder</li>
                    </ul>
                </div>
                
                <div class="spec-category">
                    <h3>Data Management</h3>
                    <ul>
                        <li>Secure Cloud Storage</li>
                        <li>Automated Backups</li>
                        <li>Data Encryption (AES-256)</li>
                        <li>GDPR Compliance</li>
                        <li>Data Export Options</li>
                    </ul>
                </div>
                
                <div class="spec-category">
                    <h3>Integration</h3>
                    <ul>
                        <li>RESTful API</li>
                        <li>Webhook Support</li>
                        <li>SSO Integration</li>
                        <li>Database Connectors</li>
                        <li>File Import/Export</li>
                    </ul>
                </div>
                
                <div class="spec-category">
                    <h3>Performance</h3>
                    <ul>
                        <li>99.9% Uptime SLA</li>
                        <li>Global CDN</li>
                        <li>Auto-scaling</li>
                        <li>Real-time Processing</li>
                        <li>Mobile Optimization</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="features-cta">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Experience Premium Features?</h2>
                <p>Start your professional journey with ProCalculator today.</p>
                <div class="cta-actions">
                    <?php if (!$user): ?>
                    <a href="/register" class="btn btn-primary btn-lg">
                        <i class="bi bi-person-plus"></i>
                        Get Started Free
                    </a>
                    <a href="/pricing" class="btn btn-outline btn-lg">
                        <i class="bi bi-currency-dollar"></i>
                        View Pricing
                    </a>
                    <?php else: ?>
                    <a href="/dashboard" class="btn btn-primary btn-lg">
                        <i class="bi bi-speedometer2"></i>
                        Go to Dashboard
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <?php $this->partial('partials/footer'); ?>
    
    <!-- ProCalculator Core JavaScript -->
    <script src="<?= $this->themeUrl('assets/js/procalculator-core.js') ?>"></script>
</body>
</html>
