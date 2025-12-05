<?php
/**
 * Premium Themes Management Page
 */
?>

<div class="admin-container">
    <div class="admin-header">
        <h1><?php echo htmlspecialchars($title ?? 'Premium Themes'); ?></h1>
        <div class="header-actions">
            <a href="/admin/premium-themes/marketplace" class="btn btn-primary">
                <i class="fas fa-shopping-cart"></i> Browse Marketplace
            </a>
            <a href="/admin/premium-themes/create" class="btn btn-success">
                <i class="fas fa-plus"></i> Create Theme
            </a>
        </div>
    </div>

    <div class="admin-content">
        <?php if (!empty($userHasAccess)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                You have access to premium themes. Explore and customize them below.
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="fas fa-lock"></i>
                Premium themes require an active subscription. <a href="/admin/subscription">Upgrade now</a>
            </div>
        <?php endif; ?>

        <div class="themes-grid">
            <?php if (!empty($themes)): ?>
                <?php foreach ($themes as $theme): ?>
                    <div class="theme-card">
                        <div class="theme-preview">
                            <?php if (!empty($theme['preview_image'])): ?>
                                <img src="<?php echo htmlspecialchars($theme['preview_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($theme['name'] ?? 'Theme'); ?>"
                                     class="theme-image">
                            <?php else: ?>
                                <div class="theme-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($activeTheme && $activeTheme['id'] == ($theme['id'] ?? null)): ?>
                                <div class="theme-badge active">
                                    <i class="fas fa-check"></i> Active
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="theme-info">
                            <h3><?php echo htmlspecialchars($theme['name'] ?? 'Unnamed Theme'); ?></h3>
                            <p class="theme-description">
                                <?php echo htmlspecialchars($theme['description'] ?? 'No description'); ?>
                            </p>
                            
                            <div class="theme-meta">
                                <?php if (!empty($theme['price'])): ?>
                                    <span class="theme-price">$<?php echo number_format($theme['price'], 2); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($theme['rating'])): ?>
                                    <span class="theme-rating">
                                        <i class="fas fa-star"></i> <?php echo number_format($theme['rating'], 1); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="theme-actions">
                                <a href="/admin/premium-themes/<?php echo htmlspecialchars($theme['id'] ?? ''); ?>" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="/admin/premium-themes/<?php echo htmlspecialchars($theme['id'] ?? ''); ?>/customize" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-palette"></i> Customize
                                </a>
                                <?php if ($activeTheme && $activeTheme['id'] != ($theme['id'] ?? null)): ?>
                                    <button class="btn btn-sm btn-success activate-theme" 
                                            data-theme-id="<?php echo htmlspecialchars($theme['id'] ?? ''); ?>">
                                        <i class="fas fa-check"></i> Activate
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No Premium Themes Available</h3>
                    <p>Browse the marketplace to find and install premium themes.</p>
                    <a href="/admin/premium-themes/marketplace" class="btn btn-primary">
                        Browse Marketplace
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.admin-container {
    padding: 20px;
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e0e0e0;
}

.admin-header h1 {
    margin: 0;
    font-size: 28px;
    color: #333;
}

.header-actions {
    display: flex;
    gap: 10px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-primary {
    background-color: #667eea;
    color: white;
}

.btn-primary:hover {
    background-color: #5568d3;
}

.btn-success {
    background-color: #28a745;
    color: white;
}

.btn-success:hover {
    background-color: #218838;
}

.btn-info {
    background-color: #17a2b8;
    color: white;
}

.btn-info:hover {
    background-color: #138496;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.alert {
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-info {
    background-color: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.alert-warning {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.themes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

.theme-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.theme-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.theme-preview {
    position: relative;
    width: 100%;
    height: 200px;
    background: #f5f5f5;
    overflow: hidden;
}

.theme-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.theme-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    color: #ccc;
}

.theme-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #28a745;
    color: white;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.theme-info {
    padding: 15px;
}

.theme-info h3 {
    margin: 0 0 10px 0;
    font-size: 16px;
    color: #333;
}

.theme-description {
    margin: 0 0 10px 0;
    font-size: 13px;
    color: #666;
    line-height: 1.4;
}

.theme-meta {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
    font-size: 12px;
}

.theme-price {
    color: #667eea;
    font-weight: bold;
}

.theme-rating {
    color: #f39c12;
}

.theme-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.theme-actions .btn {
    flex: 1;
    min-width: 80px;
    justify-content: center;
    font-size: 12px;
    padding: 8px 10px;
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    color: #999;
}

.empty-state i {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.5;
}

.empty-state h3 {
    margin: 20px 0 10px 0;
    color: #666;
}

.empty-state p {
    margin: 0 0 20px 0;
}
</style>

<script>
document.querySelectorAll('.activate-theme').forEach(btn => {
    btn.addEventListener('click', function() {
        const themeId = this.dataset.themeId;
        if (confirm('Activate this theme?')) {
            fetch(`/admin/premium-themes/${themeId}/activate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to activate theme'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error activating theme');
            });
        }
    });
});
</script>
