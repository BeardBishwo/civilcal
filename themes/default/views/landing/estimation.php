<?php
$page_title = 'Estimation Suite';
$breadcrumb = [
	['name' => 'Home', 'url' => app_base_url('/')],
	['name' => 'Estimation', 'url' => '#']
];
?>

<?php if (!function_exists('load_theme_css')) { require_once __DIR__ . '/../partials/theme-helpers.php'; } ?>
<?php load_theme_css('estimation.css'); ?>

<div class="container">
	<div class="hero">
		<h1>Estimation Suite</h1>
		<p>A professional set of estimation tools for quantity, material, cost, labor and financial analysis.</p>
	</div>

	<!-- Sub-navigation for categories -->
	<div class="sub-nav" id="sub-nav">
		<a href="#quantity" class="sub-nav-btn">Quantity Takeoff</a>
		<a href="#materials" class="sub-nav-btn">Material Estimation</a>
		<a href="#cost" class="sub-nav-btn">Cost Estimation</a>
		<a href="#labor" class="sub-nav-btn">Labor Estimation</a>
		<a href="#equipment" class="sub-nav-btn">Equipment</a>
		<a href="#financials" class="sub-nav-btn">Financials</a>
		<a href="#tender" class="sub-nav-btn">Tender & Bidding</a>
		<a href="#reports" class="sub-nav-btn">Reports</a>
	</div>

	<div class="category-grid">
		<!-- Quantity Takeoff -->
		<div id="quantity" class="category-card">
			<div class="category-header">
				<i class="fas fa-ruler-combined category-icon"></i>
				<div class="category-title">
					<h3>Quantity Takeoff</h3>
					<p>Concrete, brickwork, plastering, flooring and more.</p>
				</div>
			</div>
			<ul class="tool-list">
				<a href="<?php echo app_base_url('modules/estimation/quantity-takeoff/concrete-quantity.php'); ?>" class="tool-item"><span>Concrete Quantity</span> <i class="fas fa-arrow-right"></i></a>
				<a href="<?php echo app_base_url('modules/estimation/quantity-takeoff/brickwork-quantity.php'); ?>" class="tool-item"><span>Brickwork Quantity</span> <i class="fas fa-arrow-right"></i></a>
				<a href="<?php echo app_base_url('modules/estimation/quantity-takeoff/plaster-quantity.php'); ?>" class="tool-item"><span>Plaster Quantity</span> <i class="fas fa-arrow-right"></i></a>
				<a href="<?php echo app_base_url('modules/estimation/quantity-takeoff/flooring-quantity.php'); ?>" class="tool-item"><span>Flooring Quantity</span> <i class="fas fa-arrow-right"></i></a>
				<a href="<?php echo app_base_url('modules/estimation/quantity-takeoff/paint-quantity.php'); ?>" class="tool-item"><span>Paint Quantity</span> <i class="fas fa-arrow-right"></i></a>
				<a href="<?php echo app_base_url('modules/estimation/quantity-takeoff/formwork-quantity.php'); ?>" class="tool-item"><span>Formwork Quantity</span> <i class="fas fa-arrow-right"></i></a>
				<a href="<?php echo app_base_url('modules/estimation/quantity-takeoff/rebar-quantity.php'); ?>" class="tool-item"><span>Rebar Quantity</span> <i class="fas fa-arrow-right"></i></a>
			</ul>
		</div>

		<!-- Material Estimation -->
		<div id="materials" class="category-card">
			<div class="category-header">
				<i class="fas fa-boxes category-icon"></i>
				<div class="category-title">
					<h3>Material Estimation</h3>
					<p>Calculate cement, sand, aggregates, adhesives and paints.</p>
				</div>
			</div>
			<ul class="tool-list">
				<a href="<?php echo app_base_url('modules/estimation/material-estimation/concrete-materials.php'); ?>" class="tool-item"><span>Concrete Materials</span> <i class="fas fa-arrow-right"></i></a>
				<a href="<?php echo app_base_url('modules/estimation/material-estimation/masonry-materials.php'); ?>" class="tool-item"><span>Masonry Materials</span> <i class="fas fa-arrow-right"></i></a>
				<a href="<?php echo app_base_url('modules/estimation/material-estimation/plaster-materials.php'); ?>" class="tool-item"><span>Plaster Materials</span> <i class="fas fa-arrow-right"></i></a>
				<a href="<?php echo app_base_url('modules/estimation/material-estimation/tile-materials.php'); ?>" class="tool-item"><span>Tile Materials</span> <i class="fas fa-arrow-right"></i></a>
				<a href="<?php echo app_base_url('modules/estimation/material-estimation/paint-materials.php'); ?>" class="tool-item"><span>Paint Materials</span> <i class="fas fa-arrow-right"></i></a>
			</ul>
		</div>

		<!-- Cost Estimation -->
		<div id="cost" class="category-card">
			<div class="category-header">
				<i class="fas fa-calculator category-icon"></i>
				<div class="category-title">
					<h3>Cost Estimation</h3>
					<p>BOQ, rate analysis and project cost summaries.</p>
				</div>
			</div>
			<ul class="tool-list">
				<a href="<?php echo app_base_url('modules/estimation/cost-estimation/item-rate-analysis.php'); ?>" class="tool-item"><span>Item Rate Analysis</span> <i class="fas fa-arrow-right"></i></a>
				<a href="<?php echo app_base_url('modules/estimation/cost-estimation/boq-preparation.php'); ?>" class="tool-item"><span>BOQ Preparation</span> <i class="fas fa-arrow-right"></i></a>
				<a href="<?php echo app_base_url('modules/estimation/cost-estimation/project-cost-summary.php'); ?>" class="tool-item"><span>Project Cost Summary</span> <i class="fas fa-arrow-right"></i></a>
				<a href="<?php echo app_base_url('modules/estimation/cost-estimation/contingency-overheads.php'); ?>" class="tool-item"><span>Contingency & Overheads</span> <i class="fas fa-arrow-right"></i></a>
			</ul>
		</div>

		<!-- Labor Estimation -->
		<div id="labor" class="category-card">
			<div class="category-header">
				<i class="fas fa-users category-icon"></i>
				<div class="category-title">
					<h3>Labor Estimation</h3>
					<p>Manpower, productivity and labor cost calculators.</p>
				</div>
			</div>
			<ul class="tool-list">
				<a href="<?php echo app_base_url('modules/estimation/labor-estimation/labor-rate-analysis.php'); ?>" class="tool-item"><span>Labor Rate Analysis</span> <i class="fas fa-arrow-right"></i></a>
				<a href="<?php echo app_base_url('modules/estimation/labor-estimation/manpower-requirement.php'); ?>" class="tool-item"><span>Manpower Requirement</span> <i class="fas fa-arrow-right"></i></a>
				<a href="<?php echo app_base_url('modules/estimation/labor-estimation/labor-hour-calculation.php'); ?>" class="tool-item"><span>Labor Hour Calculation</span> <i class="fas fa-arrow-right"></i></a>
			</ul>
		</div>

		<!-- Equipment Estimation -->
		<div id="equipment" class="category-card">
			<div class="category-header">
				<i class="fas fa-truck-loading category-icon"></i>
				<div class="category-title">
					<h3>Equipment</h3>
					<p>Equipment rates, usage and fuel consumption.</p>
				</div>
			</div>
			<ul class="tool-list">
				<a href="<?php echo app_base_url('modules/estimation/equipment-estimation/equipment-hourly-rate.php'); ?>" class="tool-item"><span>Equipment Hourly Rate</span> <i class="fas fa-arrow-right"></i></a>
				<a href="<?php echo app_base_url('modules/estimation/equipment-estimation/machinery-usage.php'); ?>" class="tool-item"><span>Machinery Usage</span> <i class="fas fa-arrow-right"></i></a>
				<a href="<?php echo app_base_url('modules/estimation/equipment-estimation/fuel-consumption.php'); ?>" class="tool-item"><span>Fuel Consumption</span> <i class="fas fa-arrow-right"></i></a>
			</ul>
		</div>

		<!-- Project Financials -->
		<div id="financials" class="category-card">
			<div class="category-header">
				<i class="fas fa-chart-line category-icon"></i>
				<div class="category-title">
					<h3>Financials</h3>
					<p>Cashflow, profitability, NPV/IRR and payback analysis.</p>
				</div>
			</div>
			<ul class="tool-list">
				<a href="<?php echo app_base_url('modules/estimation/project-financials/cash-flow-analysis.php'); ?>" class="tool-item"><span>Cash Flow Analysis</span> <i class="fas fa-arrow-right"></i></a>
				<a href="<?php echo app_base_url('modules/estimation/project-financials/profit-loss-analysis.php'); ?>" class="tool-item"><span>Profit & Loss</span> <i class="fas fa-arrow-right"></i></a>
				<a href="<?php echo app_base_url('modules/estimation/project-financials/npv-irr-analysis.php'); ?>" class="tool-item"><span>NPV / IRR Analysis</span> <i class="fas fa-arrow-right"></i></a>
			</ul>
		</div>

		<!-- Tender & Bidding -->
		<div id="tender" class="category-card">
			<div class="category-header">
				<i class="fas fa-file-signature category-icon"></i>
				<div class="category-title">
					<h3>Tender & Bidding</h3>
					<p>Bid comparison, sheets and pre-bid analysis tools.</p>
				</div>
			</div>
			<ul class="tool-list">
				<a href="<?php echo app_base_url('modules/estimation/tender-bidding/bid-price-comparison.php'); ?>" class="tool-item"><span>Bid Price Comparison</span> <i class="fas fa-arrow-right"></i></a>
				<a href="<?php echo app_base_url('modules/estimation/tender-bidding/bid-sheet-generator.php'); ?>" class="tool-item"><span>Bid Sheet Generator</span> <i class="fas fa-arrow-right"></i></a>
			</ul>
		</div>

		<!-- Reports -->
		<div id="reports" class="category-card">
			<div class="category-header">
				<i class="fas fa-file-alt category-icon"></i>
				<div class="category-title">
					<h3>Reports</h3>
					<p>BOQ, material, labor and equipment reports.</p>
				</div>
			</div>
			<ul class="tool-list">
				<a href="<?php echo app_base_url('modules/estimation/reports/summary-report.php'); ?>" class="tool-item"><span>Summary Report</span> <i class="fas fa-arrow-right"></i></a>
				<a href="<?php echo app_base_url('modules/estimation/reports/detailed-boq-report.php'); ?>" class="tool-item"><span>Detailed BOQ</span> <i class="fas fa-arrow-right"></i></a>
				<a href="<?php echo app_base_url('modules/estimation/reports/financial-dashboard.php'); ?>" class="tool-item"><span>Financial Dashboard</span> <i class="fas fa-arrow-right"></i></a>
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


