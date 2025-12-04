<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><i class="fas fa-cog"></i> Module Settings: <?php echo htmlspecialchars($moduleName); ?></h1>
            <p class="page-description">Configure settings for this module.</p>
        </div>
        <div class="page-header-actions">
            <a href="<?php echo app_base_url('/admin/modules'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Modules
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-content">
        <form action="<?php echo app_base_url('/admin/modules/settings/update'); ?>" method="post">
            <input type="hidden" name="module" value="<?php echo htmlspecialchars($moduleName); ?>">
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Settings configuration for this module is not yet implemented.
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary" disabled>
                    <i class="fas fa-save"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>