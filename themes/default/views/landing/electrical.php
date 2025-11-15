<?php
$page_title = 'Electrical Engineering Toolkit';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Electrical Engineering', 'url' => '#']
];
?>

    <?php if (!function_exists('load_theme_css')) { require_once __DIR__ . '/../partials/theme-helpers.php'; } ?>
    <?php load_theme_css('electrical.css'); ?>

<div class="container">
    <div class="hero">
        <h1>Electrical Engineering Toolkit</h1>
        <p>Professional calculators and reference tools for electrical engineers and electricians.</p>
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
                <a href="<?php echo app_base_url('modules/electrical/wire-sizing/wire-size-by-current.php'); ?>" class="tool-item"><span>Wire Size by Current</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/electrical/wire-sizing/wire-ampacity.php'); ?>" class="tool-item"><span>Wire Ampacity</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/electrical/wire-sizing/motor-circuit-wiring.php'); ?>" class="tool-item"><span>Motor Circuit Wiring</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/electrical/wire-sizing/transformer-kva-sizing.php'); ?>" class="tool-item"><span>Transformer kVA Sizing</span> <i class="fas fa-arrow-right"></i></a>
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
                <a href="<?php echo app_base_url('modules/electrical/voltage-drop/single-phase-voltage-drop.php'); ?>" class="tool-item"><span>Single Phase Voltage Drop</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/electrical/voltage-drop/three-phase-voltage-drop.php'); ?>" class="tool-item"><span>Three Phase Voltage Drop</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/electrical/voltage-drop/voltage-drop-sizing.php'); ?>" class="tool-item"><span>Voltage Drop Sizing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/electrical/voltage-drop/voltage-regulation.php'); ?>" class="tool-item"><span>Voltage Regulation</span> <i class="fas fa-arrow-right"></i></a>
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
                <a href="<?php echo app_base_url('modules/electrical/load-calculation/general-lighting-load.php'); ?>" class="tool-item"><span>General Lighting Load</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/electrical/load-calculation/receptacle-load.php'); ?>" class="tool-item"><span>Receptacle Load</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/electrical/load-calculation/panel-schedule.php'); ?>" class="tool-item"><span>Panel Schedule</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/electrical/load-calculation/feeder-sizing.php'); ?>" class="tool-item"><span>Feeder Sizing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/electrical/load-calculation/motor-full-load-amps.php'); ?>" class="tool-item"><span>Motor Full Load Currents</span> <i class="fas fa-arrow-right"></i></a>
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
                <a href="<?php echo app_base_url('modules/electrical/short-circuit/available-fault-current.php'); ?>" class="tool-item"><span>Available Fault Current</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/electrical/short-circuit/ground-conductor-sizing.php'); ?>" class="tool-item"><span>Ground Conductor Sizing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/electrical/short-circuit/power-factor-correction.php'); ?>" class="tool-item"><span>Power Factor Correction</span> <i class="fas fa-arrow-right"></i></a>
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
                <a href="<?php echo app_base_url('modules/electrical/conduit-sizing/conduit-fill-calculation.php'); ?>" class="tool-item"><span>Conduit Fill Calculation</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/electrical/conduit-sizing/cable-tray-sizing.php'); ?>" class="tool-item"><span>Cable Tray Sizing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/electrical/conduit-sizing/junction-box-sizing.php'); ?>" class="tool-item"><span>Junction Box Sizing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/electrical/conduit-sizing/entrance-service-sizing.php'); ?>" class="tool-item"><span>Entrance & Service Sizing</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>
    </div>

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
                    top: targetElement.offsetTop - 150, // Adjust for fixed nav height
                    behavior: 'smooth'
                });
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

    // Toggle tool lists
    const categoryCards = document.querySelectorAll('.category-card');
    categoryCards.forEach(card => {
        // Expand all cards by default
        card.classList.add('active');

        card.addEventListener('click', (e) => {
            // Prevent clicks on links from toggling the card
            if (e.target.closest('a')) return;
            
            card.classList.toggle('active');
        });
    });

    // Active state for sub-nav buttons with expand and blur effect
    const subNavButtons = document.querySelectorAll('.sub-nav-btn');
    subNavButtons.forEach(button => {
        button.addEventListener('click', () => {
            subNavButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            // Get target card ID from href
            const targetId = button.getAttribute('href').slice(1);
            const targetCard = document.getElementById(targetId);
            
            // Apply focused and blur effects
            categoryCards.forEach(card => {
                card.classList.remove('focused', 'blurred');
                if (card.id === targetId) {
                    card.classList.add('focused');
                } else {
                    card.classList.add('blurred');
                }
            });
            
            // Remove all effects after 1 second
            setTimeout(() => {
                categoryCards.forEach(card => {
                    card.classList.remove('focused', 'blurred');
                });
            }, 1000);
        });
    });
});
</script>

