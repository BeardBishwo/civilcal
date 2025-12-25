<?php
$page_title = 'Structural Engineering Toolkit';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Structural Engineering', 'url' => '#']
];
?>

<?php if (!function_exists('load_theme_css')) { require_once __DIR__ . '/../partials/theme-helpers.php'; } ?>
<?php load_theme_css('structural.css'); ?>

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
        <div id="beam" class="category-card">
            <div class="category-header">
                <i class="fas fa-ruler-horizontal category-icon"></i>
                <div class="category-title">
                    <h3>Beam Analysis</h3>
                    <p>Beam design and load analysis tools.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('beam-design'); ?>" class="tool-item"><span>Beam Design</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('beam-load-combination'); ?>" class="tool-item"><span>Load Combination</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('cantilever-beam'); ?>" class="tool-item"><span>Cantilever Beam</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('continuous-beam'); ?>" class="tool-item"><span>Continuous Beam</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('simply-supported-beam'); ?>" class="tool-item"><span>Simply Supported Beam</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Column Design -->
        <div id="column" class="category-card">
            <div class="category-header">
                <i class="fas fa-columns category-icon"></i>
                <div class="category-title">
                    <h3>Column Design</h3>
                    <p>Column analysis and design tools.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('biaxial-column'); ?>" class="tool-item"><span>Biaxial Column</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('column-footing-link'); ?>" class="tool-item"><span>Column-Footing Link</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('long-column'); ?>" class="tool-item"><span>Long Column</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('short-column'); ?>" class="tool-item"><span>Short Column</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('steel-column-design'); ?>" class="tool-item"><span>Steel Column Design</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Steel Structure -->
        <div id="steel" class="category-card">
            <div class="category-header">
                <i class="fas fa-industry category-icon"></i>
                <div class="category-title">
                    <h3>Steel Structure</h3>
                    <p>Steel structural components and connections.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('connection-design'); ?>" class="tool-item"><span>Connection Design</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('purlin-design'); ?>" class="tool-item"><span>Purlin Design</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('steel-base-plate'); ?>" class="tool-item"><span>Base Plate Design</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('steel-beam-design'); ?>" class="tool-item"><span>Steel Beam Design</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('steel-truss-analysis'); ?>" class="tool-item"><span>Truss Analysis</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Foundation Design -->
        <div id="foundation" class="category-card">
            <div class="category-header">
                <i class="fas fa-university category-icon"></i>
                <div class="category-title">
                    <h3>Foundation Design</h3>
                    <p>Foundation analysis and design tools.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('combined-footing'); ?>" class="tool-item"><span>Combined Footing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('foundation-pressure'); ?>" class="tool-item"><span>Foundation Pressure</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('isolated-footing'); ?>" class="tool-item"><span>Isolated Footing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('pile-foundation'); ?>" class="tool-item"><span>Pile Foundation</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('raft-foundation'); ?>" class="tool-item"><span>Raft Foundation</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Slab Design -->
        <div id="slab" class="category-card">
            <div class="category-header">
                <i class="fas fa-square category-icon"></i>
                <div class="category-title">
                    <h3>Slab Design</h3>
                    <p>Slab analysis and design tools.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('flat-slab'); ?>" class="tool-item"><span>Flat Slab Design</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('one-way-slab'); ?>" class="tool-item"><span>One Way Slab</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('two-way-slab'); ?>" class="tool-item"><span>Two Way Slab</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('waffle-slab'); ?>" class="tool-item"><span>Waffle Slab</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('slab-load-calculation'); ?>" class="tool-item"><span>Slab Load Calculator</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Load Analysis -->
        <div id="loads" class="category-card">
            <div class="category-header">
                <i class="fas fa-weight-hanging category-icon"></i>
                <div class="category-title">
                    <h3>Load Analysis</h3>
                    <p>Structural load calculation tools.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('dead-load'); ?>" class="tool-item"><span>Dead Load</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('live-load'); ?>" class="tool-item"><span>Live Load</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('wind-load'); ?>" class="tool-item"><span>Wind Load</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('seismic-load'); ?>" class="tool-item"><span>Seismic Load</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('load-combination'); ?>" class="tool-item"><span>Load Combinations</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Reinforcement -->
        <div id="reinforce" class="category-card">
            <div class="category-header">
                <i class="fas fa-hashtag category-icon"></i>
                <div class="category-title">
                    <h3>Reinforcement</h3>
                    <p>Reinforcement design and detailing tools.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('bar-bending-schedule'); ?>" class="tool-item"><span>Bar Bending Schedule</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('detailing-drawing'); ?>" class="tool-item"><span>Detailing Drawing</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('rebar-anchorage'); ?>" class="tool-item"><span>Rebar Anchorage</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('reinforcement-optimizer'); ?>" class="tool-item"><span>Reinforcement Optimizer</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('stirrup-design'); ?>" class="tool-item"><span>Stirrup Design</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Reports -->
        <div id="reports" class="category-card">
            <div class="category-header">
                <i class="fas fa-file-alt category-icon"></i>
                <div class="category-title">
                    <h3>Reports</h3>
                    <p>Generate detailed structural reports and summaries.</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('beam-report'); ?>" class="tool-item"><span>Beam Report</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('column-report'); ?>" class="tool-item"><span>Column Report</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('foundation-report'); ?>" class="tool-item"><span>Foundation Report</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('full-structure-summary'); ?>" class="tool-item"><span>Full Structure Summary</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo \App\Helpers\UrlHelper::calculator('load-analysis-summary'); ?>" class="tool-item"><span>Load Analysis Summary</span> <i class="fas fa-arrow-right"></i></a>
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


