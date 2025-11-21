<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Theme Analytics: <?php echo htmlspecialchars($theme['name'] ?? 'Unknown Theme'); ?></h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Track usage and performance of this theme</p>
        </div>
    </div>
</div>

<!-- Analytics Navigation Tabs -->
<div class="admin-card">
    <div style="display: flex; border-bottom: 1px solid rgba(102, 126, 234, 0.2); margin-bottom: 1.5rem;">
        <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/overview'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-th-large"></i>
            <span>Themes</span>
        </a>
        <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/customize'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-paint-brush"></i>
            <span>Customize</span>
        </a>
        <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/settings'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #9ca3af; text-decoration: none; margin: 0 0.5rem;">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
        <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/analytics'); ?>" 
           style="padding: 0.75rem 1.5rem; color: #f9fafb; text-decoration: none; border-bottom: 2px solid #4cc9f0; background: rgba(76, 201, 240, 0.1);">
            <i class="fas fa-chart-bar"></i>
            <span>Analytics</span>
        </a>
    </div>
    
    <h2 class="admin-card-title">Theme Performance Analytics</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; text-align: center;">
            <i class="fas fa-users" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
            <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Active Users</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($analytics['active_users'] ?? 0); ?></div>
            <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Using Theme</div>
            <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +8% this week</small>
        </div>

        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; text-align: center;">
            <i class="fas fa-mouse-pointer" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
            <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Engagement</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($analytics['engagement_rate'] ?? 0, 1); ?>%</div>
            <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">User Engagement</div>
            <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-chart-line"></i> Increasing</small>
        </div>

        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; text-align: center;">
            <i class="fas fa-chart-line" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
            <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Page Views</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($analytics['page_views'] ?? 0); ?></div>
            <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">This Month</div>
            <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-trending-up"></i> Growing</small>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px; text-align: center;">
            <i class="fas fa-clock" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 1rem;"></i>
            <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Avg. Time</h3>
            <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo $analytics['avg_time'] ?? '0m'; ?></div>
            <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">On Theme</div>
            <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-bolt"></i> Good Engagement</small>
        </div>
    </div>
</div>

<!-- Analytics Charts -->
<div class="admin-card">
    <h2 class="admin-card-title">Analytics Charts</h2>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-chart-line" style="color: #4cc9f0;"></i>
                Usage Over Time
            </h3>
            <div style="height: 300px; display: flex; align-items: flex-end; justify-content: space-around; padding: 1rem;">
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <?php $height = rand(20, 80); ?>
                    <div style="display: flex; flex-direction: column; align-items: center;">
                        <div style="width: 20px; background: rgba(76, 201, 240, 0.5); height: <?php echo $height; ?>px; margin-bottom: 0.5rem;"></div>
                        <span style="color: #9ca3af; font-size: 0.75rem;"><?php echo date('M', mktime(0, 0, 0, $i, 1)); ?></span>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user-check" style="color: #34d399;"></i>
                User Satisfaction
            </h3>
            <div style="height: 300px; display: flex; align-items: flex-end; justify-content: space-around; padding: 1rem;">
                <?php for ($i = 0; $i < 7; $i++): ?>
                    <?php $rating = rand(60, 100); ?>
                    <div style="display: flex; flex-direction: column; align-items: center;">
                        <div style="width: 20px; background: rgba(52, 211, 153, 0.5); height: <?php echo $rating; ?>px; margin-bottom: 0.5rem;"></div>
                        <span style="color: #9ca3af; font-size: 0.75rem;"><?php echo ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'][$i]; ?></span>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</div>

