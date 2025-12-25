<?php
$page_title = 'Project Management Suite';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('/')],
    ['name' => 'Project Management', 'url' => '#']
];
?>

<?php if (!function_exists('load_theme_css')) { require_once __DIR__ . '/../partials/theme-helpers.php'; } ?>
<?php load_theme_css('management.css'); ?>

<div class="container">
    <div class="hero">
        <h1>Project Management Suite</h1>
        <p>Comprehensive tools for construction project planning, tracking, and control</p>
    </div>

    <div class="sub-nav">
        <a href="#dashboard" class="sub-nav-btn active">Dashboard</a>
        <a href="#scheduling" class="sub-nav-btn">Scheduling</a>
        <a href="#resources" class="sub-nav-btn">Resources</a>
        <a href="#financial" class="sub-nav-btn">Financial</a>
        <a href="#quality" class="sub-nav-btn">Quality</a>
        <a href="#documents" class="sub-nav-btn">Documents</a>
    </div>

    <div class="category-grid">
        <!-- Dashboard Module -->
        <div id="dashboard" class="category-card">
            <div class="category-header">
                <i class="fas fa-tachometer-alt category-icon"></i>
                <div class="category-title">
                    <h3>Project Dashboard</h3>
                    <p>Monitor and track project progress</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo function_exists('app_base_url') ? \App\Helpers\UrlHelper::calculator('project-overview') : 'modules/project-management/dashboard/project-overview.php'; ?>" class="tool-item"><span>Project Overview</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo function_exists('app_base_url') ? \App\Helpers\UrlHelper::calculator('gantt-chart') : 'modules/project-management/dashboard/gantt-chart.php'; ?>" class="tool-item"><span>Gantt Chart</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo function_exists('app_base_url') ? \App\Helpers\UrlHelper::calculator('milestone-tracker') : 'modules/project-management/dashboard/milestone-tracker.php'; ?>" class="tool-item"><span>Milestone Tracker</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Scheduling Module -->
        <div id="scheduling" class="category-card">
            <div class="category-header">
                <i class="fas fa-calendar-alt category-icon"></i>
                <div class="category-title">
                    <h3>Task & Scheduling</h3>
                    <p>Manage project timeline and tasks</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo function_exists('app_base_url') ? \App\Helpers\UrlHelper::calculator('create-task') : 'modules/project-management/scheduling/create-task.php'; ?>" class="tool-item"><span>Create Task</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo function_exists('app_base_url') ? \App\Helpers\UrlHelper::calculator('assign-task') : 'modules/project-management/scheduling/assign-task.php'; ?>" class="tool-item"><span>Assign Task</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo function_exists('app_base_url') ? \App\Helpers\UrlHelper::calculator('task-dependency') : 'modules/project-management/scheduling/task-dependency.php'; ?>" class="tool-item"><span>Task Dependencies</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Resources Module -->
        <div id="resources" class="category-card">
            <div class="category-header">
                <i class="fas fa-users category-icon"></i>
                <div class="category-title">
                    <h3>Resource Management</h3>
                    <p>Manage workforce and equipment</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo function_exists('app_base_url') ? \App\Helpers\UrlHelper::calculator('manpower-planning') : 'modules/project-management/resources/manpower-planning.php'; ?>" class="tool-item"><span>Manpower Planning</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo function_exists('app_base_url') ? \App\Helpers\UrlHelper::calculator('equipment-allocation') : 'modules/project-management/resources/equipment-allocation.php'; ?>" class="tool-item"><span>Equipment Allocation</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo function_exists('app_base_url') ? \App\Helpers\UrlHelper::calculator('material-tracking') : 'modules/project-management/resources/material-tracking.php'; ?>" class="tool-item"><span>Material Tracking</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Financial Module -->
        <div id="financial" class="category-card">
            <div class="category-header">
                <i class="fas fa-dollar-sign category-icon"></i>
                <div class="category-title">
                    <h3>Financial Management</h3>
                    <p>Track costs and budgets</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo function_exists('app_base_url') ? \App\Helpers\UrlHelper::calculator('budget-tracking') : 'modules/project-management/financial/budget-tracking.php'; ?>" class="tool-item"><span>Budget Tracking</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo function_exists('app_base_url') ? \App\Helpers\UrlHelper::calculator('cost-control') : 'modules/project-management/financial/cost-control.php'; ?>" class="tool-item"><span>Cost Control</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo function_exists('app_base_url') ? \App\Helpers\UrlHelper::calculator('forecast-analysis') : 'modules/project-management/financial/forecast-analysis.php'; ?>" class="tool-item"><span>Forecast Analysis</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Quality Module -->
        <div id="quality" class="category-card">
            <div class="category-header">
                <i class="fas fa-check-circle category-icon"></i>
                <div class="category-title">
                    <h3>Quality & Safety</h3>
                    <p>Manage quality control and safety</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo function_exists('app_base_url') ? \App\Helpers\UrlHelper::calculator('quality-checklist') : 'modules/project-management/quality/quality-checklist.php'; ?>" class="tool-item"><span>Quality Checklist</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo function_exists('app_base_url') ? \App\Helpers\UrlHelper::calculator('safety-incidents') : 'modules/project-management/quality/safety-incidents.php'; ?>" class="tool-item"><span>Safety Incidents</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo function_exists('app_base_url') ? \App\Helpers\UrlHelper::calculator('audit-reports') : 'modules/project-management/quality/audit-reports.php'; ?>" class="tool-item"><span>Audit Reports</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>

        <!-- Documents Module -->
        <div id="documents" class="category-card">
            <div class="category-header">
                <i class="fas fa-file-alt category-icon"></i>
                <div class="category-title">
                    <h3>Document Control</h3>
                    <p>Manage project documentation</p>
                </div>
            </div>
            <ul class="tool-list">
                <a href="<?php echo function_exists('app_base_url') ? \App\Helpers\UrlHelper::calculator('document-repository') : 'modules/project-management/documents/document-repository.php'; ?>" class="tool-item"><span>Document Repository</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo function_exists('app_base_url') ? \App\Helpers\UrlHelper::calculator('drawing-register') : 'modules/project-management/documents/drawing-register.php'; ?>" class="tool-item"><span>Drawing Register</span> <i class="fas fa-arrow-right"></i></a>
                <a href="<?php echo function_exists('app_base_url') ? \App\Helpers\UrlHelper::calculator('submittal-tracking') : 'modules/project-management/documents/submittal-tracking.php'; ?>" class="tool-item"><span>Submittal Tracking</span> <i class="fas fa-arrow-right"></i></a>
            </ul>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const subNav = document.querySelector('.sub-nav');
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

