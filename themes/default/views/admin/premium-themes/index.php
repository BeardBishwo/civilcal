<?php
ob_start();
?>

<!-- Page Header -->
<div class="admin-card">
    <div class="admin-card-header">
        <div>
            <h1>Premium Themes</h1>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">Manage premium themes and licensing</p>
        </div>
    </div>
</div>

<!-- Theme Statistics -->
<div class="admin-grid">
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-crown" style="font-size: 1.5rem; color: #4cc9f0; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Total Premium Themes</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #4cc9f0; margin-bottom: 0.5rem;"><?php echo number_format($stats['total_premium_themes'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Available</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-arrow-up"></i> +2 this month</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-lock" style="font-size: 1.5rem; color: #34d399; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Licensed Themes</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #34d399; margin-bottom: 0.5rem;"><?php echo number_format($stats['licensed_themes'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Active Licenses</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Validated</small>
    </div>

    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-download" style="font-size: 1.5rem; color: #fbbf24; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Downloads Today</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #fbbf24; margin-bottom: 0.5rem;"><?php echo number_format($stats['downloads_today'] ?? 0); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Themes</div>
        <small style="color: #fbbf24; font-size: 0.75rem;"><i class="fas fa-chart-line"></i> Growing</small>
    </div>
    
    <div class="admin-card" style="text-align: center; padding: 1.5rem;">
        <i class="fas fa-star" style="font-size: 1.5rem; color: #22d3ee; margin-bottom: 1rem;"></i>
        <h3 style="font-size: 1rem; color: #f9fafb; margin-bottom: 0.5rem;">Top Rated Theme</h3>
        <div style="font-size: 2rem; font-weight: 700; color: #22d3ee; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($stats['top_rated_theme'] ?? 'None'); ?></div>
        <div style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 0.5rem;">Average Rating</div>
        <small style="color: #10b981; font-size: 0.75rem;"><i class="fas fa-trophy"></i> Popular</small>
    </div>
</div>

