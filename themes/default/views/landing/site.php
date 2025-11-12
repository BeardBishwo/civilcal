<?php
$page_title = 'Construction Site Tools';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('index.php')],
    ['name' => 'Site Tools', 'url' => '#']
];
require_once __DIR__ . '/includes/header.php';
?>

<link rel="stylesheet" href="<?php echo function_exists('app_base_url') ? app_base_url('assets/css/site.css') : 'assets/css/site.css'; ?>">

<div class="container">
    <div class="hero">
        <h1>Construction Site Tools</h1>
        <p>Specialized tools for construction site operations and field engineering</p>
    </div>

    <!-- Sub-navigation for categories -->
    <div class="sub-nav" id="sub-nav">
        <a href="#surveying" class="sub-nav-btn">Field Surveying</a>
        <a href="#earthwork" class="sub-nav-btn">Earthwork & Grading</a>
        <a href="#concrete" class="sub-nav-btn">Concrete Tools</a>
        <a href="#safety" class="sub-nav-btn">Site Safety</a>
        <a href="#productivity" class="sub-nav-btn">Productivity</a>
    </div>

    <div class="category-grid">
        <!-- Field Surveying Section -->
        <div id="surveying" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-ruler-combined category-icon"></i>
                <div class="category-title">
                    <h3>Field Surveying</h3>
                    <p>Tools for field layout, staking, and surveying calculations.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="modules/site/surveying/slope-staking.php"><span>Slope Staking Calculator</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/site/surveying/batter-boards.php"><span>Batter Board Setup</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/site/surveying/horizontal-curve-staking.php"><span>Horizontal Curve Staking</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/site/surveying/grade-rod.php"><span>Grade Rod Calculator</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Earthwork & Grading Section -->
        <div id="earthwork" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-mountain category-icon"></i>
                <div class="category-title">
                    <h3>Earthwork & Grading</h3>
                    <p>Calculators for excavation, earthwork, and volume calculations.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="modules/site/earthwork/swelling-shrinkage.php"><span>Swelling & Shrinkage</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/site/earthwork/equipment-production.php"><span>Equipment Production</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/site/earthwork/cut-fill-balancing.php"><span>Cut/Fill Balancing</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/site/earthwork/slope-paving.php"><span>Slope Paving Calculator</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Concrete Field Tools Section -->
        <div id="concrete" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-cube category-icon"></i>
                <div class="category-title">
                    <h3>Concrete Field Tools</h3>
                    <p>Specialized calculators for concrete placement and testing.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="modules/site/concrete-tools/temperature-control.php"><span>Temperature Control</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/site/concrete-tools/yardage-adjustments.php"><span>Yardage Adjustments</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/site/concrete-tools/placement-rate.php"><span>Placement Rate Calculator</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/site/concrete-tools/testing-requirements.php"><span>Testing Requirements</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Site Safety Section -->
        <div id="safety" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-shield-alt category-icon"></i>
                <div class="category-title">
                    <h3>Site Safety</h3>
                    <p>Safety calculators and planning tools for construction sites.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="modules/site/safety/fall-protection.php"><span>Fall Protection Planning</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/site/safety/trench-safety.php"><span>Trench Safety Calculator</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/site/safety/crane-setup.php"><span>Crane Setup Calculator</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/site/safety/evacuation-planning.php"><span>Evacuation Planning</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Productivity Section -->
        <div id="productivity" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-tachometer-alt category-icon"></i>
                <div class="category-title">
                    <h3>Productivity Tools</h3>
                    <p>Analysis tools for labor, equipment, and schedule productivity.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="modules/site/productivity/labor-productivity.php"><span>Labor Productivity</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/site/productivity/equipment-utilization.php"><span>Equipment Utilization</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/site/productivity/schedule-compression.php"><span>Schedule Compression</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/site/productivity/cost-productivity.php"><span>Cost Productivity Analysis</span> <i class="fas fa-arrow-right"></i></a></li>
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
