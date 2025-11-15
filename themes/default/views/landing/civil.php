<?php
$page_title = 'Civil Engineering Toolkit';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Civil Engineering', 'url' => '#']
];
?>

<?php if (!function_exists('load_theme_css')) { require_once __DIR__ . '/../partials/theme-helpers.php'; } ?>
<?php load_theme_css('civil.css'); ?>

<div class="container">
    <div class="hero">
        <h1>Civil Engineering Toolkit</h1>
        <p>A comprehensive suite of calculators for civil engineering professionals.</p>
    </div>

    <!-- Sub-navigation for categories -->
    <div class="sub-nav" id="sub-nav">
        <a href="#concrete" class="sub-nav-btn">Concrete</a>
        <a href="#earthwork" class="sub-nav-btn">Earthwork</a>
        <a href="#structural" class="sub-nav-btn">Structural</a>
        <a href="#brickwork" class="sub-nav-btn">Brickwork</a>
    </div>

    <div class="category-grid">
        <!-- Concrete Section -->
        <div id="concrete" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-cube category-icon"></i>
                <div class="category-title">
                    <h3>Concrete</h3>
                    <p>All things concrete, from volume to strength.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/civil/concrete/concrete-volume.php'); ?>"><span>Concrete Volume</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/civil/concrete/rebar-calculation.php'); ?>"><span>Rebar Calculation</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/civil/concrete/concrete-mix.php'); ?>"><span>Concrete Mix Design</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/civil/concrete/concrete-strength.php'); ?>"><span>Concrete Strength</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Earthwork Section -->
        <div id="earthwork" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-mountain category-icon"></i>
                <div class="category-title">
                    <h3>Earthwork</h3>
                    <p>Calculators for excavation, slope, and volume.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/civil/earthwork/cut-and-fill-volume.php'); ?>"><span>Cut & Fill Volume</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/civil/earthwork/slope-calculation.php'); ?>"><span>Slope Calculation</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/civil/earthwork/excavation-volume.php'); ?>"><span>Excavation Volume</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Structural Section -->
        <div id="structural" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-building category-icon"></i>
                <div class="category-title">
                    <h3>Structural</h3>
                    <p>Analyze beams, columns, slabs, and foundations.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/civil/structural/beam-load-capacity.php'); ?>"><span>Beam Load Capacity</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/civil/structural/column-design.php'); ?>"><span>Column Design</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/civil/structural/slab-design.php'); ?>"><span>Slab Design</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/civil/structural/foundation-design.php'); ?>"><span>Foundation Design</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Brickwork Section -->
        <div id="brickwork" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-bricks category-icon"></i>
                <div class="category-title">
                    <h3>Brickwork</h3>
                    <p>Tools for brick and mortar calculations.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/civil/brickwork/brick-quantity.php'); ?>"><span>Brick Quantity</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/civil/brickwork/mortar-ratio.php'); ?>"><span>Mortar Ratio</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/civil/brickwork/plastering-estimator.php'); ?>"><span>Plastering Estimator</span> <i class="fas fa-arrow-right"></i></a></li>
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

