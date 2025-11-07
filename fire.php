<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Fire Protection Toolkit';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('index.php')],
    ['name' => 'Fire Protection', 'url' => '#']
];
require_once __DIR__ . '/includes/header.php';
?>

<link rel="stylesheet" href="assets/css/fire.css">

<div class="container">
    <div class="hero">
        <h1>Fire Protection Toolkit</h1>
        <p>A comprehensive suite of calculators for fire protection engineering professionals.</p>
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
        <div id="sprinklers" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-spray-can category-icon"></i>
                <div class="category-title">
                    <h3>Sprinkler Systems</h3>
                    <p>Comprehensive sprinkler system calculations for layout, discharge, and pipe sizing.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="modules/fire/sprinklers/sprinkler-layout.php"><span>Sprinkler Layout</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/fire/sprinklers/discharge-calculations.php"><span>Sprinkler Discharge</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/fire/sprinklers/pipe-sizing.php"><span>Pipe Sizing</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Hydraulic Calculations Section -->
        <div id="hydraulics" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-tint category-icon"></i>
                <div class="category-title">
                    <h3>Hydraulic Calculations</h3>
                    <p>Advanced hydraulic calculations using Hazen-Williams formula for pressure loss.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="modules/fire/hydraulics/hazen-williams.php"><span>Hazen-Williams Calculator</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Standpipe Systems Section -->
        <div id="standpipes" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-building category-icon"></i>
                <div class="category-title">
                    <h3>Standpipe Systems</h3>
                    <p>Complete standpipe system analysis including classification and pressure calculations.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="modules/fire/standpipes/standpipe-classification.php"><span>Standpipe Classification</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/fire/standpipes/hose-demand.php"><span>Hose Demand</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/fire/standpipes/pressure-calculations.php"><span>Pressure Calculations</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Hazard Classification Section -->
        <div id="hazard" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-exclamation-triangle category-icon"></i>
                <div class="category-title">
                    <h3>Hazard Classification</h3>
                    <p>Determine hazard classifications for sprinkler system design and requirements.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="modules/fire/hazard-classification/occupancy-assessment.php"><span>Occupancy Assessment</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/fire/hazard-classification/commodity-classification.php"><span>Commodity Classification</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/fire/hazard-classification/design-density.php"><span>Design Density</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Fire Pumps Section -->
        <div id="fire-pumps" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-pump-medical category-icon"></i>
                <div class="category-title">
                    <h3>Fire Pumps</h3>
                    <p>Comprehensive fire pump calculations including sizing and driver power requirements.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="modules/fire/fire-pumps/pump-sizing.php"><span>Pump Sizing</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/fire/fire-pumps/driver-power.php"><span>Driver Power</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/fire/fire-pumps/jockey-pump.php"><span>Jockey Pump</span> <i class="fas fa-arrow-right"></i></a></li>
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
require_once __DIR__ . '/includes/footer.php';
?>
