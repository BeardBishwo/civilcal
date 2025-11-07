<?php
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Estimation Suite';
$breadcrumb = [
	['name' => 'Home', 'url' => app_base_url('index.php')],
	['name' => 'Estimation', 'url' => '#']
];
require_once __DIR__ . '/includes/header.php';
?>

<link rel="stylesheet" href="assets/css/estimation.css">

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
		<div id="quantity" class="category-card category-section">
			<div class="category-header">
				<i class="fas fa-ruler-combined category-icon"></i>
				<div class="category-title">
					<h3>Quantity Takeoff</h3>
					<p>Concrete, brickwork, plastering, flooring and more.</p>
				</div>
			</div>
			<ul class="tool-list">
				<li class="tool-item"><a href="modules/estimation/quantity-takeoff/concrete-quantity.php"><span>Concrete Quantity</span> <i class="fas fa-arrow-right"></i></a></li>
				<li class="tool-item"><a href="modules/estimation/quantity-takeoff/brickwork-quantity.php"><span>Brickwork Quantity</span> <i class="fas fa-arrow-right"></i></a></li>
				<li class="tool-item"><a href="modules/estimation/quantity-takeoff/plaster-quantity.php"><span>Plaster Quantity</span> <i class="fas fa-arrow-right"></i></a></li>
				<li class="tool-item"><a href="modules/estimation/quantity-takeoff/flooring-quantity.php"><span>Flooring Quantity</span> <i class="fas fa-arrow-right"></i></a></li>
				<li class="tool-item"><a href="modules/estimation/quantity-takeoff/paint-quantity.php"><span>Paint Quantity</span> <i class="fas fa-arrow-right"></i></a></li>
				<li class="tool-item"><a href="modules/estimation/quantity-takeoff/formwork-quantity.php"><span>Formwork Quantity</span> <i class="fas fa-arrow-right"></i></a></li>
				<li class="tool-item"><a href="modules/estimation/quantity-takeoff/rebar-quantity.php"><span>Rebar Quantity</span> <i class="fas fa-arrow-right"></i></a></li>
			</ul>
		</div>

		<!-- Material Estimation -->
		<div id="materials" class="category-card category-section">
			<div class="category-header">
				<i class="fas fa-boxes category-icon"></i>
				<div class="category-title">
					<h3>Material Estimation</h3>
					<p>Calculate cement, sand, aggregates, adhesives and paints.</p>
				</div>
			</div>
			<ul class="tool-list">
				<li class="tool-item"><a href="modules/estimation/material-estimation/concrete-materials.php"><span>Concrete Materials</span> <i class="fas fa-arrow-right"></i></a></li>
				<li class="tool-item"><a href="modules/estimation/material-estimation/masonry-materials.php"><span>Masonry Materials</span> <i class="fas fa-arrow-right"></i></a></li>
				<li class="tool-item"><a href="modules/estimation/material-estimation/plaster-materials.php"><span>Plaster Materials</span> <i class="fas fa-arrow-right"></i></a></li>
				<li class="tool-item"><a href="modules/estimation/material-estimation/tile-materials.php"><span>Tile Materials</span> <i class="fas fa-arrow-right"></i></a></li>
				<li class="tool-item"><a href="modules/estimation/material-estimation/paint-materials.php"><span>Paint Materials</span> <i class="fas fa-arrow-right"></i></a></li>
			</ul>
		</div>

		<!-- Cost Estimation -->
		<div id="cost" class="category-card category-section">
			<div class="category-header">
				<i class="fas fa-calculator category-icon"></i>
				<div class="category-title">
					<h3>Cost Estimation</h3>
					<p>BOQ, rate analysis and project cost summaries.</p>
				</div>
			</div>
			<ul class="tool-list">
				<li class="tool-item"><a href="modules/estimation/cost-estimation/item-rate-analysis.php"><span>Item Rate Analysis</span> <i class="fas fa-arrow-right"></i></a></li>
				<li class="tool-item"><a href="modules/estimation/cost-estimation/boq-preparation.php"><span>BOQ Preparation</span> <i class="fas fa-arrow-right"></i></a></li>
				<li class="tool-item"><a href="modules/estimation/cost-estimation/project-cost-summary.php"><span>Project Cost Summary</span> <i class="fas fa-arrow-right"></i></a></li>
				<li class="tool-item"><a href="modules/estimation/cost-estimation/contingency-overheads.php"><span>Contingency & Overheads</span> <i class="fas fa-arrow-right"></i></a></li>
			</ul>
		</div>

		<!-- Labor Estimation -->
		<div id="labor" class="category-card category-section">
			<div class="category-header">
				<i class="fas fa-users category-icon"></i>
				<div class="category-title">
					<h3>Labor Estimation</h3>
					<p>Manpower, productivity and labor cost calculators.</p>
				</div>
			</div>
			<ul class="tool-list">
				<li class="tool-item"><a href="modules/estimation/labor-estimation/labor-rate-analysis.php"><span>Labor Rate Analysis</span> <i class="fas fa-arrow-right"></i></a></li>
				<li class="tool-item"><a href="modules/estimation/labor-estimation/manpower-requirement.php"><span>Manpower Requirement</span> <i class="fas fa-arrow-right"></i></a></li>
				<li class="tool-item"><a href="modules/estimation/labor-estimation/labor-hour-calculation.php"><span>Labor Hour Calculation</span> <i class="fas fa-arrow-right"></i></a></li>
			</ul>
		</div>

		<!-- Equipment Estimation -->
		<div id="equipment" class="category-card category-section">
			<div class="category-header">
				<i class="fas fa-truck-loading category-icon"></i>
				<div class="category-title">
					<h3>Equipment</h3>
					<p>Equipment rates, usage and fuel consumption.</p>
				</div>
			</div>
			<ul class="tool-list">
				<li class="tool-item"><a href="modules/estimation/equipment-estimation/equipment-hourly-rate.php"><span>Equipment Hourly Rate</span> <i class="fas fa-arrow-right"></i></a></li>
				<li class="tool-item"><a href="modules/estimation/equipment-estimation/machinery-usage.php"><span>Machinery Usage</span> <i class="fas fa-arrow-right"></i></a></li>
				<li class="tool-item"><a href="modules/estimation/equipment-estimation/fuel-consumption.php"><span>Fuel Consumption</span> <i class="fas fa-arrow-right"></i></a></li>
			</ul>
		</div>

		<!-- Project Financials -->
		<div id="financials" class="category-card category-section">
			<div class="category-header">
				<i class="fas fa-chart-line category-icon"></i>
				<div class="category-title">
					<h3>Financials</h3>
					<p>Cashflow, profitability, NPV/IRR and payback analysis.</p>
				</div>
			</div>
			<ul class="tool-list">
				<li class="tool-item"><a href="modules/estimation/project-financials/cash-flow-analysis.php"><span>Cash Flow Analysis</span> <i class="fas fa-arrow-right"></i></a></li>
				<li class="tool-item"><a href="modules/estimation/project-financials/profit-loss-analysis.php"><span>Profit & Loss</span> <i class="fas fa-arrow-right"></i></a></li>
				<li class="tool-item"><a href="modules/estimation/project-financials/npv-irr-analysis.php"><span>NPV / IRR Analysis</span> <i class="fas fa-arrow-right"></i></a></li>
			</ul>
		</div>

		<!-- Tender & Bidding -->
		<div id="tender" class="category-card category-section">
			<div class="category-header">
				<i class="fas fa-file-signature category-icon"></i>
				<div class="category-title">
					<h3>Tender & Bidding</h3>
					<p>Bid comparison, sheets and pre-bid analysis tools.</p>
				</div>
			</div>
			<ul class="tool-list">
				<li class="tool-item"><a href="modules/estimation/tender-bidding/bid-price-comparison.php"><span>Bid Price Comparison</span> <i class="fas fa-arrow-right"></i></a></li>
				<li class="tool-item"><a href="modules/estimation/tender-bidding/bid-sheet-generator.php"><span>Bid Sheet Generator</span> <i class="fas fa-arrow-right"></i></a></li>
			</ul>
		</div>

		<!-- Reports -->
		<div id="reports" class="category-card category-section">
			<div class="category-header">
				<i class="fas fa-file-alt category-icon"></i>
				<div class="category-title">
					<h3>Reports</h3>
					<p>BOQ, material, labor and equipment reports.</p>
				</div>
			</div>
			<ul class="tool-list">
				<li class="tool-item"><a href="modules/estimation/reports/summary-report.php"><span>Summary Report</span> <i class="fas fa-arrow-right"></i></a></li>
				<li class="tool-item"><a href="modules/estimation/reports/detailed-boq-report.php"><span>Detailed BOQ</span> <i class="fas fa-arrow-right"></i></a></li>
				<li class="tool-item"><a href="modules/estimation/reports/financial-dashboard.php"><span>Financial Dashboard</span> <i class="fas fa-arrow-right"></i></a></li>
			</ul>
		</div>
	</div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
	const subNav = document.getElementById("sub-nav");
	const subNavOffsetTop = subNav.offsetTop;
	const body = document.body;

    // Track section visibility and update active state
    const sections = document.querySelectorAll('.category-section');
    const navBtns = document.querySelectorAll('.sub-nav-btn');
    
    const updateActiveSection = () => {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 150;
            const sectionHeight = section.offsetHeight;
            if (pageYOffset >= sectionTop && pageYOffset < sectionTop + sectionHeight) {
                current = section.getAttribute('id');
            }
        });
        
        navBtns.forEach(btn => {
            btn.classList.remove('active');
            if (btn.getAttribute('href').substring(1) === current) {
                btn.classList.add('active');
            }
        });
    };

    // Smooth scrolling for sub-navigation links with active state
    document.querySelectorAll('.sub-nav-btn').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if(targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 100,
                    behavior: 'smooth'
                });
                targetElement.classList.add('highlight');
                setTimeout(() => { targetElement.classList.remove('highlight'); }, 2000);
                
                // Update active state
                navBtns.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });

    // Sticky sub-navigation and track scroll position
    window.addEventListener("scroll", function() {
        if (window.pageYOffset >= subNavOffsetTop) {
            body.classList.add("sticky-nav");
        } else {
            body.classList.remove("sticky-nav");
        }
        updateActiveSection();
    });

	// Toggle tool lists
	const categoryCards = document.querySelectorAll('.category-card');
	categoryCards.forEach(card => {
		card.classList.add('active');
		card.addEventListener('click', (e) => {
			if (e.target.closest('a')) return;
			card.classList.toggle('active');
		});
	});
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
