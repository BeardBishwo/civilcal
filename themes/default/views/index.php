<?php
// Homepage with 3D animated calculator tools
// Use default values since functions may not be available in view context
$settings = isset($settings) ? $settings : ['hero_background' => 'gradient'];
$meta = isset($meta) ? $meta : [
    'hero_heading' => 'Engineering Toolkit',
    'hero_subheading' => 'Professional Calculators for Modern Construction'
];

//
use App\Helpers\UrlHelper;
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
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('concrete-volume'); ?>">Concrete Volume</a></li>
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('rebar-calculation'); ?>">Rebar Calculation</a></li>
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('concrete-mix'); ?>">Concrete Mix</a></li>
                    </ul>
                </div>
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Brickwork</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('brick-quantity'); ?>">Brick Quantity</a></li>
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('mortar-ratio'); ?>">Mortar Ratio</a></li>
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
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('total-load'); ?>">Total Load</a></li>
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('wire-gauge'); ?>">Wire Gauge</a></li>
                    </ul>
                </div>
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Analysis</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('drop-calculator'); ?>">Voltage Drop</a></li>
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('sc-current'); ?>">Short Circuit</a></li>
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
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('pipe-sizing'); ?>">Pipe Sizing</a></li>
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('pump-head'); ?>">Pump Head</a></li>
                    </ul>
                </div>
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Drainage</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('slope-calculator'); ?>">Slope Calculator</a></li>
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('trap-sizing'); ?>">Trap Sizing</a></li>
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
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('cooling-load'); ?>">Cooling Load</a></li>
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('heating-load'); ?>">Heating Load</a></li>
                    </ul>
                </div>
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Duct Sizing</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('duct-dimension'); ?>">Duct Dimensions</a></li>
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('static-pressure'); ?>">Static Pressure</a></li>
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
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('pressure-drop'); ?>">Pressure Drop</a></li>
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('spacing'); ?>">Sprinkler Spacing</a></li>
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
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('cut-fill'); ?>">Cut & Fill</a></li>
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('leveling'); ?>">Leveling</a></li>
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
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('bending-moment'); ?>">Beam Analysis</a></li>
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('axial-load'); ?>">Column Design</a></li>
                    </ul>
                </div>
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Components</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('slab-thickness'); ?>">Slab Design</a></li>
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('footing-size'); ?>">Foundation</a></li>
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
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('material-list'); ?>">Material Estimation</a></li>
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('takeoff-summary'); ?>">Quantity Takeoff</a></li>
                    </ul>
                </div>
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Project Cost</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('project-cost'); ?>">Project Budget</a></li>
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('cash-flow'); ?>">Cash Flow</a></li>
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
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('clash-detection'); ?>">Clash Detection</a></li>
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('system-sync'); ?>">System Integration</a></li>
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
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('critical-path'); ?>">Scheduling</a></li>
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('allocation'); ?>">Resource Allocation</a></li>
                    </ul>
                </div>
                <div class="category-card" data-tilt>
                    <div class="category-header">
                        <h3>Management</h3>
                        <div class="separator"></div>
                    </div>
                    <ul class="tool-list">
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('budget-tracking'); ?>">Project Financials</a></li>
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('kpi-summary'); ?>">Analytics</a></li>
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
                        <li class="tool-item"><a href="<?php echo UrlHelper::calculator('unit-calculator'); ?>">Nepal Unit Converter</a></li>
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
