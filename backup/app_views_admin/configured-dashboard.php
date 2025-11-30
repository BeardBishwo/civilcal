<?php
/**
 * Configured Admin Dashboard
 * Fixed version that works with current Bishwo Calculator setup
 */

// Security check
if (!defined('ABSPATH')) {
    exit('Access denied');
}

// Get performance data for the dashboard
$performanceMonitor = $container->make('PerformanceMonitor');
$queryOptimizer = $container->make('QueryOptimizer');
$advancedCache = $container->make('AdvancedCache');

// Get statistics
$perfStats = $performanceMonitor->getAllStats();
$queryStats = $queryOptimizer->getQueryStats();
$cacheStats = $advancedCache->getStats();
$bottlenecks = $performanceMonitor->identifyBottlenecks();
$optimizationRecommendations = $queryOptimizer->getOptimizationRecommendations();

// Mock some basic stats for the main dashboard
$stats = [
    'total_users' => rand(150, 250),
    'total_calculations' => rand(10000, 15000),
    'active_modules' => rand(8, 12),
    'system_health' => rand(95, 100),
    'active_users' => rand(20, 50),
    'monthly_calculations' => rand(5000, 8000),
    'storage_used' => rand(45, 65)
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Bishwo Calculator</title>
    
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Admin CSS -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        
        /* Admin Layout */
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }
        
        .admin-sidebar {
            width: 250px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .admin-content {
            flex: 1;
            padding: 2rem;
            margin-left: 250px;
            width: calc(100% - 250px);
        }
        
        /* Sidebar Styles */
        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.25rem;
            font-weight: bold;
            color: #667eea;
        }
        
        .sidebar-menu ul {
            list-style: none;
            padding: 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 0.25rem;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #666;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: #667eea;
            color: white;
        }
        
        .menu-divider {
            margin: 1.5rem 0 0.5rem 0;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Main Content */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .page-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .page-header p {
            color: rgba(255, 255, 255, 0.8);
            margin-top: 0.5rem;
        }
        
        /* Statistics Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: transform 0.2s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem auto;
            font-size: 1.5rem;
            color: white;
        }
        
        .stat-users { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-calculations { background: linear-gradient(135deg, #10b981, #34d399); }
        .stat-modules { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
        .stat-health { background: linear-gradient(135deg, #06b6d4, #22d3ee); }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }
        
        .stat-change {
            color: #10b981;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        /* Main Grid */
        .main-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 1.75rem;
        }
        
        .card h5 {
            font-size: 1.125rem;
            font-weight: 600;
            margin: 0 0 1.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .overview-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .overview-item {
            text-align: center;
        }
        
        .overview-value {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .overview-label {
            color: #666;
            font-size: 0.875rem;
        }
        
        .quick-actions a {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
            margin-bottom: 0.75rem;
        }
        
        .quick-actions a:hover {
            background: rgba(0, 0, 0, 0.1);
            transform: translateX(5px);
        }
        
        .quick-actions i {
            font-size: 1.25rem;
            margin-top: 0.125rem;
        }
        
        .quick-actions strong {
            color: #333;
            font-size: 0.875rem;
            display: block;
            margin-bottom: 0.25rem;
        }
        
        .quick-actions small {
            color: #666;
            font-size: 0.75rem;
        }
        
        .users-color { color: #667eea; }
        .calculations-color { color: #10b981; }
        .modules-color { color: #f59e0b; }
        .health-color { color: #06b6d4; }
        
        .users-bg { background: linear-gradient(135deg, #667eea, #764ba2); }
        .calculations-bg { background: linear-gradient(135deg, #10b981, #34d399); }
        .modules-bg { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
        .health-bg { background: linear-gradient(135deg, #06b6d4, #22d3ee); }
        
        /* Status Bar */
        .status-bar {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
        }
        
        .status-title {
            font-size: 1rem;
            font-weight: 600;
            color: #10b981;
            margin: 0 0 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .status-text {
            color: #666;
            font-size: 0.875rem;
        }
        
        /* Performance Dashboard Link */
        .performance-link {
            margin-top: 2rem;
            text-align: center;
        }
        
        .btn-performance {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-performance:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 100%;
                position: relative;
                height: auto;
            }
            
            .admin-content {
                margin-left: 0;
                width: 100%;
                padding: 1rem;
            }
            
            .main-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i class="fas fa-calculator-alt"></i>
                    <span class="sidebar-logo-text"><?php echo htmlspecialchars(\App\Services\SettingsService::get('site_name', 'Admin Panel')); ?></span>
                </div>
            </div>
            
            <nav class="sidebar-menu">
                <ul>
                    <li class="active">
                        <a href="#">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="#">
                            <i class="fas fa-users"></i>
                            <span>Users</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="#">
                            <i class="fas fa-calculator"></i>
                            <span>Calculations</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="#">
                            <i class="fas fa-cubes"></i>
                            <span>Modules</span>
                        </a>
                    </li>
                    
                    <li class="menu-divider">
                        <span>Configuration</span>
                    </li>
                    
                    <li>
                        <a href="#">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="performance-dashboard.php">
                            <i class="fas fa-chart-line"></i>
                            <span>Performance</span>
                        </a>
                    </li>
                    
                    <li class="menu-divider">
                        <span>System</span>
                    </li>
                    
                    <li>
                        <a href="#">
                            <i class="fas fa-file-alt"></i>
                            <span>Logs</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="#">
                            <i class="fas fa-database"></i>
                            <span>Backup</span>
                        </a>
                    </li>
                    
                    <li>
                        <a href="#">
                            <i class="fas fa-heartbeat"></i>
                            <span>System Status</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="admin-content">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1>Admin Dashboard</h1>
                    <p>Welcome back! Here's an overview of your engineering calculator platform.</p>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <!-- Total Users -->
                <div class="stat-card">
                    <div class="stat-icon stat-users">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value users-color"><?php echo number_format($stats['total_users']); ?></div>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-change"><i class="fas fa-arrow-up"></i> +12% this month</div>
                </div>
                
                <!-- Calculations -->
                <div class="stat-card">
                    <div class="stat-icon stat-calculations">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="stat-value calculations-color"><?php echo number_format($stats['total_calculations']); ?></div>
                    <div class="stat-label">Calculations</div>
                    <div class="stat-change"><i class="fas fa-arrow-up"></i> +8% this month</div>
                </div>
                
                <!-- Active Modules -->
                <div class="stat-card">
                    <div class="stat-icon stat-modules">
                        <i class="fas fa-cubes"></i>
                    </div>
                    <div class="stat-value modules-color"><?php echo number_format($stats['active_modules']); ?></div>
                    <div class="stat-label">Active Modules</div>
                    <div class="stat-change"><i class="fas fa-check"></i> All operational</div>
                </div>
                
                <!-- System Health -->
                <div class="stat-card">
                    <div class="stat-icon stat-health">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div class="stat-value health-color"><?php echo number_format($stats['system_health'], 1); ?>%</div>
                    <div class="stat-label">System Health</div>
                    <div class="stat-change"><i class="fas fa-check-circle"></i> Excellent</div>
                </div>
            </div>
            
            <!-- Main Content Grid -->
            <div class="main-grid">
                <!-- System Overview -->
                <div class="card">
                    <h5><i class="fas fa-chart-bar"></i> System Overview</h5>
                    
                    <div class="overview-grid">
                        <div class="overview-item">
                            <h3 class="overview-value users-color"><?php echo number_format($stats['active_users']); ?></h3>
                            <p class="overview-label">Active Users</p>
                        </div>
                        <div class="overview-item">
                            <h3 class="overview-value calculations-color"><?php echo number_format($stats['monthly_calculations']); ?></h3>
                            <p class="overview-label">Monthly Calculations</p>
                        </div>
                        <div class="overview-item">
                            <h3 class="overview-value modules-color"><?php echo number_format($stats['storage_used']); ?>%</h3>
                            <p class="overview-label">Storage Used</p>
                        </div>
                    </div>
                    
                    <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.2);">
                        <h6 style="font-size: 0.875rem; font-weight: 600; margin: 0 0 1rem 0;">Recent Activity</h6>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem;">
                                <i class="fas fa-user-plus" style="color: #667eea;"></i>
                                <span style="font-size: 0.875rem;">New user registered</span>
                                <small style="color: #666; margin-left: auto; font-size: 0.75rem;">2 hours ago</small>
                            </li>
                            <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem;">
                                <i class="fas fa-cog" style="color: #10b981;"></i>
                                <span style="font-size: 0.875rem;">System settings updated</span>
                                <small style="color: #666; margin-left: auto; font-size: 0.75rem;">4 hours ago</small>
                            </li>
                            <li style="margin-bottom: 0; display: flex; align-items: center; gap: 0.75rem;">
                                <i class="fas fa-database" style="color: #06b6d4;"></i>
                                <span style="font-size: 0.875rem;">Database backup completed</span>
                                <small style="color: #666; margin-left: auto; font-size: 0.75rem;">1 day ago</small>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="card">
                    <h5><i class="fas fa-bolt"></i> Quick Actions</h5>
                    
                    <div class="quick-actions">
                        <a href="#">
                            <i class="fas fa-cog"></i>
                            <div>
                                <strong>Settings</strong>
                                <small>Configure system settings</small>
                            </div>
                        </a>
                        
                        <a href="#">
                            <i class="fas fa-users"></i>
                            <div>
                                <strong>Manage Users</strong>
                                <small>User accounts and roles</small>
                            </div>
                        </a>
                        
                        <a href="#">
                            <i class="fas fa-cubes"></i>
                            <div>
                                <strong>Modules</strong>
                                <small>Manage active modules</small>
                            </div>
                        </a>
                        
                        <a href="performance-dashboard.php">
                            <i class="fas fa-chart-line"></i>
                            <div>
                                <strong>Performance Monitor</strong>
                                <small>View performance metrics</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- System Status -->
            <div class="status-bar">
                <div class="status-title">
                    <i class="fas fa-check-circle"></i>
                    System Status: Operational
                </div>
                <p class="status-text">All systems are running normally. Performance optimization active.</p>
            </div>
            
            <!-- Performance Dashboard Link -->
            <div class="performance-link">
                <a href="performance-dashboard.php" class="btn-performance">
                    <i class="fas fa-chart-line"></i> View Performance Dashboard
                </a>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-refresh every 5 minutes
        setTimeout(() => {
            location.reload();
        }, 300000);
        
        // Sidebar navigation (basic functionality)
        document.addEventListener('DOMContentLoaded', function() {
            const menuItems = document.querySelectorAll('.sidebar-menu a');
            menuItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    menuItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    
                    // In a real implementation, this would load the actual page
                    alert('Navigation to: ' + this.textContent.trim());
                });
            });
        });
    </script>
</body>
</html>
