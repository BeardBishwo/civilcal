<?php
ob_start();
?>

<!-- Page Header -->
<div class="page-header" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 600; color: #f9fafb; margin: 0 0 0.5rem 0;">Analytics Overview</h1>
            <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Comprehensive analytics and insights for your platform.</p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <button onclick="window.location.reload()" style="background: #4361ee; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-sync-alt"></i>
                <span>Refresh</span>
            </button>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    
    <!-- Total Users -->
    <div class="stat-card" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.5rem; text-align: center; transition: transform 0.2s ease;">
        <div style="width: 50px; height: 50px; background: rgba(67, 97, 238, 0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">
            <i class="fas fa-users" style="font-size: 1.5rem; color: #4cc9f0;"></i>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_users'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Total Users</div>
        <?php /* TODO: Calculate real growth percentage from database */ ?>
        <?php /* <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +12% this month</small> */ ?>
    </div>
    
    <!-- Active Users -->
    <div class="stat-card" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.5rem; text-align: center; transition: transform 0.2s ease;">
        <div style="width: 50px; height: 50px; background: rgba(16, 185, 129, 0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">
            <i class="fas fa-user-check" style="font-size: 1.5rem; color: #34d399;"></i>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['active_users'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Active Users (30d)</div>
        <?php /* TODO: Calculate real growth percentage from database */ ?>
        <?php /* <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +8% this month</small> */ ?>
    </div>
    
    <!-- Total Calculations -->
    <div class="stat-card" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.5rem; text-align: center; transition: transform 0.2s ease;">
        <div style="width: 50px; height: 50px; background: rgba(245, 158, 11, 0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">
            <i class="fas fa-calculator" style="font-size: 1.5rem; color: #fbbf24;"></i>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_calculations'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Total Calculations</div>
    </div>
    
    <!-- Monthly Calculations -->
    <div class="stat-card" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.5rem; text-align: center; transition: transform 0.2s ease;">
        <div style="width: 50px; height: 50px; background: rgba(6, 182, 212, 0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">
            <i class="fas fa-chart-line" style="font-size: 1.5rem; color: #22d3ee;"></i>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo number_format($stats['monthly_calculations'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">Monthly Calculations</div>
    </div>
    
</div>

<!-- Charts Section -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    
    <!-- Daily Calculations Chart -->
    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem;">
        <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-chart-area" style="color: #4cc9f0;"></i>
            Daily Calculations (Last 30 Days)
        </h5>
        <canvas id="dailyCalculationsChart" style="max-height: 300px;"></canvas>
    </div>
    
    <!-- User Growth Chart -->
    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem;">
        <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-users" style="color: #34d399;"></i>
            User Growth
        </h5>
        <canvas id="userGrowthChart" style="max-height: 300px;"></canvas>
    </div>
    
</div>

<!-- Quick Links -->
<div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem;">
    <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0;">Detailed Analytics</h5>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <a href="<?php echo app_base_url('/admin/analytics/users'); ?>" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: rgba(67, 97, 238, 0.05); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 8px; text-decoration: none; transition: all 0.2s ease;">
            <i class="fas fa-users" style="color: #4cc9f0; font-size: 1.5rem;"></i>
            <div>
                <strong style="color: #f9fafb; font-size: 0.875rem; display: block;">User Analytics</strong>
                <small style="color: #9ca3af; font-size: 0.75rem;">Detailed user metrics</small>
            </div>
        </a>
        <a href="<?php echo app_base_url('/admin/analytics/calculators'); ?>" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: rgba(52, 211, 153, 0.05); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 8px; text-decoration: none; transition: all 0.2s ease;">
            <i class="fas fa-calculator" style="color: #34d399; font-size: 1.5rem;"></i>
            <div>
                <strong style="color: #f9fafb; font-size: 0.875rem; display: block;">Calculator Analytics</strong>
                <small style="color: #9ca3af; font-size: 0.75rem;">Usage statistics</small>
            </div>
        </a>
        <a href="<?php echo app_base_url('/admin/analytics/performance'); ?>" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: rgba(251, 191, 36, 0.05); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 8px; text-decoration: none; transition: all 0.2s ease;">
            <i class="fas fa-tachometer-alt" style="color: #fbbf24; font-size: 1.5rem;"></i>
            <div>
                <strong style="color: #f9fafb; font-size: 0.875rem; display: block;">Performance</strong>
                <small style="color: #9ca3af; font-size: 0.75rem;">System metrics</small>
            </div>
        </a>
    </div>
</div>

<script>
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
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: '#f9fafb' }
                    }
                },
                scales: {
                    y: {
                        ticks: { color: '#9ca3af' },
                        grid: { color: 'rgba(102, 126, 234, 0.1)' }
                    },
                    x: {
                        ticks: { color: '#9ca3af' },
                        grid: { color: 'rgba(102, 126, 234, 0.1)' }
                    }
                }
            }
        });
    }
}
</script>

<style>
.stat-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(67, 97, 238, 0.3);
}

a[href*="/admin/analytics/"]:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