<!-- Premium Themes List -->
<div class="admin-card">
    <h2 class="admin-card-title">Available Premium Themes</h2>
    <div class="admin-card-content">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <?php if (!empty($themes)): ?>
                <?php foreach ($themes as $theme): ?>
                    <div style="background: rgba(15, 23, 42, 0.5); border-radius: 8px; overflow: hidden; border: 1px solid <?php echo $theme['licensed'] ? 'rgba(52, 211, 153, 0.3)' : 'rgba(248, 113, 113, 0.3)'; ?>;">
                        <div style="height: 150px; background: linear-gradient(to right, <?php echo $theme['color_scheme'] ?? '#4cc9f0, #34d399'; ?>); display: flex; align-items: center; justify-content: center;">
                            <?php if (isset($theme['screenshot'])): ?>
                                <img src="<?php echo $theme['screenshot']; ?>" alt="<?php echo htmlspecialchars($theme['name'] ?? 'Theme'); ?>" 
                                     style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            <?php else: ?>
                                <div style="background: rgba(0, 0, 0, 0.2); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-crown" style="font-size: 2.5rem; color: rgba(255, 255, 255, 0.5);"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div style="padding: 1.25rem;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                <div>
                                    <h3 style="color: <?php echo $theme['licensed'] ? '#f9fafb' : '#9ca3af'; ?>; margin: 0 0 0.5rem 0; font-size: 1.125rem; display: flex; align-items: center; gap: 0.5rem;">
                                        <?php if ($theme['licensed']): ?>
                                            <i class="fas fa-check-circle" style="color: #34d399;"></i>
                                        <?php else: ?>
                                            <i class="fas fa-lock" style="color: #f87171;"></i>
                                        <?php endif; ?>
                                        <?php echo htmlspecialchars($theme['name'] ?? 'Unknown Theme'); ?>
                                    </h3>
                                    <p style="color: #9ca3af; margin: 0;"><?php echo htmlspecialchars(substr($theme['description'] ?? '', 0, 80)).(strlen($theme['description'] ?? '') > 80 ? '...' : ''); ?></p>
                                </div>
                                <span style="color: <?php echo $theme['licensed'] ? '#fbbf24' : '#9ca3af'; ?>; 
                                      background: <?php echo $theme['licensed'] ? 'rgba(245, 158, 11, 0.1)' : 'rgba(156, 163, 175, 0.1)'; ?>;
                                      padding: 0.25rem 0.5rem; 
                                      border-radius: 4px; 
                                      font-size: 0.75rem;">
                                    <?php echo $theme['licensed'] ? 'Licensed' : 'Unlicensed'; ?>
                                </span>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; color: #9ca3af; font-size: 0.75rem; margin-bottom: 1rem;">
                                <span>By <?php echo htmlspecialchars($theme['author'] ?? 'Developer'); ?></span>
                                <span>v<?php echo $theme['version'] ?? '1.0'; ?></span>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                <span style="color: <?php echo $theme['licensed'] ? '#34d399' : '#f87171'; ?>; font-weight: 700;">
                                    <?php echo $theme['price'] ? '$'.$theme['price'] : 'Free'; ?>
                                </span>
                                <div style="display: flex; gap: 0.25rem;">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star" style="color: <?php echo $i <= ($theme['rating'] ?? 0) ? '#fbbf24' : '#4b5563'; ?>;"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <?php if ($theme['licensed']): ?>
                                    <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/activate'); ?>" 
                                       style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem; justify-content: center; min-width: 80px;">
                                        <i class="fas fa-play"></i>
                                        <span>Activate</span>
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/purchase'); ?>" 
                                       style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24; font-size: 0.875rem; justify-content: center; min-width: 80px;">
                                        <i class="fas fa-shopping-cart"></i>
                                        <span>Purchase</span>
                                    </a>
                                <?php endif; ?>
                                
                                <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/details'); ?>" 
                                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem; justify-content: center; min-width: 80px;">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Details</span>
                                </a>
                                
                                <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/preview'); ?>" 
                                   style="flex: 1; display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee; font-size: 0.875rem; justify-content: center; min-width: 80px;">
                                    <i class="fas fa-eye"></i>
                                    <span>Preview</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #9ca3af;">
                    <i class="fas fa-crown" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                    <p>No premium themes available</p>
                    <a href="<?php echo app_base_url('/admin/premium-themes/marketplace'); ?>" 
                       style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
                        <i class="fas fa-store"></i>
                        <span>Visit Marketplace</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- License Management -->
<div class="admin-card">
    <h2 class="admin-card-title">License Management</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-key" style="color: #4cc9f0;"></i>
                Current License
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo htmlspecialchars($license_status['key'] ?? 'No license key set'); ?></p>
            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                <span style="color: #9ca3af;">Expires:</span>
                <span style="color: <?php echo $license_status['is_expired'] ? '#f87171' : '#34d399'; ?>;"><?php echo $license_status['expires'] ?? 'Never'; ?></span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: #9ca3af;">Status:</span>
                <span style="color: <?php echo $license_status['is_valid'] ? '#34d399' : ($license_status['is_expired'] ? '#f87171' : '#fbbf24'); ?>;">
                    <?php echo $license_status['status'] ?? 'Invalid'; ?>
                </span>
            </div>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-file-invoice" style="color: #34d399;"></i>
                License Activation
            </h3>
            <form method="POST" action="<?php echo app_base_url('/admin/premium-themes/activate-license'); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                <div style="margin-bottom: 1rem;">
                    <input type="text" name="license_key" placeholder="Enter license key" 
                           style="width: 100%; padding: 0.75rem; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(102, 126, 234, 0.3); border-radius: 6px; color: #f9fafb;">
                </div>
                <button type="submit" 
                        style="width: 100%; padding: 0.75rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; color: #34d399; cursor: pointer;">
                    <i class="fas fa-key"></i>
                    <span>Activate License</span>
                </button>
            </form>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-sync-alt" style="color: #fbbf24;"></i>
                Update Licenses
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Check for license updates and renewals</p>
            <a href="<?php echo app_base_url('/admin/premium-themes/check-license-updates'); ?>" 
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; color: #fbbf24; text-decoration: none; font-size: 0.875rem;">
                <i class="fas fa-sync-alt"></i>
                <span>Check Updates</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-shield-alt" style="color: #22d3ee;"></i>
                License Security
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;">Manage license security settings</p>
            <a href="<?php echo app_base_url('/admin/premium-themes/license-security'); ?>" 
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; color: #22d3ee; text-decoration: none; font-size: 0.875rem;">
                <i class="fas fa-cog"></i>
                <span>Security Settings</span>
            </a>
        </div>
    </div>
