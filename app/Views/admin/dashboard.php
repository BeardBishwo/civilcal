<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Bishwo Calculator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
        }
        .quick-action {
            background: white;
            border: none;
            border-radius: 8px;
            padding: 1rem;
            text-align: left;
            width: 100%;
            margin-bottom: 0.5rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.2s;
        }
        .quick-action:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div class="container">
            <h1><i class="fas fa-tachometer-alt me-3"></i>Admin Dashboard</h1>
            <p class="mb-0">Welcome back! Here's an overview of your engineering calculator platform.</p>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Statistics Cards -->
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <div class="stat-number">1,247</div>
                    <div class="text-muted">Total Users</div>
                    <small class="text-success"><i class="fas fa-arrow-up"></i> +12% this month</small>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="fas fa-calculator fa-2x text-success mb-2"></i>
                    <div class="stat-number">15,673</div>
                    <div class="text-muted">Calculations</div>
                    <small class="text-success"><i class="fas fa-arrow-up"></i> +8% this month</small>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="fas fa-puzzle-piece fa-2x text-warning mb-2"></i>
                    <div class="stat-number">12</div>
                    <div class="text-muted">Active Modules</div>
                    <small class="text-success"><i class="fas fa-check"></i> All operational</small>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card text-center">
                    <i class="fas fa-chart-line fa-2x text-info mb-2"></i>
                    <div class="stat-number">98.5%</div>
                    <div class="text-muted">System Health</div>
                    <small class="text-success"><i class="fas fa-check-circle"></i> Excellent</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-chart-bar me-2"></i>System Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <h3 class="text-primary">892</h3>
                                <p class="text-muted">Active Users</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <h3 class="text-success">2,341</h3>
                                <p class="text-muted">Monthly Calculations</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <h3 class="text-warning">67%</h3>
                                <p class="text-muted">Storage Used</p>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h6>Recent Activity</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-user-plus text-primary me-2"></i>
                                    New user registered - <small class="text-muted">2 hours ago</small>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-cog text-success me-2"></i>
                                    System settings updated - <small class="text-muted">4 hours ago</small>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-database text-info me-2"></i>
                                    Database backup completed - <small class="text-muted">1 day ago</small>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="/bishwo_calculator/admin/settings" class="quick-action btn">
                            <i class="fas fa-cog text-primary me-2"></i>
                            <strong>Settings</strong><br>
                            <small class="text-muted">Configure system settings</small>
                        </a>
                        
                        <a href="/bishwo_calculator/admin/users" class="quick-action btn">
                            <i class="fas fa-users text-success me-2"></i>
                            <strong>Manage Users</strong><br>
                            <small class="text-muted">User accounts and roles</small>
                        </a>
                        
                        <a href="/bishwo_calculator/admin/setup/checklist" class="quick-action btn">
                            <i class="fas fa-tasks text-warning me-2"></i>
                            <strong>Setup Checklist</strong><br>
                            <small class="text-muted">Complete site setup</small>
                        </a>
                        
                        <a href="/bishwo_calculator/help" class="quick-action btn">
                            <i class="fas fa-question-circle text-info me-2"></i>
                            <strong>Help Center</strong><br>
                            <small class="text-muted">Documentation and support</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-success">
                    <h5><i class="fas fa-check-circle me-2"></i>System Status: Operational</h5>
                    <p class="mb-0">All systems are running normally. Last checked: <?php echo date('Y-m-d H:i:s'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
