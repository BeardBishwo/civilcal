<?php
ob_start();
?>

<!-- Page Header -->
<div class="page-header" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 600; color: #f9fafb; margin: 0 0 0.5rem 0;">User Analytics</h1>
            <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Detailed user metrics and growth analysis.</p>
        </div>
    </div>
</div>

<!-- User Statistics -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <?php if (isset($user_stats['by_role']) && is_array($user_stats['by_role'])): ?>
        <?php foreach ($user_stats['by_role'] as $role_data): ?>
            <div class="stat-card" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.5rem; text-align: center;">
                <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($role_data['count'] ?? 0); ?></div>
                <div style="color: #9ca3af; font-size: 0.875rem;"><?php echo ucfirst($role_data['role'] ?? 'Unknown'); ?> Users</div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <div class="stat-card" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.5rem; text-align: center;">
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($user_stats['new_this_month'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem;">New This Month</div>
    </div>
</div>

<!-- User Growth Chart -->
<div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem; margin-bottom: 2rem;">
    <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0;">User Growth (Last 90 Days)</h5>
    <canvas id="userGrowthChart" style="max-height: 400px;"></canvas>
</div>

<script>
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
                    legend: { labels: { color: '#f9fafb' } }
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

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
