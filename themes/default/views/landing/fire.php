<?php
$page_title = 'Fire Protection Engineering Toolkit';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Fire Protection Engineering', 'url' => '#']
];
?>

<?php if (!function_exists('load_theme_css')) { require_once __DIR__ . '/../partials/theme-helpers.php'; } ?>
<?php load_theme_css('fire.css'); ?>

<div class="container">
    <div class="hero">
        <h1>Fire Protection Engineering Toolkit</h1>
        <p>Professional calculators and reference tools for fire protection engineers and designers.</p>
    </div>

    <!-- Sub-navigation for categories -->
    <div class="sub-nav" id="sub-nav">
        <a href="#sprinklers" class="sub-nav-btn">Sprinkler Systems</a>
        <a href="#hydraulics" class="sub-nav-btn">Hydraulic Calculations</a>
        <a href="#standpipes" class="sub-nav-btn">Standpipe Systems</a>
        <a href="#hazard" class="sub-nav-btn">Hazard Classification</a>
        <a href="#fire-pumps" class="sub-nav-btn">Fire Pumps</a>
    </div>

    <div class="category-grid">
        <!-- Sprinkler Systems Section -->
        <div id="sprinklers" class="category-card">
            <div class="category-header">
                <i class="fas fa-spray-can category-icon"></i>
                <div class="category-title">
                    <h3>Sprinkler Systems</h3>
                    <p>Comprehensive sprinkler system calculations for layout, discharge, and pipe sizing.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('sprinkler-layout'); ?>" class="tool-item"><span>Sprinkler Layout</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('discharge-calculations'); ?>" class="tool-item"><span>Sprinkler Discharge</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('pipe-sizing'); ?>" class="tool-item"><span>Pipe Sizing</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Hydraulic Calculations Section -->
        <div id="hydraulics" class="category-card">
            <div class="category-header">
                <i class="fas fa-tint category-icon"></i>
                <div class="category-title">
                    <h3>Hydraulic Calculations</h3>
                    <p>Advanced hydraulic calculations using Hazen-Williams formula for pressure loss.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('hazen-williams'); ?>" class="tool-item"><span>Hazen-Williams Calculator</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Standpipe Systems Section -->
        <div id="standpipes" class="category-card">
            <div class="category-header">
                <i class="fas fa-building category-icon"></i>
                <div class="category-title">
                    <h3>Standpipe Systems</h3>
                    <p>Complete standpipe system analysis including classification and pressure calculations.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('standpipe-classification'); ?>" class="tool-item"><span>Standpipe Classification</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('hose-demand'); ?>" class="tool-item"><span>Hose Demand</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('pressure-calculations'); ?>" class="tool-item"><span>Pressure Calculations</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Hazard Classification Section -->
        <div id="hazard" class="category-card">
            <div class="category-header">
                <i class="fas fa-exclamation-triangle category-icon"></i>
                <div class="category-title">
                    <h3>Hazard Classification</h3>
                    <p>Determine hazard classifications for sprinkler system design and requirements.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('occupancy-assessment'); ?>" class="tool-item"><span>Occupancy Assessment</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('commodity-classification'); ?>" class="tool-item"><span>Commodity Classification</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('design-density'); ?>" class="tool-item"><span>Design Density</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Fire Pumps Section -->
        <div id="fire-pumps" class="category-card">
            <div class="category-header">
                <i class="fas fa-pump-medical category-icon"></i>
                <div class="category-title">
                    <h3>Fire Pumps</h3>
                    <p>Comprehensive fire pump calculations including sizing and driver power requirements.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('pump-sizing'); ?>" class="tool-item"><span>Pump Sizing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('driver-power'); ?>" class="tool-item"><span>Driver Power</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('jockey-pump'); ?>" class="tool-item"><span>Jockey Pump</span> <i class="fas fa-arrow-right"></i></a>
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

