<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Error Logs</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Monitor and manage system errors and exceptions</p>
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

<!-- Recent Errors -->
<div class="admin-card">
    <h2 class="admin-card-title">Recent Errors</h2>
    <div class="admin-card-content">
        <div style="max-height: 500px; overflow-y: auto;">
            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php if (!empty($errors)): ?>
                    <?php foreach (array_slice($errors, 0, 15) as $error): ?>
                        <li style="margin-bottom: 1rem; padding: 0.75rem; border-left: 3px solid <?php echo $error['level'] === 'critical' ? '#f87171' : ($error['level'] === 'warning' ? '#fbbf24' : '#34d399'); ?>; background: rgba(15, 23, 42, 0.5);">
                            <div style="display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 0.25rem;">
                                <i class="fas <?php echo $error['level'] === 'critical' ? 'fa-bug' : ($error['level'] === 'warning' ? 'fa-exclamation-triangle' : 'fa-exclamation-circle'); ?>" 
                                   style="color: <?php echo $error['level'] === 'critical' ? '#f87171' : ($error['level'] === 'warning' ? '#fbbf24' : '#34d399'); ?>; margin-top: 0.25rem;"></i>
                                <div style="flex: 1;">
                                    <span style="color: #f87171; font-size: 0.875rem; display: block; font-weight: 500;"><?php echo htmlspecialchars($error['title'] ?? 'Unknown Error'); ?></span>
                                    <small style="color: #9ca3af; font-size: 0.75rem;"><?php echo htmlspecialchars($error['message'] ?? ''); ?></small>
                                </div>
                                <small style="color: #9ca3af; font-size: 0.75rem; white-space: nowrap;"><?php echo $error['timestamp'] ?? ''; ?></small>
                            </div>
                            <div style="color: #9ca3af; font-size: 0.75rem; margin-left: 2rem; display: flex; justify-content: space-between;">
                                <span><i class="fas fa-code"></i> <?php echo $error['file'] ?? 'Unknown'; ?>:<?php echo $error['line'] ?? '0'; ?></span>
                                <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($error['user'] ?? 'System'); ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="text-align: center; padding: 2rem; color: #9ca3af;">
                        <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                        <p>No errors found. System is running smoothly!</p>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
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

<!-- Error Management -->
<div class="admin-card">
    <h2 class="admin-card-title">Error Management</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/error-logs/export'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-file-export"></i>
            <span>Export Logs</span>
        </a>

        <a href="<?php echo app_base_url('/admin/error-logs/confirm-clear'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171;">
            <i class="fas fa-trash-alt"></i>
            <span>Clear Old Logs</span>
        </a>

        <a href="<?php echo app_base_url('/admin/error-logs/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-cog"></i>
            <span>Log Settings</span>
        </a>

        <a href="<?php echo app_base_url('/admin/error-logs/report'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-chart-bar"></i>
            <span>Error Report</span>
        </a>
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

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>