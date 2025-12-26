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
        <div id="concrete" class="category-card">
            <div class="category-header">
                <i class="fas fa-cube category-icon"></i>
                <div class="category-title">
                    <h3>Concrete</h3>
                    <p>All things concrete, from volume to strength.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('concrete-volume'); ?>" class="tool-item"><span>Concrete Volume</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('rebar-calculation'); ?>" class="tool-item"><span>Rebar Calculation</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('formwork-quantity'); ?>" class="tool-item"><span>Formwork & Shuttering</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('concrete-mix'); ?>" class="tool-item"><span>Concrete Mix Design</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('concrete-strength'); ?>" class="tool-item"><span>Concrete Strength</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Earthwork Section -->
        <div id="earthwork" class="category-card">
            <div class="category-header">
                <i class="fas fa-mountain category-icon"></i>
                <div class="category-title">
                    <h3>Earthwork</h3>
                    <p>Calculators for excavation, slope, and volume.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('cut-and-fill-volume'); ?>" class="tool-item"><span>Cut & Fill Volume</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('slope-calculation'); ?>" class="tool-item"><span>Slope Calculation</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('excavation-volume'); ?>" class="tool-item"><span>Excavation Volume</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Structural Section -->
        <div id="structural" class="category-card">
            <div class="category-header">
                <i class="fas fa-building category-icon"></i>
                <div class="category-title">
                    <h3>Structural</h3>
                    <p>Analyze beams, columns, slabs, and foundations.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('beam-load-capacity'); ?>" class="tool-item"><span>Beam Load Capacity</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('column-design'); ?>" class="tool-item"><span>Column Design</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('slab-design'); ?>" class="tool-item"><span>Slab Design</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('foundation-design'); ?>" class="tool-item"><span>Foundation Design</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Brickwork Section -->
        <div id="brickwork" class="category-card">
            <div class="category-header">
                <i class="fas fa-bricks category-icon"></i>
                <div class="category-title">
                    <h3>Brickwork</h3>
                    <p>Tools for brick and mortar calculations.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('brick-quantity'); ?>" class="tool-item"><span>Brick Quantity</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('mortar-ratio'); ?>" class="tool-item"><span>Mortar Ratio</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('plastering-estimator'); ?>" class="tool-item"><span>Plastering Estimator</span> <i class="fas fa-arrow-right"></i></a>
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
