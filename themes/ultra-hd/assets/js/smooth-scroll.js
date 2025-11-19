/**
 * Ultra HD Theme - Smooth Scroll Enhancement
 * Butter-smooth scrolling with easing
 */

(function() {
    'use strict';
    
    // Check if smooth scroll is supported
    const supportsNativeSmoothScroll = 'scrollBehavior' in document.documentElement.style;
    
    if (!supportsNativeSmoothScroll) {
        // Polyfill for older browsers
        const easeInOutCubic = (t) => {
            return t < 0.5 ? 4 * t * t * t : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1;
        };
        
        const smoothScrollTo = (targetY, duration = 1000) => {
            const startY = window.pageYOffset;
            const difference = targetY - startY;
            const startTime = performance.now();
            
            const step = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const ease = easeInOutCubic(progress);
                
                window.scrollTo(0, startY + (difference * ease));
                
                if (progress < 1) {
                    requestAnimationFrame(step);
                }
            };
            
            requestAnimationFrame(step);
        };
        
        // Override scroll behavior
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#') return;
                
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    const targetY = target.getBoundingClientRect().top + window.pageYOffset - 100;
                    smoothScrollTo(targetY);
                }
            });
        });
    }
    
    // Enhanced scroll for ultra HD displays
    if (window.innerWidth >= 2560) {
        // Add momentum scrolling for 4K displays
        let isScrolling = false;
        let scrollTimeout;
        
        window.addEventListener('scroll', () => {
            window.clearTimeout(scrollTimeout);
            
            if (!isScrolling) {
                document.body.classList.add('is-scrolling');
                isScrolling = true;
            }
            
            scrollTimeout = setTimeout(() => {
                document.body.classList.remove('is-scrolling');
                isScrolling = false;
            }, 150);
        });
    }
    
})();
