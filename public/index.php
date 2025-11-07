<?php
// index.php - Homepage with 3D animated calculator tools
require_once 'includes/functions.php';
require_once 'includes/header.php';
$settings = get_site_settings();
$meta = get_site_meta();
?>
<style type="text/css">
    :root {
        --primary: #667eea;
        --secondary: #764ba2;
        --accent: #f093fb;
        --dark: #1a202c;
        --light: #f7fafc;
        --gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --glass: rgba(255, 255, 255, 0.1);
        --shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        /* Removed inline background - using theme.css instead */
        min-height: 100vh;
        color: var(--light);
        overflow-x: hidden;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    /* Header Styles */
    .hero {
        text-align: center;
        padding: 8rem 0;
        position: relative;
        <?php 
        $hero_bg = $settings['hero_background'] ?? 'image';
        if ($hero_bg === 'image'): 
        echo "background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('assets/images/banner.jpg');";
        echo "background-size: cover;";
        echo "background-position: center;";
        elseif ($hero_bg === 'gradient'): 
        echo "background: var(--gradient);";
        else: // 'none'
        echo "background: transparent;";
        endif;
        ?>
        border-radius: 20px;
        overflow: hidden;
    }

    .hero h1 {
        font-size: 4rem;
        font-weight: 800;
        background: linear-gradient(45deg, #667eea, #f093fb, #764ba2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 1rem;
        text-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .hero p {
        font-size: 1.5rem;
        opacity: 0.9;
        margin-bottom: 2rem;
    }

    /* Module Title */
    .module-title {
        text-align: center;
        margin: 3rem 0;
        position: relative;
    }

    .module-title h2 {
        font-size: 2.5rem;
        background: linear-gradient(45deg, #ff6b6b, #feca57);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        display: inline-block;
        padding: 0 2rem;
    }

    .module-title::before,
    .module-title::after {
        content: '';
        position: absolute;
        top: 50%;
        width: 30%;
        height: 2px;
        background: linear-gradient(90deg, transparent, #f093fb, transparent);
    }

    .module-title::before {
        left: 0;
    }

    .module-title::after {
        right: 0;
    }

    /* Calculator Grid */
    .calculator-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 2rem;
        margin: 3rem 0;
    }

    .category-card {
        background: var(--glass);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 2rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .category-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .category-card:hover::before {
        left: 100%;
    }

    .category-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
    }

    .category-header {
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    }

    .category-header h3 {
        font-size: 1.8rem;
        color: #feca57;
        margin-bottom: 0.5rem;
    }

    .separator {
        height: 3px;
        background: linear-gradient(90deg, transparent, #667eea, transparent);
        margin: 1rem 0;
        border-radius: 2px;
    }

    .tool-list {
        list-style: none;
    }

    .tool-item {
        padding: 1rem;
        margin: 0.5rem 0;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        transition: all 0.3s ease;
        cursor: pointer;
        border-left: 4px solid transparent;
    }

    .tool-item:hover {
        background: rgba(255, 255, 255, 0.1);
        border-left: 4px solid #f093fb;
        transform: translateX(10px);
    }

    .tool-item a {
        color: var(--light);
        text-decoration: none;
        display: block;
        font-size: 1.1rem;
        transition: color 0.3s ease;
    }

    .tool-item:hover a {
        color: #f093fb;
    }

    /* 3D Animation Elements */
    .floating-elements {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        pointer-events: none;
        z-index: -1;
    }

    .floating-element {
        position: absolute;
        background: var(--glass);
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

    /* Responsive Design */
    @media (max-width: 768px) {
        .hero h1 {
            font-size: 2.5rem;
        }
        
        .calculator-grid {
            grid-template-columns: 1fr;
        }
        
        .module-title h2 {
            font-size: 2rem;
        }
    }

    /* Navigation */
    .main-nav {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin: 2rem 0;
        flex-wrap: wrap;
    }

    .nav-btn {
        padding: 1rem 2rem;
        background: var(--glass);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 50px;
        color: var(--light);
        text-decoration: none;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .nav-btn:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

</style>
</head>
<body>
<!-- Floating Background Elements -->
<div class="floating-elements">
    <div class="floating-element element-1"></div>
    <div class="floating-element element-2"></div>
    <div class="floating-element element-3"></div>
</div>

<div class="container">
    <!-- Hero Section -->
    <div class="hero">
        <h1><?php echo htmlspecialchars($meta['hero_heading'] ?? 'Engineering Toolkit'); ?></h1>
        <p><?php echo htmlspecialchars($meta['hero_subheading'] ?? 'Professional Calculators for Modern Construction'); ?></p>
    </div>

    <!-- Navigation -->
    <div class="main-nav">
        <a href="civil.php" class="nav-btn">Civil Construction</a>
        <a href="plumbing.php" class="nav-btn">Plumbing Services</a>
        <a href="hvac.php" class="nav-btn">HVAC Systems</a>
        <a href="electrical.php" class="nav-btn">Electrical Works</a>
        <a href="fire.php" class="nav-btn">Fire Protection</a>
        <a href="site.php" class="nav-btn">Site Development</a>
    </div>

    <!-- Civil Engineering Module -->
    <div class="module-title">
        <h2>Civil Engineering Calculator</h2>
    </div>

    <div class="calculator-grid">
        <!-- Concrete Category -->
        <div class="category-card" data-tilt>
            <div class="category-header">
                <h3>Concrete</h3>
                <div class="separator"></div>
            </div>
            <ul class="tool-list">
                <li class="tool-item">
                    <a href="modules/civil/concrete/concrete-volume.php">Concrete Volume</a>
                </li>
                <li class="tool-item">
                    <a href="modules/civil/concrete/rebar-calculation.php">Rebar Calculation</a>
                </li>
                <li class="tool-item">
                    <a href="modules/civil/concrete/concrete-mix.php">Concrete Mix Design</a>
                </li>
                <li class="tool-item">
                    <a href="modules/civil/concrete/concrete-strength.php">Concrete Strength</a>
                </li>
            </ul>
        </div>

        <!-- Brickwork Category -->
        <div class="category-card" data-tilt>
            <div class="category-header">
                <h3>Brickwork</h3>
                <div class="separator"></div>
            </div>
            <ul class="tool-list">
                <li class="tool-item">
                    <a href="modules/civil/brickwork/brick-quantity.php">Brick Quantity</a>
                </li>
                <li class="tool-item">
                    <a href="modules/civil/brickwork/mortar-ratio.php">Mortar Ratio</a>
                </li>
                <li class="tool-item">
                    <a href="modules/civil/brickwork/plastering-estimator.php">Plastering Estimator</a>
                </li>
            </ul>
        </div>

        <!-- Earthwork Category -->
        <div class="category-card" data-tilt>
            <div class="category-header">
                <h3>Earthwork</h3>
                <div class="separator"></div>
            </div>
            <ul class="tool-list">
                <li class="tool-item">
                    <a href="modules/civil/earthwork/cut-and-fill-volume.php">Cut & Fill Volume</a>
                </li>
                <li class="tool-item">
                    <a href="modules/civil/earthwork/slope-calculation.php">Slope Calculation</a>
                </li>
                <li class="tool-item">
                    <a href="modules/civil/earthwork/excavation-volume.php">Excavation Volume</a>
                </li>
            </ul>
        </div>

        <!-- Structural Category -->
        <div class="category-card" data-tilt>
            <div class="category-header">
                <h3>Structural</h3>
                <div class="separator"></div>
            </div>
            <ul class="tool-list">
                <li class="tool-item">
                    <a href="modules/civil/structural/beam-load-capacity.php">Beam Load Capacity</a>
                </li>
                <li class="tool-item">
                    <a href="modules/civil/structural/column-design.php">Column Design</a>
                </li>
                <li class="tool-item">
                    <a href="modules/civil/structural/slab-design.php">Slab Design</a>
                </li>
                <li class="tool-item">
                    <a href="modules/civil/structural/foundation-design.php">Foundation Design</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Electrical Engineering Module -->
    <div class="module-title">
        <h2>Electrical Engineering Calculator</h2>
    </div>

    <div class="calculator-grid">
        <!-- Load Calculation Category -->
        <div class="category-card" data-tilt>
            <div class="category-header">
                <h3>Load Calculation</h3>
                <div class="separator"></div>
            </div>
            <ul class="tool-list">
                <li class="tool-item">
                    <a href="modules/electrical/load-calculation/general-lighting-load.php">General Lighting Load</a>
                </li>
                <li class="tool-item">
                    <a href="modules/electrical/load-calculation/receptacle-load.php">Receptacle Load</a>
                </li>
                <li class="tool-item">
                    <a href="modules/electrical/load-calculation/panel-schedule.php">Panel Schedule</a>
                </li>
                <li class="tool-item">
                    <a href="modules/electrical/load-calculation/demand-load-calculation.php">Demand Load Calculation</a>
                </li>
                <li class="tool-item">
                    <a href="modules/electrical/load-calculation/feeder-sizing.php">Feeder Sizing</a>
                </li>
            </ul>
        </div>

        <!-- Short Circuit Category -->
        <div class="category-card" data-tilt>
            <div class="category-header">
                <h3>Short Circuit</h3>
                <div class="separator"></div>
            </div>
            <ul class="tool-list">
                <li class="tool-item">
                    <a href="modules/electrical/short-circuit/available-fault-current.php">Available Fault Current</a>
                </li>
                <li class="tool-item">
                    <a href="modules/electrical/short-circuit/ground-conductor-sizing.php">Grounding Conductor</a>
                </li>
                <li class="tool-item">
                    <a href="modules/electrical/load-calculation/ocpd-sizing.php">Circuit Breaker Sizing</a>
                </li>
                <li class="tool-item">
                    <a href="modules/electrical/voltage-drop/voltage-drop-sizing.php">Wire Impedance</a>
                </li>
                <li class="tool-item">
                    <a href="modules/electrical/short-circuit/power-factor-correction.php">Power Factor Correction</a>
                </li>
            </ul>
        </div>

        <!-- Conduit Sizing Category -->
        <div class="category-card" data-tilt>
            <div class="category-header">
                <h3>Conduit Sizing</h3>
                <div class="separator"></div>
            </div>
            <ul class="tool-list">
                <li class="tool-item">
                    <a href="modules/electrical/conduit-sizing/cable-tray-sizing.php">Cable Tray Sizing</a>
                </li>
                <li class="tool-item">
                    <a href="modules/electrical/conduit-sizing/conduit-fill-calculation.php">Conduit Fill Calculation</a>
                </li>
                <li class="tool-item">
                    <a href="modules/electrical/conduit-sizing/junction-box-sizing.php">Junction Box Sizing</a>
                </li>
            </ul>
        </div>

        <!-- Wire Sizing Category -->
        <div class="category-card" data-tilt>
            <div class="category-header">
                <h3>Wire Sizing</h3>
                <div class="separator"></div>
            </div>
            <ul class="tool-list">
                <li class="tool-item">
                    <a href="modules/electrical/wire-sizing/wire-ampacity.php">Wire Ampacity</a>
                </li>
                <li class="tool-item">
                    <a href="modules/electrical/wire-sizing/wire-size-by-current.php">Wire Size by Current</a>
                </li>
                <li class="tool-item">
                    <a href="modules/electrical/wire-sizing/motor-circuit-wire-sizing.php">Motor Circuit Wire Sizing</a>
                </li>
                <li class="tool-item">
                    <a href="modules/electrical/wire-sizing/transformer-kva-sizing.php">Transformer KVA Sizing</a>
                </li>
                <li class="tool-item">
                    <a href="modules/electrical/voltage-drop/single-phase-voltage-drop.php">Single Phase Voltage Drop</a>
                </li>
                <li class="tool-item">
                    <a href="modules/electrical/voltage-drop/three-phase-voltage-drop.php">Three Phase Voltage Drop</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Plumbing Engineering Module -->
    <div class="module-title">
        <h2>Plumbing Engineering Calculator</h2>
    </div>

    <div class="calculator-grid">
        <!-- Water Supply Category -->
        <div class="category-card" data-tilt>
            <div class="category-header">
                <h3>Water Supply</h3>
                <div class="separator"></div>
            </div>
            <ul class="tool-list">
                <li class="tool-item">
                    <a href="modules/plumbing/water_supply/water-demand-calculation.php">Water Demand Calculation</a>
                </li>
                <li class="tool-item">
                    <a href="modules/plumbing/water_supply/cold-water-demand.php">Cold Water Demand</a>
                </li>
                <li class="tool-item">
                    <a href="modules/plumbing/water_supply/hot-water-demand.php">Hot Water Demand</a>
                </li>
                <li class="tool-item">
                    <a href="modules/plumbing/water_supply/pressure-loss.php">Pressure Loss</a>
                </li>
                <li class="tool-item">
                    <a href="modules/plumbing/water_supply/pump-sizing.php">Pump Sizing</a>
                </li>
                <li class="tool-item">
                    <a href="modules/plumbing/water_supply/storage-tank-sizing.php">Storage Tank Sizing</a>
                </li>
                <li class="tool-item">
                    <a href="modules/plumbing/water_supply/water-hammer-calculation.php">Water Hammer Calculation</a>
                </li>
                <li class="tool-item">
                    <a href="modules/plumbing/water_supply/main-isolation-valve.php">Main Isolation Valve</a>
                </li>
            </ul>
        </div>

        <!-- Fixtures Category -->
        <div class="category-card" data-tilt>
            <div class="category-header">
                <h3>Fixtures</h3>
                <div class="separator"></div>
            </div>
            <ul class="tool-list">
                <li class="tool-item">
                    <a href="modules/plumbing/fixtures/fixture-unit-calculation.php">Fixture Unit Calculation</a>
                </li>
                <li class="tool-item">
                    <a href="modules/plumbing/fixtures/sink-sizing.php">Sink Sizing</a>
                </li>
                <li class="tool-item">
                    <a href="modules/plumbing/fixtures/toilet-flow.php">Toilet Flow</a>
                </li>
                <li class="tool-item">
                    <a href="modules/plumbing/fixtures/shower-sizing.php">Shower Sizing</a>
                </li>
            </ul>
        </div>

        <!-- Hot Water Category -->
        <div class="category-card" data-tilt>
            <div class="category-header">
                <h3>Hot Water</h3>
                <div class="separator"></div>
            </div>
            <ul class="tool-list">
                <li class="tool-item">
                    <a href="modules/plumbing/hot_water/water-heater-sizing.php">Water Heater Sizing</a>
                </li>
                <li class="tool-item">
                    <a href="modules/plumbing/hot_water/heat-loss-calculation.php">Heat Loss Calculation</a>
                </li>
                <li class="tool-item">
                    <a href="modules/plumbing/hot_water/recirculation-loop.php">Recirculation Loop</a>
                </li>
                <li class="tool-item">
                    <a href="modules/plumbing/hot_water/safety-valve.php">Safety Valve</a>
                </li>
            </ul>
        </div>

        <!-- Pipe Sizing Category -->
        <div class="category-card" data-tilt>
            <div class="category-header">
                <h3>Pipe Sizing</h3>
                <div class="separator"></div>
            </div>
            <ul class="tool-list">
                <li class="tool-item">
                    <a href="modules/plumbing/pipe_sizing/water-pipe-sizing.php">Water Pipe Sizing</a>
                </li>
                <li class="tool-item">
                    <a href="modules/plumbing/pipe_sizing/pipe-flow-capacity.php">Pipe Flow Capacity</a>
                </li>
                <li class="tool-item">
                    <a href="modules/plumbing/pipe_sizing/gas-pipe-sizing.php">Gas Pipe Sizing</a>
                </li>
                <li class="tool-item">
                    <a href="modules/plumbing/pipe_sizing/expansion-loop-sizing.php">Expansion Loop Sizing</a>
                </li>
            </ul>
        </div>

        <!-- Drainage Category -->
        <div class="category-card" data-tilt>
            <div class="category-header">
                <h3>Drainage</h3>
                <div class="separator"></div>
            </div>
            <ul class="tool-list">
                <li class="tool-item">
                    <a href="modules/plumbing/drainage/drainage-pipe-sizing.php">Drainage Pipe Sizing</a>
                </li>
                <li class="tool-item">
                    <a href="modules/plumbing/drainage/vent-pipe-sizing.php">Vent Pipe Sizing</a>
                </li>
                <li class="tool-item">
                    <a href="modules/plumbing/drainage/soil-stack-sizing.php">Soil Stack Sizing</a>
                </li>
                <li class="tool-item">
                    <a href="modules/plumbing/drainage/grease-trap-sizing.php">Grease Trap Sizing</a>
                </li>
                <li class="tool-item">
                    <a href="modules/plumbing/drainage/storm-drainage.php">Storm Drainage</a>
                </li>
                <li class="tool-item">
                    <a href="modules/plumbing/drainage/trap-sizing.php">Trap Sizing</a>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- JavaScript for 3D Tilt Effect -->
<script>
    // Simple tilt effect without external library
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.category-card');
        
        cards.forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateY = (x - centerX) / 25;
                const rotateX = (centerY - y) / 25;
                
                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-10px)`;
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(-10px)';
            });
        });
    });
</script>

<?php
require_once 'includes/footer.php';
?>
