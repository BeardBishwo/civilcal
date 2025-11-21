<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>System Status</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Monitor overall system health and performance metrics</p>
        </div>
    </div>
</div>

<!-- System Status Overview -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-heartbeat" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">System Health</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo $system_status['overall_health'] ?? 'Excellent'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Overall Status</div>
        <small style="color: #10b981; font-size: 0.75rem;"><?php echo $system_status['health_percentage'] ?? '100'; ?>% Healthy</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-server" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Server Status</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo $system_status['server_status'] ?? 'Operational'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Current State</div>
        <small style="color: #10b981; font-size: 0.75rem;"><?php echo $system_status['uptime']; ?> uptime</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-database" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Database</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo $system_status['db_status'] ?? 'Connected'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Connection</div>
        <small style="color: #10b981; font-size: 0.75rem;">Responsive</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-bolt" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Performance</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo $system_status['performance_level'] ?? 'Excellent'; ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Current</div>
        <small style="color: #10b981; font-size: 0.75rem;"><?php echo $system_status['response_time'] ?? 'Fast'; ?> response</small>
    </div>
</div>

<!-- System Resources -->
<div class="admin-card">
    <h2 class="admin-card-title">System Resources</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-microchip" style="color: #4cc9f0;"></i>
                CPU Usage
            </h3>
            <div style="text-align: center; margin-bottom: 1rem;">
                <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo $system_resources['cpu_usage'] ?? '0%'; ?></div>
                <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Current Load</div>
            </div>
            <div style="height: 10px; background: rgba(102, 126, 234, 0.2); border-radius: 5px; overflow: hidden; margin-bottom: 0.5rem;">
                <div style="height: 100%; width: <?php echo $system_resources['cpu_usage'] ?? 0; ?>%; background: <?php echo ($system_resources['cpu_usage'] ?? 0) > 80 ? '#f87171' : (($system_resources['cpu_usage'] ?? 0) > 60 ? '#fbbf24' : '#34d399'); ?>;"></div>
            </div>
            <div style="display: flex; justify-content: space-between; color: #9ca3af; font-size: 0.75rem;">
                <span>Load Average: <?php echo $system_resources['load_average'] ?? '0.00'; ?></span>
                <span>Cores: <?php echo $system_resources['cpu_cores'] ?? '4'; ?></span>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-memory" style="color: #34d399;"></i>
                Memory Usage
            </h3>
            <div style="text-align: center; margin-bottom: 1rem;">
                <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo $system_resources['memory_usage'] ?? '0%'; ?></div>
                <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">of <?php echo $system_resources['memory_total'] ?? '0GB'; ?></div>
            </div>
            <div style="height: 10px; background: rgba(102, 126, 234, 0.2); border-radius: 5px; overflow: hidden; margin-bottom: 0.5rem;">
                <div style="height: 100%; width: <?php echo $system_resources['memory_usage'] ?? 0; ?>%; background: <?php echo ($system_resources['memory_usage'] ?? 0) > 80 ? '#f87171' : (($system_resources['memory_usage'] ?? 0) > 60 ? '#fbbf24' : '#34d399'); ?>;"></div>
            </div>
            <div style="display: flex; justify-content: space-between; color: #9ca3af; font-size: 0.75rem;">
                <span>Used: <?php echo $system_resources['memory_used'] ?? '0GB'; ?></span>
                <span>Free: <?php echo $system_resources['memory_free'] ?? '0GB'; ?></span>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-hdd" style="color: #fbbf24;"></i>
                Disk Space
            </h3>
            <div style="text-align: center; margin-bottom: 1rem;">
                <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo $system_resources['disk_usage'] ?? '0%'; ?></div>
                <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Storage Used</div>
            </div>
            <div style="height: 10px; background: rgba(102, 126, 234, 0.2); border-radius: 5px; overflow: hidden; margin-bottom: 0.5rem;">
                <div style="height: 100%; width: <?php echo $system_resources['disk_usage'] ?? 0; ?>%; background: <?php echo ($system_resources['disk_usage'] ?? 0) > 90 ? '#f87171' : (($system_resources['disk_usage'] ?? 0) > 70 ? '#fbbf24' : '#34d399'); ?>;"></div>
            </div>
            <div style="display: flex; justify-content: space-between; color: #9ca3af; font-size: 0.75rem;">
                <span>Used: <?php echo $system_resources['disk_used'] ?? '0GB'; ?></span>
                <span>Total: <?php echo $system_resources['disk_total'] ?? '0GB'; ?></span>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-network-wired" style="color: #22d3ee;"></i>
                Network
            </h3>
            <div style="text-align: center; margin-bottom: 1rem;">
                <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo $system_resources['network_status'] ?? 'Connected'; ?></div>
                <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Status</div>
            </div>
            <div style="height: 10px; background: rgba(102, 126, 234, 0.2); border-radius: 5px; overflow: hidden; margin-bottom: 0.5rem;">
                <div style="height: 100%; width: <?php echo min(100, ($system_resources['bandwidth_usage'] ?? 0) / ($system_resources['bandwidth_limit'] ?? 1) * 100); ?>%; background: <?php echo ($system_resources['bandwidth_usage'] ?? 0) / ($system_resources['bandwidth_limit'] ?? 1) > 0.9 ? '#f87171' : (($system_resources['bandwidth_usage'] ?? 0) / ($system_resources['bandwidth_limit'] ?? 1) > 0.7 ? '#fbbf24' : '#34d399'); ?>;"></div>
            </div>
            <div style="display: flex; justify-content: space-between; color: #9ca3af; font-size: 0.75rem;">
                <span>In: <?php echo $system_resources['net_in'] ?? '0 KB/s'; ?></span>
                <span>Out: <?php echo $system_resources['net_out'] ?? '0 KB/s'; ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Service Status -->