<!-- Theme Performance Details -->
<div class="admin-card">
    <h2 class="admin-card-title">Theme Performance Details</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-desktop" style="color: #4cc9f0;"></i>
                Desktop vs Mobile
            </h3>
            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                <div style="text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.25rem;"><?php echo number_format($performance['desktop_usage'] ?? 0, 1); ?>%</div>
                    <div style="color: #9ca3af; font-size: 0.75rem;">Desktop</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: #34d399; margin-bottom: 0.25rem;"><?php echo number_format($performance['mobile_usage'] ?? 0, 1); ?>%</div>
                    <div style="color: #9ca3af; font-size: 0.75rem;">Mobile</div>
                </div>
            </div>
            <div style="display: flex; gap: 0.25rem;">
                <div style="flex: <?php echo ($performance['desktop_usage'] ?? 50); ?>; background: rgba(76, 201, 240, 0.5); height: 20px; border-radius: 0 4px 4px 0;"></div>
                <div style="flex: <?php echo ($performance['mobile_usage'] ?? 50); ?>; background: rgba(52, 211, 153, 0.5); height: 20px; border-radius: 0 4px 4px 0;"></div>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-bolt" style="color: #34d399;"></i>
                Performance Metrics
            </h3>
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: #9ca3af;">Loading Speed:</span>
                    <span style="color: #f9fafb;"><?php echo $performance['loading_speed'] ?? '2.1s'; ?></span>
                </div>
                <div style="height: 8px; background: rgba(102, 126, 234, 0.2); border-radius: 4px; overflow: hidden;">
                    <div style="height: 100%; width: <?php echo min(100, ($performance['loading_speed'] ?? 2.1) > 3 ? 20 : ($performance['loading_speed'] ?? 2.1) > 2 ? 40 : ($performance['loading_speed'] ?? 2.1) > 1 ? 80 : 100); ?>%; background: <?php echo ($performance['loading_speed'] ?? 2.1) > 3 ? '#f87171' : '#34d399'; ?>;"></div>
                </div>
            </div>
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: #9ca3af;">CSS Performance:</span>
                    <span style="color: #f9fafb;"><?php echo $performance['css_score'] ?? '95%'; ?></span>
                </div>
                <div style="height: 8px; background: rgba(102, 126, 234, 0.2); border-radius: 4px; overflow: hidden;">
                    <div style="height: 100%; width: <?php echo $performance['css_score'] ?? 95; ?>%; background: <?php echo ($performance['css_score'] ?? 95) < 70 ? '#f87171' : (($performance['css_score'] ?? 95) < 85 ? '#fbbf24' : '#34d399'); ?>;"></div>
                </div>
            </div>
            <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="color: #9ca3af;">JS Performance:</span>
                    <span style="color: #f9fafb;"><?php echo $performance['js_score'] ?? '92%'; ?></span>
                </div>
                <div style="height: 8px; background: rgba(102, 126, 234, 0.2); border-radius: 4px; overflow: hidden;">
                    <div style="height: 100%; width: <?php echo $performance['js_score'] ?? 92; ?>%; background: <?php echo ($performance['js_score'] ?? 92) < 70 ? '#f87171' : (($performance['js_score'] ?? 92) < 85 ? '#fbbf24' : '#34d399'); ?>;"></div>
                </div>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user-circle" style="color: #fbbf24;"></i>
                User Feedback
            </h3>
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div style="font-size: 2rem; font-weight: 700; color: #fbbf24;"><?php echo number_format($feedback['avg_rating'] ?? 0, 1); ?></div>
                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                        <div style="display: flex; gap: 0.25rem;">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star" style="color: <?php echo $i <= ($feedback['avg_rating'] ?? 0) ? '#fbbf24' : '#4b5563'; ?>;"></i>
                            <?php endfor; ?>
                        </div>
                        <span style="color: #9ca3af; font-size: 0.75rem;"><?php echo number_format($feedback['total_reviews'] ?? 0); ?> reviews</span>
                    </div>
                </div>
            </div>
            <div style="margin-bottom: 1rem; display: flex; justify-content: space-between;">
                <span style="color: #9ca3af;">Positive:</span>
                <span style="color: #f9fafb;"><?php echo number_format($feedback['positive'] ?? 0); ?></span>
            </div>
            <div>
                <span style="color: #9ca3af;">Negative:</span>
                <span style="color: #f9fafb;"><?php echo number_format($feedback['negative'] ?? 0); ?></span>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-exclamation-triangle" style="color: #22d3ee;"></i>
                Theme Issues
            </h3>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <span style="color: #f9fafb;"><?php echo number_format($issues['total_issues'] ?? 0); ?> issues</span>
                <span style="color: <?php echo ($issues['severity'] ?? 'low') === 'high' ? '#f87171' : (($issues['severity'] ?? 'low') === 'medium' ? '#fbbf24' : '#34d399'); ?>;">
                    <?php echo ucfirst($issues['severity'] ?? 'low'); ?> priority
                </span>
            </div>
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                    <span style="color: #9ca3af;">CSS Issues:</span>
                    <span style="color: #f9fafb;"><?php echo number_format($issues['css_issues'] ?? 0); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                    <span style="color: #9ca3af;">JS Issues:</span>
                    <span style="color: #f9fafb;"><?php echo number_format($issues['js_issues'] ?? 0); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #9ca3af;">Accessibility:</span>
                    <span style="color: #f9fafb;"><?php echo number_format($issues['a11y_issues'] ?? 0); ?></span>
                </div>
            </div>
            <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/issues'); ?>" 
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; color: #22d3ee; text-decoration: none; font-size: 0.875rem;">
                <i class="fas fa-bug"></i>
                <span>View Issues</span>
            </a>
        </div>
    </div>
