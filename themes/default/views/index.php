<?php
// Homepage with 3D animated calculator tools
// Use default values since functions may not be available in view context
$settings = isset($settings) ? $settings : ['hero_background' => 'gradient'];
$meta = isset($meta) ? $meta : [
    'hero_heading' => 'Engineering Toolkit',
    'hero_subheading' => 'Professional Calculators for Modern Construction'
];

//
?>

<!-- Floating Background Elements -->
<div class="floating-elements">
    <div class="floating-element element-1"></div>
    <div class="floating-element element-2"></div>
    <div class="floating-element element-3"></div>
</div>

<div class="container">
    <!-- Hero Section -->
    <?php
    $hero_bg = $settings['hero_background'] ?? 'gradient';
    $hero_class = '';
    if ($hero_bg === 'image') {
        $hero_class = 'bg-image';
    } elseif ($hero_bg === 'gradient') {
        $hero_class = 'bg-gradient';
    } else {
        $hero_class = 'bg-none';
    }
    ?>
    <div class="hero <?php echo $hero_class; ?>">
        <h1><?php echo htmlspecialchars($meta['hero_heading'] ?? 'Engineering Toolkit'); ?></h1>
        <p><?php echo htmlspecialchars($meta['hero_subheading'] ?? 'Professional Calculators for Modern Construction'); ?></p>
    </div>

    <!-- Navigation -->
    <div class="main-nav">
        <a href="#civil" class="nav-btn">Civil Construction</a>
        <a href="#electrical" class="nav-btn">Electrical Works</a>
        <a href="#plumbing" class="nav-btn">Plumbing Services</a>
        <a href="#hvac" class="nav-btn">HVAC Systems</a>
        <a href="#fire" class="nav-btn">Fire Protection</a>
        <a href="#site" class="nav-btn">Site Development</a>
        <a href="#structural" class="nav-btn">Structural Design</a>
        <a href="#estimation" class="nav-btn">Estimation & Cost</a>
        <a href="#mep" class="nav-btn">MEP Integration</a>
        <a href="#project-management" class="nav-btn">Project Management</a>
        <a href="#country" class="nav-btn">Country Specific</a>
    </div>

    <!-- Module Sections -->
    <div class="all-modules-container">
        <!-- Civil Engineering -->
        <div class="module-group" id="civil">
            <div class="module-title">
                <h2>Civil Construction</h2>
            </div>
            <div class="calculator-grid">
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Concrete</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/civil/concrete/concrete-volume.php'); ?>">Concrete Volume</a></li>
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/civil/concrete/rebar-calculation.php'); ?>">Rebar Calculation</a></li>
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/civil/concrete/concrete-mix.php'); ?>">Concrete Mix</a></li>
                    </ul>
                </div>
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Brickwork</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/civil/brickwork/brick-quantity.php'); ?>">Brick Quantity</a></li>
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/civil/brickwork/mortar-ratio.php'); ?>">Mortar Ratio</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Electrical -->
        <div class="module-group" id="electrical">
            <div class="module-title">
                <h2>Electrical Works</h2>
            </div>
            <div class="calculator-grid">
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Load & Sizing</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/load-calculation/total-load.php'); ?>">Total Load</a></li>
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/wire-sizing/wire-gauge.php'); ?>">Wire Gauge</a></li>
                    </ul>
                </div>
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Analysis</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/voltage-drop/drop-calculator.php'); ?>">Voltage Drop</a></li>
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/electrical/short-circuit/sc-current.php'); ?>">Short Circuit</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Plumbing -->
        <div class="module-group" id="plumbing">
            <div class="module-title">
                <h2>Plumbing Services</h2>
            </div>
            <div class="calculator-grid">
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Water Supply</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/plumbing/water_supply/pipe-sizing.php'); ?>">Pipe Sizing</a></li>
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/plumbing/water_supply/pump-head.php'); ?>">Pump Head</a></li>
                    </ul>
                </div>
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Drainage</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/plumbing/drainage/slope-calculator.php'); ?>">Slope Calculator</a></li>
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/plumbing/drainage/trap-sizing.php'); ?>">Trap Sizing</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- HVAC -->
        <div class="module-group" id="hvac">
            <div class="module-title">
                <h2>HVAC Systems</h2>
            </div>
            <div class="calculator-grid">
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Load Calculation</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/load-calculation/cooling-load.php'); ?>">Cooling Load</a></li>
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/load-calculation/heating-load.php'); ?>">Heating Load</a></li>
                    </ul>
                </div>
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Duct Sizing</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/duct-sizing/duct-dimension.php'); ?>">Duct Dimensions</a></li>
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/hvac/duct-sizing/static-pressure.php'); ?>">Static Pressure</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Fire Protection -->
        <div class="module-group" id="fire">
            <div class="module-title">
                <h2>Fire Protection</h2>
            </div>
            <div class="calculator-grid">
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Hydraulics</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/fire/hydraulics/pressure-drop.php'); ?>">Pressure Drop</a></li>
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/fire/sprinklers/spacing.php'); ?>">Sprinkler Spacing</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Site Development -->
        <div class="module-group" id="site">
            <div class="module-title">
                <h2>Site Development</h2>
            </div>
            <div class="calculator-grid">
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Earthwork</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/site/earthwork/cut-fill.php'); ?>">Cut & Fill</a></li>
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/site/surveying/leveling.php'); ?>">Leveling</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Structural Design -->
        <div class="module-group" id="structural">
            <div class="module-title">
                <h2>Structural Design</h2>
            </div>
            <div class="calculator-grid">
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Analysis</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/structural/beam-analysis/bending-moment.php'); ?>">Beam Analysis</a></li>
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/structural/column-design/axial-load.php'); ?>">Column Design</a></li>
                    </ul>
                </div>
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Components</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/structural/slab-design/slab-thickness.php'); ?>">Slab Design</a></li>
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/structural/foundation-design/footing-size.php'); ?>">Foundation</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Estimation & Cost -->
        <div class="module-group" id="estimation">
            <div class="module-title">
                <h2>Estimation & Cost</h2>
            </div>
            <div class="calculator-grid">
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Material</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/estimation/material-estimation/material-list.php'); ?>">Material Estimation</a></li>
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/estimation/quantity-takeoff/takeoff-summary.php'); ?>">Quantity Takeoff</a></li>
                    </ul>
                </div>
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Project Cost</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/estimation/cost-estimation/project-cost.php'); ?>">Project Budget</a></li>
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/estimation/project-financials/cash-flow.php'); ?>">Cash Flow</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- MEP Integration -->
        <div class="module-group" id="mep">
            <div class="module-title">
                <h2>MEP Integration</h2>
            </div>
            <div class="calculator-grid">
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Coordination</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/coordination/clash-detection.php'); ?>">Clash Detection</a></li>
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/integration/system-sync.php'); ?>">System Integration</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Project Management -->
        <div class="module-group" id="project-management">
            <div class="module-title">
                <h2>Project Management</h2>
            </div>
            <div class="calculator-grid">
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Scheduling</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/project-management/scheduling/critical-path.php'); ?>">Scheduling</a></li>
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/project-management/resources/allocation.php'); ?>">Resource Allocation</a></li>
                    </ul>
                </div>
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Management</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/project-management/financial/budget-tracking.php'); ?>">Project Financials</a></li>
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/project-management/analytics/kpi-summary.php'); ?>">Analytics</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Country Specific -->
        <div class="module-group" id="country">
            <div class="module-title">
                <h2>Country Specific</h2>
            </div>
            <div class="calculator-grid">
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Local Codes</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo app_base_url('modules/country/nepal/unit-calculator.php'); ?>">Nepal Unit Converter</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for 3D Tilt Effect -->
<?php
$tm = new \App\Services\ThemeManager();
?>
<script src="<?php echo $tm->themeUrl('assets/js/tilt.js'); ?>"></script>

<?php
//
?>
