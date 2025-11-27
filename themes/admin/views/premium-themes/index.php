<style>
.premium-header {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.premium-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.stat-title {
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.stat-value {
    color: #1f2937;
    font-size: 1.875rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.stat-trend {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.875rem;
}

.stat-trend.up {
    color: #10b981;
}

.stat-trend.down {
    color: #ef4444;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.themes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.theme-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.theme-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.theme-screenshot {
    height: 150px;
    background: linear-gradient(to right, #4cc9f0, #34d399);
    display: flex;
    align-items: center;
    justify-content: center;
}

.theme-screenshot img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.theme-content {
    padding: 1.25rem;
}

.theme-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.theme-name {
    margin: 0 0 0.5rem 0;
    font-size: 1.125rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.theme-name.licensed {
    color: #1f2937;
}

.theme-name.unlicensed {
    color: #9ca3af;
}

.theme-description {
    color: #6b7280;
    margin: 0;
    font-size: 0.875rem;
}

.theme-status {
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.theme-status.licensed {
    background: #dcfce7;
    color: #166534;
}

.theme-status.unlicensed {
    background: #fee2e2;
    color: #991b1b;
}

.theme-meta {
    display: flex;
    justify-content: space-between;
    color: #9ca3af;
    font-size: 0.75rem;
    margin-bottom: 1rem;
}

.theme-price-rating {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.theme-price {
    font-weight: 700;
}

.theme-price.licensed {
    color: #10b981;
}

.theme-price.unlicensed {
    color: #ef4444;
}

.rating-stars {
    display: flex;
    gap: 0.25rem;
}

.rating-star {
    color: #d1d5db;
}

.rating-star.filled {
    color: #fbbf24;
}

.theme-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn-action {
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    flex: 1;
    min-width: 80px;
    justify-content: center;
    text-decoration: none;
}

.btn-action.purchase {
    background: #fef3c7;
    color: #92400e;
    border-color: #fde68a;
}

.btn-action.purchase:hover {
    background: #fde68a;
    border-color: #fcd34d;
}

.btn-action.activate {
    background: #dcfce7;
    color: #166534;
    border-color: #bbf7d0;
}

.btn-action.activate:hover {
    background: #bbf7d0;
    border-color: #86efac;
}

.btn-action.details {
    background: #dbeafe;
    color: #1e40af;
    border-color: #bfdbfe;
}

.btn-action.details:hover {
    background: #bfdbfe;
    border-color: #93c5fd;
}

.btn-action.preview {
    background: #e0f2fe;
    color: #0369a1;
    border-color: #bae6fd;
}

.btn-action.preview:hover {
    background: #bae6fd;
    border-color: #7dd3fc;
}

.btn-primary {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.license-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.license-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.license-card h3 {
    color: #1f2937;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.license-card p {
    color: #6b7280;
    margin-bottom: 1rem;
}

.license-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.license-label {
    color: #6b7280;
}

.license-value {
    font-weight: 500;
}

.license-value.valid {
    color: #10b981;
}

.license-value.expired {
    color: #ef4444;
}

.license-value.pending {
    color: #f59e0b;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    background: #f9fafb;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    color: #1f2937;
    transition: border-color 0.2s ease;
}

.form-control:focus {
    outline: none;
    border-color: #f59e0b;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
}

.btn-submit {
    width: 100%;
    padding: 0.75rem;
    background: #dcfce7;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    color: #166534;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-submit:hover {
    background: #bbf7d0;
    border-color: #86efac;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-bottom: 2rem;
}

.action-button {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1.25rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
}

.action-button.marketplace {
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #bfdbfe;
}

.action-button.marketplace:hover {
    background: #bfdbfe;
    border-color: #93c5fd;
}

.action-button.install {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.action-button.install:hover {
    background: #bbf7d0;
    border-color: #86efac;
}

.action-button.upload {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
}

.action-button.upload:hover {
    background: #fde68a;
    border-color: #fcd34d;
}

.action-button.licenses {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
}

.action-button.licenses:hover {
    background: #bae6fd;
    border-color: #7dd3fc;
}

.action-button.analytics {
    background: #f3e8ff;
    color: #6b21a8;
    border: 1px solid #e9d5ff;
}

.action-button.analytics:hover {
    background: #e9d5ff;
    border-color: #d8b4fe;
}

.action-button.settings {
    background: #fce7f3;
    color: #9d174d;
    border: 1px solid #fbcfe8;
}

.action-button.settings:hover {
    background: #fbcfe8;
    border-color: #f9a8d4;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.category-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    text-align: center;
}

.category-card h3 {
    color: #1f2937;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.category-card p {
    color: #6b7280;
    margin-bottom: 1rem;
    font-size: 0.875rem;
}

.btn-category {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #dbeafe;
    border: 1px solid #bfdbfe;
    border-radius: 6px;
    color: #1e40af;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-category:hover {
    background: #bfdbfe;
    border-color: #93c5fd;
}

.no-themes {
    grid-column: 1 / -1;
    text-align: center;
    padding: 3rem;
    color: #6b7280;
}

.no-themes i {
    font-size: 2rem;
    margin-bottom: 1rem;
    display: block;
    color: #d1d5db;
}

.btn-visit-marketplace {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1rem;
    padding: 0.75rem 1.5rem;
    background: #dbeafe;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    color: #1e40af;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-visit-marketplace:hover {
    background: #bfdbfe;
    border-color: #93c5fd;
}
</style>

<div class="premium-header">
    <h1>💎 Premium Themes</h1>
    <p style="color: rgba(255, 255, 255, 0.9); margin: 0.5rem 0 0 0; font-size: 1.1rem;">Manage premium themes, licenses, and customization options</p>
</div>

<!-- Stats Cards -->
<div class="premium-stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
            <i class="fas fa-crown"></i>
        </div>
        <div class="stat-title">Total Premium Themes</div>
        <div class="stat-value"><?php echo number_format($stats['total_premium_themes'] ?? 0); ?></div>
        <div class="stat-trend up">
            <i class="fas fa-arrow-up"></i>
            <span>+2 this month</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
            <i class="fas fa-lock"></i>
        </div>
        <div class="stat-title">Licensed Themes</div>
        <div class="stat-value"><?php echo number_format($stats['licensed_themes'] ?? 0); ?></div>
        <div class="stat-trend up">
            <i class="fas fa-check-circle"></i>
            <span>Validated</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: white;">
            <i class="fas fa-download"></i>
        </div>
        <div class="stat-title">Downloads Today</div>
        <div class="stat-value"><?php echo number_format($stats['downloads_today'] ?? 0); ?></div>
        <div class="stat-trend up">
            <i class="fas fa-chart-line"></i>
            <span>Growing</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white;">
            <i class="fas fa-star"></i>
        </div>
        <div class="stat-title">Top Rated Theme</div>
        <div class="stat-value"><?php echo htmlspecialchars($stats['top_rated_theme'] ?? 'None'); ?></div>
        <div class="stat-trend up">
            <i class="fas fa-trophy"></i>
            <span>Popular</span>
        </div>
    </div>
</div>

<!-- Premium Themes List -->
<div class="section-header">
    <h2 class="section-title">Available Premium Themes</h2>
    <button class="btn-primary" onclick="window.location.href='<?php echo app_base_url('/admin/premium-themes/marketplace'); ?>'">
        <i class="fas fa-store"></i> Visit Marketplace
    </button>
</div>

<div class="themes-grid">
    <?php if (!empty($themes)): ?>
        <?php foreach ($themes as $theme): ?>
            <div class="theme-card">
                <div class="theme-screenshot" style="background: linear-gradient(to right, <?php echo $theme['color_scheme'] ?? '#4cc9f0, #34d399'; ?>);">
                    <?php if (isset($theme['screenshot'])): ?>
                        <img src="<?php echo $theme['screenshot']; ?>" alt="<?php echo htmlspecialchars($theme['name'] ?? 'Theme'); ?>">
                    <?php else: ?>
                        <div style="background: rgba(0, 0, 0, 0.2); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-crown" style="font-size: 2.5rem; color: rgba(255, 255, 255, 0.5);"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="theme-content">
                    <div class="theme-header">
                        <div>
                            <h3 class="theme-name <?php echo $theme['licensed'] ? 'licensed' : 'unlicensed'; ?>">
                                <?php if ($theme['licensed']): ?>
                                    <i class="fas fa-check-circle" style="color: #10b981;"></i>
                                <?php else: ?>
                                    <i class="fas fa-lock" style="color: #ef4444;"></i>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($theme['name'] ?? 'Unknown Theme'); ?>
                            </h3>
                            <p class="theme-description"><?php echo htmlspecialchars(substr($theme['description'] ?? '', 0, 80)).(strlen($theme['description'] ?? '') > 80 ? '...' : ''); ?></p>
                        </div>
                        <span class="theme-status <?php echo $theme['licensed'] ? 'licensed' : 'unlicensed'; ?>">
                            <?php echo $theme['licensed'] ? 'Licensed' : 'Unlicensed'; ?>
                        </span>
                    </div>
                    
                    <div class="theme-meta">
                        <span>By <?php echo htmlspecialchars($theme['author'] ?? 'Developer'); ?></span>
                        <span>v<?php echo $theme['version'] ?? '1.0'; ?></span>
                    </div>
                    
                    <div class="theme-price-rating">
                        <span class="theme-price <?php echo $theme['licensed'] ? 'licensed' : 'unlicensed'; ?>">
                            <?php echo $theme['price'] ? '$'.$theme['price'] : 'Free'; ?>
                        </span>
                        <div class="rating-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star rating-star <?php echo $i <= ($theme['rating'] ?? 0) ? 'filled' : ''; ?>"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <div class="theme-actions">
                        <?php if ($theme['licensed']): ?>
                            <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/activate'); ?>" class="btn-action activate">
                                <i class="fas fa-play"></i>
                                <span>Activate</span>
                            </a>
                        <?php else: ?>
                            <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/purchase'); ?>" class="btn-action purchase">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Purchase</span>
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/details'); ?>" class="btn-action details">
                            <i class="fas fa-info-circle"></i>
                            <span>Details</span>
                        </a>
                        
                        <a href="<?php echo app_base_url('/admin/premium-themes/'.($theme['id'] ?? 0).'/preview'); ?>" class="btn-action preview">
                            <i class="fas fa-eye"></i>
                            <span>Preview</span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-themes">
            <i class="fas fa-crown"></i>
            <p>No premium themes available</p>
            <a href="<?php echo app_base_url('/admin/premium-themes/marketplace'); ?>" class="btn-visit-marketplace">
                <i class="fas fa-store"></i>
                <span>Visit Marketplace</span>
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- License Management -->
<div class="section-header">
    <h2 class="section-title">License Management</h2>
    <button class="btn-primary" onclick="window.location.href='<?php echo app_base_url('/admin/premium-themes/licenses'); ?>'">
        <i class="fas fa-key"></i> Manage Licenses
    </button>
</div>

<div class="license-grid">
    <div class="license-card">
        <h3><i class="fas fa-key" style="color: #f59e0b;"></i> Current License</h3>
        <p><?php echo htmlspecialchars($license_status['key'] ?? 'No license key set'); ?></p>
        <div class="license-info">
            <span class="license-label">Expires:</span>
            <span class="license-value <?php echo $license_status['is_expired'] ? 'expired' : 'valid'; ?>"><?php echo $license_status['expires'] ?? 'Never'; ?></span>
        </div>
        <div class="license-info">
            <span class="license-label">Status:</span>
            <span class="license-value <?php echo $license_status['is_valid'] ? 'valid' : ($license_status['is_expired'] ? 'expired' : 'pending'); ?>">
                <?php echo $license_status['status'] ?? 'Invalid'; ?>
            </span>
        </div>
    </div>
    
    <div class="license-card">
        <h3><i class="fas fa-file-invoice" style="color: #10b981;"></i> License Activation</h3>
        <form method="POST" action="<?php echo app_base_url('/admin/premium-themes/activate-license'); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
            <div style="margin-bottom: 1rem;">
                <input type="text" name="license_key" placeholder="Enter license key" class="form-control">
            </div>
            <button type="submit" class="btn-submit">
                <i class="fas fa-key"></i>
                <span>Activate License</span>
            </button>
        </form>
    </div>
    
    <div class="license-card">
        <h3><i class="fas fa-sync-alt" style="color: #fbbf24;"></i> Update Licenses</h3>
        <p>Check for license updates and renewals</p>
        <a href="<?php echo app_base_url('/admin/premium-themes/check-license-updates'); ?>" class="btn-action details">
            <i class="fas fa-sync-alt"></i>
            <span>Check Updates</span>
        </a>
    </div>
    
    <div class="license-card">
        <h3><i class="fas fa-shield-alt" style="color: #0ea5e9;"></i> License Security</h3>
        <p>Manage license security settings</p>
        <a href="<?php echo app_base_url('/admin/premium-themes/license-security'); ?>" class="btn-action preview">
            <i class="fas fa-cog"></i>
            <span>Security Settings</span>
        </a>
    </div>
</div>

<!-- Premium Theme Actions -->
<div class="section-header">
    <h2 class="section-title">Premium Theme Actions</h2>
</div>

<div class="action-buttons">
    <a href="<?php echo app_base_url('/admin/premium-themes/marketplace'); ?>" class="action-button marketplace">
        <i class="fas fa-store"></i>
        <span>Marketplace</span>
    </a>

    <a href="<?php echo app_base_url('/admin/premium-themes/install'); ?>" class="action-button install">
        <i class="fas fa-download"></i>
        <span>Install Themes</span>
    </a>

    <a href="<?php echo app_base_url('/admin/premium-themes/upload'); ?>" class="action-button upload">
        <i class="fas fa-upload"></i>
        <span>Upload Theme</span>
    </a>

    <a href="<?php echo app_base_url('/admin/premium-themes/licenses'); ?>" class="action-button licenses">
        <i class="fas fa-key"></i>
        <span>License Management</span>
    </a>

    <a href="<?php echo app_base_url('/admin/premium-themes/analytics'); ?>" class="action-button analytics">
        <i class="fas fa-chart-bar"></i>
        <span>Analytics</span>
    </a>

    <a href="<?php echo app_base_url('/admin/premium-themes/settings'); ?>" class="action-button settings">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
    </a>
</div>

<!-- Theme Categories -->
<div class="section-header">
    <h2 class="section-title">Premium Theme Categories</h2>
</div>

<div class="categories-grid">
    <div class="category-card">
        <h3><i class="fas fa-calculator" style="color: #f59e0b;"></i> Calculator Themes</h3>
        <p><?php echo number_format($categories['calculator'] ?? 0); ?> themes</p>
        <a href="<?php echo app_base_url('/admin/premium-themes/category/calculator'); ?>" class="btn-category">
            <span>Browse</span>
        </a>
    </div>
    
    <div class="category-card">
        <h3><i class="fas fa-user" style="color: #10b981;"></i> Dashboard Themes</h3>
        <p><?php echo number_format($categories['dashboard'] ?? 0); ?> themes</p>
        <a href="<?php echo app_base_url('/admin/premium-themes/category/dashboard'); ?>" class="btn-category">
            <span>Explore</span>
        </a>
    </div>
    
    <div class="category-card">
        <h3><i class="fas fa-home" style="color: #fbbf24;"></i> Homepage Themes</h3>
        <p><?php echo number_format($categories['homepage'] ?? 0); ?> themes</p>
        <a href="<?php echo app_base_url('/admin/premium-themes/category/homepage'); ?>" class="btn-category">
            <span>Browse</span>
        </a>
    </div>
    
    <div class="category-card">
        <h3><i class="fas fa-gem" style="color: #0ea5e9;"></i> Premium Exclusive</h3>
        <p><?php echo number_format($categories['exclusive'] ?? 0); ?> themes</p>
        <a href="<?php echo app_base_url('/admin/premium-themes/category/exclusive'); ?>" class="btn-category">
            <span>View</span>
        </a>
    </div>
</div>