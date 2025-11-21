<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Error Logs</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Monitor and analyze application errors and exceptions</p>
        </div>
    </div>
</div>

<!-- Error Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem; color: #f87171; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Errors</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #f87171; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_errors'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">All Time</div>
        <small style="color: #f87171; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +5% this month</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-fire" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Critical Errors</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($stats['critical_errors'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">High Priority</div>
        <small style="color: #f87171; font-size: 0.75rem;"><i class="fas fa-exclamation-circle"></i> Requires Attention</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-clock" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Today's Errors</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['today_errors'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Current Day</div>
        <small style="color: #f87171; font-size: 0.75rem;"><i class="fas fa-warning"></i> Monitor Closely</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-shield-alt" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Resolved</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['resolved_errors'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Fixed</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Improving</small>
    </div>
</div>

<!-- Error Filters -->
<div class="admin-card">
    <h2 class="admin-card-title">Filter Errors</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
        <select style="padding: 0.5rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
            <option value="">All Error Levels</option>
            <option value="critical">Critical</option>
            <option value="error">Errors</option>
            <option value="warning">Warnings</option>
            <option value="notice">Notices</option>
        </select>
        
        <select style="padding: 0.5rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
            <option value="">All Error Types</option>
            <option value="php">PHP Errors</option>
            <option value="database">Database Errors</option>
            <option value="system">System Errors</option>
            <option value="user">User Errors</option>
        </select>
        
        <input type="date" style="padding: 0.5rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
        
        <button style="padding: 0.5rem 1rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; cursor: pointer;">
            <i class="fas fa-search"></i> Apply Filters
        </button>
    </div>
</div>

<!-- Error Logs Table -->
<div class="admin-card">
    <h2 class="admin-card-title">Error Logs</h2>
    <div class="admin-card-content">
        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Level</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Message</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">File</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Line</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Timestamp</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($error_logs)): ?>
                        <?php foreach ($error_logs as $error): ?>
                            <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <td style="padding: 0.75rem;">
                                    <span class="status-<?php echo $error['level'] === 'critical' ? 'error' : ($error['level'] === 'warning' ? 'warning' : 'error'); ?>" 
                                          style="background: rgba(<?php echo $error['level'] === 'critical' ? '248, 113, 113, 0.1' : ($error['level'] === 'warning' ? '251, 191, 36, 0.1' : '248, 113, 113, 0.1'); ?>); 
                                                 border: 1px solid rgba(<?php echo $error['level'] === 'critical' ? '248, 113, 113, 0.3' : ($error['level'] === 'warning' ? '251, 191, 36, 0.3' : '248, 113, 113, 0.3'); ?>); 
                                                 padding: 0.25rem 0.5rem; 
                                                 border-radius: 4px; 
                                                 font-size: 0.75rem;">
                                        <?php echo ucfirst(htmlspecialchars($error['level'] ?? 'error')); ?>
                                    </span>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <div>
                                        <span style="color: #f87171;"><?php echo htmlspecialchars(substr($error['message'] ?? 'Unknown error', 0, 100)).(strlen($error['message'] ?? '') > 100 ? '...' : ''); ?></span>
                                        <div style="color: #9ca3af; font-size: 0.75rem;"><?php echo htmlspecialchars($error['type'] ?? 'Error'); ?> in <?php echo htmlspecialchars($error['function'] ?? ''); ?></div>
                                    </div>
                                </td>
                                <td style="padding: 0.75rem; color: #9ca3af;"><?php echo htmlspecialchars(substr(basename($error['file'] ?? ''), 0, 20)).(strlen(basename($error['file'] ?? '')) > 20 ? '...' : ''); ?></td>
                                <td style="padding: 0.75rem; color: #9ca3af;"><?php echo $error['line'] ?? '0'; ?></td>
                                <td style="padding: 0.75rem; color: #9ca3af;"><?php echo $error['timestamp'] ?? 'Unknown'; ?></td>
                                <td style="padding: 0.75rem;">
                                    <a href="<?php echo app_base_url('/admin/debug/error-logs/'.($error['id'] ?? 0).'/view'); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem; margin-right: 0.5rem;">
                                        <i class="fas fa-eye"></i>
                                        <span>View</span>
                                    </a>
                                    <a href="<?php echo app_base_url('/admin/debug/error-logs/'.($error['id'] ?? 0).'/resolve'); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 4px; text-decoration: none; color: #34d399; font-size: 0.875rem; margin-right: 0.5rem;">
                                        <i class="fas fa-check"></i>
                                        <span>Resolve</span>
                                    </a>
                                    <a href="<?php echo app_base_url('/admin/debug/error-logs/'.($error['id'] ?? 0).'/delete'); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 4px; text-decoration: none; color: #f87171; font-size: 0.875rem;">
                                        <i class="fas fa-trash"></i>
                                        <span>Delete</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 1rem; color: #9ca3af;">No error logs found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Error Summary -->
<div class="admin-card">
    <h2 class="admin-card-title">Error Summary</h2>
    <div class="admin-card-content">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
                <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-bug" style="color: #f87171;"></i>
                    PHP Errors
                </h3>
                <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($error_summary['php_errors'] ?? 0); ?> errors</p>
                <div style="display: flex; gap: 0.5rem;">
                    <span style="color: #f87171; background: rgba(248, 113, 113, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Fatal: <?php echo $error_summary['fatal_errors'] ?? 0; ?></span>
                    <span style="color: #fbbf24; background: rgba(251, 191, 36, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Warning: <?php echo $error_summary['warning_errors'] ?? 0; ?></span>
                </div>
            </div>
            
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
                <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-database" style="color: #34d399;"></i>
                    Database Errors
                </h3>
                <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($error_summary['database_errors'] ?? 0); ?> errors</p>
                <div style="display: flex; gap: 0.5rem;">
                    <span style="color: #34d399; background: rgba(52, 211, 153, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Connection: <?php echo $error_summary['connection_errors'] ?? 0; ?></span>
                    <span style="color: #fbbf24; background: rgba(251, 191, 36, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Query: <?php echo $error_summary['query_errors'] ?? 0; ?></span>
                </div>
            </div>
            
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
                <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-server" style="color: #4cc9f0;"></i>
                    System Errors
                </h3>
                <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($error_summary['system_errors'] ?? 0); ?> errors</p>
                <div style="display: flex; gap: 0.5rem;">
                    <span style="color: #4cc9f0; background: rgba(76, 201, 240, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Memory: <?php echo $error_summary['memory_errors'] ?? 0; ?></span>
                    <span style="color: #a78bfa; background: rgba(167, 139, 250, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Timeout: <?php echo $error_summary['timeout_errors'] ?? 0; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Error Management Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Error Management</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/debug/error-logs/export'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-file-export"></i>
            <span>Export Logs</span>
        </a>

        <a href="<?php echo app_base_url('/admin/debug/error-logs/clear'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171;">
            <i class="fas fa-trash-alt"></i>
            <span>Clear Old Logs</span>
        </a>

        <a href="<?php echo app_base_url('/admin/debug/error-logs/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-cog"></i>
            <span>Log Settings</span>
        </a>

        <a href="<?php echo app_base_url('/admin/debug/error-logs/alerts'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-bell"></i>
            <span>Error Alerts</span>
        </a>
    </div>
</div>

<!-- Error Trend Analysis -->
<div class="admin-card">
    <h2 class="admin-card-title">Error Trend Analysis</h2>
    <div style="height: 300px; background: rgba(15, 23, 42, 0.5); border-radius: 8px; padding: 1rem; display: flex; align-items: center; justify-content: center;">
        <p style="color: #9ca3af; text-align: center;">Error Trend Chart<br>(Placeholder for chart showing error trends over time)</p>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>