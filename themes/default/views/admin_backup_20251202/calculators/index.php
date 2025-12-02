<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Calculator Management</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Manage available calculators and their settings</p>
        </div>
    </div>
</div>

<!-- Calculator Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-cube" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Calculators</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_calculators'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Available</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +5 this month</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-toggle-on" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Active Calculators</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['active_calculators'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Enabled</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> All Operational</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-calculator" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Usage Today</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($stats['today_usage'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Calculations</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-fire"></i> High Usage</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-star" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Top Calculator</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($stats['top_calculator'] ?? 'None'); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Most Used</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-trophy"></i> Popular</small>
    </div>
</div>

<!-- Available Calculators -->
<div class="admin-card">
    <h2 class="admin-card-title">Available Calculators</h2>
    <div class="admin-card-content">
        <div style="overflow-x: auto;">
            <table class="admin-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Name</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Type</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Status</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Usage</th>
                        <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($calculators)): ?>
                        <?php foreach ($calculators as $calc): ?>
                            <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                                <td style="padding: 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fas <?php echo $calc['icon'] ?? 'fa-calculator'; ?>" style="color: #4cc9f0;"></i>
                                        <span><?php echo htmlspecialchars($calc['name'] ?? 'Unknown Calculator'); ?></span>
                                    </div>
                                </td>
                                <td style="padding: 0.75rem;"><?php echo htmlspecialchars(ucfirst($calc['type'] ?? 'general')); ?></td>
                                <td style="padding: 0.75rem;">
                                    <span class="status-<?php echo $calc['status'] === 'active' ? 'success' : 'error'; ?>" 
                                          style="background: rgba(<?php echo $calc['status'] === 'active' ? '52, 211, 153, 0.1' : '248, 113, 113, 0.1'; ?>); 
                                                 border: 1px solid rgba(<?php echo $calc['status'] === 'active' ? '52, 211, 153, 0.3' : '248, 113, 113, 0.3'; ?>); 
                                                 padding: 0.25rem 0.5rem; 
                                                 border-radius: 4px; 
                                                 font-size: 0.75rem;">
                                        <?php echo ucfirst($calc['status'] ?? 'inactive'); ?>
                                    </span>
                                </td>
                                <td style="padding: 0.75rem;"><?php echo number_format($calc['usage'] ?? 0); ?> uses</td>
                                <td style="padding: 0.75rem;">
                                    <a href="<?php echo app_base_url('/admin/calculators/'.($calc['id'] ?? 0).'/edit'); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 4px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem; margin-right: 0.5rem;">
                                        <i class="fas fa-edit"></i>
                                        <span>Edit</span>
                                    </a>
                                    <a href="<?php echo app_base_url('/admin/calculators/'.($calc['id'] ?? 0).'/toggle'); ?>" 
                                       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(<?php echo $calc['status'] === 'active' ? '248, 113, 113, 0.1' : '52, 211, 153, 0.1'; ?>); border: 1px solid rgba(<?php echo $calc['status'] === 'active' ? '248, 113, 113, 0.2' : '52, 211, 153, 0.2'; ?>); border-radius: 4px; text-decoration: none; color: <?php echo $calc['status'] === 'active' ? '#f87171' : '#34d399'; ?>; font-size: 0.875rem;">
                                        <i class="fas fa-<?php echo $calc['status'] === 'active' ? 'toggle-off' : 'toggle-on'; ?>"></i>
                                        <span><?php echo $calc['status'] === 'active' ? 'Disable' : 'Enable'; ?></span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 1rem; color: #9ca3af;">No calculators available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Calculator Categories -->
<div class="admin-card">
    <h2 class="admin-card-title">Calculator Categories</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-calculator" style="color: #4cc9f0;"></i>
                Basic Calculators
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($stats['basic_calculators'] ?? 0); ?> calculators</p>
            <a href="<?php echo app_base_url('/admin/calculators/category/basic'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem;">
                <span>View Category</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-flask" style="color: #34d399;"></i>
                Scientific Calculators
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($stats['scientific_calculators'] ?? 0); ?> calculators</p>
            <a href="<?php echo app_base_url('/admin/calculators/category/scientific'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                <span>View Category</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-chart-line" style="color: #fbbf24;"></i>
                Financial Calculators
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($stats['financial_calculators'] ?? 0); ?> calculators</p>
            <a href="<?php echo app_base_url('/admin/calculators/category/financial'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24; font-size: 0.875rem;">
                <span>View Category</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-cogs" style="color: #22d3ee;"></i>
                Engineering Calculators
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($stats['engineering_calculators'] ?? 0); ?> calculators</p>
            <a href="<?php echo app_base_url('/admin/calculators/category/engineering'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee; font-size: 0.875rem;">
                <span>View Category</span>
            </a>
        </div>
    </div>
</div>

<!-- Calculator Management -->
<div class="admin-card">
    <h2 class="admin-card-title">Calculator Management</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/calculators/create'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-plus-circle"></i>
            <span>Add New Calculator</span>
        </a>

        <a href="<?php echo app_base_url('/admin/calculators/import'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-file-import"></i>
            <span>Import Calculator</span>
        </a>

        <a href="<?php echo app_base_url('/admin/calculators/export'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-file-export"></i>
            <span>Export Calculators</span>
        </a>

        <a href="<?php echo app_base_url('/admin/calculators/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>