</div>

<!-- User Usage Reports -->
<div class="admin-card">
    <h2 class="admin-card-title">User Usage Reports</h2>
    <div style="overflow-x: auto;">
        <table class="admin-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.2);">
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">User</th>
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Usage</th>
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Duration</th>
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Last Active</th>
                    <th style="text-align: left; padding: 0.75rem; color: #9ca3af; font-weight: 600;">Satisfaction</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($user_reports)): ?>
                    <?php foreach (array_slice($user_reports, 0, 10) as $report): ?>
                        <tr style="border-bottom: 1px solid rgba(102, 126, 234, 0.1);">
                            <td style="padding: 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 32px; height: 32px; background: rgba(76, 201, 240, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <span style="color: #4cc9f0; font-size: 0.875rem;"><?php echo strtoupper(substr($report['username'] ?? 'U', 0, 1)); ?></span>
                                    </div>
                                    <div>
                                        <div style="color: #f9fafb;"><?php echo htmlspecialchars($report['username'] ?? 'Unknown User'); ?></div>
                                        <div style="color: #9ca3af; font-size: 0.75rem;"><?php echo htmlspecialchars($report['email'] ?? ''); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 0.75rem;"><?php echo number_format($report['page_views'] ?? 0); ?> pages</td>
                            <td style="padding: 0.75rem;"><?php echo $report['time_spent'] ?? '0m'; ?></td>
                            <td style="padding: 0.75rem;"><?php echo $report['last_active'] ?? 'Unknown'; ?></td>
                            <td style="padding: 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.25rem;">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star" style="color: <?php echo $i <= ($report['rating'] ?? 0) ? '#fbbf24' : '#4b5563'; ?>;"></i>
                                    <?php endfor; ?>
                                    <span style="color: #f9fafb; margin-left: 0.5rem;"><?php echo $report['rating'] ?? '0'; ?>/5</span>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 1rem; color: #9ca3af;">No usage reports available</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Analytics Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Analytics Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/analytics/export'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-file-export"></i>
            <span>Export Report</span>
        </a>

        <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/analytics/refresh'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-sync-alt"></i>
            <span>Refresh Data</span>
        </a>

        <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/analytics/compare'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-chart-pie"></i>
            <span>Compare Themes</span>
        </a>

        <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/analytics/schedule'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-calendar-alt"></i>
            <span>Schedule Reports</span>
        </a>

        <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/analytics/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 6px; text-decoration: none; color: #a78bfa;">
            <i class="fas fa-cog"></i>
            <span>Analytics Settings</span>
        </a>
    </div>
</div>

<!-- Theme Comparison -->
<div class="admin-card">
    <h2 class="admin-card-title">Theme Comparison</h2>
    <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
        <h3 style="color: #f9fafb; margin-bottom: 1rem;">Compare with Other Themes</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
            <?php if (!empty($comparison_themes)): ?>
                <?php foreach ($comparison_themes as $comp_theme): ?>
                    <div style="background: rgba(15, 23, 42, 0.8); padding: 1rem; border-radius: 6px; border: 1px solid rgba(102, 126, 234, 0.2);">
                        <h4 style="color: #f9fafb; margin: 0 0 0.5rem 0;"><?php echo htmlspecialchars($comp_theme['name']); ?></h4>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                            <span style="color: #9ca3af;">Users:</span>
                            <span style="color: #f9fafb;"><?php echo number_format($comp_theme['users'] ?? 0); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                            <span style="color: #9ca3af;">Rating:</span>
                            <span style="color: #f9fafb;"><?php echo number_format($comp_theme['rating'] ?? 0, 1); ?>/5</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #9ca3af;">Performance:</span>
                            <span style="color: #f9fafb;"><?php echo $comp_theme['performance_score'] ?? '0%'; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 1rem; color: #9ca3af;">
                    <i class="fas fa-chart-bar" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                    <p>No comparison data available</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>