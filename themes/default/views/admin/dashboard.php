<?php
ob_start();
?>

<!-- Page Header -->
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
    <div>
        <h1 style="font-size: 1.75rem; font-weight: 600; color: #f9fafb; margin: 0 0 0.5rem 0;">Admin Dashboard</h1>
        <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Welcome back! Here's an overview of your engineering calculator platform.</p>
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
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Total Users</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +12% this month</small>
    </div>
    
    <!-- Calculations -->
    <div class="stat-card" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.5rem; text-align: center; transition: transform 0.2s ease;">
        <div style="width: 50px; height: 50px; background: rgba(16, 185, 129, 0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">
            <i class="fas fa-calculator" style="font-size: 1.5rem; color: #34d399;"></i>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_calculations'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Calculations</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +8% this month</small>
    </div>
    
    <!-- Active Modules -->
    <div class="stat-card" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.5rem; text-align: center; transition: transform 0.2s ease;">
        <div style="width: 50px; height: 50px; background: rgba(245, 158, 11, 0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">
            <i class="fas fa-cubes" style="font-size: 1.5rem; color: #fbbf24;"></i>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($stats['active_modules'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Active Modules</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check"></i> All operational</small>
    </div>
    
    <!-- System Health -->
    <div class="stat-card" style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.5rem; text-align: center; transition: transform 0.2s ease;">
        <div style="width: 50px; height: 50px; background: rgba(6, 182, 212, 0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">
            <i class="fas fa-heartbeat" style="font-size: 1.5rem; color: #22d3ee;"></i>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo number_format($stats['system_health'] ?? 100, 1); ?>%</div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">System Health</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Excellent</small>
    </div>
    
</div>

<!-- Main Content Grid -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    
    <!-- System Overview -->
    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem;">
        <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-chart-bar" style="color: #4cc9f0;"></i>
            System Overview
        </h5>
        
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; margin-bottom: 2rem;">
            <div style="text-align: center;">
                <h3 style="font-size: 1.75rem; color: #4cc9f0; margin: 0 0 0.5rem 0;"><?php echo number_format($stats['active_users'] ?? 0); ?></h3>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Active Users</p>
            </div>
            <div style="text-align: center;">
                <h3 style="font-size: 1.75rem; color: #34d399; margin: 0 0 0.5rem 0;"><?php echo number_format($stats['monthly_calculations'] ?? 0); ?></h3>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Monthly Calculations</p>
            </div>
            <div style="text-align: center;">
                <h3 style="font-size: 1.75rem; color: #fbbf24; margin: 0 0 0.5rem 0;"><?php echo number_format($stats['storage_used'] ?? 0); ?>%</h3>
                <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Storage Used</p>
            </div>
        </div>
        
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(102, 126, 234, 0.2);">
            <h6 style="font-size: 0.875rem; font-weight: 600; color: #f9fafb; margin: 0 0 1rem 0;">Recent Activity</h6>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-user-plus" style="color: #4cc9f0;"></i>
                    <span style="color: #e5e7eb; font-size: 0.875rem;">New user registered</span>
                    <small style="color: #9ca3af; margin-left: auto; font-size: 0.75rem;">2 hours ago</small>
                </li>
                <li style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-cog" style="color: #34d399;"></i>
                    <span style="color: #e5e7eb; font-size: 0.875rem;">System settings updated</span>
                    <small style="color: #9ca3af; margin-left: auto; font-size: 0.75rem;">4 hours ago</small>
                </li>
                <li style="margin-bottom: 0; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-database" style="color: #22d3ee;"></i>
                    <span style="color: #e5e7eb; font-size: 0.875rem;">Database backup completed</span>
                    <small style="color: #9ca3af; margin-left: auto; font-size: 0.75rem;">1 day ago</small>
                </li>
            </ul>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem;">
        <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-bolt" style="color: #fbbf24;"></i>
            Quick Actions
        </h5>
        
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <a href="<?php echo app_base_url('/admin/settings'); ?>" style="display: flex; align-items: flex-start; gap: 1rem; padding: 1rem; background: rgba(67, 97, 238, 0.05); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 8px; text-decoration: none; transition: all 0.2s ease; color: inherit;">
                <i class="fas fa-cog" style="color: #4cc9f0; font-size: 1.25rem; margin-top: 0.125rem;"></i>
                <div>
                    <strong style="color: #f9fafb; font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">Settings</strong>
                    <small style="color: #9ca3af; font-size: 0.75rem;">Configure system settings</small>
                </div>
            </a>
            
            <a href="<?php echo app_base_url('/admin/users'); ?>" style="display: flex; align-items: flex-start; gap: 1rem; padding: 1rem; background: rgba(67, 97, 238, 0.05); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 8px; text-decoration: none; transition: all 0.2s ease; color: inherit;">
                <i class="fas fa-users" style="color: #34d399; font-size: 1.25rem; margin-top: 0.125rem;"></i>
                <div>
                    <strong style="color: #f9fafb; font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">Manage Users</strong>
                    <small style="color: #9ca3af; font-size: 0.75rem;">User accounts and roles</small>
                </div>
            </a>
            
            <a href="<?php echo app_base_url('/admin/modules'); ?>" style="display: flex; align-items: flex-start; gap: 1rem; padding: 1rem; background: rgba(67, 97, 238, 0.05); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 8px; text-decoration: none; transition: all 0.2s ease; color: inherit;">
                <i class="fas fa-cubes" style="color: #fbbf24; font-size: 1.25rem; margin-top: 0.125rem;"></i>
                <div>
                    <strong style="color: #f9fafb; font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">Modules</strong>
                    <small style="color: #9ca3af; font-size: 0.75rem;">Manage active modules</small>
                </div>
            </a>
            
            <a href="<?php echo app_base_url('/help'); ?>" style="display: flex; align-items: flex-start; gap: 1rem; padding: 1rem; background: rgba(67, 97, 238, 0.05); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 8px; text-decoration: none; transition: all 0.2s ease; color: inherit;">
                <i class="fas fa-question-circle" style="color: #22d3ee; font-size: 1.25rem; margin-top: 0.125rem;"></i>
                <div>
                    <strong style="color: #f9fafb; font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">Help Center</strong>
                    <small style="color: #9ca3af; font-size: 0.75rem;">Documentation and support</small>
                </div>
            </a>
        </div>
    </div>
    
</div>

<!-- System Status -->
<div style="margin-top: 1.5rem; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 12px; padding: 1.5rem;">
    <h5 style="font-size: 1rem; font-weight: 600; color: #34d399; margin: 0 0 0.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
        <i class="fas fa-check-circle"></i>
        System Status: Operational
    </h5>
    <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">All systems are running normally. Last checked: <?php echo date('Y-m-d H:i:s'); ?></p>
</div>

<style>
.stat-card:hover {
    transform: translateY(-2px);
}
a[style*="background: rgba(67, 97, 238, 0.05)"]:hover {
    background: rgba(67, 97, 238, 0.1) !important;
    transform: translateX(5px);
}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
