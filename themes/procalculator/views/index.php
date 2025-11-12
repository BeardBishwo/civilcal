<?php
// ProCalculator Premium Homepage with Blue Background
?>

<!-- Floating Background Elements -->
<div class="floating-elements">
    <div class="floating-element element-1"></div>
    <div class="floating-element element-2"></div>
    <div class="floating-element element-3"></div>
</div>

<div class="container">
    <!-- Hero Section -->
    <div class="home-hero">
        <h1>Engineering Toolkit</h1>
        <p class="lead">Professional Calculators for Modern Construction</p>
        <div class="hero-ctas">
            <a href="#calculators" class="btn btn-primary">Explore Tools</a>
            <a href="landing/civil.php" class="btn btn-outline-light">Get Started</a>
        </div>
    </div>

    <!-- Main Calculator Grid with Premium Styling -->
    <div class="glow-wrap" id="calculators">
        <div class="glow-inner">
            <div class="row g-4">
                <!-- Civil Engineering Module -->
                <div class="col-lg-4 col-md-6">
                    <div class="module-card" data-tilt>
                        <div class="module-icon">
                            <i class="fas fa-hard-hat"></i>
                        </div>
                        <h3 class="module-title">Civil Engineering</h3>
                        <p class="module-desc">Concrete, structural, and earthwork calculations</p>
                        <div class="tool-links">
                            <a href="modules/civil/concrete/concrete-volume.php">Concrete Volume</a>
                            <a href="modules/civil/structural/beam-design.php">Beam Design</a>
                            <a href="landing/civil.php">View All Tools</a>
                        </div>
                    </div>
                </div>

                <!-- Plumbing Module -->
                <div class="col-lg-4 col-md-6">
                    <div class="module-card" data-tilt>
                        <div class="module-icon">
                            <i class="fas fa-faucet"></i>
                        </div>
                        <h3 class="module-title">Plumbing Systems</h3>
                        <p class="module-desc">Water supply, drainage, and pipe sizing tools</p>
                        <div class="tool-links">
                            <a href="modules/plumbing/water_supply/pump-sizing.php">Pump Sizing</a>
                            <a href="modules/plumbing/water_supply/water-demand-calculation.php">Water Demand</a>
                            <a href="landing/plumbing.php">View All Tools</a>
                        </div>
                    </div>
                </div>

                <!-- Electrical Module -->
                <div class="col-lg-4 col-md-6">
                    <div class="module-card" data-tilt>
                        <div class="module-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3 class="module-title">Electrical Systems</h3>
                        <p class="module-desc">Load calculations, wire sizing, and power distribution</p>
                        <div class="tool-links">
                            <a href="landing/electrical.php">View All Tools</a>
                        </div>
                    </div>
                </div>

                <!-- HVAC Module -->
                <div class="col-lg-4 col-md-6">
                    <div class="module-card" data-tilt>
                        <div class="module-icon">
                            <i class="fas fa-wind"></i>
                        </div>
                        <h3 class="module-title">HVAC Systems</h3>
                        <p class="module-desc">Heating, ventilation, and air conditioning calculations</p>
                        <div class="tool-links">
                            <a href="landing/hvac.php">View All Tools</a>
                        </div>
                    </div>
                </div>

                <!-- Fire Protection Module -->
                <div class="col-lg-4 col-md-6">
                    <div class="module-card" data-tilt>
                        <div class="module-icon">
                            <i class="fas fa-fire-extinguisher"></i>
                        </div>
                        <h3 class="module-title">Fire Protection</h3>
                        <p class="module-desc">Sprinkler systems, fire pumps, and safety calculations</p>
                        <div class="tool-links">
                            <a href="landing/fire.php">View All Tools</a>
                        </div>
                    </div>
                </div>

                <!-- Site Development Module -->
                <div class="col-lg-4 col-md-6">
                    <div class="module-card" data-tilt>
                        <div class="module-icon">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <h3 class="module-title">Site Development</h3>
                        <p class="module-desc">Earthwork, utilities, and site planning tools</p>
                        <div class="tool-links">
                            <a href="landing/site.php">View All Tools</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for 3D Tilt Effect -->
