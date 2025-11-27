<style>
.marketplace-header {
    background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.marketplace-stats-grid {
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

.theme-description {
    color: #6b7280;
    margin: 0;
    font-size: 0.875rem;
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
    color: #0ea5e9;
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
    background: #dbeafe;
    color: #1e40af;
    border-color: #bfdbfe;
}

.btn-action.purchase:hover {
    background: #bfdbfe;
    border-color: #93c5fd;
}

.btn-action.details {
    background: #e0f2fe;
    color: #0369a1;
    border-color: #bae6fd;
}

.btn-action.details:hover {
    background: #bae6fd;
    border-color: #7dd3fc;
}

.btn-primary {
    background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
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

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
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
</style>

<div class="marketplace-header">
    <h1>🛍️ Theme Marketplace</h1>
    <p style="color: rgba(255, 255, 255, 0.9); margin: 0.5rem 0 0 0; font-size: 1.1rem;">Browse and purchase premium themes for your calculator</p>
</div>

<!-- Stats Cards -->
<div class="marketplace-stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white;">
            <i class="fas fa-store"></i>
        </div>
        <div class="stat-title">Total Themes</div>
        <div class="stat-value">127</div>
        <div class="stat-trend up">
            <i class="fas fa-arrow-up"></i>
            <span>+12 this month</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-title">Sales Today</div>
        <div class="stat-value">24</div>
        <div class="stat-trend up">
            <i class="fas fa-chart-line"></i>
            <span>Growing</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: white;">
            <i class="fas fa-star"></i>
        </div>
        <div class="stat-title">Avg. Rating</div>
        <div class="stat-value">4.7</div>
        <div class="stat-trend up">
            <i class="fas fa-trophy"></i>
            <span>Excellent</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white;">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-title">Active Users</div>
        <div class="stat-value">1.2K</div>
        <div class="stat-trend up">
            <i class="fas fa-user-plus"></i>
            <span>+142 today</span>
        </div>
    </div>
</div>

<!-- Featured Themes -->
<div class="section-header">
    <h2 class="section-title">Featured Themes</h2>
    <button class="btn-primary" onclick="window.location.href='#'">
        <i class="fas fa-fire"></i> View All Featured
    </button>
</div>

<div class="themes-grid">
    <?php if (!empty($themes)): ?>
        <?php foreach ($themes as $theme): ?>
            <div class="theme-card">
                <div class="theme-screenshot" style="background: linear-gradient(to right, #4cc9f0, #34d399);">
                    <?php if (isset($theme['preview_image'])): ?>
                        <img src="<?php echo $theme['preview_image']; ?>" alt="<?php echo htmlspecialchars($theme['name'] ?? 'Theme'); ?>">
                    <?php else: ?>
                        <div style="background: rgba(0, 0, 0, 0.2); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-palette" style="font-size: 2.5rem; color: rgba(255, 255, 255, 0.5);"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="theme-content">
                    <div class="theme-header">
                        <div>
                            <h3 class="theme-name">
                                <i class="fas fa-crown" style="color: #fbbf24;"></i>
                                <?php echo htmlspecialchars($theme['name'] ?? 'Unknown Theme'); ?>
                            </h3>
                            <p class="theme-description"><?php echo htmlspecialchars(substr($theme['description'] ?? '', 0, 80)).(strlen($theme['description'] ?? '') > 80 ? '...' : ''); ?></p>
                        </div>
                    </div>
                    
                    <div class="theme-meta">
                        <span>By Developer</span>
                        <span>v1.0</span>
                    </div>
                    
                    <div class="theme-price-rating">
                        <span class="theme-price">
                            $<?php echo number_format($theme['price'] ?? 0, 2); ?>
                        </span>
                        <div class="rating-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star rating-star <?php echo $i <= ($theme['rating'] ?? 0) ? 'filled' : ''; ?>"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <div class="theme-actions">
                        <a href="#" class="btn-action purchase">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Purchase</span>
                        </a>
                        
                        <a href="#" class="btn-action details">
                            <i class="fas fa-info-circle"></i>
                            <span>Details</span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-themes">
            <i class="fas fa-store"></i>
            <p>No themes available in the marketplace</p>
            <p style="font-size: 0.9rem; margin-top: 0.5rem;">Check back later for new themes!</p>
        </div>
    <?php endif; ?>
</div>

<!-- Theme Categories -->
<div class="section-header">
    <h2 class="section-title">Theme Categories</h2>
</div>

<div class="categories-grid">
    <div class="category-card">
        <h3><i class="fas fa-calculator" style="color: #0ea5e9;"></i> Calculator Themes</h3>
        <p>32 themes</p>
        <a href="#" class="btn-category">
            <span>Browse</span>
        </a>
    </div>
    
    <div class="category-card">
        <h3><i class="fas fa-desktop" style="color: #10b981;"></i> Dashboard Themes</h3>
        <p>28 themes</p>
        <a href="#" class="btn-category">
            <span>Explore</span>
        </a>
    </div>
    
    <div class="category-card">
        <h3><i class="fas fa-home" style="color: #fbbf24;"></i> Homepage Themes</h3>
        <p>19 themes</p>
        <a href="#" class="btn-category">
            <span>Browse</span>
        </a>
    </div>
    
    <div class="category-card">
        <h3><i class="fas fa-mobile-alt" style="color: #8b5cf6;"></i> Mobile Themes</h3>
        <p>15 themes</p>
        <a href="#" class="btn-category">
            <span>View</span>
        </a>
    </div>
    
    <div class="category-card">
        <h3><i class="fas fa-gamepad" style="color: #ec4899;"></i> Gaming Themes</h3>
        <p>12 themes</p>
        <a href="#" class="btn-category">
            <span>See All</span>
        </a>
    </div>
    
    <div class="category-card">
        <h3><i class="fas fa-business-time" style="color: #f97316;"></i> Business Themes</h3>
        <p>11 themes</p>
        <a href="#" class="btn-category">
            <span>Explore</span>
        </a>
    </div>
</div>