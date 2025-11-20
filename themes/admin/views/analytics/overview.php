<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title">Analytics Overview</h1>
                <p class="text-muted">Comprehensive analytics and insights for your platform.</p>
            </div>
            <button onclick="window.location.reload()" class="btn btn-primary">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <!-- Total Users -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="stat-icon bg-primary-light text-primary mb-3 mx-auto" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <h2 class="mb-1 text-primary"><?php echo number_format($stats['total_users'] ?? 0); ?></h2>
                <p class="text-muted mb-0">Total Users</p>
            </div>
        </div>
    </div>
    
    <!-- Active Users -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="stat-icon bg-success-light text-success mb-3 mx-auto" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-user-check fa-2x"></i>
                </div>
                <h2 class="mb-1 text-success"><?php echo number_format($stats['active_users'] ?? 0); ?></h2>
                <p class="text-muted mb-0">Active Users (30d)</p>
            </div>
        </div>
    </div>
    
    <!-- Total Calculations -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="stat-icon bg-warning-light text-warning mb-3 mx-auto" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-calculator fa-2x"></i>
                </div>
                <h2 class="mb-1 text-warning"><?php echo number_format($stats['total_calculations'] ?? 0); ?></h2>
                <p class="text-muted mb-0">Total Calculations</p>
            </div>
        </div>
    </div>
    
    <!-- Monthly Calculations -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="stat-icon bg-info-light text-info mb-3 mx-auto" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-chart-line fa-2x"></i>
                </div>
                <h2 class="mb-1 text-info"><?php echo number_format($stats['monthly_calculations'] ?? 0); ?></h2>
                <p class="text-muted mb-0">Monthly Calculations</p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row mb-4">
    <!-- Daily Calculations Chart -->
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-area text-info mr-2"></i>
                    Daily Calculations (Last 30 Days)
                </h5>
            </div>
            <div class="card-body">
                <canvas id="dailyCalculationsChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Quick Links -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Detailed Analytics</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-3">
                    <a href="<?php echo app_base_url('/admin/analytics/users'); ?>" class="d-flex align-items-center p-3 bg-light rounded text-decoration-none">
                        <i class="fas fa-users text-primary fa-2x mr-3"></i>
                        <div>
                            <strong class="d-block text-dark">User Analytics</strong>
                            <small class="text-muted">Detailed user metrics</small>
                        </div>
                    </a>
                    <a href="<?php echo app_base_url('/admin/analytics/calculators'); ?>" class="d-flex align-items-center p-3 bg-light rounded text-decoration-none mt-3">
                        <i class="fas fa-calculator text-success fa-2x mr-3"></i>
                        <div>
                            <strong class="d-block text-dark">Calculator Analytics</strong>
                            <small class="text-muted">Usage statistics</small>
                        </div>
                    </a>
                    <a href="<?php echo app_base_url('/admin/analytics/performance'); ?>" class="d-flex align-items-center p-3 bg-light rounded text-decoration-none mt-3">
                        <i class="fas fa-tachometer-alt text-warning fa-2x mr-3"></i>
                        <div>
                            <strong class="d-block text-dark">Performance</strong>
                            <small class="text-muted">System metrics</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart data from PHP
    const dailyData = <?php echo json_encode($charts['daily_calculations'] ?? []); ?>;

    // Initialize charts if Chart.js is loaded
    if (typeof Chart !== 'undefined') {
        // Daily Calculations Chart
        const dailyCtx = document.getElementById('dailyCalculationsChart');
        if (dailyCtx) {
            new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: dailyData.map(d => d.date),
                    datasets: [{
                        label: 'Calculations',
                        data: dailyData.map(d => d.count),
                        borderColor: '#4cc9f0',
                        backgroundColor: 'rgba(76, 201, 240, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    }
});
</script>