<script src="<?php echo function_exists('app_base_url') ? app_base_url('assets/js/tilt.js') : 'assets/js/tilt.js'; ?>"></script>

<style>
/* Premium Blue Background and Styling */
body.index-page,
body.procalculator-home,
body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    background-attachment: fixed;
    background-repeat: no-repeat;
    background-size: cover;
    min-height: 100vh;
}

/* Floating Elements */
.floating-elements {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: -1;
}

.floating-element {
    position: absolute;
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
}

.element-1 {
    width: 100px;
    height: 100px;
    top: 10%;
    left: 10%;
    background: linear-gradient(45deg, #667eea, #764ba2);
}

.element-2 {
    width: 150px;
    height: 150px;
    top: 60%;
    right: 10%;
    background: linear-gradient(45deg, #f093fb, #f5576c);
    animation-delay: -2s;
}

.element-3 {
    width: 80px;
    height: 80px;
    bottom: 20%;
    left: 20%;
    background: linear-gradient(45deg, #4facfe, #00f2fe);
    animation-delay: -4s;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px) rotate(0deg);
    }
    50% {
        transform: translateY(-20px) rotate(180deg);
    }
}

/* Hero */
.home-hero {
    padding: 4rem 1rem 3rem;
    text-align: center;
    color: #fff;
    background: linear-gradient(180deg, rgba(0, 0, 0, 0.12), rgba(0, 0, 0, 0.04));
}

.home-hero p.lead {
    opacity: 0.92;
    margin-bottom: 1.4rem;
    font-size: 1.05rem;
}

/* Glowing grid wrapper */
.glow-wrap {
    position: relative;
    padding: 18px;
    border-radius: 24px;
    margin-bottom: 2rem;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.02), rgba(255, 255, 255, 0.01));
}

.glow-wrap:before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    padding: 2px;
    background: linear-gradient(90deg, #6a11cb, #2575fc, #00d2ff, #7b2ff7);
    -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
    mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    padding: 2px;
    z-index: 0;
    filter: blur(10px) saturate(130%);
    opacity: 0.95;
    animation: glowShift 6s linear infinite;
    pointer-events: none;
}

@keyframes glowShift {
    0% { transform: translateX(-10%) }
    50% { transform: translateX(10%) }
    100% { transform: translateX(-10%) }
}

.glow-inner {
    position: relative;
    z-index: 1;
    padding: 12px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.03);
}

/* Module cards */
.module-card {
    border-radius: 12px;
    padding: 18px;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.02), rgba(255, 255, 255, 0.01));
    box-shadow: 0 10px 30px rgba(2, 6, 23, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.04);
    transition: transform .28s ease, box-shadow .28s ease;
}

.module-card:hover {
    transform: translateY(-8px) scale(1.01);
    box-shadow: 0 20px 60px rgba(2, 6, 23, 0.35);
}

.module-icon {
    font-size: 2.2rem;
    padding: 12px;
    border-radius: 10px;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.04), rgba(255, 255, 255, 0.02));
    display: inline-block;
    color: #f093fb;
}

.module-title {
    font-weight: 700;
    margin-top: 8px;
    color: #fff;
}

.module-desc {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.95rem;
}

/* Tool Links */
.tool-links {
    margin-top: 1rem;
}

.tool-links a {
    display: block;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.tool-links a:hover {
    color: #f093fb;
    transform: translateX(5px);
}

.tool-links a:last-child {
    border-bottom: none;
    font-weight: 600;
    color: #f093fb;
}

/* Hero CTAs */
.hero-ctas {
    margin-top: 2rem;
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.hero-ctas .btn {
    padding: 0.75rem 2rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.hero-ctas .btn-primary {
    background: #f093fb;
    color: white;
}

.hero-ctas .btn-outline-light {
    background: transparent;
    color: white;
    border-color: rgba(255, 255, 255, 0.3);
}

.hero-ctas .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}
</style>
