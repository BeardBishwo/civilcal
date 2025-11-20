<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title">User Analytics</h1>
                <p class="text-muted">Detailed user metrics and growth analysis.</p>
            </div>
        </div>
    </div>
</div>

<!-- User Statistics -->
<div class="row mb-4">
    <?php if (isset($user_stats['by_role']) && is_array($user_stats['by_role'])): ?>
        <?php foreach ($user_stats['by_role'] as $role_data): ?>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h2 class="mb-1 text-primary"><?php echo number_format($role_data['count'] ?? 0); ?></h2>
                        <p class="text-muted mb-0"><?php echo ucfirst($role_data['role'] ?? 'Unknown'); ?> Users</p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h2 class="mb-1 text-success"><?php echo number_format($user_stats['new_this_month'] ?? 0); ?></h2>
                <p class="text-muted mb-0">New This Month</p>
            </div>
        </div>
    </div>
</div>

<!-- User Growth Chart -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">User Growth (Last 90 Days)</h5>
            </div>
            <div class="card-body">
                <canvas id="userGrowthChart" style="max-height: 400px;"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const growthData = <?php echo json_encode($growth_data ?? []); ?>;

    if (typeof Chart !== 'undefined' && growthData.length > 0) {
        const ctx = document.getElementById('userGrowthChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: growthData.map(d => d.date),
                    datasets: [{
                        label: 'New Users',
                        data: growthData.map(d => d.count),
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
                        legend: { display: false }
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
