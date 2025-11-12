<?php
// Homepage with 3D animated calculator tools
// Use default values since functions may not be available in view context
$settings = isset($settings) ? $settings : ['hero_background' => 'gradient'];
$meta = isset($meta) ? $meta : [
    'hero_heading' => 'Engineering Toolkit',
    'hero_subheading' => 'Professional Calculators for Modern Construction'
];

// Include header with CSS
require_once __DIR__ . '/partials/header.php';
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
        <a href="<?php echo app_base_url('landing/civil.php'); ?>" class="nav-btn">Civil Construction</a>
        <a href="<?php echo app_base_url('landing/plumbing.php'); ?>" class="nav-btn">Plumbing Services</a>
        <a href="<?php echo app_base_url('landing/hvac.php'); ?>" class="nav-btn">HVAC Systems</a>
        <a href="<?php echo app_base_url('landing/electrical.php'); ?>" class="nav-btn">Electrical Works</a>
        <a href="<?php echo app_base_url('landing/fire.php'); ?>" class="nav-btn">Fire Protection</a>
        <a href="<?php echo app_base_url('landing/site.php'); ?>" class="nav-btn">Site Development</a>
    </div>

    <!-- Calculator Module -->
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
                    <a href="<?php echo app_base_url('modules/civil/concrete/concrete-volume.php'); ?>">Concrete Volume</a>
                </li>
                <li class="tool-item">
                    <a href="<?php echo app_base_url('modules/civil/concrete/rebar-calculation.php'); ?>">Rebar Calculation</a>
                </li>
                <li class="tool-item">
                    <a href="<?php echo app_base_url('modules/civil/concrete/concrete-mix.php'); ?>">Concrete Mix Design</a>
                </li>
                <li class="tool-item">
                    <a href="<?php echo app_base_url('modules/civil/concrete/concrete-strength.php'); ?>">Concrete Strength</a>
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
                    <a href="<?php echo app_base_url('modules/civil/brickwork/brick-quantity.php'); ?>">Brick Quantity</a>
                </li>
                <li class="tool-item">
                    <a href="<?php echo app_base_url('modules/civil/brickwork/mortar-ratio.php'); ?>">Mortar Ratio</a>
                </li>
                <li class="tool-item">
                    <a href="<?php echo app_base_url('modules/civil/brickwork/plastering-estimator.php'); ?>">Plastering Estimator</a>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- JavaScript for 3D Tilt Effect -->
<?php
$tm = new \App\Services\ThemeManager();
?>
<script src="<?php echo $tm->themeUrl('assets/js/tilt.js'); ?>"></script>

<?php
// Include footer
require_once __DIR__ . '/partials/footer.php';
?>
