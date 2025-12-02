<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Audit Logs</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Track all administrative actions and security events</p>
        </div>
    </div>
</div>

<!-- Audit Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-shield-alt" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Events</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_events'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Security Events</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-lock"></i> Secure System</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-user-check" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">User Actions</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['user_actions'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Logged</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Monitored</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem; color: #f87171; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Security Alerts</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #f87171; margin-bottom: 0.5rem;"><?php echo number_format($stats['security_alerts'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">High Priority</div>
        <small style="color: #f87171; font-size: 0.75rem;"><i class="fas fa-bell"></i> Active Alerts</small>
    </div>
</div>

<!-- Recent Audit Events -->
<div class="admin-card">
    <h2 class="admin-card-title">Recent Audit Events</h2>
    <div class="admin-card-content">
        <div style="max-height: 400px; overflow-y: auto;">
            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php if (!empty($audit_events)): ?>
                    <?php foreach (array_slice($audit_events, 0, 15) as $event): ?>
                        <li style="margin-bottom: 1rem; padding: 0.75rem; border-left: 3px solid <?php echo $event['severity'] === 'high' ? '#f87171' : ($event['severity'] === 'medium' ? '#fbbf24' : '#34d399'); ?>; background: rgba(15, 23, 42, 0.5);">
                            <div style="display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 0.25rem;">
                                <i class="fas <?php echo $event['icon'] ?? 'fa-info-circle'; ?>" 
                                   style="color: <?php echo $event['severity'] === 'high' ? '#f87171' : ($event['severity'] === 'medium' ? '#fbbf24' : '#34d399'); ?>; margin-top: 0.25rem;"></i>
                                <div style="flex: 1;">
                                    <span style="color: #e5e7eb; font-size: 0.875rem; display: block;"><?php echo htmlspecialchars($event['action'] ?? ''); ?></span>
                                    <small style="color: #9ca3af; font-size: 0.75rem;"><?php echo htmlspecialchars($event['description'] ?? ''); ?></small>
                                </div>
                                <small style="color: #9ca3af; font-size: 0.75rem; white-space: nowrap;"><?php echo $event['timestamp'] ?? ''; ?></small>
                            </div>
                            <div style="color: #9ca3af; font-size: 0.75rem; margin-left: 2rem; display: flex; justify-content: space-between;">
                                <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($event['user'] ?? 'System'); ?></span>
                                <span><i class="fas fa-ip"></i> <?php echo htmlspecialchars($event['ip'] ?? ''); ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="text-align: center; padding: 2rem; color: #9ca3af;">
                        <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                        <p>No recent audit events found</p>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<!-- Audit Filters -->
<div class="admin-card">
    <h2 class="admin-card-title">Filter Audit Events</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <select style="padding: 0.5rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
            <option value="">All Severities</option>
            <option value="high">High Priority</option>
            <option value="medium">Medium Priority</option>
            <option value="low">Low Priority</option>
        </select>
        
        <select style="padding: 0.5rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
            <option value="">All Actions</option>
            <option value="login">Logins</option>
            <option value="delete">Deletions</option>
            <option value="create">Creations</option>
            <option value="update">Updates</option>
            <option value="access">Access</option>
        </select>
        
        <input type="date" style="padding: 0.5rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
        
        <button style="padding: 0.5rem 1rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer;">
            <i class="fas fa-search"></i> Apply Filters
        </button>
    </div>
</div>

<!-- Audit Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Audit Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/audit/export'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-file-export"></i>
            <span>Export Audit Log</span>
        </a>

        <a href="<?php echo app_base_url('/admin/audit/report'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-chart-bar"></i>
            <span>Generate Report</span>
        </a>

        <a href="<?php echo app_base_url('/admin/audit/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-cog"></i>
            <span>Audit Settings</span>
        </a>

        <a href="<?php echo app_base_url('/admin/audit/clear'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171;">
            <i class="fas fa-trash-alt"></i>
            <span>Clear Old Logs</span>
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>