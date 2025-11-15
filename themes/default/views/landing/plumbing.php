<?php
$page_title = 'Plumbing Engineering Toolkit';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Plumbing Engineering', 'url' => '#']
];
?>

<?php if (!function_exists('load_theme_css')) { require_once __DIR__ . '/../partials/theme-helpers.php'; } ?>
<?php load_theme_css('plumbing.css'); ?>

<div class="container">
    <div class="hero">
        <h1>Plumbing Engineering Toolkit</h1>
        <p>Professional calculators and reference tools for plumbing engineers and technicians.</p>
    </div>

    <!-- Sub-navigation for categories -->
    <div class="sub-nav" id="sub-nav">
        <a href="#pipeSizing" class="sub-nav-btn">Pipe Sizing</a>
        <a href="#waterSupply" class="sub-nav-btn">Water Supply</a>
        <a href="#drainage" class="sub-nav-btn">Drainage</a>
        <a href="#pressure" class="sub-nav-btn">Pressure Loss</a>
        <a href="#hotWater" class="sub-nav-btn">Hot Water</a>
        <a href="#stormwater" class="sub-nav-btn">Stormwater</a>
        <a href="#fixtures" class="sub-nav-btn">Fixture Units</a>
    </div>

    <div class="category-grid">
        <!-- Pipe Sizing Section -->
        <div id="pipeSizing" class="category-card">
            <div class="category-header">
                <i class="fas fa-pipe category-icon"></i>
                <div class="category-title">
                    <h3>Pipe Sizing</h3>
                    <p>Calculate pipe diameters, flow rates, and expansion.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo app_base_url('modules/plumbing/pipe_sizing/water-pipe-sizing.php'); ?>" class="tool-item"><span>Water Pipe Sizing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/plumbing/pipe_sizing/gas-pipe-sizing.php'); ?>" class="tool-item"><span>Gas Pipe Sizing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/plumbing/pipe_sizing/pipe-flow-capacity.php'); ?>" class="tool-item"><span>Pipe Flow Capacity</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/plumbing/pipe_sizing/expansion-loop-sizing.php'); ?>" class="tool-item"><span>Expansion Loop Sizing</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Water Supply Section -->
        <div id="waterSupply" class="category-card">
            <div class="category-header">
                <i class="fas fa-tint category-icon"></i>
                <div class="category-title">
                    <h3>Water Supply</h3>
                    <p>Water demand, storage, and pumping calculations.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo app_base_url('modules/plumbing/water_supply/water-demand-calculation.php'); ?>" class="tool-item"><span>Water Demand Calculation</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/plumbing/water_supply/storage-tank-sizing.php'); ?>" class="tool-item"><span>Storage Tank Sizing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/plumbing/water_supply/pump-sizing.php'); ?>" class="tool-item"><span>Pump Sizing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/plumbing/water_supply/water-hammer-calculation.php'); ?>" class="tool-item"><span>Water Hammer Calculation</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Drainage Section -->
        <div id="drainage" class="category-card">
            <div class="category-header">
                <i class="fas fa-water category-icon"></i>
                <div class="category-title">
                    <h3>Drainage</h3>
                    <p>Sanitary and storm water drainage calculations.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo app_base_url('modules/plumbing/drainage/drainage-pipe-sizing.php'); ?>" class="tool-item"><span>Drain Pipe Sizing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/plumbing/drainage/storm-drainage.php'); ?>" class="tool-item"><span>Storm Water Drainage</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/plumbing/drainage/vent-pipe-sizing.php'); ?>" class="tool-item"><span>Vent Pipe Sizing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/plumbing/drainage/grease-trap-sizing.php'); ?>" class="tool-item"><span>Grease Trap Sizing</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Pressure Loss Section -->
        <div id="pressure" class="category-card">
            <div class="category-header">
                <i class="fas fa-gauge-high category-icon"></i>
                <div class="category-title">
                    <h3>Pressure Loss</h3>
                    <p>Friction loss, fitting loss, and system pressure.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo app_base_url('modules/plumbing/water_supply/pressure-loss.php'); ?>" class="tool-item"><span>Pipe Friction Loss</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/plumbing/hot_water/heat-loss-calculation.php'); ?>" class="tool-item"><span>Heat Loss Calculation</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/plumbing/water_supply/main-isolation-valve.php'); ?>" class="tool-item"><span>Isolation Valve Sizing</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Hot Water Section -->
        <div id="hotWater" class="category-card">
            <div class="category-header">
                <i class="fas fa-fire category-icon"></i>
                <div class="category-title">
                    <h3>Hot Water</h3>
                    <p>Hot water systems, recirculation, and safety.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo app_base_url('modules/plumbing/hot_water/water-heater-sizing.php'); ?>" class="tool-item"><span>Water Heater Sizing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/plumbing/hot_water/recirculation-loop.php'); ?>" class="tool-item"><span>Recirculation Loop</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/plumbing/hot_water/safety-valve.php'); ?>" class="tool-item"><span>Safety Valve Sizing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/plumbing/hot_water/storage-tank-sizing.php'); ?>" class="tool-item"><span>Hot Water Storage</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Stormwater Section -->
        <div id="stormwater" class="category-card">
            <div class="category-header">
                <i class="fas fa-cloud-rain category-icon"></i>
                <div class="category-title">
                    <h3>Stormwater</h3>
                    <p>Rainwater management and drainage systems.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo app_base_url('modules/plumbing/stormwater/gutter-sizing.php'); ?>" class="tool-item"><span>Gutter Sizing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/plumbing/stormwater/downpipe-sizing.php'); ?>" class="tool-item"><span>Downpipe Sizing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/plumbing/stormwater/stormwater-storage.php'); ?>" class="tool-item"><span>Stormwater Storage</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/plumbing/stormwater/pervious-area.php'); ?>" class="tool-item"><span>Pervious Area</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Fixture Units Section -->
        <div id="fixtures" class="category-card">
            <div class="category-header">
                <i class="fas fa-toilet category-icon"></i>
                <div class="category-title">
                    <h3>Fixture Units</h3>
                    <p>Fixture unit calculations and pipe sizing.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo app_base_url('modules/plumbing/fixtures/fixture-unit-calculation.php'); ?>" class="tool-item"><span>Fixture Unit Calculation</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/plumbing/fixtures/shower-sizing.php'); ?>" class="tool-item"><span>Shower Sizing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/plumbing/fixtures/sink-sizing.php'); ?>" class="tool-item"><span>Sink Sizing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo app_base_url('modules/plumbing/fixtures/toilet-flow.php'); ?>" class="tool-item"><span>Toilet Flow</span> <i class="fas fa-arrow-right"></i></a>
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

