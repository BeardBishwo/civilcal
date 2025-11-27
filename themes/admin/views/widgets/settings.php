<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><i class="fas fa-sliders-h"></i> Widget Settings</h1>
            <p class="page-description">Configure advanced settings for "<?php echo htmlspecialchars($widget->getTitle()); ?>"</p>
        </div>
        <div class="page-header-actions">
            <a href="<?php echo app_base_url('/admin/widgets'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Widgets
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-info-circle"></i>
            Widget Information
        </h5>
    </div>
    
    <div class="card-content">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
                <label class="form-label">Widget Type</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($widget->getType()); ?>" readonly>
            </div>
            
            <div class="form-group">
                <label class="form-label">Widget ID</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($widget->getId()); ?>" readonly>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-cog"></i>
            Custom Settings
        </h5>
    </div>
    
    <div class="card-content">
        <form method="post" action="<?php echo app_base_url('/admin/widgets/settings/' . $widget->getId()); ?>">
            <div class="form-group">
                <label class="form-label">Configuration Data</label>
                <textarea name="settings[config]" class="form-control" rows="6" placeholder="Enter JSON configuration data"><?php 
                    $config = $widget->getConfig();
                    echo htmlspecialchars(json_encode($config, JSON_PRETTY_PRINT));
                ?></textarea>
                <div class="form-help">Advanced configuration in JSON format</div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Settings
                </button>
                <a href="<?php echo app_base_url('/admin/widgets'); ?>" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Widget settings interface loaded');
});
</script>