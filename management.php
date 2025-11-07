<?php
require_once 'includes/config.php';
require_once 'includes/header.php';
?>

<link rel="stylesheet" href="/aec-calculator/assets/css/management.css">

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
        <div class="category-card" id="dashboard">
            <div class="category-header">
                <div class="category-icon">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <div class="category-title">
                    <h3>Project Dashboard</h3>
                    <p>Monitor and track project progress</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item">
                    <a href="/aec-calculator/modules/project-management/dashboard/project-overview.php">
                        Project Overview
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <li class="tool-item">
                    <a href="/aec-calculator/modules/project-management/dashboard/gantt-chart.php">
                        Gantt Chart
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <li class="tool-item">
                    <a href="/aec-calculator/modules/project-management/dashboard/milestone-tracker.php">
                        Milestone Tracker
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Scheduling Module -->
        <div class="category-card" id="scheduling">
            <div class="category-header">
                <div class="category-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="category-title">
                    <h3>Task & Scheduling</h3>
                    <p>Manage project timeline and tasks</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item">
                    <a href="/aec-calculator/modules/project-management/scheduling/create-task.php">
                        Create Task
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <li class="tool-item">
                    <a href="/aec-calculator/modules/project-management/scheduling/assign-task.php">
                        Assign Task
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <li class="tool-item">
                    <a href="/aec-calculator/modules/project-management/scheduling/task-dependency.php">
                        Task Dependencies
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Resources Module -->
        <div class="category-card" id="resources">
            <div class="category-header">
                <div class="category-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="category-title">
                    <h3>Resource Management</h3>
                    <p>Manage workforce and equipment</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item">
                    <a href="/aec-calculator/modules/project-management/resources/manpower-planning.php">
                        Manpower Planning
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <li class="tool-item">
                    <a href="/aec-calculator/modules/project-management/resources/equipment-allocation.php">
                        Equipment Allocation
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <li class="tool-item">
                    <a href="/aec-calculator/modules/project-management/resources/material-tracking.php">
                        Material Tracking
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Financial Module -->
        <div class="category-card" id="financial">
            <div class="category-header">
                <div class="category-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="category-title">
                    <h3>Financial Management</h3>
                    <p>Track costs and budgets</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item">
                    <a href="/aec-calculator/modules/project-management/financial/budget-tracking.php">
                        Budget Tracking
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <li class="tool-item">
                    <a href="/aec-calculator/modules/project-management/financial/cost-control.php">
                        Cost Control
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <li class="tool-item">
                    <a href="/aec-calculator/modules/project-management/financial/forecast-analysis.php">
                        Forecast Analysis
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Quality Module -->
        <div class="category-card" id="quality">
            <div class="category-header">
                <div class="category-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="category-title">
                    <h3>Quality & Safety</h3>
                    <p>Manage quality control and safety</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item">
                    <a href="/aec-calculator/modules/project-management/quality/quality-checklist.php">
                        Quality Checklist
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <li class="tool-item">
                    <a href="/aec-calculator/modules/project-management/quality/safety-incidents.php">
                        Safety Incidents
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <li class="tool-item">
                    <a href="/aec-calculator/modules/project-management/quality/audit-reports.php">
                        Audit Reports
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Documents Module -->
        <div class="category-card" id="documents">
            <div class="category-header">
                <div class="category-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="category-title">
                    <h3>Document Control</h3>
                    <p>Manage project documentation</p>
                </div>
            </div>
            <ul class="tool-list">
                <li class="tool-item">
                    <a href="/aec-calculator/modules/project-management/documents/document-repository.php">
                        Document Repository
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <li class="tool-item">
                    <a href="/aec-calculator/modules/project-management/documents/drawing-register.php">
                        Drawing Register
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <li class="tool-item">
                    <a href="/aec-calculator/modules/project-management/documents/submittal-tracking.php">
                        Submittal Tracking
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Sticky navigation
    const subNav = document.querySelector('.sub-nav');
    const subNavTop = subNav.offsetTop;

    function handleScroll() {
        if (window.pageYOffset >= subNavTop) {
            document.body.classList.add('sticky-nav');
        } else {
            document.body.classList.remove('sticky-nav');
        }
    }

    window.addEventListener('scroll', handleScroll);

    // Smooth scroll to sections
    const navLinks = document.querySelectorAll('.sub-nav-btn');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            navLinks.forEach(link => link.classList.remove('active'));
            this.classList.add('active');

            targetElement.scrollIntoView({
                behavior: 'smooth'
            });
            
            // Highlight the target card
            targetElement.classList.add('highlight');
            setTimeout(() => {
                targetElement.classList.remove('highlight');
            }, 1500);
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>