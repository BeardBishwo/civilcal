<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><i class="fas fa-eye"></i> Theme Preview</h1>
            <p class="page-description">Previewing: <?php echo htmlspecialchars($activeTheme['name'] ?? 'Unknown'); ?></p>
        </div>
        <div class="page-header-actions">
            <a href="<?php echo app_base_url('/admin/themes'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Themes
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-content text-center" style="padding: 40px;">
        <i class="fas fa-desktop fa-4x text-muted mb-3"></i>
        <h3>Preview Mode</h3>
        <p>This is a placeholder for the theme preview functionality.</p>
        <a href="<?php echo app_base_url('/'); ?>" target="_blank" class="btn btn-primary mt-3">
            <i class="fas fa-external-link-alt"></i> View Live Site
        </a>
    </div>
</div>