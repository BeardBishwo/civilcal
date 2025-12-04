<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><i class="fas fa-edit"></i> Edit Widget</h1>
            <p class="page-description">Modify widget configuration and settings</p>
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
            <i class="fas fa-cube"></i>
            Widget Configuration
        </h5>
    </div>
    
    <div class="card-content">
        <form method="post" action="<?php echo app_base_url('/admin/widgets/edit/' . $widget->getId()); ?>">
            <div class="form-group">
                <label class="form-label">Widget Type</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($widget->getType()); ?>" readonly>
                <div class="form-help">Widget type cannot be changed after creation</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($widget->getTitle()); ?>" placeholder="Enter widget title" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Enter widget description"><?php echo htmlspecialchars($widget->getDescription()); ?></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Position</label>
                <input type="number" name="position" class="form-control" value="<?php echo $widget->getPosition(); ?>" min="0">
            </div>
            
            <div class="form-check">
                <input type="checkbox" name="is_enabled" id="is_enabled" class="form-check-input" <?php echo $widget->isEnabled() ? 'checked' : ''; ?>>
                <label for="is_enabled" class="form-check-label">Enabled</label>
            </div>
            
            <div class="form-check">
                <input type="checkbox" name="is_visible" id="is_visible" class="form-check-input" <?php echo $widget->isVisible() ? 'checked' : ''; ?>>
                <label for="is_visible" class="form-check-label">Visible</label>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save
                </button>
                <a href="<?php echo app_base_url('/admin/widgets'); ?>" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-cog"></i>
            Advanced Settings
        </h5>
    </div>
    
    <div class="card-content">
        <p class="text-gray-600">For advanced widget configuration, visit the dedicated settings page.</p>
        <a href="<?php echo app_base_url('/admin/widgets/settings/' . $widget->getId()); ?>" class="btn btn-outline-primary">
            <i class="fas fa-sliders-h"></i> Widget Settings
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Widget edit interface loaded');
});
</script>