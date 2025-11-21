<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Setup Wizard</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Complete initial system setup and configuration</p>
        </div>
    </div>
</div>

<!-- Setup Progress -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-tasks" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Setup Progress</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($setup_progress['percentage'] ?? 0); ?>%</div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Complete</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> <?php echo $setup_progress['completed'] ?? 0; ?>/<?php echo $setup_progress['total'] ?? 0; ?> steps</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-check-circle" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Steps Completed</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($setup_stats['completed_steps'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Finished</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check"></i> Well done!</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-clock" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Remaining Steps</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($setup_progress['remaining'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">To Complete</div>
        <small style="color: #fbbf24; font-size: 0.75rem;"><i class="fas fa-list"></i> Finish Setup</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-bolt" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Recommended</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($setup_progress['recommended'] ?? 'Continue Setup'); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Next Step</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-star"></i> Recommended</small>
    </div>
</div>

<!-- Setup Checklist -->
<div class="admin-card">
    <h2 class="admin-card-title">Setup Checklist</h2>
    <div class="admin-card-content">
        <ul style="list-style: none; padding: 0; margin: 0;">
            <?php if (!empty($setup_tasks)): ?>
                <?php foreach ($setup_tasks as $task): ?>
                    <li style="margin-bottom: 1rem; padding: 1rem; background: rgba(15, 23, 42, 0.5); border-radius: 6px; border-left: 3px solid <?php echo $task['completed'] ? '#34d399' : '#fbbf24'; ?>;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                                    <i class="fas <?php echo $task['completed'] ? 'fa-check-circle' : 'fa-circle'; ?>" 
                                       style="color: <?php echo $task['completed'] ? '#34d399' : '#9ca3af'; ?>;"></i>
                                    <h4 style="color: <?php echo $task['completed'] ? '#34d399' : '#f9fafb'; ?>; margin: 0; font-size: 1rem;"><?php echo htmlspecialchars($task['title'] ?? 'Unknown Task'); ?></h4>
                                </div>
                                <p style="color: #9ca3af; margin: 0.5rem 0 0 2.5rem; font-size: 0.875rem;"><?php echo htmlspecialchars($task['description'] ?? ''); ?></p>
                                <div style="margin-left: 2.5rem; margin-top: 0.5rem; display: flex; gap: 1rem; font-size: 0.75rem; color: #9ca3af;">
                                    <span><i class="fas fa-tag"></i> <?php echo $task['category'] ?? 'General'; ?></span>
                                    <span><i class="fas fa-star"></i> <?php echo $task['priority'] ?? 'Medium'; ?> Priority</span>
                                    <span><i class="fas fa-clock"></i> <?php echo $task['estimated_time'] ?? '5 min'; ?></span>
                                </div>
                            </div>
                            
                            <?php if (!$task['completed']): ?>
                                <a href="<?php echo app_base_url($task['action_url'] ?? '#'); ?>" 
                                   style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem 1rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem; align-self: flex-start; margin-left: 1rem;">
                                    <i class="fas <?php echo $task['action_icon'] ?? 'fa-cog'; ?>"></i>
                                    <span><?php echo $task['action_text'] ?? 'Start'; ?></span>
                                </a>
                            <?php else: ?>
                                <span style="color: #34d399; background: rgba(52, 211, 153, 0.1); padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; align-self: flex-start; margin-left: 1rem;">
                                    <i class="fas fa-check"></i>
                                    <span>Completed</span>
                                </span>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li style="text-align: center; padding: 2rem; color: #9ca3af;">
                    <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                    <p>System setup is complete</p>
                    <a href="<?php echo app_base_url('/admin'); ?>" 
                       style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Go to Dashboard</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<!-- Setup Categories -->
<div class="admin-grid">
    <div class="admin-card" style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
        <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-cog" style="color: #4cc9f0;"></i>
            System Configuration
        </h3>
        <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['system_config'] ?? 0); ?>/<?php echo $categories['system_total'] ?? 1; ?> tasks complete</p>
        <div style="height: 8px; background: rgba(102, 126, 234, 0.2); border-radius: 4px; margin-bottom: 1rem; overflow: hidden;">
            <div style="height: 100%; width: <?php echo round(($categories['system_config'] ?? 0) / ($categories['system_total'] ?? 1) * 100); ?>%; background: #4cc9f0;"></div>
        </div>
        <a href="<?php echo app_base_url('/admin/setup/system'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem;">
            <i class="fas fa-forward"></i>
            <span>Continue Setup</span>
        </a>
    </div>
    
    <div class="admin-card" style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
        <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-user-shield" style="color: #34d399;"></i>
            Security Configuration
        </h3>
        <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['security_config'] ?? 0); ?>/<?php echo $categories['security_total'] ?? 1; ?> tasks complete</p>
        <div style="height: 8px; background: rgba(102, 126, 234, 0.2); border-radius: 4px; margin-bottom: 1rem; overflow: hidden;">
            <div style="height: 100%; width: <?php echo round(($categories['security_config'] ?? 0) / ($categories['security_total'] ?? 1) * 100); ?>%; background: #34d399;"></div>
        </div>
        <a href="<?php echo app_base_url('/admin/setup/security'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
            <i class="fas fa-forward"></i>
            <span>Continue Setup</span>
        </a>
    </div>

    <div class="admin-card" style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
        <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-envelope" style="color: #fbbf24;"></i>
            Email Configuration
        </h3>
        <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['email_config'] ?? 0); ?>/<?php echo $categories['email_total'] ?? 1; ?> tasks complete</p>
        <div style="height: 8px; background: rgba(102, 126, 234, 0.2); border-radius: 4px; margin-bottom: 1rem; overflow: hidden;">
            <div style="height: 100%; width: <?php echo round(($categories['email_config'] ?? 0) / ($categories['email_total'] ?? 1) * 100); ?>%; background: #fbbf24;"></div>
        </div>
        <a href="<?php echo app_base_url('/admin/setup/email'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24; font-size: 0.875rem;">
            <i class="fas fa-forward"></i>
            <span>Continue Setup</span>
        </a>
    </div>
    
    <div class="admin-card" style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
        <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-users" style="color: #22d3ee;"></i>
            User Management
        </h3>
        <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['user_config'] ?? 0); ?>/<?php echo $categories['user_total'] ?? 1; ?> tasks complete</p>
        <div style="height: 8px; background: rgba(102, 126, 234, 0.2); border-radius: 4px; margin-bottom: 1rem; overflow: hidden;">
            <div style="height: 100%; width: <?php echo round(($categories['user_config'] ?? 0) / ($categories['user_total'] ?? 1) * 100); ?>%; background: #22d3ee;"></div>
        </div>
        <a href="<?php echo app_base_url('/admin/setup/users'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee; font-size: 0.875rem;">
            <i class="fas fa-forward"></i>
            <span>Continue Setup</span>
        </a>
    </div>
