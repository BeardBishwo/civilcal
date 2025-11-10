/**
 * Home Page JavaScript - Theme Default
 * Statistics counter animation and home page specific functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Statistics counter animation for home page
    animateStatisticsCounters();

    // Home page specific animations
    initHomeAnimations();
});

/**
 * Animate statistics counters with smooth counting effect
 */
function animateStatisticsCounters() {
    const counters = document.querySelectorAll('.stat-number-premium');

    counters.forEach(counter => {
        const target = counter.textContent;
        if (target.includes('+')) {
            // Animate numbers like "56+", "100%"
            const num = parseInt(target.replace(/[+%]/g, ''));
            let current = 0;
            const increment = num / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= num) {
                    if (target.includes('%')) {
                        counter.textContent = num + '%';
                    } else {
                        counter.textContent = num + '+';
                    }
                    clearInterval(timer);
                } else {
                    if (target.includes('%')) {
                        counter.textContent = Math.floor(current) + '%';
                    } else {
                        counter.textContent = Math.floor(current) + '+';
                    }
                }
            }, 30);
        } else if (target.includes('/')) {
            // Handle "24/7" style displays
            counter.style.opacity = '0';
            counter.style.transform = 'translateY(20px)';

            setTimeout(() => {
                counter.style.transition = 'all 0.6s ease-out';
                counter.style.opacity = '1';
                counter.style.transform = 'translateY(0)';
            }, Math.random() * 500);
        }
    });
}

/**
 * Initialize home page specific animations
 */
function initHomeAnimations() {
    // Add stagger animation to category cards
    const categoryCards = document.querySelectorAll('.premium-card-premium');
    categoryCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `all 0.6s ease ${index * 0.1}s`;

        // Animate cards when they come into view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        observer.observe(card);
    });

    // Animate premium badges
    const premiumBadges = document.querySelectorAll('.premium-badge-premium');
    premiumBadges.forEach((badge, index) => {
        badge.style.opacity = '0';
        badge.style.transform = 'translateY(-20px)';
        badge.style.transition = `all 0.5s ease ${index * 0.2}s`;

        setTimeout(() => {
            badge.style.opacity = '1';
            badge.style.transform = 'translateY(0)';
        }, 500);
    });

    // Smooth scroll for anchor links (if any)
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
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

    // Add hover effects to category cards
    categoryCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
            this.style.transition = 'all 0.3s ease';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
}

/**
 * Show notification to user
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check' : 'info'}-circle"></i>
        <span>${message}</span>
    `;

    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : '#3b82f6'};
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 10px;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);

    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Export functions for use in other scripts if needed
window.BishwoHome = {
    animateStatisticsCounters,
    initHomeAnimations,
    showNotification
};