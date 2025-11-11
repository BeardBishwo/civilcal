 
<?php
/**
 * ProCalculator Pricing Page
 */
$user = $user ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing - ProCalculator</title>
    
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
<body class="procalculator-pricing">
    <!-- Header -->
    <?php $viewHelper->partial('partials/header', compact('user')); ?>

<div class="container-fluid px-0">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-75">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4 fw-bold text-white mb-4">
                        Choose Your <span class="text-gradient">Perfect Plan</span>
                    </h1>
                    <p class="lead text-white-50 mb-4">
                        Flexible pricing designed for individuals, teams, and enterprises. 
                        Start free and scale as you grow.
                    </p>
                    <div class="d-flex gap-3 justify-content-center mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="billingToggle" onchange="toggleBilling()">
                            <label class="form-check-label text-white" for="billingToggle">
                                Annual Billing (Save 20%)
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Plans -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <!-- Starter Plan -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm pricing-card">
                        <div class="card-body p-5 text-center">
                            <div class="plan-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 4rem; height: 4rem;">
                                <i class="fas fa-rocket fa-2x"></i>
                            </div>
                            <h3 class="plan-title">Starter</h3>
                            <p class="text-muted mb-4">Perfect for individual engineers and small projects</p>
                            <div class="price-display mb-4">
                                <span class="price-monthly text-primary display-4 fw-bold">$29</span>
                                <span class="price-annual text-primary display-4 fw-bold" style="display: none;">$23</span>
                                <span class="text-muted">/month</span>
                            </div>
                            <ul class="plan-features list-unstyled text-start mb-4">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>25 Calculation Modules</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Basic Project Management</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Standard Support</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Export to PDF</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>5 Project Collaborators</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Cloud Storage (10GB)</li>
                            </ul>
                            <button class="btn btn-outline-primary btn-lg w-100">Start Free Trial</button>
                        </div>
                    </div>
                </div>

                <!-- Professional Plan -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm pricing-card position-relative popular">
                        <div class="position-absolute top-0 start-50 translate-middle">
                            <span class="badge bg-primary px-4 py-2">Most Popular</span>
                        </div>
                        <div class="card-body p-5 text-center">
                            <div class="plan-icon bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 4rem; height: 4rem;">
                                <i class="fas fa-star fa-2x"></i>
                            </div>
                            <h3 class="plan-title">Professional</h3>
                            <p class="text-muted mb-4">Ideal for engineering teams and medium businesses</p>
                            <div class="price-display mb-4">
                                <span class="price-monthly text-success display-4 fw-bold">$79</span>
                                <span class="price-annual text-success display-4 fw-bold" style="display: none;">$63</span>
                                <span class="text-muted">/month</span>
                            </div>
                            <ul class="plan-features list-unstyled text-start mb-4">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>All Calculation Modules (150+)</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Advanced Project Management</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Priority Support</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Export to Multiple Formats</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Unlimited Collaborators</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Cloud Storage (100GB)</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>API Access</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Team Dashboard</li>
                            </ul>
                            <button class="btn btn-success btn-lg w-100">Start Free Trial</button>
                        </div>
                    </div>
                </div>

                <!-- Enterprise Plan -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm pricing-card">
                        <div class="card-body p-5 text-center">
                            <div class="plan-icon bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 4rem; height: 4rem;">
                                <i class="fas fa-building fa-2x"></i>
                            </div>
                            <h3 class="plan-title">Enterprise</h3>
                            <p class="text-muted mb-4">Custom solutions for large organizations</p>
                            <div class="price-display mb-4">
                                <span class="price-monthly text-warning display-4 fw-bold">Custom</span>
                                <span class="text-muted">Contact Sales</span>
                            </div>
                            <ul class="plan-features list-unstyled text-start mb-4">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Everything in Professional</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Custom Modules & Integrations</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Dedicated Account Manager</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>24/7 Phone Support</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Advanced Security & Compliance</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Unlimited Cloud Storage</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>On-Premise Deployment</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Custom Training & Onboarding</li>
                            </ul>
                            <button class="btn btn-outline-warning btn-lg w-100">Contact Sales</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Comparison -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="section-title">Compare All Features</h2>
                    <p class="section-subtitle">Detailed comparison of what's included in each plan</p>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th class="border-0">Features</th>
                                            <th class="text-center border-0">Starter</th>
                                            <th class="text-center border-0">Professional</th>
                                            <th class="text-center border-0">Enterprise</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Calculation Modules</td>
                                            <td class="text-center">25</td>
                                            <td class="text-center">150+</td>
                                            <td class="text-center">Unlimited + Custom</td>
                                        </tr>
                                        <tr>
                                            <td>Project Collaborators</td>
                                            <td class="text-center">5</td>
                                            <td class="text-center">Unlimited</td>
                                            <td class="text-center">Unlimited</td>
                                        </tr>
                                        <tr>
                                            <td>Cloud Storage</td>
                                            <td class="text-center">10GB</td>
                                            <td class="text-center">100GB</td>
                                            <td class="text-center">Unlimited</td>
                                        </tr>
                                        <tr>
                                            <td>API Access</td>
                                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        </tr>
                                        <tr>
                                            <td>Export Formats</td>
                                            <td class="text-center">PDF</td>
                                            <td class="text-center">PDF, Excel, CAD</td>
                                            <td class="text-center">All Formats + Custom</td>
                                        </tr>
                                        <tr>
                                            <td>Support Level</td>
                                            <td class="text-center">Email</td>
                                            <td class="text-center">Priority</td>
                                            <td class="text-center">24/7 Dedicated</td>
                                        </tr>
                                        <tr>
                                            <td>Security & Compliance</td>
                                            <td class="text-center">Standard</td>
                                            <td class="text-center">Enhanced</td>
                                            <td class="text-center">Enterprise Grade</td>
                                        </tr>
                                        <tr>
                                            <td>On-Premise Deployment</td>
                                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                            <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                            <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="section-title">What Our Customers Say</h2>
                    <p class="section-subtitle">Trusted by engineering professionals worldwide</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="testimonial-avatar bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 3rem; height: 3rem;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Sarah Mitchell</h6>
                                    <small class="text-muted">Structural Engineer</small>
                                </div>
                            </div>
                            <div class="text-warning mb-3">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="text-muted">"ProCalculator has revolutionized our design process. The accuracy and speed are unmatched. Our project delivery time has improved by 40%."</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="testimonial-avatar bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 3rem; height: 3rem;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Michael Chen</h6>
                                    <small class="text-muted">MEP Consultant</small>
                                </div>
                            </div>
                            <div class="text-warning mb-3">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="text-muted">"The collaboration features are incredible. Our distributed team can work on complex projects seamlessly. Best investment we've made."</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="testimonial-avatar bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 3rem; height: 3rem;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Dr. Emily Rodriguez</h6>
                                    <small class="text-muted">Civil Engineering Director</small>
                                </div>
                            </div>
                            <div class="text-warning mb-3">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="text-muted">"Enterprise features like API access and custom modules have transformed our workflow. The ROI has been extraordinary."</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="section-title">Frequently Asked Questions</h2>
                    <p class="section-subtitle">Everything you need to know about our pricing</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="accordion" id="pricingFAQ">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="pricing1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#pricingCollapse1" aria-expanded="true" aria-controls="pricingCollapse1">
                                    Can I change my plan at any time?
                                </button>
                            </h2>
                            <div id="pricingCollapse1" class="accordion-collapse collapse show" aria-labelledby="pricing1" data-bs-parent="#pricingFAQ">
                                <div class="accordion-body">
                                    Yes! You can upgrade or downgrade your plan at any time. Changes take effect immediately, and we'll prorate the billing accordingly.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="pricing2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pricingCollapse2" aria-expanded="false" aria-controls="pricingCollapse2">
                                    Is there a free trial?
                                </button>
                            </h2>
                            <div id="pricingCollapse2" class="accordion-collapse collapse" aria-labelledby="pricing2" data-bs-parent="#pricingFAQ">
                                <div class="accordion-body">
                                    Yes! We offer a 14-day free trial for all plans. No credit card required to get started. You can explore all features risk-free.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="pricing3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pricingCollapse3" aria-expanded="false" aria-controls="pricingCollapse3">
                                    What payment methods do you accept?
                                </button>
                            </h2>
                            <div id="pricingCollapse3" class="accordion-collapse collapse" aria-labelledby="pricing3" data-bs-parent="#pricingFAQ">
                                <div class="accordion-body">
                                    We accept all major credit cards, PayPal, and bank transfers for enterprise accounts. Payments are processed securely through Stripe.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="pricing4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pricingCollapse4" aria-expanded="false" aria-controls="pricingCollapse4">
                                    Do you offer educational discounts?
                                </button>
                            </h2>
                            <div id="pricingCollapse4" class="accordion-collapse collapse" aria-labelledby="pricing4" data-bs-parent="#pricingFAQ">
                                <div class="accordion-body">
                                    Yes! We offer special pricing for students, educators, and academic institutions. Contact our sales team to learn about our educational programs.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="pricing5">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pricingCollapse5" aria-expanded="false" aria-controls="pricingCollapse5">
                                    What happens to my data if I cancel?
                                </button>
                            </h2>
                            <div id="pricingCollapse5" class="accordion-collapse collapse" aria-labelledby="pricing5" data-bs-parent="#pricingFAQ">
                                <div class="accordion-body">
                                    Your data remains accessible for 30 days after cancellation. You can export all your projects and calculations before your account is permanently closed.
                                </div>
                            </div>
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
                    <h2 class="text-white mb-4">Ready to Get Started?</h2>
                    <p class="text-white-50 mb-4 lead">
                        Join thousands of engineers who trust ProCalculator for their most critical calculations.
                    </p>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="#" class="btn btn-primary btn-lg">Start Free Trial</a>
                        <a href="/contact" class="btn btn-outline-light btn-lg">Schedule Demo</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function toggleBilling() {
    const toggle = document.getElementById('billingToggle');
    const monthlyPrices = document.querySelectorAll('.price-monthly');
    const annualPrices = document.querySelectorAll('.price-annual');
    
    if (toggle.checked) {
        monthlyPrices.forEach(price => price.style.display = 'none');
        annualPrices.forEach(price => price.style.display = 'inline');
    } else {
        monthlyPrices.forEach(price => price.style.display = 'inline');
        annualPrices.forEach(price => price.style.display = 'none');
    }
}

// Initialize pricing toggle
document.addEventListener('DOMContentLoaded', function() {
    // Set initial state based on toggle (default to monthly)
    const toggle = document.getElementById('billingToggle');
    if (toggle) {
        toggle.checked = false;
        toggleBilling();
    }
});
</script>

</div>

<!-- Footer -->
<?php $viewHelper->partial('partials/footer'); ?>

<!-- ProCalculator Core JavaScript -->
<script src="<?= $viewHelper->themeUrl('assets/js/procalculator-core.js') ?>"></script>
</body>
</html>