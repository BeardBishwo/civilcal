<?php
$page_title = 'MEP Engineering Toolkit';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'MEP Engineering', 'url' => '#']
];
?>

<?php if (!function_exists('load_theme_css')) { require_once __DIR__ . '/../partials/theme-helpers.php'; } ?>
<?php load_theme_css('mep.css'); ?>

<div class="container">
    <div class="hero">
        <h1>MEP Engineering Toolkit</h1>
        <p>A comprehensive suite of calculators for mechanical, electrical, and plumbing engineering professionals.</p>
    </div>

    <!-- Sub-navigation for categories -->
    <div class="sub-nav" id="sub-nav">
        <a href="#electrical" class="sub-nav-btn">Electrical</a>
        <a href="#mechanical" class="sub-nav-btn">Mechanical</a>
        <a href="#plumbing" class="sub-nav-btn">Plumbing</a>
        <a href="#fire-protection" class="sub-nav-btn">Fire Protection</a>
        <a href="#energy-efficiency" class="sub-nav-btn">Energy Efficiency</a>
        <a href="#coordination" class="sub-nav-btn">Coordination</a>
        <a href="#cost-management" class="sub-nav-btn">Cost Management</a>
        <a href="#reports-documentation" class="sub-nav-btn">Reports</a>
        <a href="#data-utilities" class="sub-nav-btn">Data & Utilities</a>
        <a href="#integration" class="sub-nav-btn">Integration</a>
    </div>

    <div class="category-grid">
        <!-- Electrical Section -->
        <div id="electrical" class="category-card">
            <div class="category-header">
                <i class="fas fa-bolt category-icon"></i>
                <div class="category-title">
                    <h3>Electrical</h3>
                    <p>Tools for electrical system design and analysis.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('conduit-sizing'); ?>" class="tool-item"><span>Conduit Sizing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('earthing-system'); ?>" class="tool-item"><span>Earthing System</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('emergency-power'); ?>" class="tool-item"><span>Emergency Power</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('lighting-layout'); ?>" class="tool-item"><span>Lighting Layout</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('mep-electrical-summary'); ?>" class="tool-item"><span>MEP Electrical Summary</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('panel-schedule'); ?>" class="tool-item"><span>Panel Schedule</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('transformer-sizing'); ?>" class="tool-item"><span>Transformer Sizing</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Mechanical Section -->
        <div id="mechanical" class="category-card">
            <div class="category-header">
                <i class="fas fa-cogs category-icon"></i>
                <div class="category-title">
                    <h3>Mechanical</h3>
                    <p>Calculators for mechanical systems and components.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('chilled-water-piping'); ?>" class="tool-item"><span>Chilled Water Piping</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('equipment-database'); ?>" class="tool-item"><span>Equipment Database</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('hvac-duct-sizing'); ?>" class="tool-item"><span>HVAC Duct Sizing</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Plumbing Section -->
        <div id="plumbing" class="category-card">
            <div class="category-header">
                <i class="fas fa-faucet category-icon"></i>
                <div class="category-title">
                    <h3>Plumbing</h3>
                    <p>Tools for plumbing and water systems.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('drainage-system'); ?>" class="tool-item"><span>Drainage System</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('plumbing-fixture-count'); ?>" class="tool-item"><span>Plumbing Fixture Count</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('pump-selection'); ?>" class="tool-item"><span>Pump Selection</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('storm-water'); ?>" class="tool-item"><span>Storm Water</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('water-supply'); ?>" class="tool-item"><span>Water Supply</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('water-tank-sizing'); ?>" class="tool-item"><span>Water Tank Sizing</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Fire Protection Section -->
        <div id="fire-protection" class="category-card">
            <div class="category-header">
                <i class="fas fa-fire-extinguisher category-icon"></i>
                <div class="category-title">
                    <h3>Fire Protection</h3>
                    <p>Calculators for fire safety and protection systems.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('fire-hydrant-system'); ?>" class="tool-item"><span>Fire Hydrant System</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('fire-pump-sizing'); ?>" class="tool-item"><span>Fire Pump Sizing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('fire-safety-zoning'); ?>" class="tool-item"><span>Fire Safety Zoning</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('fire-tank-sizing'); ?>" class="tool-item"><span>Fire Tank Sizing</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Energy Efficiency Section -->
        <div id="energy-efficiency" class="category-card">
            <div class="category-header">
                <i class="fas fa-leaf category-icon"></i>
                <div class="category-title">
                    <h3>Energy Efficiency</h3>
                    <p>Tools for optimizing energy consumption.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('energy-consumption'); ?>" class="tool-item"><span>Energy Consumption</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('green-rating'); ?>" class="tool-item"><span>Green Rating</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('hvac-efficiency'); ?>" class="tool-item"><span>HVAC Efficiency</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('solar-system'); ?>" class="tool-item"><span>Solar System</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('water-efficiency'); ?>" class="tool-item"><span>Water Efficiency</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Coordination Section -->
        <div id="coordination" class="category-card">
            <div class="category-header">
                <i class="fas fa-project-diagram category-icon"></i>
                <div class="category-title">
                    <h3>Coordination</h3>
                    <p>Tools for MEP coordination and clash detection.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('bim-export'); ?>" class="tool-item"><span>BIM Export</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('clash-detection'); ?>" class="tool-item"><span>Clash Detection</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('coordination-map'); ?>" class="tool-item"><span>Coordination Map</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('space-allocation'); ?>" class="tool-item"><span>Space Allocation</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('system-priority'); ?>" class="tool-item"><span>System Priority</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Cost Management Section -->
        <div id="cost-management" class="category-card">
            <div class="category-header">
                <i class="fas fa-dollar-sign category-icon"></i>
                <div class="category-title">
                    <h3>Cost Management</h3>
                    <p>Tools for cost estimation and management.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('boq-generator'); ?>" class="tool-item"><span>BOQ Generator</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('cost-optimization'); ?>" class="tool-item"><span>Cost Optimization</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('cost-summary'); ?>" class="tool-item"><span>Cost Summary</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('material-takeoff'); ?>" class="tool-item"><span>Material Takeoff</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('vendor-pricing'); ?>" class="tool-item"><span>Vendor Pricing</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Reports & Documentation Section -->
        <div id="reports-documentation" class="category-card">
            <div class="category-header">
                <i class="fas fa-file-alt category-icon"></i>
                <div class="category-title">
                    <h3>Reports & Documentation</h3>
                    <p>Generate reports and documentation for MEP systems.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('clash-detection-report'); ?>" class="tool-item"><span>Clash Detection Report</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('equipment-schedule'); ?>" class="tool-item"><span>Equipment Schedule</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('load-summary'); ?>" class="tool-item"><span>Load Summary</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('mep-summary'); ?>" class="tool-item"><span>MEP Summary</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('pdf-export'); ?>" class="tool-item"><span>PDF Export</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Data & Utilities Section -->
        <div id="data-utilities" class="category-card">
            <div class="category-header">
                <i class="fas fa-database category-icon"></i>
                <div class="category-title">
                    <h3>Data & Utilities</h3>
                    <p>Utilities for data management and conversion.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('api-endpoints'); ?>" class="tool-item"><span>API Endpoints</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('input-validator'); ?>" class="tool-item"><span>Input Validator</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('material-database'); ?>" class="tool-item"><span>Material Database</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('mep-config'); ?>" class="tool-item"><span>MEP Config</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('permissions'); ?>" class="tool-item"><span>Permissions</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('unit-converter'); ?>" class="tool-item"><span>Unit Converter</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Integration Section -->
        <div id="integration" class="category-card">
            <div class="category-header">
                <i class="fas fa-plug category-icon"></i>
                <div class="category-title">
                    <h3>Integration</h3>
                    <p>Tools for integrating with other platforms.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('autocad-layer-mapper'); ?>" class="tool-item"><span>AutoCAD Layer Mapper</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('bim-integration'); ?>" class="tool-item"><span>BIM Integration</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('cloud-sync'); ?>" class="tool-item"><span>Cloud Sync</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('project-sharing'); ?>" class="tool-item"><span>Project Sharing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('revit-plugin'); ?>" class="tool-item"><span>Revit Plugin</span> <i class="fas fa-arrow-right"></i></a>
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


