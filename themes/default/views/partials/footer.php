<footer class="footer">
    <div class="footer-container">
        <!-- Main Footer Content -->
        <div class="footer-content">
            <div class="footer-section">
                <div class="footer-logo">
                    <img src="<?php echo $base_url; ?>assets/images/applogo.png" alt="Bishwo Calculator" onerror="this.style.display='none'">
                    <h3>Bishwo Calculator</h3>
                </div>
                <p class="footer-description">
                    Professional engineering calculators and tools for civil, electrical, mechanical, plumbing, 
                    fire protection, structural analysis, and construction estimation. Streamline your engineering 
                    workflow with precision tools designed by professionals for professionals.
                </p>
                <div class="footer-social">
                    <a href="#" class="social-link" aria-label="Follow us on LinkedIn">
                        <i class="fab fa-linkedin"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Follow us on Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Follow us on GitHub">
                        <i class="fab fa-github"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Follow us on YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
            
            <div class="footer-section">
                <h4>Engineering Disciplines</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo $base_url; ?>modules/civil/">Civil Engineering</a></li>
                    <li><a href="<?php echo $base_url; ?>modules/electrical/">Electrical Engineering</a></li>
                    <li><a href="<?php echo $base_url; ?>modules/hvac/">Mechanical/HVAC</a></li>
                    <li><a href="<?php echo $base_url; ?>modules/plumbing/">Plumbing Engineering</a></li>
                    <li><a href="<?php echo $base_url; ?>modules/fire/">Fire Protection</a></li>
                    <li><a href="<?php echo $base_url; ?>modules/structural/">Structural Engineering</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Construction & Management</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo $base_url; ?>modules/site/">Site Engineering</a></li>
                    <li><a href="<?php echo $base_url; ?>modules/estimation/">Estimation & Costing</a></li>
                    <li><a href="<?php echo $base_url; ?>modules/project-management/">Project Management</a></li>
                    <li><a href="<?php echo $base_url; ?>modules/mep/">MEP Coordination</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Resources & Support</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo $base_url; ?>help.php">Help & Documentation</a></li>
                    <li><a href="<?php echo $base_url; ?>tutorials.php">Video Tutorials</a></li>
                    <li><a href="<?php echo $base_url; ?>calculations-guide.php">Calculations Guide</a></li>
                    <li><a href="<?php echo $base_url; ?>standards-compliance.php">Standards & Compliance</a></li>
                    <li><a href="<?php echo $base_url; ?>api-documentation.php">API Documentation</a></li>
                    <li><a href="<?php echo $base_url; ?>changelog.php">What's New</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Company</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo $base_url; ?>about.php">About Us</a></li>
                    <li><a href="<?php echo $base_url; ?>contact.php">Contact Support</a></li>
                    <li><a href="<?php echo $base_url; ?>privacy.php">Privacy Policy</a></li>
                    <li><a href="<?php echo $base_url; ?>terms.php">Terms of Service</a></li>
                    <li><a href="<?php echo $base_url; ?>careers.php">Careers</a></li>
                    <li><a href="<?php echo $base_url; ?>partnerships.php">Partnerships</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Engineering Standards Footer -->
        <div class="footer-standards">
            <h4>Compliance & Standards</h4>
            <p>
                Our calculators comply with international engineering standards including ACI, AISC, ASCE, IEEE, 
                NFPA, IPC, and local building codes. All calculations are based on established engineering 
                principles and verified against industry standards.
            </p>
            <div class="standards-logos">
                <img src="<?php echo $base_url; ?>assets/images/standards/aci.png" alt="ACI Standards" onerror="this.style.display='none'">
                <img src="<?php echo $base_url; ?>assets/images/standards/aisc.png" alt="AISC Standards" onerror="this.style.display='none'">
                <img src="<?php echo $base_url; ?>assets/images/standards/ieee.png" alt="IEEE Standards" onerror="this.style.display='none'">
                <img src="<?php echo $base_url; ?>assets/images/standards/nfpa.png" alt="NFPA Standards" onerror="this.style.display='none'">
                <img src="<?php echo $base_url; ?>assets/images/standards/ipc.png" alt="IPC Standards" onerror="this.style.display='none'">
            </div>
        </div>
        
        <!-- Newsletter Signup -->
        <div class="footer-newsletter">
            <h4>Stay Updated</h4>
            <p>Get notified about new calculators, features, and engineering insights.</p>
            <form class="newsletter-form" onsubmit="subscribeNewsletter(event)">
                <div class="form-group">
                    <input type="email" class="newsletter-input" placeholder="Enter your email address" required aria-label="Email address for newsletter">
                    <button type="submit" class="newsletter-btn">
                        <i class="fas fa-paper-plane"></i>
                        Subscribe
                    </button>
                </div>
                <p class="newsletter-privacy">By subscribing, you agree to our privacy policy and terms of service.</p>
            </form>
        </div>
        
        <!-- Bottom Footer -->
        <div class="footer-bottom">
            <div class="footer-copyright">
                <p>&copy; <?php echo date('Y'); ?> Bishwo Calculator. All rights reserved.</p>
                <p>Professional Engineering Tools for the Modern World</p>
            </div>
            
            <div class="footer-cta">
                <div class="version-info">
                    <i class="fas fa-code-branch"></i>
                    <span>Version <?php echo isset($version) ? $version : '2.1.0'; ?></span>
                </div>
                <div class="footer-actions">
                    <button onclick="toggleTheme()" class="footer-theme-toggle" title="Toggle theme">
                        <i class="fas fa-moon"></i>
                    </button>
                    <a href="#top" class="back-to-top-btn" title="Back to top">
                        <i class="fas fa-arrow-up"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Include Back to Top Button -->
