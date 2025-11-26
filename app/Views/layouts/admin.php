<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Bishwo Calculator - Admin Panel'; ?></title>
    <meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --sidebar-width: 250px;
            --header-height: 60px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        /* Header Styles */
        .admin-header {
            background: linear-gradient(135deg, var(--primary-color), #1a2530);
            height: var(--header-height);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .logo {
            font-weight: 700;
            font-size: 1.5rem;
            color: white;
        }

        .search-box {
            max-width: 400px;
        }

        /* Sidebar Styles */
        .admin-sidebar {
            width: var(--sidebar-width);
            background: white;
            height: calc(100vh - var(--header-height));
            position: fixed;
            top: var(--header-height);
            left: 0;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .sidebar-collapsed .admin-sidebar {
            width: 60px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            border-bottom: 1px solid #f0f0f0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .sidebar-menu a:hover {
            background-color: #f8f9fa;
            color: var(--secondary-color);
        }

        .sidebar-menu a.active {
            background-color: var(--secondary-color);
            color: white;
        }

        .sidebar-menu .bi {
            width: 20px;
            margin-right: 10px;
        }

        .sidebar-collapsed .sidebar-menu span {
            display: none;
        }

        /* Main Content */
        .admin-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 20px;
            transition: all 0.3s ease;
        }

        .sidebar-collapsed .admin-content {
            margin-left: 60px;
        }

        /* Card Styles */
        .stat-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card .card-body {
            padding: 20px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        /* Quick Actions */
        .quick-actions .btn {
            margin: 5px;
            border-radius: 20px;
        }
    </style>
</head>

<body class="<?php echo $_COOKIE['sidebar_collapsed'] ?? ''; ?>">
    <!-- Header -->
    <header class="admin-header">
        <div class="container-fluid h-100">
            <div class="row h-100 align-items-center">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-sm btn-outline-light me-3" id="sidebarToggle">
                            <i class="bi bi-list"></i>
                        </button>
                        <div class="logo">Bishwo Calculator</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="search-box mx-auto">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" placeholder="Search calculators, users, modules...">
                            <button class="btn btn-sm btn-outline-light" type="button">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="d-flex justify-content-end align-items-center">
                    </div>
                </div>
            </div>
        </div>
        </div>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <ul class="sidebar-menu">
            <li><a href="<?= app_base_url('/admin/dashboard') ?>"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a></li>
            <li><a href="<?= app_base_url('/admin/users') ?>"><i class="bi bi-people"></i><span>Users</span></a></li>
            <li><a href="<?= app_base_url('/admin/modules') ?>"><i class="bi bi-grid-3x3-gap"></i><span>Modules</span></a></li>
            <li><a href="<?= app_base_url('/admin/analytics') ?>"><i class="bi bi-graph-up"></i><span>Analytics</span></a></li>
            <li><a href="<?= app_base_url('/admin/settings') ?>"><i class="bi bi-gear"></i><span>Settings</span></a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="admin-content">
        <?php echo $content; ?>
    </main>

    <!-- Bootstrap & Custom JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');

            // Save preference in cookie
            const isCollapsed = document.body.classList.contains('sidebar-collapsed');
            document.cookie = `sidebar_collapsed=${isCollapsed ? 'sidebar-collapsed' : ''}; path=/; max-age=31536000`;
        });

        // Initialize Charts
        function initCharts() {
            // User Growth Chart
            const userCtx = document.getElementById('userGrowthChart');
            if (userCtx) {
                new Chart(userCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'New Users',
                            data: [65, 59, 80, 81, 56, 72],
                            borderColor: '#3498db',
                            tension: 0.4,
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        }
                    }
                });
            }

            // Calculator Usage Chart
            const usageCtx = document.getElementById('calculatorUsageChart');
            if (usageCtx) {
                new Chart(usageCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Civil', 'Electrical', 'Structural', 'HVAC', 'Plumbing'],
                        datasets: [{
                            label: 'Usage Count',
                            data: [1250, 980, 756, 543, 432],
                            backgroundColor: '#2ecc71'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        }
                    }
                });
            }
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', initCharts);
    </script>
</body>

</html>