</div>

<!-- Premium Theme Actions -->
<div class="admin-card">
    <h2 class="admin-card-title">Premium Theme Actions</h2>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="<?php echo app_base_url('/admin/premium-themes/marketplace'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0;">
            <i class="fas fa-store"></i>
            <span>Marketplace</span>
        </a>

        <a href="<?php echo app_base_url('/admin/premium-themes/install'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399;">
            <i class="fas fa-download"></i>
            <span>Install Themes</span>
        </a>

        <a href="<?php echo app_base_url('/admin/premium-themes/upload'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24;">
            <i class="fas fa-upload"></i>
            <span>Upload Theme</span>
        </a>

        <a href="<?php echo app_base_url('/admin/premium-themes/licenses'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(102, 126, 234, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee;">
            <i class="fas fa-key"></i>
            <span>License Management</span>
        </a>

        <a href="<?php echo app_base_url('/admin/premium-themes/analytics'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(167, 139, 250, 0.1); border: 1px solid rgba(167, 139, 250, 0.2); border-radius: 6px; text-decoration: none; color: #a78bfa;">
            <i class="fas fa-chart-bar"></i>
            <span>Analytics</span>
        </a>

        <a href="<?php echo app_base_url('/admin/premium-themes/settings'); ?>"
           style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; background: rgba(236, 72, 153, 0.1); border: 1px solid rgba(236, 72, 153, 0.2); border-radius: 6px; text-decoration: none; color: #ec4899;">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </div>
</div>

<!-- Theme Categories -->
<div class="admin-card">
    <h2 class="admin-card-title">Premium Theme Categories</h2>
    <div class="admin-grid">
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-calculator" style="color: #4cc9f0;"></i>
                Calculator Themes
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['calculator'] ?? 0); ?> themes</p>
            <a href="<?php echo app_base_url('/admin/premium-themes/category/calculator'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(76, 201, 240, 0.1); border: 1px solid rgba(102, 126, 234, 0.2); border-radius: 6px; text-decoration: none; color: #4cc9f0; font-size: 0.875rem;">
                <span>Browse</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-user" style="color: #34d399;"></i>
                Dashboard Themes
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['dashboard'] ?? 0); ?> themes</p>
            <a href="<?php echo app_base_url('/admin/premium-themes/category/dashboard'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(52, 211, 153, 0.1); border: 1px solid rgba(52, 211, 153, 0.2); border-radius: 6px; text-decoration: none; color: #34d399; font-size: 0.875rem;">
                <span>Explore</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-home" style="color: #fbbf24;"></i>
                Homepage Themes
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['homepage'] ?? 0); ?> themes</p>
            <a href="<?php echo app_base_url('/admin/premium-themes/category/homepage'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; text-decoration: none; color: #fbbf24; font-size: 0.875rem;">
                <span>Browse</span>
            </a>
        </div>
        
        <div style="background: rgba(15, 23, 42, 0.5); padding: 1.5rem; border-radius: 8px;">
            <h3 style="color: #f9fafb; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-gem" style="color: #22d3ee;"></i>
                Premium Exclusive
            </h3>
            <p style="color: #9ca3af; margin-bottom: 1rem;"><?php echo number_format($categories['exclusive'] ?? 0); ?> themes</p>
            <a href="<?php echo app_base_url('/admin/premium-themes/category/exclusive'); ?>"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem; background: rgba(34, 211, 238, 0.1); border: 1px solid rgba(34, 211, 238, 0.2); border-radius: 6px; text-decoration: none; color: #22d3ee; font-size: 0.875rem;">
                <span>View</span>
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>