<?php if (file_exists($base_url . 'includes/back-to-top.php')): ?>
    <?php include_once $base_url . 'includes/back-to-top.php'; ?>
<?php endif; ?>

<script>
    // Newsletter Subscription
    function subscribeNewsletter(event) {
        event.preventDefault();
        const email = event.target.querySelector('.newsletter-input').value;
        
        // Simulate API call
        const btn = event.target.querySelector('.newsletter-btn');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subscribing...';
        btn.disabled = true;
        
        setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-check"></i> Subscribed!';
            event.target.reset();
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 3000);
        }, 1500);
    }
    
    // Back to Top Smooth Scroll
    document.querySelectorAll('.back-to-top-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
    
    // Show/Hide Back to Top Button
    window.addEventListener('scroll', function() {
        const backToTop = document.querySelector('.back-to-top-btn');
        if (backToTop) {
            if (window.pageYOffset > 300) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        }
    });
    
    // Footer Animation
    document.addEventListener('DOMContentLoaded', function() {
        const footerObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, { threshold: 0.1 });
        
        document.querySelectorAll('.footer-section').forEach(section => {
            footerObserver.observe(section);
        });
    });
</script>

<style>
    .footer {
        background: var(--bg-primary);
        border-top: 1px solid var(--border-color);
        margin-top: var(--spacing-3xl);
        position: relative;
        overflow: hidden;
    }
    
    .footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--primary-color), transparent);
    }
    
    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: var(--spacing-3xl) var(--spacing-md) var(--spacing-lg);
    }
    
    .footer-content {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
        gap: var(--spacing-2xl);
        margin-bottom: var(--spacing-3xl);
    }
    
    .footer-section {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.6s ease-out forwards;
    }
    
    .footer-section:nth-child(1) { animation-delay: 0.1s; }
    .footer-section:nth-child(2) { animation-delay: 0.2s; }
    .footer-section:nth-child(3) { animation-delay: 0.3s; }
    .footer-section:nth-child(4) { animation-delay: 0.4s; }
    .footer-section:nth-child(5) { animation-delay: 0.5s; }
    
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .footer-logo {
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
        margin-bottom: var(--spacing-md);
    }
    
    .footer-logo img {
        height: 32px;
        width: auto;
    }
    
    .footer-logo h3 {
        color: var(--text-primary);
        font-size: var(--font-size-xl);
        font-weight: 700;
        margin: 0;
    }
    
    .footer-description {
        color: var(--text-secondary);
        line-height: 1.6;
        margin-bottom: var(--spacing-lg);
        font-size: var(--font-size-sm);
    }
    
    .footer-social {
        display: flex;
        gap: var(--spacing-sm);
    }
    
    .social-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        color: var(--text-secondary);
        text-decoration: none;
        transition: all var(--transition-fast);
    }
    
    .social-link:hover {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
        transform: translateY(-2px);
    }
    
    .footer-section h4 {
        color: var(--text-primary);
        font-size: var(--font-size-lg);
        font-weight: 600;
        margin-bottom: var(--spacing-md);
        position: relative;
        padding-bottom: var(--spacing-sm);
    }
    
    .footer-section h4::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 30px;
        height: 2px;
        background: var(--primary-color);
        border-radius: 1px;
    }
    
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .footer-links li {
        margin-bottom: var(--spacing-xs);
    }
    
    .footer-links a {
        color: var(--text-secondary);
        text-decoration: none;
        font-size: var(--font-size-sm);
        transition: all var(--transition-fast);
        display: inline-block;
    }
    
    .footer-links a:hover {
        color: var(--primary-color);
        transform: translateX(4px);
    }
    
    .footer-standards {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: var(--spacing-xl);
        margin-bottom: var(--spacing-2xl);
    }
    
    .footer-standards h4 {
        color: var(--text-primary);
        font-size: var(--font-size-lg);
        font-weight: 600;
        margin-bottom: var(--spacing-md);
    }
    
    .footer-standards p {
        color: var(--text-secondary);
        line-height: 1.6;
        margin-bottom: var(--spacing-lg);
        font-size: var(--font-size-sm);
    }
    
    .standards-logos {
        display: flex;
        gap: var(--spacing-lg);
        flex-wrap: wrap;
        align-items: center;
    }
    
    .standards-logos img {
        height: 32px;
        width: auto;
        opacity: 0.7;
        transition: opacity var(--transition-fast);
        filter: grayscale(100%);
    }
    
    .standards-logos img:hover {
        opacity: 1;
        filter: grayscale(0%);
    }
    
    .footer-newsletter {
        background: var(--bg-tertiary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: var(--spacing-xl);
        margin-bottom: var(--spacing-2xl);
        text-align: center;
    }
    
    .footer-newsletter h4 {
        color: var(--text-primary);
        font-size: var(--font-size-lg);
        font-weight: 600;
        margin-bottom: var(--spacing-sm);
    }
    
    .footer-newsletter p {
        color: var(--text-secondary);
        margin-bottom: var(--spacing-lg);
        font-size: var(--font-size-sm);
    }
    
    .form-group {
        display: flex;
        gap: var(--spacing-sm);
        margin-bottom: var(--spacing-sm);
    }
    
    .newsletter-input {
        flex: 1;
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: var(--spacing-sm) var(--spacing-md);
        font-size: var(--font-size-sm);
        color: var(--text-primary);
        transition: all var(--transition-fast);
    }
    
    .newsletter-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    
    .newsletter-btn {
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        padding: var(--spacing-sm) var(--spacing-lg);
        font-size: var(--font-size-sm);
        font-weight: 500;
        cursor: pointer;
        transition: all var(--transition-fast);
        display: flex;
        align-items: center;
        gap: var(--spacing-xs);
    }
    
    .newsletter-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
    }
    
    .newsletter-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }
    
    .newsletter-privacy {
        color: var(--text-light);
        font-size: var(--font-size-xs);
        margin: 0;
    }
    
    .footer-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: var(--spacing-lg);
        border-top: 1px solid var(--border-color);
        flex-wrap: wrap;
        gap: var(--spacing-md);
    }
    
    .footer-copyright {
        color: var(--text-light);
        font-size: var(--font-size-sm);
    }
    
    .footer-copyright p {
        margin: 0 0 var(--spacing-xs) 0;
    }
    
    .footer-cta {
        display: flex;
        align-items: center;
        gap: var(--spacing-lg);
    }
    
    .version-info {
        display: flex;
        align-items: center;
        gap: var(--spacing-xs);
        color: var(--text-light);
        font-size: var(--font-size-sm);
    }
    
    .footer-actions {
        display: flex;
        gap: var(--spacing-sm);
    }
    
    .footer-theme-toggle,
    .back-to-top-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        color: var(--text-secondary);
        text-decoration: none;
        cursor: pointer;
        transition: all var(--transition-fast);
    }
    
    .footer-theme-toggle:hover,
    .back-to-top-btn:hover {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
        transform: translateY(-2px);
    }
    
    .back-to-top-btn {
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: all var(--transition-normal);
    }
    
    .back-to-top-btn.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    /* Responsive Design */
    @media (max-width: 1024px) {
        .footer-content {
            grid-template-columns: 1fr 1fr 1fr;
            gap: var(--spacing-xl);
        }
        
        .footer-section:first-child {
            grid-column: 1 / -1;
            margin-bottom: var(--spacing-lg);
        }
    }
    
    @media (max-width: 768px) {
        .footer-container {
            padding: var(--spacing-xl) var(--spacing-sm) var(--spacing-md);
        }
        
        .footer-content {
            grid-template-columns: 1fr;
            gap: var(--spacing-lg);
        }
        
        .footer-standards,
        .footer-newsletter {
            padding: var(--spacing-lg);
        }
        
        .form-group {
            flex-direction: column;
        }
        
        .footer-bottom {
            flex-direction: column;
            text-align: center;
        }
        
        .footer-cta {
            flex-direction: column;
            gap: var(--spacing-md);
        }
        
        .standards-logos {
            justify-content: center;
        }
    }
    
    @media (max-width: 480px) {
        .footer-social {
            justify-content: center;
        }
        
        .standards-logos {
            gap: var(--spacing-md);
        }
        
        .standards-logos img {
            height: 24px;
        }
    }
</style>
