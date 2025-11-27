<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><i class="fas fa-plus-circle"></i> Create Widget</h1>
            <p class="page-description">Create a new widget for your application</p>
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
        <form method="post" action="<?php echo app_base_url('/admin/widgets/create'); ?>">
            <div class="form-group">
                <label class="form-label">Widget Class</label>
                <select name="class_name" class="form-select" required>
                    <option value="">Select a widget class</option>
                    <?php foreach ($available_classes ?? [] as $className): ?>
                        <option value="<?php echo htmlspecialchars($className); ?>" 
                                <?php echo (isset($_GET['class']) && $_GET['class'] === $className) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($className); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" placeholder="Enter widget title" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Enter widget description"></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Position</label>
                <input type="number" name="position" class="form-control" value="0" min="0">
            </div>
            
            <div class="form-check">
                <input type="checkbox" name="is_enabled" id="is_enabled" class="form-check-input" checked>
                <label for="is_enabled" class="form-check-label">Enabled</label>
            </div>
            
            <div class="form-check">
                <input type="checkbox" name="is_visible" id="is_visible" class="form-check-input" checked>
                <label for="is_visible" class="form-check-label">Visible</label>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Widget
                </button>
                <a href="<?php echo app_base_url('/admin/widgets'); ?>" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($widget_class_info)): ?>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-info-circle"></i>
                Widget Class Information
            </h5>
        </div>
        
        <div class="card-content">
            <?php foreach ($widget_class_info as $className => $info): ?>
                <div class="widget-class-info">
                    <h6><?php echo htmlspecialchars($className); ?></h6>
                    <?php if (!empty($info['description'])): ?>
                        <p><?php echo htmlspecialchars($info['description']); ?></p>
                    <?php endif; ?>
                    
                    <?php if (!empty($info['methods'])): ?>
                        <div class="methods-list">
                            <h6>Methods:</h6>
                            <ul>
                                <?php foreach ($info['methods'] as $method): ?>
                                    <li><?php echo htmlspecialchars($method['name']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>