<div class="admin-card">
    <h2 class="admin-card-title">Service Status</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
        <?php if (!empty($services)): ?>
            <?php foreach ($services as $service): ?>
                <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; border-left: 3px solid <?php echo $service['status'] === 'running' ? '#34d399' : ($service['status'] === 'stopped' ? '#f87171' : '#fbbf24'); ?>;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <div>
                            <h3 style="color: #f9fafb; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas <?php echo $service['icon'] ?? 'fa-cog'; ?>" style="color: <?php echo $service['status'] === 'running' ? '#34d399' : '#f87171'; ?>;"></i>
                                <?php echo htmlspecialchars($service['name'] ?? 'Unknown Service'); ?>
                            </h3>
                            <p style="color: #9ca3af; margin: 0.25rem 0 0 0; font-size: 0.75rem;"><?php echo htmlspecialchars($service['description'] ?? ''); ?></p>
                        </div>
                        <span style="color: <?php echo $service['status'] === 'running' ? '#34d399' : ($service['status'] === 'stopped' ? '#f87171' : '#fbbf24'); ?>; 
                              background: <?php echo $service['status'] === 'running' ? 'rgba(52, 211, 153, 0.1)' : ($service['status'] === 'stopped' ? 'rgba(248, 113, 113, 0.1)' : 'rgba(251, 191, 36, 0.1)'); ?>;
                              padding: 0.25rem 0.5rem; 
                              border-radius: 4px; 
                              font-size: 0.75rem;">
                            <?php echo ucfirst($service['status'] ?? 'unknown'); ?>
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <span style="color: #9ca3af; font-size: 0.75rem;">Version: <?php echo $service['version'] ?? 'Unknown'; ?></span>
                        <span style="color: #9ca3af; font-size: 0.75rem;">Uptime: <?php echo $service['uptime'] ?? '0d 0h'; ?></span>
                    </div>
                    <div>
                        <div style="display: flex; gap: 0.5rem;">
                            <?php if ($service['status'] !== 'running'): ?>
                                <a href="<?php echo app_base_url('/admin/system-status/service/'.($service['id'] ?? '').'/start'); ?>" 
                                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 4px; text-decoration: none; color: #34d399; font-size: 0.875rem; justify-content: center;">
                                    <i class="fas fa-play"></i>
                                    <span>Start</span>
                                </a>
                            <?php else: ?>
                                <a href="<?php echo app_base_url('/admin/system-status/service/'.($service['id'] ?? '').'/stop'); ?>" 
                                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 4px; text-decoration: none; color: #f87171; font-size: 0.875rem; justify-content: center;">
                                    <i class="fas fa-stop"></i>
                                    <span>Stop</span>
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo app_base_url('/admin/system-status/service/'.($service['id'] ?? '').'/restart'); ?>" 
                               style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 4px; text-decoration: none; color: #fbbf24; font-size: 0.875rem; justify-content: center;">
                                <i class="fas fa-redo"></i>
                                <span>Restart</span>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: #9ca3af;">
                <i class="fas fa-server" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                <p>No services configured</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- System Monitoring -->
