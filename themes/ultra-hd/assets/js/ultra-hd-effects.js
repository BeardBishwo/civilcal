/**
 * Ultra HD Theme - Advanced Effects & Interactions
 * Smooth animations, parallax, and interactive elements
 */

(function() {
    'use strict';
    
    // ===== SMOOTH SCROLL =====
    const initSmoothScroll = () => {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#') return;
                
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    };
    
    // ===== PARALLAX EFFECT =====
    const initParallax = () => {
        const parallaxElements = document.querySelectorAll('[data-parallax]');
        
        if (parallaxElements.length === 0) return;
        
        let ticking = false;
        
        const updateParallax = () => {
            const scrollY = window.pageYOffset;
            
            parallaxElements.forEach(element => {
                const speed = parseFloat(element.dataset.parallax) || 0.5;
                const yPos = -(scrollY * speed);
                element.style.transform = `translate3d(0, ${yPos}px, 0)`;
            });
            
            ticking = false;
        };
        
        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(updateParallax);
                ticking = true;
            }
        });
    };
    
    // ===== INTERSECTION OBSERVER FOR ANIMATIONS =====
    const initScrollAnimations = () => {
        const animatedElements = document.querySelectorAll(
            '.animate-fade-in-up, .animate-fade-in-down, ' +
            '.animate-fade-in-left, .animate-fade-in-right, ' +
            '.animate-scale-in'
        );
        
        if (animatedElements.length === 0) return;
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'none';
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        animatedElements.forEach(element => {
            element.style.opacity = '0';
            observer.observe(element);
        });
    };
    
    // ===== 3D TILT EFFECT ON CARDS =====
    const init3DTilt = () => {
        const tiltCards = document.querySelectorAll('.glass-card, .ultra-card');
        
        tiltCards.forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = (y - centerY) / 10;
                const rotateY = (centerX - x) / 10;
                
                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(10px)`;
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateZ(0)';
            });
        });
    };
    
    // ===== CURSOR GLOW EFFECT =====
    const initCursorGlow = () => {
        // Only on larger screens
        if (window.innerWidth < 1024) return;
        
        const cursor = document.createElement('div');
        cursor.style.cssText = `
            position: fixed;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.4) 0%, transparent 70%);
            pointer-events: none;
            z-index: 9999;
            transition: transform 0.15s ease;
            display: none;
        `;
        document.body.appendChild(cursor);
        
        let mouseX = 0, mouseY = 0;
        let cursorX = 0, cursorY = 0;
        
        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
            cursor.style.display = 'block';
        });
        
        document.addEventListener('mouseleave', () => {
            cursor.style.display = 'none';
        });
        
        // Smooth cursor animation
        const animateCursor = () => {
            cursorX += (mouseX - cursorX) * 0.1;
            cursorY += (mouseY - cursorY) * 0.1;
            
            cursor.style.left = cursorX - 10 + 'px';
            cursor.style.top = cursorY - 10 + 'px';
            
            requestAnimationFrame(animateCursor);
        };
        animateCursor();
        
        // Scale up on hoverable elements
        const hoverables = document.querySelectorAll('a, button, input, .btn-ultra, .glass-card');
        hoverables.forEach(element => {
            element.addEventListener('mouseenter', () => {
                cursor.style.transform = 'scale(2)';
            });
            element.addEventListener('mouseleave', () => {
                cursor.style.transform = 'scale(1)';
            });
        });
    };
    
    // ===== PERFORMANCE MONITORING =====
    const optimizeForPerformance = () => {
        // Check device capabilities
        const isLowEndDevice = navigator.hardwareConcurrency && navigator.hardwareConcurrency < 4;
        
        if (isLowEndDevice) {
            // Reduce animations
            document.body.classList.add('reduced-animations');
            
            // Remove cursor glow
            const cursorGlow = document.querySelector('[style*="cursor"]');
            if (cursorGlow) cursorGlow.remove();
        }
    };
    
    // ===== INITIALIZE ALL EFFECTS =====
    const init = () => {
        // Check for reduced motion preference
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        
        if (!prefersReducedMotion) {
            initSmoothScroll();
            initParallax();
            initScrollAnimations();
            init3DTilt();
            initCursorGlow();
        } else {
            // Still init smooth scroll even with reduced motion
            initSmoothScroll();
        }
        
        optimizeForPerformance();
    };
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
})();
