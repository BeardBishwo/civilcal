<?php
/**
 * ProCalculator Contact Page
 */
$user = $user ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - ProCalculator</title>
    
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
<body class="procalculator-contact">
    <!-- Header -->
    <?php $viewHelper->partial('partials/header', compact('user')); ?>

<div class="container-fluid px-0">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-75">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4 fw-bold text-white mb-4">
                        Get in <span class="text-gradient">Touch</span>
                    </h1>
                    <p class="lead text-white-50 mb-4">
                        Have questions about ProCalculator? Need support with your calculations? 
                        Our team of experts is here to help you succeed.
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="#contact-form" class="btn btn-primary btn-lg">Send us a Message</a>
                        <a href="mailto:support@procalculator.com" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-envelope me-2"></i>Email Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Options -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm text-center">
                        <div class="card-body p-4">
                            <div class="contact-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 4rem; height: 4rem;">
                                <i class="fas fa-phone fa-2x"></i>
                            </div>
                            <h5>Phone Support</h5>
                            <p class="text-muted mb-3">Speak directly with our technical experts</p>
                            <p class="fw-bold text-primary">+1 (555) 123-4567</p>
                            <small class="text-muted">Mon-Fri, 9AM-6PM EST</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm text-center">
                        <div class="card-body p-4">
                            <div class="contact-icon bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 4rem; height: 4rem;">
                                <i class="fas fa-envelope fa-2x"></i>
                            </div>
                            <h5>Email Support</h5>
                            <p class="text-muted mb-3">Get detailed help via email</p>
                            <p class="fw-bold text-success">support@procalculator.com</p>
                            <small class="text-muted">24-hour response time</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm text-center">
                        <div class="card-body p-4">
                            <div class="contact-icon bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 4rem; height: 4rem;">
                                <i class="fas fa-comments fa-2x"></i>
                            </div>
                            <h5>Live Chat</h5>
                            <p class="text-muted mb-3">Instant support when you need it</p>
                            <p class="fw-bold text-warning">Chat Now</p>
                            <small class="text-muted">Available 24/7</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm text-center">
                        <div class="card-body p-4">
                            <div class="contact-icon bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 4rem; height: 4rem;">
                                <i class="fas fa-calendar fa-2x"></i>
                            </div>
                            <h5>Schedule Demo</h5>
                            <p class="text-muted mb-3">Book a personalized demo session</p>
                            <p class="fw-bold text-info">Book Now</p>
                            <small class="text-muted">Free 30-min session</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section id="contact-form" class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="text-center mb-5">
                        <h2 class="section-title">Send us a Message</h2>
                        <p class="section-subtitle">Fill out the form below and we'll get back to you within 24 hours</p>
                    </div>
                    
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-5">
                            <form id="contactForm" class="needs-validation" novalidate>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="firstName" class="form-label">First Name *</label>
                                        <input type="text" class="form-control form-control-lg" id="firstName" name="firstName" required>
                                        <div class="invalid-feedback">
                                            Please provide your first name.
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lastName" class="form-label">Last Name *</label>
                                        <input type="text" class="form-control form-control-lg" id="lastName" name="lastName" required>
                                        <div class="invalid-feedback">
                                            Please provide your last name.
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email Address *</label>
                                        <input type="email" class="form-control form-control-lg" id="email" name="email" required>
                                        <div class="invalid-feedback">
                                            Please provide a valid email address.
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="company" class="form-label">Company/Organization</label>
                                        <input type="text" class="form-control form-control-lg" id="company" name="company">
                                    </div>
                                    <div class="col-12">
                                        <label for="subject" class="form-label">Subject *</label>
                                        <select class="form-select form-select-lg" id="subject" name="subject" required>
                                            <option value="">Select a subject...</option>
                                            <option value="general">General Inquiry</option>
                                            <option value="technical">Technical Support</option>
                                            <option value="sales">Sales & Pricing</option>
                                            <option value="partnership">Partnership</option>
                                            <option value="demo">Request Demo</option>
                                            <option value="bug">Bug Report</option>
                                            <option value="feature">Feature Request</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select a subject.
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="message" class="form-label">Message *</label>
                                        <textarea class="form-control form-control-lg" id="message" name="message" rows="5" placeholder="Please describe your question or how we can help you..." required></textarea>
                                        <div class="invalid-feedback">
                                            Please provide a message.
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter">
                                            <label class="form-check-label" for="newsletter">
                                                Subscribe to our newsletter for updates and engineering tips
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="privacy" name="privacy" required>
                                            <label class="form-check-label" for="privacy">
                                                I agree to the <a href="/privacy" target="_blank">Privacy Policy</a> and <a href="/terms" target="_blank">Terms of Service</a> *
                                            </label>
                                            <div class="invalid-feedback">
                                                You must agree before submitting.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-paper-plane me-2"></i>Send Message
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Office Locations -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="section-title">Our Global Offices</h2>
                    <p class="section-subtitle">Serving engineers worldwide from key locations</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="office-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 3rem; height: 3rem;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <h5>Headquarters - San Francisco</h5>
                            <p class="text-muted mb-2">123 Innovation Drive</p>
                            <p class="text-muted mb-2">San Francisco, CA 94105</p>
                            <p class="text-muted mb-2">United States</p>
                            <p class="fw-bold text-primary">+1 (555) 123-4567</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="office-icon bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 3rem; height: 3rem;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <h5>European Office - London</h5>
                            <p class="text-muted mb-2">456 Tech Square</p>
                            <p class="text-muted mb-2">London EC2A 4DP</p>
                            <p class="text-muted mb-2">United Kingdom</p>
                            <p class="fw-bold text-success">+44 20 7123 4567</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="office-icon bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 3rem; height: 3rem;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <h5>Asia-Pacific - Singapore</h5>
                            <p class="text-muted mb-2">789 Engineering Hub</p>
                            <p class="text-muted mb-2">Singapore 018956</p>
                            <p class="text-muted mb-2">Singapore</p>
                            <p class="fw-bold text-warning">+65 6123 4567</p>
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
                    <p class="section-subtitle">Quick answers to common questions</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                    How quickly can I get started with ProCalculator?
                                </button>
                            </h2>
                            <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="faq1" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    You can start using ProCalculator immediately after signing up. Our platform is cloud-based, so there's no software installation required. Most users are up and running within 5 minutes.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                    Is my data secure and backed up?
                                </button>
                            </h2>
                            <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="faq2" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Absolutely. We use enterprise-grade security with end-to-end encryption, regular automated backups, and compliance with industry standards like SOC 2 and ISO 27001.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                    Can I collaborate with my team members?
                                </button>
                            </h2>
                            <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="faq3" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes! ProCalculator includes real-time collaboration features, team workspaces, shared calculation templates, and role-based access controls to streamline your team's workflow.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq4">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                    What calculation modules are available?
                                </button>
                            </h2>
                            <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="faq4" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We offer 150+ calculation modules covering structural, electrical, mechanical, civil, HVAC, plumbing, and fire protection engineering. New modules are added regularly based on user feedback.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq5">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                    Do you offer training and support?
                                </button>
                            </h2>
                            <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="faq5" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes! We provide comprehensive onboarding, video tutorials, documentation, live training sessions, and 24/7 technical support to ensure your success with ProCalculator.
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
                        <a href="/pricing" class="btn btn-primary btn-lg">View Pricing Plans</a>
                        <a href="/features" class="btn btn-outline-light btn-lg">Explore Features</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
// Form validation and submission
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!this.checkValidity()) {
        e.stopPropagation();
        this.classList.add('was-validated');
        return;
    }
    
    // Here you would typically send the form data to your server
    const formData = new FormData(this);
    
    // Show success message
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
    submitBtn.disabled = true;
    
    setTimeout(() => {
        submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Message Sent!';
        submitBtn.classList.remove('btn-primary');
        submitBtn.classList.add('btn-success');
        
        // Reset form after 2 seconds
        setTimeout(() => {
            this.reset();
            this.classList.remove('was-validated');
            submitBtn.innerHTML = originalText;
            submitBtn.classList.remove('btn-success');
            submitBtn.classList.add('btn-primary');
            submitBtn.disabled = false;
        }, 2000);
    }, 1500);
});

// Smooth scrolling for navigation links
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
</script>

<!-- Footer -->
<?php $viewHelper->partial('partials/footer'); ?>

<!-- ProCalculator Core JavaScript -->
<script src="<?= $viewHelper->themeUrl('assets/js/procalculator-core.js') ?>"></script>
</body>
</html>
