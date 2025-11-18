<?php
ob_start();
?>

<!-- Page Header -->
<div class="page-header" style="margin-bottom: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 600; color: #f9fafb; margin: 0 0 0.5rem 0;">Admin Dashboard</h1>
            <p style="color: #9ca3af; margin: 0; font-size: 0.875rem;">Welcome back! Here's an overview of your engineering calculator platform.</p>
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
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
    
    <!-- System Overview -->
    <div style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 12px; padding: 1.75rem;">
        <h5 style="font-size: 1.125rem; font-weight: 600; color: #f9fafb; margin: 0 0 1.5rem 0; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-chart-line" style="color: #4cc9f0;"></i>
            System Overview
        </h5>
        
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 2rem;">
            <div style="text-align: center; padding: 1rem; background: rgba(67, 97, 238, 0.05); border-radius: 8px;">
                <h3 style="font-size: 1.5rem; color: #4cc9f0; margin: 0 0 0.5rem 0;"><?php echo number_format($stats['active_users'] ?? 0); ?></h3>
                <p style="color: #9ca3af; font-size: 0.75rem; margin: 0;">Active Users</p>
            </div>
            <div style="text-align: center; padding: 1rem; background: rgba(52, 211, 153, 0.05); border-radius: 8px;">
                <h3 style="font-size: 1.5rem; color: #34d399; margin: 0 0 0.5rem 0;"><?php echo number_format($stats['monthly_calculations'] ?? 0); ?></h3>
                <p style="color: #9ca3af; font-size: 0.75rem; margin: 0;">Monthly Calcs</p>
            </div>
            <div style="text-align: center; padding: 1rem; background: rgba(251, 191, 36, 0.05); border-radius: 8px;">
                <h3 style="font-size: 1.5rem; color: #fbbf24; margin: 0 0 0.5rem 0;"><?php echo number_format($stats['storage_used'] ?? 0); ?>%</h3>
                <p style="color: #9ca3af; font-size: 0.75rem; margin: 0;">Storage</p>
            </div>
        </div>
        
        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(102, 126, 234, 0.2);">
            <h6 style="font-size: 0.875rem; font-weight: 600; color: #f9fafb; margin: 0 0 1rem 0;">Recent Activity</h6>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem; background: rgba(67, 97, 238, 0.03); border-radius: 6px;">
                    <i class="fas fa-user-plus" style="color: #4cc9f0; width: 20px;"></i>
                    <span style="color: #e5e7eb; font-size: 0.875rem;">New user registered</span>
                    <small style="color: #9ca3af; margin-left: auto; font-size: 0.75rem;">2h ago</small>
                </li>
                <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.03); border-radius: 6px;">
                    <i class="fas fa-cog" style="color: #34d399; width: 20px;"></i>
                    <span style="color: #e5e7eb; font-size: 0.875rem;">System settings updated</span>
                    <small style="color: #9ca3af; margin-left: auto; font-size: 0.75rem;">4h ago</small>
                </li>
                <li style="margin-bottom: 0; display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem; background: rgba(34, 211, 238, 0.03); border-radius: 6px;">
                    <i class="fas fa-database" style="color: #22d3ee; width: 20px;"></i>
                    <span style="color: #e5e7eb; font-size: 0.875rem;">Database backup completed</span>
                    <small style="color: #9ca3af; margin-left: auto; font-size: 0.75rem;">1d ago</small>
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
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.75rem;">
            <a href="<?php echo app_base_url('/admin/settings'); ?>" style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.25rem; background: rgba(67, 97, 238, 0.05); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 8px; text-decoration: none; transition: all 0.2s ease; text-align: center; cursor: pointer;">
                <div style="width: 48px; height: 48px; background: rgba(67, 97, 238, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-cog" style="color: #4cc9f0; font-size: 1.25rem;"></i>
                </div>
                <div>
                    <strong style="color: #f9fafb; font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">Settings</strong>
                    <small style="color: #9ca3af; font-size: 0.75rem;">System config</small>
                </div>
            </a>
            
            <a href="<?php echo app_base_url('/admin/users'); ?>" style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.25rem; background: rgba(52, 211, 153, 0.05); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 8px; text-decoration: none; transition: all 0.2s ease; text-align: center; cursor: pointer;">
                <div style="width: 48px; height: 48px; background: rgba(52, 211, 153, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-users" style="color: #34d399; font-size: 1.25rem;"></i>
                </div>
                <div>
                    <strong style="color: #f9fafb; font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">Users</strong>
                    <small style="color: #9ca3af; font-size: 0.75rem;">Manage users</small>
                </div>
            </a>
            
            <a href="<?php echo app_base_url('/admin/modules'); ?>" style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.25rem; background: rgba(251, 191, 36, 0.05); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 8px; text-decoration: none; transition: all 0.2s ease; text-align: center; cursor: pointer;">
                <div style="width: 48px; height: 48px; background: rgba(251, 191, 36, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-cubes" style="color: #fbbf24; font-size: 1.25rem;"></i>
                </div>
                <div>
                    <strong style="color: #f9fafb; font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">Modules</strong>
                    <small style="color: #9ca3af; font-size: 0.75rem;">View modules</small>
                </div>
            </a>
            
            <a href="<?php echo app_base_url('/admin/backup'); ?>" style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem; padding: 1.25rem; background: rgba(34, 211, 238, 0.05); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 8px; text-decoration: none; transition: all 0.2s ease; text-align: center; cursor: pointer;">
                <div style="width: 48px; height: 48px; background: rgba(34, 211, 238, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-database" style="color: #22d3ee; font-size: 1.25rem;"></i>
                </div>
                <div>
                    <strong style="color: #f9fafb; font-size: 0.875rem; display: block; margin-bottom: 0.25rem;">Backup</strong>
                    <small style="color: #9ca3af; font-size: 0.75rem;">Data backup</small>
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
/* Dashboard Enhancements */
.stat-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(67, 97, 238, 0.3);
}

/* Quick Action Buttons */
a[href*="/admin/"]:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
}

/* Activity Items */
li[style*="background: rgba"] {
    transition: all 0.2s ease;
}

li[style*="background: rgba"]:hover {
    transform: translateX(4px);
    box-shadow: 0 2px 8px rgba(67, 97, 238, 0.15);
}

/* Responsive Design */
@media (max-width: 768px) {
    div[style*="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr))"] {
        grid-template-columns: 1fr !important;
    }
    
    div[style*="grid-template-columns: repeat(auto-fit, minmax(400px, 1fr))"] {
        grid-template-columns: 1fr !important;
    }
    
    div[style*="grid-template-columns: repeat(3, 1fr)"] {
        grid-template-columns: 1fr !important;
    }
}

@media (max-width: 480px) {
    .admin-content {
        padding: 1rem !important;
    }
    
    h1 {
        font-size: 1.5rem !important;
    }
}
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