<div class="admin-card">
    <h2 class="admin-card-title">System Monitoring</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-thermometer-half" style="color: #f87171;"></i>
                Temperature
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">System temperature monitoring</p>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #f9fafb;"><?php echo $system_monitoring['temperature'] ?? 'Normal'; ?></span>
                <span style="color: <?php echo ($system_monitoring['temperature_level'] ?? 'normal') === 'high' ? '#f87171' : '#34d399'; ?>; 
                      background: <?php echo ($system_monitoring['temperature_level'] ?? 'normal') === 'high' ? 'rgba(248, 113, 113, 0.1)' : 'rgba(52, 211, 153, 0.1)'; ?>; 
                      padding: 0.25rem 0.5rem; 
                      border-radius: 4px; 
                      font-size: 0.75rem;">
                    <?php echo ucfirst($system_monitoring['temperature_level'] ?? 'normal'); ?>
                </span>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-battery-full" style="color: #34d399;"></i>
                Power Supply
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">System power status</p>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #f9fafb;"><?php echo $system_monitoring['power_supply'] ?? 'Stable'; ?></span>
                <span style="color: #34d399; background: rgba(52, 211, 153, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">OK</span>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-fan" style="color: #fbbf24;"></i>
                Fan Status
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Cooling system status</p>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #f9fafb;"><?php echo $system_monitoring['fan_status'] ?? 'Normal'; ?></span>
                <span style="color: #fbbf24; background: rgba(251, 191, 36, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">OK</span>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-hdd" style="color: #22d3ee;"></i>
                Storage Health
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Hard disk health status</p>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #f9fafb;"><?php echo $system_monitoring['storage_health'] ?? 'Healthy'; ?></span>
                <span style="color: #34d399; background: rgba(52, 211, 153, 0.1); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Healthy</span>
            </div>
        </div>
    </div>
</div>

<!-- System Logs -->
<div class="admin-card">
    <h2 class="admin-card-title">System Logs</h2>
    <div class="admin-card-content">
        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Level</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Message</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Timestamp</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Source</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($system_logs)): ?>
                        <?php foreach (array_slice($system_logs, 0, 10) as $log): ?>
                            <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <td style="padding: 0.75rem;">
                                    <span style="color: <?php echo $log['level'] === 'error' ? '#f87171' : ($log['level'] === 'warning' ? '#fbbf24' : '#34d399'); ?>; 
                                          background: <?php echo $log['level'] === 'error' ? 'rgba(248, 113, 113, 0.1)' : ($log['level'] === 'warning' ? 'rgba(251, 191, 36, 0.1)' : 'rgba(52, 211, 153, 0.1)'); ?>; 
                                          padding: 0.25rem 0.5rem; 
                                          border-radius: 4px; 
                                          font-size: 0.75rem;">
                                        <?php echo ucfirst($log['level'] ?? 'info'); ?>
                                    </span>
                                </td>
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars(substr($log['message'] ?? 'System message', 0, 60)).(strlen($log['message'] ?? '') > 60 ? '...' : ''); ?></td>
                                <td style="padding: 0.75rem;"><?php echo $log['timestamp'] ?? 'Unknown'; ?></td>
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars($log['source'] ?? 'System'); ?></td>
                                <td style="padding: 0.75rem;">
                                    <a href="<?php echo app_base_url('/admin/system-status/logs/'.($log['id'] ?? 0).'/view'); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem; margin-right: 0.5rem;">
                                        <i class="fas fa-eye"></i>
                                        <span>View</span>
                                    </a>
                                    <a href="<?php echo app_base_url('/admin/system-status/logs/'.($log['id'] ?? 0).'/export'); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 4px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                                        <i class="fas fa-download"></i>
                                        <span>Export</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 1rem; color: #9ca3af;">No system logs available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- System Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">System Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/system-status/diagnostics'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-stethoscope"></i>
            <span>Run Diagnostics</span>
        </a>

        <a href="<?php echo app_base_url('/admin/system-status/maintenance-mode'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171;">
            <i class="fas fa-tools"></i>
            <span>Maintenance Mode</span>
        </a>

        <a href="<?php echo app_base_url('/admin/system-status/check-updates'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-sync-alt"></i>
            <span>Check Updates</span>
        </a>

        <a href="<?php echo app_base_url('/admin/system-status/optimize'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-bolt"></i>
            <span>Optimize System</span>
        </a>

        <a href="<?php echo app_base_url('/admin/system-status/restart'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-power-off"></i>
            <span>Restart System</span>
        </a>

        <a href="<?php echo app_base_url('/admin/system-status/backup'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 6px; text-decoration: none; color: #a78bfa;">
            <i class="fas fa-database"></i>
            <span>System Backup</span>
        </a>

        <a href="<?php echo app_base_url('/admin/system-status/logs'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(236, 72, 153, 0.1); border: 1px solid rgba(236, 72, 153, 0.2); border-radius: 6px; text-decoration: none; color: #ec4899;">
            <i class="fas fa-file-alt"></i>
            <span>View All Logs</span>
        </a>
    </div>
