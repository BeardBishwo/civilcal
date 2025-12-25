<?php
$page_title = 'Site Development Engineering Toolkit';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Site Development Engineering', 'url' => '#']
];
?>

<?php if (!function_exists('load_theme_css')) { require_once __DIR__ . '/../partials/theme-helpers.php'; } ?>
<?php load_theme_css('site.css'); ?>

<div class="container">
    <div class="hero">
        <h1>Site Development Engineering Toolkit</h1>
        <p>Professional calculators and reference tools for site development and construction professionals.</p>
    </div>

    <!-- Sub-navigation for categories -->
    <div class="sub-nav" id="sub-nav">
        <a href="#surveying" class="sub-nav-btn">Field Surveying</a>
        <a href="#earthwork" class="sub-nav-btn">Earthwork & Grading</a>
        <a href="#concrete" class="sub-nav-btn">Concrete Field Tools</a>
        <a href="#safety" class="sub-nav-btn">Site Safety</a>
        <a href="#productivity" class="sub-nav-btn">Productivity Tools</a>
    </div>

    <div class="category-grid">
        <!-- Field Surveying Section -->
        <div id="surveying" class="category-card">
            <div class="category-header">
                <i class="fas fa-ruler-combined category-icon"></i>
                <div class="category-title">
                    <h3>Field Surveying</h3>
                    <p>Tools for field layout, staking, and surveying calculations.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('slope-staking'); ?>" class="tool-item"><span>Slope Staking Calculator</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('batter-boards'); ?>" class="tool-item"><span>Batter Board Setup</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('horizontal-curve-staking'); ?>" class="tool-item"><span>Horizontal Curve Staking</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('grade-rod'); ?>" class="tool-item"><span>Grade Rod Calculator</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Earthwork & Grading Section -->
        <div id="earthwork" class="category-card">
            <div class="category-header">
                <i class="fas fa-mountain category-icon"></i>
                <div class="category-title">
                    <h3>Earthwork & Grading</h3>
                    <p>Calculators for excavation, earthwork, and volume calculations.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('swelling-shrinkage'); ?>" class="tool-item"><span>Swelling & Shrinkage</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('equipment-production'); ?>" class="tool-item"><span>Equipment Production</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('cut-fill-balancing'); ?>" class="tool-item"><span>Cut/Fill Balancing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('slope-paving'); ?>" class="tool-item"><span>Slope Paving Calculator</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Concrete Field Tools Section -->
        <div id="concrete" class="category-card">
            <div class="category-header">
                <i class="fas fa-cube category-icon"></i>
                <div class="category-title">
                    <h3>Concrete Field Tools</h3>
                    <p>Specialized calculators for concrete placement and testing.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('temperature-control'); ?>" class="tool-item"><span>Temperature Control</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('yardage-adjustments'); ?>" class="tool-item"><span>Yardage Adjustments</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('placement-rate'); ?>" class="tool-item"><span>Placement Rate Calculator</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('testing-requirements'); ?>" class="tool-item"><span>Testing Requirements</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Site Safety Section -->
        <div id="safety" class="category-card">
            <div class="category-header">
                <i class="fas fa-shield-alt category-icon"></i>
                <div class="category-title">
                    <h3>Site Safety</h3>
                    <p>Safety calculators and planning tools for construction sites.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('fall-protection'); ?>" class="tool-item"><span>Fall Protection Planning</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('trench-safety'); ?>" class="tool-item"><span>Trench Safety Calculator</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('crane-setup'); ?>" class="tool-item"><span>Crane Setup Calculator</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('evacuation-planning'); ?>" class="tool-item"><span>Evacuation Planning</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Productivity Section -->
        <div id="productivity" class="category-card">
            <div class="category-header">
                <i class="fas fa-tachometer-alt category-icon"></i>
                <div class="category-title">
                    <h3>Productivity Tools</h3>
                    <p>Analysis tools for labor, equipment, and schedule productivity.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('labor-productivity'); ?>" class="tool-item"><span>Labor Productivity</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('equipment-utilization'); ?>" class="tool-item"><span>Equipment Utilization</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('schedule-compression'); ?>" class="tool-item"><span>Schedule Compression</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('cost-productivity'); ?>" class="tool-item"><span>Cost Productivity Analysis</span> <i class="fas fa-arrow-right"></i></a>
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

<?php ?>

