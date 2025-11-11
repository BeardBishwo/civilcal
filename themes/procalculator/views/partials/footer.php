<?php
/**
 * ProCalculator Footer Partial
 * Footer component
 */
?>
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
                            <li><a href="<?= $viewHelper->url('calculator/civil') ?>" class="footer-link">Civil Engineering</a></li>
                            <li><a href="<?= $viewHelper->url('calculator/electrical') ?>" class="footer-link">Electrical</a></li>
                            <li><a href="<?= $viewHelper->url('calculator/plumbing') ?>" class="footer-link">Plumbing</a></li>
                            <li><a href="<?= $viewHelper->url('calculator/hvac') ?>" class="footer-link">HVAC</a></li>
                            <li><a href="<?= $viewHelper->url('calculator/structural') ?>" class="footer-link">Structural</a></li>
                            <li><a href="<?= $viewHelper->url('calculators') ?>" class="footer-link premium-accent">View All Calculators</a></li>
                        </ul>
                    </div>

                    <div class="footer-section">
                        <h4 class="footer-section-title">Features</h4>
                        <ul class="footer-menu">
                            <li><a href="<?= $viewHelper->url('dashboard') ?>" class="footer-link">Dashboard</a></li>
                            <li><a href="<?= $viewHelper->url('history') ?>" class="footer-link">Calculation History</a></li>
                            <li><a href="<?= $viewHelper->url('history') ?>" class="footer-link">Favorites</a></li>
                            <li><a href="<?= $viewHelper->url('user/exports/templates') ?>" class="footer-link">Export Tools</a></li>
                            <li><a href="<?= $viewHelper->url('features') ?>" class="footer-link">Team Collaboration</a></li>
                            <li><a href="<?= $viewHelper->url('features') ?>" class="footer-link">API Access</a></li>
                        </ul>
                    </div>

                    <div class="footer-section">
                        <h4 class="footer-section-title">Support</h4>
                        <ul class="footer-menu">
                            <li><a href="<?= $viewHelper->url('features') ?>" class="footer-link">Documentation</a></li>
                            <li><a href="<?= $viewHelper->url('features') ?>" class="footer-link">Tutorials</a></li>
                            <li><a href="<?= $viewHelper->url('features') ?>" class="footer-link">Calculators Guide</a></li>
                            <li><a href="<?= $viewHelper->url('features') ?>" class="footer-link">API Reference</a></li>
                            <li><a href="<?= $viewHelper->url('contact') ?>" class="footer-link">Contact Support</a></li>
                            <li><a href="<?= $viewHelper->url('features') ?>" class="footer-link">System Status</a></li>
                        </ul>
                    </div>

                    <div class="footer-section">
                        <h4 class="footer-section-title">Company</h4>
                        <ul class="footer-menu">
                            <li><a href="<?= $viewHelper->url('about') ?>" class="footer-link">About Us</a></li>
                            <li><a href="<?= $viewHelper->url('about') ?>" class="footer-link">Careers</a></li>
                            <li><a href="<?= $viewHelper->url('about') ?>" class="footer-link">Engineering Blog</a></li>
                            <li><a href="<?= $viewHelper->url('about') ?>" class="footer-link">Case Studies</a></li>
                            <li><a href="<?= $viewHelper->url('about') ?>" class="footer-link">Partners</a></li>
                            <li><a href="<?= $viewHelper->url('about') ?>" class="footer-link">Press Kit</a></li>
                        </ul>
                    </div>

                    <div class="footer-section">
                        <h4 class="footer-section-title">Legal</h4>
                        <ul class="footer-menu">
                            <li><a href="<?= $viewHelper->url('about') ?>" class="footer-link">Privacy Policy</a></li>
                            <li><a href="<?= $viewHelper->url('about') ?>" class="footer-link">Terms of Service</a></li>
                            <li><a href="<?= $viewHelper->url('about') ?>" class="footer-link">Cookie Policy</a></li>
                            <li><a href="<?= $viewHelper->url('about') ?>" class="footer-link">License Agreement</a></li>
                            <li><a href="<?= $viewHelper->url('about') ?>" class="footer-link">Compliance</a></li>
                            <li><a href="<?= $viewHelper->url('about') ?>" class="footer-link">Security</a></li>
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

    <!-- ProCalculator Core JavaScript (global include) -->
    <script src="/assets/themes/procalculator/js/procalculator-core.js?v=1.0.0" defer></script>