</div>

<!-- Performance Indicators -->
<div class="admin-card">
    <h2 class="admin-card-title">Performance Indicators</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-bolt" style="color: #4cc9f0;"></i>
                Response Time
            </h3>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <span style="color: #9ca3af;">Average Response:</span>
                <span style="color: #f9fafb;"><?php echo $performance_indicators['avg_response_time'] ?? '<100ms'; ?></span>
            </div>
            <div style="height: 10px; background: rgba(102, 126, 234, 0.2); border-radius: 5px; overflow: hidden;">
                <div style="height: 100%; width: <?php echo min(100, ($performance_indicators['avg_response_time_ms'] ?? 100) / 1000 * 100); ?>%; background: <?php echo ($performance_indicators['avg_response_time_ms'] ?? 100) > 500 ? '#f87171' : (($performance_indicators['avg_response_time_ms'] ?? 100) > 200 ? '#fbbf24' : '#34d399'); ?>;"></div>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-tachometer-alt" style="color: #34d399;"></i>
                Throughput
            </h3>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <span style="color: #9ca3af;">Requests/Second:</span>
                <span style="color: #f9fafb;"><?php echo number_format($performance_indicators['requests_per_second'] ?? 0, 2); ?></span>
            </div>
            <div style="height: 10px; background: rgba(102, 126, 234, 0.2); border-radius: 5px; overflow: hidden;">
                <div style="height: 100%; width: <?php echo min(100, ($performance_indicators['requests_per_second'] ?? 0) / 100 * 100); ?>%; background: <?php echo ($performance_indicators['requests_per_second'] ?? 0) > 50 ? '#34d399' : (($performance_indicators['requests_per_second'] ?? 0) > 20 ? '#fbbf24' : '#f87171'); ?>;"></div>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-concierge-bell" style="color: #fbbf24;"></i>
                Availability
            </h3>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <span style="color: #9ca3af;">Uptime Percentage:</span>
                <span style="color: #f9fafb;"><?php echo $performance_indicators['uptime_percentage'] ?? '100%'; ?></span>
            </div>
            <div style="height: 10px; background: rgba(102, 126, 234, 0.2); border-radius: 5px; overflow: hidden;">
                <div style="height: 100%; width: <?php echo $performance_indicators['uptime_percentage'] ?? 100; ?>%; background: <?php echo ($performance_indicators['uptime_percentage'] ?? 100) > 99 ? '#34d399' : (($performance_indicators['uptime_percentage'] ?? 100) > 95 ? '#fbbf24' : '#f87171'); ?>;"></div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>