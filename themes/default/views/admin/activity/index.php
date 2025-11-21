<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Activity Logs</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Track user activities and system events</p>
        </div>
    </div>
</div>

<!-- Activity Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-user-clock" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Activities</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_activities'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">All Time</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +12% this month</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-user" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Active Users</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['active_users'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Today</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-bolt"></i> High activity</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-calculator" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Calculations</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($stats['calculations'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Today</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-chart-line"></i> Steady growth</small>
    </div>
</div>

<!-- Recent Activities -->
<div class="admin-card">
    <h2 class="admin-card-title">Recent Activities</h2>
    <div class="admin-card-content">
        <div style="max-height: 400px; overflow-y: auto;">
            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php if (!empty($activities)): ?>
                    <?php foreach (array_slice($activities, 0, 15) as $activity): ?>
                        <li style="margin-bottom: 1rem; padding: 0.75rem; border-left: 3px solid <?php echo $activity['type'] === 'error' ? '#f87171' : ($activity['type'] === 'warning' ? '#fbbf24' : '#34d399'); ?>; background: rgba(15, 23, 42, 0.5);">
                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
                                <i class="fas <?php echo $activity['icon'] ?? 'fa-info-circle'; ?>" 
                                   style="color: <?php echo $activity['type'] === 'error' ? '#f87171' : ($activity['type'] === 'warning' ? '#fbbf24' : '#34d399'); ?>;"></i>
                                <span style="color: #e5e7eb; font-size: 0.875rem; flex: 1;"><?php echo htmlspecialchars($activity['description'] ?? ''); ?></span>
                                <small style="color: #9ca3af; font-size: 0.75rem;"><?php echo $activity['timestamp'] ?? ''; ?></small>
                            </div>
                            <?php if (isset($activity['user'])): ?>
                                <div style="color: #9ca3af; font-size: 0.75rem; margin-left: 2rem;">
                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($activity['user'] ?? ''); ?>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="text-align: center; padding: 2rem; color: #9ca3af;">
                        <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                        <p>No recent activities found</p>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<!-- Activity Filters -->
<div class="admin-card">
    <h2 class="admin-card-title">Filter Activities</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <select style="flex: 1; padding: 0.5rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb; min-width: 150px;">
            <option value="">All Types</option>
            <option value="user_action">User Actions</option>
            <option value="system">System Events</option>
            <option value="error">Errors</option>
            <option value="warning">Warnings</option>
        </select>
        
        <input type="date" style="flex: 1; padding: 0.5rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb; min-width: 150px;">
        
        <button style="padding: 0.5rem 1rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer;">
            <i class="fas fa-search"></i> Filter
        </button>
    </div>
</div>

<!-- Activity Management -->
<div class="admin-card">
    <h2 class="admin-card-title">Activity Management</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/activity/export'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-file-export"></i>
            <span>Export Activities</span>
        </a>

        <a href="<?php echo app_base_url('/admin/activity/clear'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171;">
            <i class="fas fa-trash-alt"></i>
            <span>Clear Old Activities</span>
        </a>
        
        <a href="<?php echo app_base_url('/admin/activity/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-cog"></i>
            <span>Activity Settings</span>
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>