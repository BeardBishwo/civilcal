<?php
$page_title = 'Electrical Engineering Toolkit';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Electrical Engineering', 'url' => '#']
];
require_once dirname(__DIR__, 4) . '/themes/default/views/partials/header.php';
?>

    <?php load_theme_css('electrical.css'); ?>

<div class="container">
    <div class="hero d-flex justify-content-between align-items-center">
        <div>
            <h1>Electrical Engineering Toolkit</h1>
            <p>Professional calculators and reference tools for electrical engineers and electricians.</p>
        </div>
    </div>

    <!-- Sub-navigation for categories -->
    <div class="sub-nav" id="sub-nav">
        <a href="#wireSizing" class="sub-nav-btn">Wire Sizing</a>
        <a href="#voltageDrop" class="sub-nav-btn">Voltage Drop</a>
        <a href="#loadCalculation" class="sub-nav-btn">Load Calculation</a>
        <a href="#shortCircuit" class="sub-nav-btn">Short Circuit</a>
        <a href="#conduit" class="sub-nav-btn">Conduit & Boxes</a>
    </div>

    <div class="category-grid">
        <!-- Wire Sizing Section -->
        <div id="wireSizing" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-bolt category-icon"></i>
                <div class="category-title">
                    <h3>Wire Sizing</h3>
                    <p>Select tools for ampacity, motor wiring and transformer sizing.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/wire-sizing/wire-size-by-current.php'); ?>"><span>Wire Size by Current</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/wire-sizing/wire-ampacity.php'); ?>"><span>Wire Ampacity</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/wire-sizing/motor-circuit-wiring.php'); ?>"><span>Motor Circuit Wiring</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/wire-sizing/transformer-kva-sizing.php'); ?>"><span>Transformer kVA Sizing</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Voltage Drop Section -->
        <div id="voltageDrop" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-tachometer-alt category-icon"></i>
                <div class="category-title">
                    <h3>Voltage Drop</h3>
                    <p>Single and three-phase voltage drop calculators and regulation tools.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/voltage-drop/single-phase-voltage-drop.php'); ?>"><span>Single Phase Voltage Drop</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/voltage-drop/three-phase-voltage-drop.php'); ?>"><span>Three Phase Voltage Drop</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/voltage-drop/voltage-drop-sizing.php'); ?>"><span>Voltage Drop Sizing</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/voltage-drop/voltage-regulation.php'); ?>"><span>Voltage Regulation</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Load Calculation Section -->
        <div id="loadCalculation" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-calculator category-icon"></i>
                <div class="category-title">
                    <h3>Load Calculation</h3>
                    <p>Lighting, receptacles, feeders, panels and related sizing tools.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/load-calculation/general-lighting-load.php'); ?>"><span>General Lighting Load</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/load-calculation/receptacle-load.php'); ?>"><span>Receptacle Load</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/load-calculation/panel-schedule.php'); ?>"><span>Panel Schedule</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/load-calculation/feeder-sizing.php'); ?>"><span>Feeder Sizing</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/load-calculation/motor-full-load-amps.php'); ?>"><span>Motor Full Load Currents</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Short Circuit Section -->
        <div id="shortCircuit" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-bolt category-icon"></i>
                <div class="category-title">
                    <h3>Short Circuit</h3>
                    <p>Available fault current, grounding and impedance calculations.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/short-circuit/available-fault-current.php'); ?>"><span>Available Fault Current</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/short-circuit/ground-conductor-sizing.php'); ?>"><span>Ground Conductor Sizing</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/short-circuit/power-factor-correction.php'); ?>"><span>Power Factor Correction</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Conduit & Boxes Section -->
        <div id="conduit" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-pipe category-icon"></i>
                <div class="category-title">
                    <h3>Conduit & Boxes</h3>
                    <p>Conduit fill, cable tray and junction box sizing tools.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/conduit-sizing/conduit-fill-calculation.php'); ?>"><span>Conduit Fill Calculation</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/conduit-sizing/cable-tray-sizing.php'); ?>"><span>Cable Tray Sizing</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/conduit-sizing/junction-box-sizing.php'); ?>"><span>Junction Box Sizing</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/conduit-sizing/entrance-service-sizing.php'); ?>"><span>Entrance & Service Sizing</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>
    </div>

    <!-- Recent calculations will be inserted here by JS -->
    <div id="recentElectricalCalculationsPlaceholder"></div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const subNav = document.getElementById("sub-nav");
    const subNavOffsetTop = subNav.offsetTop;
    const body = document.body;

    // Smooth scrolling for sub-navigation links
    document.querySelectorAll('.sub-nav-btn').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if(targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 100,
                    behavior: 'smooth'
                });

                // Highlight effect
                targetElement.classList.add('highlight');
                setTimeout(() => targetElement.classList.remove('highlight'), 1800);
            }
        });
    });

    // Sticky sub-navigation
    window.addEventListener("scroll", function() {
        if (window.pageYOffset >= subNavOffsetTop) {
            body.classList.add("sticky-nav");
        } else {
            body.classList.remove("sticky-nav");
        }
    });

    // Expand all category cards by default
    document.querySelectorAll('.category-card').forEach(c => c.classList.add('active'));

    // Initialize recent calculations
    renderRecentCalculations();
});

function renderRecentCalculations() {
    const recent = JSON.parse(localStorage.getItem('recentElectricalCalculations') || '[]');
    const placeholder = document.getElementById('recentElectricalCalculationsPlaceholder');
    if (!placeholder) return;

    if (!recent || recent.length === 0) {
        placeholder.innerHTML = '<div class="mt-4"><h4>Recent Calculations</h4><p class="text-muted">No recent calculations</p></div>';
        return;
    }

    const items = recent.slice(0,10).map(calc => `
        <div class="recent-item mb-2 p-2 border rounded">
            <div class="small"><strong>${calc.type}</strong></div>
            <div class="small text-muted">${calc.calculation}</div>
            <div class="small text-muted">${calc.timestamp}</div>
        </div>
    `).join('');

    placeholder.innerHTML = `<div class="mt-4"><h4>Recent Calculations</h4>${items}</div>`;
}
</script>

<style>
.recent-item { background: rgba(255,255,255,0.03); }
</style>

<?php
require_once dirname(__DIR__, 4) . '/themes/default/views/partials/footer.php';
?>

