/**
 * Ultra HD Theme - Particle Background System
 * Creates dynamic particle effects for visual depth
 */

class ParticleSystem {
    constructor(options = {}) {
        this.container = options.container || document.body;
        this.particleCount = options.particleCount || 50;
        this.colors = options.colors || ['primary', 'secondary', 'accent', 'glow'];
        this.sizes = options.sizes || ['sm', 'md', 'lg'];
        this.particles = [];
        
        this.init();
    }
    
    init() {
        // Create particles container
        this.particlesContainer = document.createElement('div');
        this.particlesContainer.className = 'particles-background';
        
        // Create glow orbs
        this.createGlowOrbs();
        
        // Create particles
        this.createParticles();
        
        // Add to DOM
        if (this.container.firstChild) {
            this.container.insertBefore(this.particlesContainer, this.container.firstChild);
        } else {
            this.container.appendChild(this.particlesContainer);
        }
        
        // Create shooting stars occasionally
        this.startShootingStars();
    }
    
    createGlowOrbs() {
        for (let i = 1; i <= 3; i++) {
            const orb = document.createElement('div');
            orb.className = `glow-orb glow-orb-${i}`;
            this.particlesContainer.appendChild(orb);
        }
    }
    
    createParticles() {
        for (let i = 0; i < this.particleCount; i++) {
            const particle = this.createParticle();
            this.particles.push(particle);
            this.particlesContainer.appendChild(particle);
        }
    }
    
    createParticle() {
        const particle = document.createElement('div');
        
        // Random properties
        const size = this.sizes[Math.floor(Math.random() * this.sizes.length)];
        const color = this.colors[Math.floor(Math.random() * this.colors.length)];
        const duration = 10 + Math.random() * 20; // 10-30 seconds
        const delay = Math.random() * 10; // 0-10 seconds delay
        const startX = Math.random() * 100; // Random horizontal start position
        
        // Apply classes and styles
        particle.className = `particle particle-${size} particle-${color}`;
        particle.style.left = `${startX}%`;
        particle.style.animationDuration = `${duration}s`;
        particle.style.animationDelay = `${delay}s`;
        
        // Vary animation for natural look
        const animations = ['floatParticle', 'floatParticleLeft', 'floatParticleSlow'];
        const animation = animations[Math.floor(Math.random() * animations.length)];
        particle.style.animationName = animation;
        
        return particle;
    }
    
    startShootingStars() {
        setInterval(() => {
            if (Math.random() > 0.7) { // 30% chance
                this.createShootingStar();
            }
        }, 3000); // Check every 3 seconds
    }
    
    createShootingStar() {
        const star = document.createElement('div');
        star.className = 'shooting-star';
        
        // Random starting position (top area of screen)
        const startX = Math.random() * 100;
        const startY = Math.random() * 30;
        const width = 50 + Math.random() * 150; // Random trail length
        
        star.style.left = `${startX}%`;
        star.style.top = `${startY}%`;
        star.style.width = `${width}px`;
        
        this.particlesContainer.appendChild(star);
        
        // Remove after animation
        setTimeout(() => {
            star.remove();
        }, 3000);
    }
    
    destroy() {
        this.particlesContainer.remove();
        this.particles = [];
    }
}

// Auto-initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    // Check if user prefers reduced motion
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    
    if (!prefersReducedMotion) {
        // Adjust particle count based on screen size
        let particleCount = 50;
        if (window.innerWidth < 768) {
            particleCount = 20;
        } else if (window.innerWidth >= 2560) {
            particleCount = 80; // More particles for 4K displays
        }
        
        window.particleSystem = new ParticleSystem({
            particleCount: particleCount
        });
    }
});

// Adjust particle count on resize
let resizeTimer;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
        if (window.particleSystem) {
            window.particleSystem.destroy();
            
            let particleCount = 50;
            if (window.innerWidth < 768) {
                particleCount = 20;
            } else if (window.innerWidth >= 2560) {
                particleCount = 80;
            }
            
            window.particleSystem = new ParticleSystem({
                particleCount: particleCount
            });
        }
    }, 250);
});
