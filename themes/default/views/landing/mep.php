<?php
$page_title = 'MEP Engineering Toolkit';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('index.php')],
    ['name' => 'MEP Engineering', 'url' => '#']
];
require_once dirname(__DIR__, 4) . '/includes/header.php';
?>

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
        <div id="electrical" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-bolt category-icon"></i>
                <div class="category-title">
                    <h3>Electrical</h3>
                    <p>Tools for electrical system design and analysis.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/electrical/conduit-sizing.php'); ?>"><span>Conduit Sizing</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/electrical/earthing-system.php'); ?>"><span>Earthing System</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/electrical/emergency-power.php'); ?>"><span>Emergency Power</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/electrical/lighting-layout.php'); ?>"><span>Lighting Layout</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/electrical/mep-electrical-summary.php'); ?>"><span>MEP Electrical Summary</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/electrical/panel-schedule.php'); ?>"><span>Panel Schedule</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/electrical/transformer-sizing.php'); ?>"><span>Transformer Sizing</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Mechanical Section -->
        <div id="mechanical" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-cogs category-icon"></i>
                <div class="category-title">
                    <h3>Mechanical</h3>
                    <p>Calculators for mechanical systems and components.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/mechanical/chilled-water-piping.php'); ?>"><span>Chilled Water Piping</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/mechanical/equipment-database.php'); ?>"><span>Equipment Database</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/mechanical/hvac-duct-sizing.php'); ?>"><span>HVAC Duct Sizing</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Plumbing Section -->
        <div id="plumbing" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-faucet category-icon"></i>
                <div class="category-title">
                    <h3>Plumbing</h3>
                    <p>Tools for plumbing and water systems.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/plumbing/drainage-system.php'); ?>"><span>Drainage System</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/plumbing/plumbing-fixture-count.php'); ?>"><span>Plumbing Fixture Count</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/plumbing/pump-selection.php'); ?>"><span>Pump Selection</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/plumbing/storm-water.php'); ?>"><span>Storm Water</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/plumbing/water-supply.php'); ?>"><span>Water Supply</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/plumbing/water-tank-sizing.php'); ?>"><span>Water Tank Sizing</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Fire Protection Section -->
        <div id="fire-protection" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-fire-extinguisher category-icon"></i>
                <div class="category-title">
                    <h3>Fire Protection</h3>
                    <p>Calculators for fire safety and protection systems.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/fire-protection/fire-hydrant-system.php'); ?>"><span>Fire Hydrant System</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/fire-protection/fire-pump-sizing.php'); ?>"><span>Fire Pump Sizing</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/fire-protection/fire-safety-zoning.php'); ?>"><span>Fire Safety Zoning</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/fire-protection/fire-tank-sizing.php'); ?>"><span>Fire Tank Sizing</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Energy Efficiency Section -->
        <div id="energy-efficiency" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-leaf category-icon"></i>
                <div class="category-title">
                    <h3>Energy Efficiency</h3>
                    <p>Tools for optimizing energy consumption.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/energy-efficiency/energy-consumption.php'); ?>"><span>Energy Consumption</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/energy-efficiency/green-rating.php'); ?>"><span>Green Rating</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/energy-efficiency/hvac-efficiency.php'); ?>"><span>HVAC Efficiency</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/energy-efficiency/solar-system.php'); ?>"><span>Solar System</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/energy-efficiency/water-efficiency.php'); ?>"><span>Water Efficiency</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Coordination Section -->
        <div id="coordination" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-project-diagram category-icon"></i>
                <div class="category-title">
                    <h3>Coordination</h3>
                    <p>Tools for MEP coordination and clash detection.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/coordination/bim-export.php'); ?>"><span>BIM Export</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/coordination/clash-detection.php'); ?>"><span>Clash Detection</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/coordination/coordination-map.php'); ?>"><span>Coordination Map</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/coordination/space-allocation.php'); ?>"><span>Space Allocation</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/coordination/system-priority.php'); ?>"><span>System Priority</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Cost Management Section -->
        <div id="cost-management" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-dollar-sign category-icon"></i>
                <div class="category-title">
                    <h3>Cost Management</h3>
                    <p>Tools for cost estimation and management.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/cost-management/boq-generator.php'); ?>"><span>BOQ Generator</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/cost-management/cost-optimization.php'); ?>"><span>Cost Optimization</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/cost-management/cost-summary.php'); ?>"><span>Cost Summary</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/cost-management/material-takeoff.php'); ?>"><span>Material Takeoff</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/cost-management/vendor-pricing.php'); ?>"><span>Vendor Pricing</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Reports & Documentation Section -->
        <div id="reports-documentation" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-file-alt category-icon"></i>
                <div class="category-title">
                    <h3>Reports & Documentation</h3>
                    <p>Generate reports and documentation for MEP systems.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/reports-documentation/clash-detection-report.php'); ?>"><span>Clash Detection Report</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/reports-documentation/equipment-schedule.php'); ?>"><span>Equipment Schedule</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/reports-documentation/load-summary.php'); ?>"><span>Load Summary</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/reports-documentation/mep-summary.php'); ?>"><span>MEP Summary</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/reports-documentation/pdf-export.php'); ?>"><span>PDF Export</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Data & Utilities Section -->
        <div id="data-utilities" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-database category-icon"></i>
                <div class="category-title">
                    <h3>Data & Utilities</h3>
                    <p>Utilities for data management and conversion.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/data-utilities/api-endpoints.php'); ?>"><span>API Endpoints</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/data-utilities/input-validator.php'); ?>"><span>Input Validator</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/data-utilities/material-database.php'); ?>"><span>Material Database</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/data-utilities/mep-config.php'); ?>"><span>MEP Config</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/data-utilities/permissions.php'); ?>"><span>Permissions</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/data-utilities/unit-converter.php'); ?>"><span>Unit Converter</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Integration Section -->
        <div id="integration" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-plug category-icon"></i>
                <div class="category-title">
                    <h3>Integration</h3>
                    <p>Tools for integrating with other platforms.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/integration/autocad-layer-mapper.php'); ?>"><span>AutoCAD Layer Mapper</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/integration/bim-integration.php'); ?>"><span>BIM Integration</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/integration/cloud-sync.php'); ?>"><span>Cloud Sync</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/integration/project-sharing.php'); ?>"><span>Project Sharing</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="<?php echo app_base_url('modules/mep/integration/revit-plugin.php'); ?>"><span>Revit Plugin</span> <i class="fas fa-arrow-right"></i></a></li>
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

<?php
require_once dirname(__DIR__, 4) . '/includes/footer.php';
?>
