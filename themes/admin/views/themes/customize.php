<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><i class="fas fa-sliders-h"></i> Customize Theme</h1>
            <p class="page-description">Adjust colors, typography, and layout settings.</p>
        </div>
        <div class="page-header-actions">
            <a href="<?php echo app_base_url('/admin/themes'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Themes
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-content">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Theme customization features are coming soon.
        </div>
        <p>You will be able to customize:</p>
        <ul class="feature-list">
            <li><i class="fas fa-palette"></i> Primary and Secondary Colors</li>
            <li><i class="fas fa-font"></i> Typography and Fonts</li>
            <li><i class="fas fa-window-maximize"></i> Header and Footer Layouts</li>
            <li><i class="fas fa-code"></i> Custom CSS</li>
        </ul>
    </div>
</div>

<style>
.feature-list {
    list-style: none;
    padding: 0;
}

.feature-list li {
    padding: 8px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.feature-list li i {
    color: var(--admin-primary);
}
</style>