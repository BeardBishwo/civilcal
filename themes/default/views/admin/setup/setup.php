<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Setup Wizard</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Complete initial system setup and configuration wizard</p>
        </div>
    </div>
</div>

<!-- Setup Progress -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-tasks" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Setup Progress</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($setup_progress['percentage'] ?? 0, 0); ?>%</div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Complete</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> <?php echo $setup_progress['completed'] ?? 0; ?>/<?php echo $setup_progress['total'] ?? 0; ?> steps</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-check-circle" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Completed Steps</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($setup_progress['completed_steps'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Finished</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check"></i> Good Progress</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-clock" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Remaining Steps</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($setup_progress['remaining_steps'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">To Complete</div>
        <small style="color: #fbbf24; font-size: 0.75rem;"><i class="fas fa-list"></i> Finish Setup</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-bolt" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Next Step</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($setup_progress['next_step'] ?? 'Start Setup'); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Recommended</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-star"></i> Priority</small>
    </div>
</div>

<!-- Setup Checklist -->
<div class="admin-card">
    <h2 class="admin-card-title">Setup Checklist</h2>
    <div class="admin-card-content">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                    <div style="width: 40px; height: 40px; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-<?php echo $setup_steps['system']['complete'] ? 'check' : 'circle'; ?>" style="color: <?php echo $setup_steps['system']['complete'] ? '#34d399' : '#9ca3af'; ?>;"></i>
                    </div>
                    <div>
                        <h3 style="color: <?php echo $setup_steps['system']['complete'] ? '#34d399' : '#f9fafb'; ?>; margin: 0;"><?php echo $setup_steps['system']['title'] ?? 'System Configuration'; ?></h3>
                        <p style="color: #9ca3af; margin: 0.25rem 0 0 0; font-size: 0.875rem;"><?php echo $setup_steps['system']['description'] ?? 'Complete system configuration'; ?></p>
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="<?php echo app_base_url('/admin/setup/system'); ?>" 
                       style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: <?php echo $setup_steps['system']['complete'] ? 'rgba(52, 211, 153, 0.1)' : 'rgba(102, 126, 234, 0.1)'; ?>; border: 1px solid <?php echo $setup_steps['system']['complete'] ? 'rgba(52, 211, 153, 0.2)' : 'rgba(102, 126, 234, 0.2)'; ?>; border-radius: 6px; text-decoration: none; color: <?php echo $setup_steps['system']['complete'] ? '#34d399' : '#4cc9f0'; ?>; font-size: 0.875rem; justify-content: center; min-width: 80px;">
                        <i class="fas fa-<?php echo $setup_steps['system']['complete'] ? 'eye' : 'cog'; ?>"></i>
                        <span><?php echo $setup_steps['system']['complete'] ? 'Review' : 'Configure'; ?></span>
                    </a>
                    <?php if ($setup_steps['system']['complete']): ?>
                        <a href="<?php echo app_base_url('/admin/setup/system/reset'); ?>" 
                           style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171; font-size: 0.875rem; justify-content: center; min-width: 80px;">
                            <i class="fas fa-undo"></i>
                            <span>Reset</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                    <div style="width: 40px; height: 40px; background: rgba(<?php echo $setup_steps['database']['complete'] ? '52, 211, 153, 0.1' : '102, 126, 234, 0.1'; ?>); border: 1px solid rgba(<?php echo $setup_steps['database']['complete'] ? '52, 211, 153, 0.2' : '102, 126, 234, 0.2'; ?>); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-<?php echo $setup_steps['database']['complete'] ? 'check' : 'circle'; ?>" style="color: <?php echo $setup_steps['database']['complete'] ? '#34d399' : '#9ca3af'; ?>;"></i>
                    </div>
                    <div>
                        <h3 style="color: <?php echo $setup_steps['database']['complete'] ? '#34d399' : '#f9fafb'; ?>; margin: 0;"><?php echo $setup_steps['database']['title'] ?? 'Database Configuration'; ?></h3>
                        <p style="color: #9ca3af; margin: 0.25rem 0 0 0; font-size: 0.875rem;"><?php echo $setup_steps['database']['description'] ?? 'Configure database connection'; ?></p>
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="<?php echo app_base_url('/admin/setup/database'); ?>" 
                       style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: <?php echo $setup_steps['database']['complete'] ? 'rgba(52, 211, 153, 0.1)' : 'rgba(102, 126, 234, 0.1)'; ?>; border: 1px solid <?php echo $setup_steps['database']['complete'] ? 'rgba(52, 211, 153, 0.2)' : 'rgba(102, 126, 234, 0.2)'; ?>; border-radius: 6px; text-decoration: none; color: <?php echo $setup_steps['database']['complete'] ? '#34d399' : '#4cc9f0'; ?>; font-size: 0.875rem; justify-content: center; min-width: 80px;">
                        <i class="fas fa-<?php echo $setup_steps['database']['complete'] ? 'eye' : 'cog'; ?>"></i>
                        <span><?php echo $setup_steps['database']['complete'] ? 'Review' : 'Configure'; ?></span>
                    </a>
                    <?php if ($setup_steps['database']['complete']): ?>
                        <a href="<?php echo app_base_url('/admin/setup/database/reset'); ?>" 
                           style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171; font-size: 0.875rem; justify-content: center; min-width: 80px;">
                            <i class="fas fa-undo"></i>
                            <span>Reset</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                    <div style="width: 40px; height: 40px; background: rgba(<?php echo $setup_steps['security']['complete'] ? '52, 211, 153, 0.1' : '102, 126, 234, 0.1'; ?>); border: 1px solid rgba(<?php echo $setup_steps['security']['complete'] ? '52, 211, 153, 0.2' : '102, 126, 234, 0.2'; ?>); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-<?php echo $setup_steps['security']['complete'] ? 'check' : 'circle'; ?>" style="color: <?php echo $setup_steps['security']['complete'] ? '#34d399' : '#9ca3af'; ?>;"></i>
                    </div>
                    <div>
                        <h3 style="color: <?php echo $setup_steps['security']['complete'] ? '#34d399' : '#f9fafb'; ?>; margin: 0;"><?php echo $setup_steps['security']['title'] ?? 'Security Setup'; ?></h3>
                        <p style="color: #9ca3af; margin: 0.25rem 0 0 0; font-size: 0.875rem;"><?php echo $setup_steps['security']['description'] ?? 'Configure security measures'; ?></p>
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="<?php echo app_base_url('/admin/setup/security'); ?>" 
                       style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: <?php echo $setup_steps['security']['complete'] ? 'rgba(52, 211, 153, 0.1)' : 'rgba(102, 126, 234, 0.1)'; ?>; border: 1px solid <?php echo $setup_steps['security']['complete'] ? 'rgba(52, 211, 153, 0.2)' : 'rgba(102, 126, 234, 0.2)'; ?>; border-radius: 6px; text-decoration: none; color: <?php echo $setup_steps['security']['complete'] ? '#34d399' : '#4cc9f0'; ?>; font-size: 0.875rem; justify-content: center; min-width: 80px;">
                        <i class="fas fa-<?php echo $setup_steps['security']['complete'] ? 'eye' : 'cog'; ?>"></i>
                        <span><?php echo $setup_steps['security']['complete'] ? 'Review' : 'Configure'; ?></span>
                    </a>
                    <?php if ($setup_steps['security']['complete']): ?>
                        <a href="<?php echo app_base_url('/admin/setup/security/reset'); ?>" 
                           style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171; font-size: 0.875rem; justify-content: center; min-width: 80px;">
                            <i class="fas fa-undo"></i>
                            <span>Reset</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                    <div style="width: 40px; height: 40px; background: rgba(<?php echo $setup_steps['email']['complete'] ? '52, 211, 153, 0.1' : '102, 126, 234, 0.1'; ?>); border: 1px solid rgba(<?php echo $setup_steps['email']['complete'] ? '52, 211, 153, 0.2' : '102, 126, 234, 0.2'; ?>); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-<?php echo $setup_steps['email']['complete'] ? 'check' : 'circle'; ?>" style="color: <?php echo $setup_steps['email']['complete'] ? '#34d399' : '#9ca3af'; ?>;"></i>
                    </div>
                    <div>
                        <h3 style="color: <?php echo $setup_steps['email']['complete'] ? '#34d399' : '#f9fafb'; ?>; margin: 0;"><?php echo $setup_steps['email']['title'] ?? 'Email Configuration'; ?></h3>
                        <p style="color: #9ca3af; margin: 0.25rem 0 0 0; font-size: 0.875rem;"><?php echo $setup_steps['email']['description'] ?? 'Setup email settings'; ?></p>
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="<?php echo app_base_url('/admin/setup/email'); ?>" 
                       style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: <?php echo $setup_steps['email']['complete'] ? 'rgba(52, 211, 153, 0.1)' : 'rgba(102, 126, 234, 0.1)'; ?>; border: 1px solid <?php echo $setup_steps['email']['complete'] ? 'rgba(52, 211, 153, 0.2)' : 'rgba(102, 126, 234, 0.2)'; ?>; border-radius: 6px; text-decoration: none; color: <?php echo $setup_steps['email']['complete'] ? '#34d399' : '#4cc9f0'; ?>; font-size: 0.875rem; justify-content: center; min-width: 80px;">
                        <i class="fas fa-<?php echo $setup_steps['email']['complete'] ? 'eye' : 'cog'; ?>"></i>
                        <span><?php echo $setup_steps['email']['complete'] ? 'Review' : 'Configure'; ?></span>
                    </a>
                    <?php if ($setup_steps['email']['complete']): ?>
                        <a href="<?php echo app_base_url('/admin/setup/email/reset'); ?>" 
                           style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171; font-size: 0.875rem; justify-content: center; min-width: 80px;">
                            <i class="fas fa-undo"></i>
                            <span>Reset</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Current Setup Step -->
<div class="admin-card">
    <h2 class="admin-card-title">Current Setup: <?php echo htmlspecialchars($current_step['title'] ?? 'System Configuration'); ?></h2>
    <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
        <div style="margin-bottom: 1.5rem;">
            <h3 style="color: #f9fafb; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-<?php echo $current_step['icon'] ?? 'cog'; ?>" style="color: #4cc9f0;"></i>
                <?php echo htmlspecialchars($current_step['title'] ?? 'System Configuration'); ?>
            </h3>
            <p style="color: #9ca3af; margin: 0;"><?php echo htmlspecialchars($current_step['description'] ?? ''); ?></p>
        </div>
        
        <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px; text-align: center;">
                <div style="font-size: 1.5rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo $current_step['step_number'] ?? '1'; ?></div>
                <div style="color: #9ca3af; font-size: 0.875rem;">Step</div>
            </div>
            
            <div style="flex: 3; min-width: 250px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: #9ca3af;">Progress</span>
                    <span style="color: #f9fafb;"><?php echo $current_step['progress'] ?? '0'; ?>%</span>
                </div>
                <div style="height: 8px; background: rgba(102, 126, 234, 0.2); border-radius: 4px; overflow: hidden;">
                    <div style="height: 100%; width: <?php echo $current_step['progress'] ?? 0; ?>%; background: #4cc9f0;"></div>
                </div>
            </div>
        </div>
        
        <div style="margin-bottom: 1.5rem;">
            <h4 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-tasks" style="color: #34d399;"></i>
                Instructions
            </h4>
            <ol style="color: #9ca3af; padding-left: 1.5rem; margin: 0;">
                <?php if (!empty($current_step['instructions'])): ?>
                    <?php foreach ($current_step['instructions'] as $instruction): ?>
                        <li style="margin-bottom: 0.75rem;"><?php echo htmlspecialchars($instruction); ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="margin-bottom: 0.75rem;">Follow the on-screen prompts to complete this setup step</li>
                <?php endif; ?>
            </ol>
        </div>
        
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="<?php echo app_base_url('/admin/setup/'.($current_step['id'] ?? 'system')); ?>" 
               style="flex: 1; padding: 0.75rem 1.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #4cc9f0; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <i class="fas fa-cog"></i>
                <span>Continue Setup</span>
            </a>
            
            <a href="<?php echo app_base_url('/admin/setup/skip-step'); ?>" 
               style="flex: 1; padding: 0.75rem 1.5rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; color: #fbbf24; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <i class="fas fa-forward"></i>
                <span>Skip Step</span>
            </a>
            
            <a href="<?php echo app_base_url('/admin/setup/previous-step'); ?>" 
               style="flex: 1; padding: 0.75rem 1.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; color: #22d3ee; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <i class="fas fa-arrow-left"></i>
                <span>Previous</span>
            </a>
        </div>
    </div>
</div>

<!-- Setup Requirements -->
<div class="admin-card">
    <h2 class="admin-card-title">System Requirements</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-server" style="color: #4cc9f0;"></i>
                Server Requirements
            </h3>
            <ul style="list-style: none; padding: 0; margin: 0; color: #9ca3af;">
                <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-<?php echo $requirements['php'] ? 'check-circle' : 'times-circle'; ?>" 
                       style="color: <?php echo $requirements['php'] ? '#34d399' : '#f87171'; ?>;"></i>
                    <span>PHP <?php echo $requirements['php_version'] ?? '7.4+'; ?>: <?php echo $requirements['php'] ? 'Met' : 'Not Met'; ?></span>
                </li>
                <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-<?php echo $requirements['mysql'] ? 'check-circle' : 'times-circle'; ?>" 
                       style="color: <?php echo $requirements['mysql'] ? '#34d399' : '#f87171'; ?>;"></i>
                    <span>MySQL <?php echo $requirements['mysql_version'] ?? '5.7+'; ?>: <?php echo $requirements['mysql'] ? 'Met' : 'Not Met'; ?></span>
                </li>
                <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-<?php echo $requirements['curl'] ? 'check-circle' : 'times-circle'; ?>" 
                       style="color: <?php echo $requirements['curl'] ? '#34d399' : '#f87171'; ?>;"></i>
                    <span>cURL Extension: <?php echo $requirements['curl'] ? 'Available' : 'Missing'; ?></span>
                </li>
                <li style="margin-bottom: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-<?php echo $requirements['gd'] ? 'check-circle' : 'times-circle'; ?>" 
                       style="color: <?php echo $requirements['gd'] ? '#34d399' : '#f87171'; ?>;"></i>
                    <span>GD Library: <?php echo $requirements['gd'] ? 'Available' : 'Missing'; ?></span>
                </li>
            </ul>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-hdd" style="color: #34d399;"></i>
                Storage Requirements
            </h3>
            <ul style="list-style: none; padding: 0; margin: 0; color: #9ca3af;">
                <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-<?php echo $requirements['disk_space'] ? 'check-circle' : 'times-circle'; ?>" 
                       style="color: <?php echo $requirements['disk_space'] ? '#34d399' : '#f87171'; ?>;"></i>
                    <span>Disk Space (<?php echo $requirements['free_space'] ?? '0MB'; ?>/<?php echo $requirements['required_space'] ?? '100MB'; ?>): <?php echo $requirements['disk_space'] ? 'Sufficient' : 'Insufficient'; ?></span>
                </li>
                <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-<?php echo $requirements['writable_dirs'] ? 'check-circle' : 'times-circle'; ?>" 
                       style="color: <?php echo $requirements['writable_dirs'] ? '#34d399' : '#f87171'; ?>;"></i>
                    <span>Writable Directories: <?php echo $requirements['writable_dirs'] ? 'Configured' : 'Problem'; ?></span>
                </li>
                <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-<?php echo $requirements['tmp_dir'] ? 'check-circle' : 'times-circle'; ?>" 
                       style="color: <?php echo $requirements['tmp_dir'] ? '#34d399' : '#f87171'; ?>;"></i>
                    <span>Temporary Directory: <?php echo $requirements['tmp_dir'] ? 'Accessible' : 'Not Accessible'; ?></span>
                </li>
                <li style="margin-bottom: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-<?php echo $requirements['log_dir'] ? 'check-circle' : 'times-circle'; ?>" 
                       style="color: <?php echo $requirements['log_dir'] ? '#34d399' : '#f87171'; ?>;"></i>
                    <span>Log Directory: <?php echo $requirements['log_dir'] ? 'Writable' : 'Not Writable'; ?></span>
                </li>
            </ul>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-shield-alt" style="color: #fbbf24;"></i>
                Security Requirements
            </h3>
            <ul style="list-style: none; padding: 0; margin: 0; color: #9ca3af;">
                <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-<?php echo $requirements['ssl'] ? 'check-circle' : 'times-circle'; ?>" 
                       style="color: <?php echo $requirements['ssl'] ? '#34d399' : '#f87171'; ?>;"></i>
                    <span>SSL Certificate: <?php echo $requirements['ssl'] ? 'Available' : 'Missing'; ?></span>
                </li>
                <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-<?php echo $requirements['htaccess'] ? 'check-circle' : 'times-circle'; ?>" 
                       style="color: <?php echo $requirements['htaccess'] ? '#34d399' : '#f87171'; ?>;"></i>
                    <span>.htaccess Protection: <?php echo $requirements['htaccess'] ? 'Configured' : 'Missing'; ?></span>
                </li>
                <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-<?php echo $requirements['php_extensions'] ? 'check-circle' : 'times-circle'; ?>" 
                       style="color: <?php echo $requirements['php_extensions'] ? '#34d399' : '#f87171'; ?>;"></i>
                    <span>Security Extensions: <?php echo $requirements['php_extensions'] ? 'Loaded' : 'Missing'; ?></span>
                </li>
                <li style="margin-bottom: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-<?php echo $requirements['file_permissions'] ? 'check-circle' : 'times-circle'; ?>" 
                       style="color: <?php echo $requirements['file_permissions'] ? '#34d399' : '#f87171'; ?>;"></i>
                    <span>File Permissions: <?php echo $requirements['file_permissions'] ? 'Secure' : 'Not Secure'; ?></span>
                </li>
            </ul>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-network-wired" style="color: #22d3ee;"></i>
                Network Requirements
            </h3>
            <ul style="list-style: none; padding: 0; margin: 0; color: #9ca3af;">
                <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-<?php echo $requirements['internet'] ? 'check-circle' : 'times-circle'; ?>" 
                       style="color: <?php echo $requirements['internet'] ? '#34d399' : '#f87171'; ?>;"></i>
                    <span>Internet Connection: <?php echo $requirements['internet'] ? 'Available' : 'Required'; ?></span>
                </li>
                <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-<?php echo $requirements['ports'] ? 'check-circle' : 'times-circle'; ?>" 
                       style="color: <?php echo $requirements['ports'] ? '#34d399' : '#f87171'; ?>;"></i>
                    <span>Required Ports Open: <?php echo $requirements['ports'] ? 'Configured' : 'Blocked'; ?></span>
                </li>
                <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-<?php echo $requirements['firewall'] ? 'check-circle' : 'times-circle'; ?>" 
                       style="color: <?php echo $requirements['firewall'] ? '#34d399' : '#f87171'; ?>;"></i>
                    <span>Firewall Configured: <?php echo $requirements['firewall'] ? 'OK' : 'Issue'; ?></span>
                </li>
                <li style="margin-bottom: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-<?php echo $requirements['dns'] ? 'check-circle' : 'times-circle'; ?>" 
                       style="color: <?php echo $requirements['dns'] ? '#34d399' : '#f87171'; ?>;"></i>
                    <span>DNS Resolution: <?php echo $requirements['dns'] ? 'Working' : 'Problem'; ?></span>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Setup Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Setup Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/setup/complete'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-check-circle"></i>
            <span>Complete Setup</span>
        </a>

        <a href="<?php echo app_base_url('/admin/setup/restart'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171;">
            <i class="fas fa-redo"></i>
            <span>Restart Setup</span>
        </a>

        <a href="<?php echo app_base_url('/admin/setup/import-config'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-file-import"></i>
            <span>Import Configuration</span>
        </a>

        <a href="<?php echo app_base_url('/admin/setup/export-config'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-file-export"></i>
            <span>Export Configuration</span>
        </a>

        <a href="<?php echo app_base_url('/admin/setup/validate'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-clipboard-check"></i>
            <span>Validate Setup</span>
        </a>

        <a href="<?php echo app_base_url('/admin/setup/help'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 6px; text-decoration: none; color: #a78bfa;">
            <i class="fas fa-question-circle"></i>
            <span>Setup Help</span>
        </a>
    </div>
</div>

<!-- Troubleshooting -->
<div class="admin-card">
    <h2 class="admin-card-title">Troubleshooting</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-wrench" style="color: #4cc9f0;"></i>
                Common Issues
            </h3>
            <ul style="list-style: none; padding: 0; margin: 0; color: #9ca3af;">
                <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-dot-circle" style="color: #f87171;"></i>
                    <span>Database connection issues</span>
                </li>
                <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-dot-circle" style="color: #fbbf24;"></i>
                    <span>File permissions problems</span>
                </li>
                <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-dot-circle" style="color: #34d399;"></i>
                    <span>PHP extension missing</span>
                </li>
                <li style="margin-bottom: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-dot-circle" style="color: #22d3ee;"></i>
                    <span>Configuration errors</span>
                </li>
            </ul>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-headset" style="color: #34d399;"></i>
                Support Options
            </h3>
            <ul style="list-style: none; padding: 0; margin: 0; color: #9ca3af;">
                <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-file-alt"></i>
                    <span><a href="<?php echo app_base_url('/admin/setup/docs'); ?>" style="color: #4cc9f0; text-decoration: none;">Setup Documentation</a></span>
                </li>
                <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-video"></i>
                    <span><a href="<?php echo app_base_url('/admin/setup/videos'); ?>" style="color: #34d399; text-decoration: none;">Video Tutorials</a></span>
                </li>
                <li style="margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-comments"></i>
                    <span><a href="<?php echo app_base_url('/admin/support'); ?>" style="color: #fbbf24; text-decoration: none;">Contact Support</a></span>
                </li>
                <li style="margin-bottom: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-bug"></i>
                    <span><a href="<?php echo app_base_url('/admin/setup/bugs'); ?>" style="color: #22d3ee; text-decoration: none;">Report Issue</a></span>
                </li>
            </ul>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-history" style="color: #fbbf24;"></i>
                Setup History
            </h3>
            <ul style="list-style: none; padding: 0; margin: 0; color: #9ca3af;">
                <li style="margin-bottom: 0.75rem; display: flex; justify-content: space-between;">
                    <span>Last Setup:</span>
                    <span style="color: #f9fafb;"><?php echo $setup_history['last_setup'] ?? 'Never'; ?></span>
                </li>
                <li style="margin-bottom: 0.75rem; display: flex; justify-content: space-between;">
                    <span>Successful Setups:</span>
                    <span style="color: #f9fafb;"><?php echo number_format($setup_history['successful_setups'] ?? 0); ?></span>
                </li>
                <li style="margin-bottom: 0.75rem; display: flex; justify-content: space-between;">
                    <span>Failed Setups:</span>
                    <span style="color: #f87171;"><?php echo number_format($setup_history['failed_setups'] ?? 0); ?></span>
                </li>
                <li style="margin-bottom: 0; display: flex; justify-content: space-between;">
                    <span>Current State:</span>
                    <span style="color: <?php echo $setup_history['is_complete'] ? '#34d399' : '#fbbf24'; ?>;"><?php echo $setup_history['is_complete'] ? 'Complete' : 'Incomplete'; ?></span>
                </li>
            </ul>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-tools" style="color: #22d3ee;"></i>
                Setup Tools
            </h3>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <a href="<?php echo app_base_url('/admin/setup/diagnostics'); ?>" 
                   style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; color: #22d3ee; text-decoration: none;">
                    <i class="fas fa-stethoscope"></i>
                    <span>Run Diagnostics</span>
                </a>
                <a href="<?php echo app_base_url('/admin/setup/test-connection'); ?>" 
                   style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; color: #34d399; text-decoration: none;">
                    <i class="fas fa-plug"></i>
                    <span>Test Connections</span>
                </a>
                <a href="<?php echo app_base_url('/admin/setup/check-permissions'); ?>" 
                   style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; color: #fbbf24; text-decoration: none;">
                    <i class="fas fa-lock"></i>
                    <span>Check Permissions</span>
                </a>
                <a href="<?php echo app_base_url('/admin/setup/cleanup'); ?>" 
                   style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; color: #f87171; text-decoration: none;">
                    <i class="fas fa-broom"></i>
                    <span>Cleanup Setup</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>