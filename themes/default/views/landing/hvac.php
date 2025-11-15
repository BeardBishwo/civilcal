<?php
$page_title = 'HVAC Engineering Toolkit';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'HVAC Engineering', 'url' => '#']
];
?>

<?php if (!function_exists('load_theme_css')) { require_once __DIR__ . '/../partials/theme-helpers.php'; } ?>
<?php load_theme_css('hvac.css'); ?>

<div class="container">
    <div class="hero">
        <h1>HVAC Engineering Toolkit</h1>
        <p>A comprehensive suite of calculators for heating, ventilation, and air conditioning professionals.</p>
    </div>

    <!-- Sub-navigation for categories -->
    <div class="sub-nav" id="sub-nav">
        <a href="#loadcalculation" class="sub-nav-btn">Load Calculation</a>
        <a href="#ductsizing" class="sub-nav-btn">Duct Sizing</a>
        <a href="#psychrometrics" class="sub-nav-btn">Psychrometrics</a>
        <a href="#equipmentsizing" class="sub-nav-btn">Equipment Sizing</a>
        <a href="#energyanalysis" class="sub-nav-btn">Energy Analysis</a>
    </div>

    <div class="category-grid">
        <!-- Load Calculation Section -->
        <div id="loadcalculation" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-thermometer-half category-icon"></i>
                <div class="category-title">
                    <h3>Load Calculation</h3>
                    <p>Calculate heating and cooling loads for accurate system sizing.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/load-calculation/cooling-load.php'); ?>"><span>Cooling Load Calculation</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/load-calculation/heating-load.php'); ?>"><span>Heating Load Calculation</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/load-calculation/ventilation.php'); ?>"><span>Ventilation Rate Calculator</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/load-calculation/infiltration.php'); ?>"><span>Infiltration Load</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Duct Sizing Section -->
        <div id="ductsizing" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-vent-damper category-icon"></i>
                <div class="category-title">
                    <h3>Duct Sizing</h3>
                    <p>Design and analyze HVAC duct systems for optimal airflow.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/duct-sizing/duct-by-velocity.php'); ?>"><span>Duct Sizing by Velocity</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/duct-sizing/pressure-drop.php'); ?>"><span>Duct Pressure Drop</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/duct-sizing/equivalent-round.php'); ?>"><span>Equivalent Round Duct</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/duct-sizing/fitting-loss.php'); ?>"><span>Duct Fitting Loss</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/duct-sizing/grille-sizing.php'); ?>"><span>Grille & Diffuser Sizing</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Psychrometrics Section -->
        <div id="psychrometrics" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-cloud category-icon"></i>
                <div class="category-title">
                    <h3>Psychrometrics</h3>
                    <p>Air properties and moisture calculations for HVAC processes.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/psychrometrics/air-properties.php'); ?>"><span>Air Properties Calculator</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/psychrometrics/enthalpy.php'); ?>"><span>Enthalpy Calculation</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/psychrometrics/cooling-load-psych.php'); ?>"><span>Cooling Load (Psych)</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/psychrometrics/sensible-heat-ratio.php'); ?>"><span>Sensible Heat Ratio</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Equipment Sizing Section -->
        <div id="equipmentsizing" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-cog category-icon"></i>
                <div class="category-title">
                    <h3>Equipment Sizing</h3>
                    <p>Size HVAC equipment including air conditioners, furnaces, chillers, and pumps.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/equipment-sizing/ac-sizing.php'); ?>"><span>AC Unit Sizing</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/equipment-sizing/furnace-sizing.php'); ?>"><span>Furnace Sizing</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/equipment-sizing/chiller-sizing.php'); ?>"><span>Chiller Sizing</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/equipment-sizing/pump-sizing.php'); ?>"><span>Pump Sizing</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Energy Analysis Section -->
        <div id="energyanalysis" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-bolt category-icon"></i>
                <div class="category-title">
                    <h3>Energy Analysis</h3>
                    <p>Energy consumption, cost analysis, and efficiency calculations.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/energy-analysis/energy-consumption.php'); ?>"><span>Energy Consumption</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/energy-analysis/payback-period.php'); ?>"><span>Payback Period</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/energy-analysis/co2-emissions.php'); ?>"><span>CO₂ Emissions</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/energy-analysis/insulation-savings.php'); ?>"><span>Insulation Savings</span> <i class="fas fa-arrow-right"></i></a></li>
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
                    top: targetElement.offsetTop - 100, // Adjust for fixed nav height
                    behavior: 'smooth'
                });

                // Add highlight effect
                targetElement.classList.add('highlight');
                setTimeout(() => {
                    targetElement.classList.remove('highlight');
                }, 2000); // Remove after 2 seconds
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
});
</script>

<?php ?>

