</main>

    <!-- Premium Footer -->
    <footer class="procalculator-footer glassmorphism-footer" role="contentinfo">
        <div class="footer-container">
            <!-- Footer Top Section -->
            <div class="footer-top">
                <div class="footer-brand-section">
                    <div class="footer-brand">
                        <div class="footer-logo premium-gradient">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <div class="footer-brand-text">
                            <h3 class="footer-title">ProCalculator</h3>
                            <p class="footer-tagline">Premium Engineering Platform</p>
                        </div>
                    </div>
                    <p class="footer-description">
                        Ultra-premium $100,000 quality engineering calculator platform designed for professional engineers. 
                        Advanced tools, premium features, and enterprise-grade reliability.
                    </p>
                    <div class="footer-social">
                        <a href="#" class="social-link premium-hover" aria-label="Follow us on Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-link premium-hover" aria-label="Follow us on LinkedIn">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <a href="#" class="social-link premium-hover" aria-label="Follow us on GitHub">
                            <i class="fab fa-github"></i>
                        </a>
                        <a href="#" class="social-link premium-hover" aria-label="Subscribe to our YouTube channel">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>

                <!-- Footer Links Sections -->
                <div class="footer-links">
                    <div class="footer-section">
                        <h4 class="footer-section-title">Products</h4>
                        <ul class="footer-menu">
                            <li><a href="/calculators/civil" class="footer-link">Civil Engineering</a></li>
                            <li><a href="/calculators/electrical" class="footer-link">Electrical</a></li>
                            <li><a href="/calculators/plumbing" class="footer-link">Plumbing</a></li>
                            <li><a href="/calculators/hvac" class="footer-link">HVAC</a></li>
                            <li><a href="/calculators/structural" class="footer-link">Structural</a></li>
                            <li><a href="/calculators" class="footer-link premium-accent">View All Calculators</a></li>
                        </ul>
                    </div>

                    <div class="footer-section">
                        <h4 class="footer-section-title">Features</h4>
                        <ul class="footer-menu">
                            <li><a href="/features/dashboard" class="footer-link">Dashboard</a></li>
                            <li><a href="/features/history" class="footer-link">Calculation History</a></li>
                            <li><a href="/features/favorites" class="footer-link">Favorites</a></li>
                            <li><a href="/features/export" class="footer-link">Export Tools</a></li>
                            <li><a href="/features/collaboration" class="footer-link">Team Collaboration</a></li>
                            <li><a href="/features/api" class="footer-link">API Access</a></li>
                        </ul>
                    </div>

                    <div class="footer-section">
                        <h4 class="footer-section-title">Support</h4>
                        <ul class="footer-menu">
                            <li><a href="/help/documentation" class="footer-link">Documentation</a></li>
                            <li><a href="/help/tutorials" class="footer-link">Tutorials</a></li>
                            <li><a href="/help/calculators-guide" class="footer-link">Calculators Guide</a></li>
                            <li><a href="/help/api-reference" class="footer-link">API Reference</a></li>
                            <li><a href="/help/contact" class="footer-link">Contact Support</a></li>
                            <li><a href="/help/status" class="footer-link">System Status</a></li>
                        </ul>
                    </div>

                    <div class="footer-section">
                        <h4 class="footer-section-title">Company</h4>
                        <ul class="footer-menu">
                            <li><a href="/about" class="footer-link">About Us</a></li>
                            <li><a href="/careers" class="footer-link">Careers</a></li>
                            <li><a href="/blog" class="footer-link">Engineering Blog</a></li>
                            <li><a href="/case-studies" class="footer-link">Case Studies</a></li>
                            <li><a href="/partners" class="footer-link">Partners</a></li>
                            <li><a href="/press" class="footer-link">Press Kit</a></li>
                        </ul>
                    </div>

                    <div class="footer-section">
                        <h4 class="footer-section-title">Legal</h4>
                        <ul class="footer-menu">
                            <li><a href="/legal/privacy" class="footer-link">Privacy Policy</a></li>
                            <li><a href="/legal/terms" class="footer-link">Terms of Service</a></li>
                            <li><a href="/legal/cookies" class="footer-link">Cookie Policy</a></li>
                            <li><a href="/legal/license" class="footer-link">License Agreement</a></li>
                            <li><a href="/legal/compliance" class="footer-link">Compliance</a></li>
                            <li><a href="/legal/security" class="footer-link">Security</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Newsletter Subscription -->
            <div class="footer-newsletter">
                <div class="newsletter-content">
                    <div class="newsletter-text">
                        <h4>Stay Updated</h4>
                        <p>Get the latest calculator updates, new features, and engineering tips delivered to your inbox.</p>
                    </div>
                    <form class="newsletter-form" action="/newsletter/subscribe" method="post">
                        <input type="email" name="email" class="newsletter-input" placeholder="Enter your email address" required>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <button type="submit" class="newsletter-submit premium-btn">
                            <i class="fas fa-paper-plane"></i>
                            Subscribe
                        </button>
                    </form>
                </div>
            </div>

            <!-- Footer Bottom Section -->
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <div class="footer-copyright">
                        <p>&copy; <?= date('Y') ?> Bishwo Calculator Team. All rights reserved.</p>
                        <p>Ultra-premium quality | Professional grade | Enterprise ready</p>
                    </div>
                    
                    <div class="footer-badges">
                        <div class="badge premium-badge">
                            <i class="fas fa-crown"></i>
                            <span>Premium</span>
                        </div>
                        <div class="badge security-badge">
                            <i class="fas fa-shield-alt"></i>
                            <span>Secure</span>
                        </div>
                        <div class="badge uptime-badge">
                            <i class="fas fa-clock"></i>
                            <span>99.9% Uptime</span>
                        </div>
                        <div class="badge support-badge">
                            <i class="fas fa-headset"></i>
                            <span>24/7 Support</span>
                        </div>
                    </div>

                    <div class="footer-version">
                        <span class="version-info">v<?= htmlspecialchars($_ENV['APP_VERSION'] ?? '1.0.0') ?></span>
                        <span class="theme-info">ProCalculator Theme</span>
                    </div>
                </div>
            </div>

            <!-- Back to Top Button -->
            <button class="back-to-top premium-btn-icon" aria-label="Back to top" id="backToTop">
                <i class="fas fa-chevron-up"></i>
            </button>
        </div>
    </footer>

    <!-- Premium JavaScript Files -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" integrity="sha512-eDcn4Z0s5bGZ0QvZ8lM3cR4QvQ3aK5r7kZ8g2G4z8I7/r5gq5r3bGfF5xN2jQ5M3bO3c7ZqK6r8s6hZ7kQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    <!-- Core JavaScript -->
    <?php foreach (['js/procalculator-core.js', 'js/glassmorphism.js', 'js/animations.js'] as $script): ?>
        <script src="/themes/procalculator/<?= htmlspecialchars($script) ?>"></script>
    <?php endforeach; ?>
    
    <!-- Page-specific scripts -->
    <?php if (isset($additional_scripts)): ?>
        <?php foreach ($additional_scripts as $script): ?>
            <script src="<?= htmlspecialchars($script) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Premium Features Script -->
    <script>
        // Initialize premium features when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize theme
            if (typeof ProCalculatorCore !== 'undefined') {
                ProCalculatorCore.init();
            }
            
            // Initialize glassmorphism effects
            if (typeof GlassmorphismEffects !== 'undefined') {
                GlassmorphismEffects.init();
            }
            
            // Initialize animations
            if (typeof PremiumAnimations !== 'undefined') {
                PremiumAnimations.init();
            }
            
            // Hide loader when page is fully loaded
            window.addEventListener('load', function() {
                const loader = document.getElementById('premium-loader');
                if (loader) {
                    loader.style.opacity = '0';
                    setTimeout(() => {
                        loader.style.display = 'none';
                    }, 600);
                }
            });
        });
    </script>

    <!-- Global Error Handler -->
    <script>
        window.addEventListener('error', function(e) {
            console.error('ProCalculator Error:', e.error);
            // Could send to logging service in production
        });
    </script>

    <!-- Performance Monitoring (Development Only) -->
    <?php if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development'): ?>
        <script>
            // Performance monitoring for development
            window.addEventListener('load', function() {
                const perfData = performance.getEntriesByType('navigation')[0];
                console.log('ProCalculator Performance:', {
                    loadTime: perfData.loadEventEnd - perfData.loadEventStart,
                    domContentLoaded: perfData.domContentLoadedEventEnd - perfData.domContentLoadedEventStart,
                    firstPaint: performance.getEntriesByType('paint').find(entry => entry.name === 'first-paint')?.startTime,
                    firstContentfulPaint: performance.getEntriesByType('paint').find(entry => entry.name === 'first-contentful-paint')?.startTime
                });
            });
        </script>
    <?php endif; ?>

</body>
</html>
