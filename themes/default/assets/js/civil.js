/**
 * Civil Engineering Module JavaScript
 * Interactive functionality for civil engineering landing page
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize civil module functionality
    initCivilModule();
});

/**
 * Initialize all civil module functionality
 */
function initCivilModule() {
    // Statistics counter animation
    animateCivilStatistics();

    // Category cards interaction
    initCategoryInteractions();

    // Scroll animations
    initScrollAnimations();

    // Button interactions
    initButtonEffects();

    // Engineering grid animation
    initEngineeringGrid();
}

/**
 * Animate statistics counters for civil module
 */
function animateCivilStatistics() {
    const statNumbers = document.querySelectorAll('.stat-number');

    if (statNumbers.length === 0) return;

    // Create intersection observer for stats
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateStatNumber(entry.target);
                statsObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    statNumbers.forEach(stat => {
        statsObserver.observe(stat);
    });
}

/**
 * Animate individual stat number
 */
function animateStatNumber(element) {
    const finalValue = element.textContent;
    let numericValue = 0;
    let increment = 0;
    let suffix = '';

    // Extract numeric value and suffix
    if (finalValue.includes('%')) {
        numericValue = parseFloat(finalValue.replace('%', ''));
        suffix = '%';
        increment = numericValue / 50;
    } else if (finalValue.includes('+')) {
        numericValue = parseInt(finalValue.replace('+', ''));
        suffix = '+';
        increment = numericValue / 50;
    } else {
        numericValue = parseInt(finalValue);
        increment = numericValue / 50;
    }

    let current = 0;
    const timer = setInterval(() => {
        current += increment;
        if (current >= numericValue) {
            current = numericValue;
            clearInterval(timer);
        }

        if (suffix === '%' || suffix === '+') {
            element.textContent = Math.floor(current) + suffix;
        } else {
            element.textContent = Math.floor(current);
        }
    }, 30);
}

/**
 * Initialize category card interactions
 */
function initCategoryInteractions() {
    const categoryCards = document.querySelectorAll('.category-card');

    categoryCards.forEach(card => {
        // Add hover effect
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
            this.style.boxShadow = 'var(--civil-shadow-hover)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = 'var(--civil-shadow)';
        });

        // Add click effect for category links
        const categoryLink = card.querySelector('.category-link');
        if (categoryLink) {
            categoryLink.addEventListener('click', function(e) {
                e.preventDefault();

                // Add loading state
                const originalText = this.textContent;
                this.textContent = 'Loading...';

                // Simulate navigation
                setTimeout(() => {
                    // In real implementation, navigate to the category page
                    console.log('Navigating to:', this.href);
                    // window.location.href = this.href;

                    // Restore original text
                    this.textContent = originalText;
                }, 1000);
            });
        }
    });
}

/**
 * Initialize scroll-based animations
 */
function initScrollAnimations() {
    // Create intersection observer for fade-in animations
    const fadeElements = document.querySelectorAll('.category-card, .feature-item, .stat-item');

    const fadeObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    // Set initial state and observe elements
    fadeElements.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        element.style.transition = `all 0.6s ease ${index * 0.1}s`;
        fadeObserver.observe(element);
    });
}

/**
 * Initialize button effects and interactions
 */
function initButtonEffects() {
    const buttons = document.querySelectorAll('.btn');

    buttons.forEach(button => {
        // Add ripple effect
        button.addEventListener('click', function(e) {
            createRippleEffect(e, this);
        });

        // Add loading state for CTA buttons
        const ctaButtons = button.closest('.civil-cta') ? button : null;
        if (ctaButtons) {
            button.addEventListener('click', function(e) {
                if (this.href && this.href !== '#') {
                    e.preventDefault();
                    showLoadingState(this);
                    setTimeout(() => {
                        window.location.href = this.href;
                    }, 1500);
                }
            });
        }
    });
}

/**
 * Create ripple effect on button click
 */
function createRippleEffect(event, element) {
    const ripple = document.createElement('span');
    const rect = element.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;

    ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
    `;

    element.style.position = 'relative';
    element.style.overflow = 'hidden';
    element.appendChild(ripple);

    setTimeout(() => {
        ripple.remove();
    }, 600);
}

/**
 * Show loading state for buttons
 */
function showLoadingState(button) {
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    button.disabled = true;
    button.style.opacity = '0.7';

    setTimeout(() => {
        button.innerHTML = originalContent;
        button.disabled = false;
        button.style.opacity = '1';
    }, 1500);
}

/**
 * Initialize engineering grid animation
 */
function initEngineeringGrid() {
    const gridLines = document.querySelectorAll('.grid-line');

    if (gridLines.length === 0) return;

    // Animate grid lines on load
    gridLines.forEach((line, index) => {
        line.style.transform = 'scaleX(0)';
        line.style.transformOrigin = 'left';
        setTimeout(() => {
            line.style.transition = 'transform 2s ease-out';
            line.style.transform = 'scaleX(1)';
        }, index * 200);
    });
}

/**
 * Smooth scroll to section
 */
function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

/**
 * Tool hover effects
 */
function initToolEffects() {
    const toolItems = document.querySelectorAll('.tool-item');

    toolItems.forEach(tool => {
        tool.addEventListener('mouseenter', function() {
            this.style.background = 'var(--civil-primary)';
            this.style.color = 'white';
            this.style.transform = 'scale(1.05)';
        });

        tool.addEventListener('mouseleave', function() {
            this.style.background = 'var(--civil-light)';
            this.style.color = 'var(--civil-text-primary)';
            this.style.transform = 'scale(1)';
        });
    });
}

/**
 * Standards logo interaction
 */
function initStandardsInteraction() {
    const standardsLogos = document.querySelectorAll('.standards-logo');

    standardsLogos.forEach(logo => {
        logo.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1) rotate(5deg)';
            this.style.boxShadow = '0 15px 30px rgba(26, 54, 93, 0.2)';
        });

        logo.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) rotate(0deg)';
            this.style.boxShadow = 'var(--civil-shadow)';
        });
    });
}

/**
 * Feature item animations
 */
function initFeatureAnimations() {
    const featureItems = document.querySelectorAll('.feature-item');

    featureItems.forEach((item, index) => {
        item.addEventListener('mouseenter', function() {
            this.style.background = 'var(--civil-light)';
            this.style.border = '2px solid var(--civil-primary)';
        });

        item.addEventListener('mouseleave', function() {
            this.style.background = 'transparent';
            this.style.border = 'none';
        });
    });
}

/**
 * Add CSS for ripple animation
 */
function addRippleStyles() {
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        .civil-hero {
            background-attachment: fixed;
        }
        
        @media (max-width: 768px) {
            .civil-hero {
                background-attachment: scroll;
            }
        }
    `;
    document.head.appendChild(style);
}

/**
 * Performance optimization
 */
function optimizePerformance() {
    // Add passive event listeners
    const passiveEvents = ['scroll', 'touchstart', 'touchmove', 'wheel'];

    passiveEvents.forEach(event => {
        document.addEventListener(event, function() {}, { passive: true });
    });

    // Lazy load non-critical animations
    setTimeout(() => {
        initToolEffects();
        initStandardsInteraction();
        initFeatureAnimations();
    }, 1000);
}

// Initialize additional features
document.addEventListener('DOMContentLoaded', function() {
    addRippleStyles();
    optimizePerformance();
});

// Export functions for global use
window.CivilModule = {
    scrollToSection,
    animateCivilStatistics,
    initCategoryInteractions,
    initScrollAnimations
};