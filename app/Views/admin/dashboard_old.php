<?php
$content = '
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Dashboard Overview</h2>
            <p class="text-muted mb-0">Welcome back, ' . htmlspecialchars($currentUser['username'] ?? 'Admin') . '! Here\'s what\'s happening with your calculators.</p>
        </div>
        <div class="quick-actions">
            <button class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-2"></i>Add Calculator</button>
            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-download me-2"></i>Export Report</button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-primary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">' . ($stats['total_users'] ?? '1,234') . '</div>
                            <small class="stat-label">+12% from last month</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-success">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Modules
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">' . ($stats['active_modules'] ?? '12') . '</div>
                            <small class="stat-label">8 categories, 4 custom</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-grid-3x3-gap fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-info">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Calculators
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">' . ($stats['total_calculators'] ?? '56') . '</div>
                            <small class="stat-label">+3 new this week</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-calculator fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card border-left-warning">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                API Requests
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-number">' . ($stats['api_requests'] ?? '789') . '</div>
                            <small class="stat-label">Today\'s count</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-hdd-network fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">User Growth Analytics</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                            <a class="dropdown-item" href="#">Export Data</a>
                            <a class="dropdown-item" href="#">Print Chart</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Refresh</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="userGrowthChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Calculator Usage</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="calculatorUsageChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Quick Stats -->
    <div class="row">
        <!-- Recent Activity -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Calculator</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="/assets/images/avatar1.jpg" class="rounded-circle me-2" width="30" height="30" alt="User">
                                            <div>John Doe</div>
                                        </div>
                                    </td>
                                    <td>Used Calculator</td>
                                    <td>Concrete Volume</td>
                                    <td>2 minutes ago</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="/assets/images/avatar2.jpg" class="rounded-circle me-2" width="30" height="30" alt="User">
                                            <div>Jane Smith</div>
                                        </div>
                                    </td>
                                    <td>Created Project</td>
                                    <td>Structural Analysis</td>
                                    <td>15 minutes ago</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="/assets/images/avatar3.jpg" class="rounded-circle me-2" width="30" height="30" alt="User">
                                            <div>Mike Johnson</div>
                                        </div>
                                    </td>
                                    <td>Exported Report</td>
                                    <td>Electrical Load</td>
                                    <td>1 hour ago</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <a href="' . app_base_url('/admin/activity') . '" class="btn btn-sm btn-outline-primary">View All Activity</a>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Status</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Server Load</span>
                            <span class="text-success">24%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 24%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Database</span>
                            <span class="text-success">Online</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Storage</span>
                            <span class="text-warning">65%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 65%"></div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>System Uptime:</strong> 99.8%
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
';

// Include the layout
include __DIR__ . '/../layouts/admin.php';
