<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Structural Engineering Toolkit';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('index.php')],
    ['name' => 'Structural Engineering', 'url' => '#']
];
require_once __DIR__ . '/includes/header.php';
?>

<link rel="stylesheet" href="assets/css/structural.css">

<div class="container">
    <div class="hero">
        <h1>Structural Engineering Toolkit</h1>
        <p>Advanced calculators for structural analysis and design.</p>
    </div>

    <!-- Sub-navigation for categories -->
    <div class="sub-nav" id="sub-nav">
        <a href="#beam" class="sub-nav-btn">Beam Analysis</a>
        <a href="#column" class="sub-nav-btn">Column Design</a>
        <a href="#steel" class="sub-nav-btn">Steel Structure</a>
        <a href="#foundation" class="sub-nav-btn">Foundation</a>
        <a href="#slab" class="sub-nav-btn">Slab Design</a>
        <a href="#loads" class="sub-nav-btn">Load Analysis</a>
        <a href="#reinforce" class="sub-nav-btn">Reinforcement</a>
        <a href="#reports" class="sub-nav-btn">Reports</a>
    </div>

    <div class="category-grid">
        <!-- Beam Analysis -->
        <div id="beam" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-ruler-horizontal category-icon"></i>
                <div class="category-title">
                    <h3>Beam Analysis</h3>
                    <p>Beam design and load analysis tools.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="modules/structural/beam-analysis/beam-design.php"><span>Beam Design</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/beam-analysis/beam-load-combination.php"><span>Load Combination</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/beam-analysis/cantilever-beam.php"><span>Cantilever Beam</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/beam-analysis/continuous-beam.php"><span>Continuous Beam</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/beam-analysis/simply-supported-beam.php"><span>Simply Supported Beam</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Column Design -->
        <div id="column" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-columns category-icon"></i>
                <div class="category-title">
                    <h3>Column Design</h3>
                    <p>Column analysis and design tools.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="modules/structural/column-design/biaxial-column.php"><span>Biaxial Column</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/column-design/column-footing-link.php"><span>Column-Footing Link</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/column-design/long-column.php"><span>Long Column</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/column-design/short-column.php"><span>Short Column</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/column-design/steel-column-design.php"><span>Steel Column Design</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Steel Structure -->
        <div id="steel" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-industry category-icon"></i>
                <div class="category-title">
                    <h3>Steel Structure</h3>
                    <p>Steel structural components and connections.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="modules/structural/steel-structure/connection-design.php"><span>Connection Design</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/steel-structure/purlin-design.php"><span>Purlin Design</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/steel-structure/steel-base-plate.php"><span>Base Plate Design</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/steel-structure/steel-beam-design.php"><span>Steel Beam Design</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/steel-structure/steel-truss-analysis.php"><span>Truss Analysis</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Foundation Design -->
        <div id="foundation" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-university category-icon"></i>
                <div class="category-title">
                    <h3>Foundation Design</h3>
                    <p>Foundation analysis and design tools.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="modules/structural/foundation-design/combined-footing.php"><span>Combined Footing</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/foundation-design/foundation-pressure.php"><span>Foundation Pressure</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/foundation-design/isolated-footing.php"><span>Isolated Footing</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/foundation-design/pile-foundation.php"><span>Pile Foundation</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/foundation-design/raft-foundation.php"><span>Raft Foundation</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Slab Design -->
        <div id="slab" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-square category-icon"></i>
                <div class="category-title">
                    <h3>Slab Design</h3>
                    <p>Slab analysis and design tools.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="modules/structural/slab-design/flat-slab.php"><span>Flat Slab Design</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/slab-design/one-way-slab.php"><span>One Way Slab</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/slab-design/two-way-slab.php"><span>Two Way Slab</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/slab-design/waffle-slab.php"><span>Waffle Slab</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/slab-design/slab-load-calculation.php"><span>Slab Load Calculator</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Load Analysis -->
        <div id="loads" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-weight-hanging category-icon"></i>
                <div class="category-title">
                    <h3>Load Analysis</h3>
                    <p>Structural load calculation tools.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="modules/structural/load-analysis/dead-load.php"><span>Dead Load</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/load-analysis/live-load.php"><span>Live Load</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/load-analysis/wind-load.php"><span>Wind Load</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/load-analysis/seismic-load.php"><span>Seismic Load</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/load-analysis/load-combination.php"><span>Load Combinations</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Reinforcement -->
        <div id="reinforce" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-hashtag category-icon"></i>
                <div class="category-title">
                    <h3>Reinforcement</h3>
                    <p>Reinforcement design and detailing tools.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="modules/structural/reinforcement/bar-bending-schedule.php"><span>Bar Bending Schedule</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/reinforcement/detailing-drawing.php"><span>Detailing Drawing</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/reinforcement/rebar-anchorage.php"><span>Rebar Anchorage</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/reinforcement/reinforcement-optimizer.php"><span>Reinforcement Optimizer</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/reinforcement/stirrup-design.php"><span>Stirrup Design</span> <i class="fas fa-arrow-right"></i></a></li>
            </ul>
        </div>

        <!-- Reports -->
        <div id="reports" class="category-card category-section">
            <div class="category-header">
                <i class="fas fa-file-alt category-icon"></i>
                <div class="category-title">
                    <h3>Reports</h3>
                    <p>Generate detailed structural reports and summaries.</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item"><a href="modules/structural/reports/beam-report.php"><span>Beam Report</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/reports/column-report.php"><span>Column Report</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/reports/foundation-report.php"><span>Foundation Report</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/reports/full-structure-summary.php"><span>Full Structure Summary</span> <i class="fas fa-arrow-right"></i></a></li>
                <li class="tool-item"><a href="modules/structural/reports/load-analysis-summary.php"><span>Load Analysis Summary</span> <i class="fas fa-arrow-right"></i></a></li>
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