</div>

<!-- Current Setup Step -->
<div class="admin-card">
    <h2 class="admin-card-title">Current Setup: <?php echo htmlspecialchars($current_step['title'] ?? 'Initial Setup'); ?></h2>
    <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
        <div style="margin-bottom: 1.5rem;">
            <h3 style="color: #f9fafb; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($current_step['title'] ?? 'System Initialization'); ?></h3>
            <p style="color: #9ca3af; margin: 0;"><?php echo htmlspecialchars($current_step['description'] ?? 'Initial system configuration'); ?></p>
        </div>
        
        <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
            <div style="flex: 1; text-align: center;">
                <div style="font-size: 1.5rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo $current_step['step_number'] ?? '1'; ?></div>
                <div style="color: #9ca3af; font-size: 0.875rem;">Step</div>
            </div>
            <div style="flex: 3;">
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
            <h4 style="color: #f9fafb; margin-bottom: 1rem;"><?php echo htmlspecialchars($current_step['instructions_title'] ?? 'Instructions'); ?></h4>
            <ol style="color: #9ca3af; padding-left: 1.5rem;">
                <?php if (!empty($current_step['instructions'])): ?>
                    <?php foreach ($current_step['instructions'] as $instruction): ?>
                        <li style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($instruction); ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="margin-bottom: 0.5rem;">Follow the on-screen instructions to complete this step</li>
                <?php endif; ?>
            </ol>
        </div>
        
        <div>
            <h4 style="color: #f9fafb; margin-bottom: 1rem;">Requirements</h4>
            <ul style="color: #9ca3af; padding-left: 1.5rem;">
                <?php if (!empty($current_step['requirements'])): ?>
                    <?php foreach ($current_step['requirements'] as $requirement): ?>
                        <li style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($requirement); ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li style="margin-bottom: 0.5rem;">No special requirements for this step</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<!-- Setup Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Setup Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/setup/continue'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-play"></i>
            <span>Continue Setup</span>
        </a>

        <a href="<?php echo app_base_url('/admin/setup/restart'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(248, 113, 113, 0.1); border: 1px solid rgba(248, 113, 113, 0.2); border-radius: 6px; text-decoration: none; color: #f87171;">
            <i class="fas fa-redo"></i>
            <span>Restart Setup</span>
        </a>

        <a href="<?php echo app_base_url('/admin/setup/skip'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-forward"></i>
            <span>Skip This Step</span>
        </a>

        <a href="<?php echo app_base_url('/admin/setup/preview'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-eye"></i>
            <span>Preview Setup</span>
        </a>

        <a href="<?php echo app_base_url('/admin/setup/help'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 6px; text-decoration: none; color: #a78bfa;">
            <i class="fas fa-question-circle"></i>
            <span>Setup Help</span>
        </a>
    </div>
</div>

<!-- Setup Documentation -->
<div class="admin-card">
    <h2 class="admin-card-title">Setup Resources</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-book" style="color: #4cc9f0;"></i>
                Setup Guide
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Step-by-step guide to complete system setup</p>
            <a href="<?php echo app_base_url('/admin/setup/guide'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem;">
                <i class="fas fa-book-open"></i>
                <span>Read Guide</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-video" style="color: #34d399;"></i>
                Video Tutorials
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Watch setup videos for visual guidance</p>
            <a href="<?php echo app_base_url('/admin/setup/videos'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                <i class="fas fa-play-circle"></i>
                <span>Watch Videos</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-exclamation-triangle" style="color: #fbbf24;"></i>
                Troubleshooting
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Common issues and solutions during setup</p>
            <a href="<?php echo app_base_url('/admin/setup/troubleshooting'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24; font-size: 0.875rem;">
                <i class="fas fa-tools"></i>
                <span>Get Help</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-headset" style="color: #22d3ee;"></i>
                Support
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Contact support for setup assistance</p>
            <a href="<?php echo app_base_url('/admin/support'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee; font-size: 0.875rem;">
                <i class="fas fa-comments"></i>
                <span>Contact Support</span>
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>