<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Debug Tools</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">System debugging and troubleshooting tools</p>
        </div>
    </div>
</div>

<!-- System Status -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-heartbeat" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">System Health</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo $system_status['health'] ?? '100%'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Overall Status</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Operational</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-microchip" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">CPU Usage</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo $system_status['cpu_usage'] ?? '0%'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Current Load</div>
        <small style="color: <?php echo ($system_status['cpu_usage'] ?? 0) > 80 ? '#f87171' : '#10b981'; ?>; font-size: 0.75rem;">
            <i class="fas <?php echo ($system_status['cpu_usage'] ?? 0) > 80 ? 'fa-exclamation-triangle' : 'fa-check-circle'; ?>"></i>
            <?php echo ($system_status['cpu_usage'] ?? 0) > 80 ? 'High Load' : 'Normal'; ?>
        </small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-memory" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Memory Usage</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo $system_status['memory_usage'] ?? '0%'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">RAM Usage</div>
        <small style="color: <?php echo ($system_status['memory_usage'] ?? 0) > 80 ? '#f87171' : '#10b981'; ?>; font-size: 0.75rem;">
            <i class="fas <?php echo ($system_status['memory_usage'] ?? 0) > 80 ? 'fa-exclamation-triangle' : 'fa-check-circle'; ?>"></i>
            <?php echo ($system_status['memory_usage'] ?? 0) > 80 ? 'High' : 'Normal'; ?>
        </small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-database" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">DB Connections</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo $system_status['db_connections'] ?? '0'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Active</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-sync"></i> Stable</small>
    </div>
</div>

<!-- Debug Information -->
<div class="admin-card">
    <h2 class="admin-card-title">Debug Information</h2>
    <div class="admin-card-content">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div>
                <h4 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-info-circle" style="color: #4cc9f0;"></i>
                    System Information
                </h4>
                <div style="background: rgba(15, 23, 42, 0.5); padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
                    <p style="margin: 0.5rem 0; color: #9ca3af;"><strong>PHP Version:</strong> <span style="color: #f9fafb;"><?php echo $debug_info['php_version'] ?? 'Unknown'; ?></span></p>
                    <p style="margin: 0.5rem 0; color: #9ca3af;"><strong>Server:</strong> <span style="color: #f9fafb;"><?php echo $debug_info['server'] ?? 'Unknown'; ?></span></p>
                    <p style="margin: 0.5rem 0; color: #9ca3af;"><strong>Memory Limit:</strong> <span style="color: #f9fafb;"><?php echo $debug_info['memory_limit'] ?? 'Unknown'; ?></span></p>
                    <p style="margin: 0.5rem 0; color: #9ca3af;"><strong>Max Execution Time:</strong> <span style="color: #f9fafb;"><?php echo $debug_info['max_execution_time'] ?? 'Unknown'; ?></span></p>
                </div>
            </div>
            
            <div>
                <h4 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-server" style="color: #34d399;"></i>
                    Application Information
                </h4>
                <div style="background: rgba(15, 23, 42, 0.5); padding: 1rem; border-radius: 6px;">
                    <p style="margin: 0.5rem 0; color: #9ca3af;"><strong>Version:</strong> <span style="color: #f9fafb;"><?php echo $debug_info['app_version'] ?? 'Unknown'; ?></span></p>
                    <p style="margin: 0.5rem 0; color: #9ca3af;"><strong>Environment:</strong> <span style="color: #f9fafb;"><?php echo $debug_info['environment'] ?? 'Unknown'; ?></span></p>
                    <p style="margin: 0.5rem 0; color: #9ca3af;"><strong>Debug Mode:</strong> 
                        <span style="color: <?php echo $debug_info['debug_mode'] ? '#34d399' : '#f87171'; ?>;">
                            <?php echo $debug_info['debug_mode'] ? 'Enabled' : 'Disabled'; ?>
                        </span>
                    </p>
                    <p style="margin: 0.5rem 0; color: #9ca3af;"><strong>Cache Status:</strong> 
                        <span style="color: <?php echo $debug_info['cache_enabled'] ? '#34d399' : '#f87171'; ?>;">
                            <?php echo $debug_info['cache_enabled'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Debug Tools -->
<div class="admin-card">
    <h2 class="admin-card-title">Debug Tools</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-bug" style="color: #f87171;"></i>
                Error Logs
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">View and analyze system error logs</p>
            <a href="<?php echo app_base_url('/admin/debug/error-logs'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171; font-size: 0.875rem;">
                <i class="fas fa-eye"></i>
                <span>View Logs</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-tachometer-alt" style="color: #34d399;"></i>
                Performance
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Performance monitoring and profiling tools</p>
            <a href="<?php echo app_base_url('/admin/debug/performance'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                <i class="fas fa-chart-line"></i>
                <span>Monitor</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-database" style="color: #4cc9f0;"></i>
                Database
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Database debugging and optimization tools</p>
            <a href="<?php echo app_base_url('/admin/debug/database'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem;">
                <i class="fas fa-cogs"></i>
                <span>Tools</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-cogs" style="color: #fbbf24;"></i>
                System Tests
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Run system diagnostic tests</p>
            <a href="<?php echo app_base_url('/admin/debug/tests'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24; font-size: 0.875rem;">
                <i class="fas fa-flask"></i>
                <span>Run Tests</span>
            </a>
        </div>
    </div>
</div>

<!-- Debug Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Debug Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/debug/clear-cache'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-sync-alt"></i>
            <span>Clear Cache</span>
        </a>

        <a href="<?php echo app_base_url('/admin/debug/reload-config'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-redo"></i>
            <span>Reload Config</span>
        </a>

        <a href="<?php echo app_base_url('/admin/debug/php-info'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-info-circle"></i>
            <span>PHP Info</span>
        </a>

        <a href="<?php echo app_base_url('/admin/debug/clear-logs'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171;">
            <i class="fas fa-trash-alt"></i>
            <span>Clear Logs</span>
        </a>

        <a href="<?php echo app_base_url('/admin/debug/restart-services'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 6px; text-decoration: none; color: #a78bfa;">
            <i class="fas fa-power-off"></i>
            <span>Restart Services</span>
        </a>
    </div>
</div>

<!-- Environment Variables -->
<div class="admin-card">
    <h2 class="admin-card-title">Environment Variables</h2>
    <div style="background: rgba(15, 23, 42, 0.5); padding: 1rem; border-radius: 6px; max-height: 300px; overflow-y: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                    <th style="text-align: left; padding: 0.5rem; color: #9ca3af; font-weight: 600;">Variable</th>
                    <th style="text-align: left; padding: 0.5rem; color: #9ca3af; font-weight: 600;">Value</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($environment_vars)): ?>
                    <?php foreach ($environment_vars as $key => $value): ?>
                        <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                            <td style="padding: 0.5rem; color: #f9fafb;"><?php echo htmlspecialchars($key); ?></td>
                            <td style="padding: 0.5rem; color: #9ca3af;"><?php echo is_string($value) ? htmlspecialchars(substr($value, 0, 50)).(strlen($value) > 50 ? '...' : '') : gettype($value); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2" style="text-align: center; padding: 1rem; color: #9ca3af;">No environment variables available</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>