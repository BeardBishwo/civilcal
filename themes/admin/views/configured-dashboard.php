<?php
$page_title = $page_title ?? 'Configured Dashboard';
$dashboard_config = $dashboard_config ?? [];
$available_widgets = $available_widgets ?? [];
$menu_items = $menu_items ?? [];
?>
<div class="admin-content">
    <div class="page-header">
        <h1><i class="fas fa-cog"></i> Configured Dashboard</h1>
        <p>Customize your dashboard layout and widgets</p>
        <div class="page-actions">
            <button class="btn btn-secondary" onclick="resetToDefault()">
                <i class="fas fa-undo"></i> Reset to Default
            </button>
            <button class="btn btn-primary" onclick="saveDashboardConfig()">
                <i class="fas fa-save"></i> Save
            </button>
        </div>
    </div>

    <div class="dashboard-configurator">
        <div class="config-sidebar">
            <h3>Available Widgets</h3>
            <div class="widget-palette">
                <?php foreach ($available_widgets as $widget): ?>
                    <div class="widget-item" data-widget-id="<?= htmlspecialchars($widget['id']) ?>">
                        <div class="widget-preview">
                            <i class="fas fa-<?= htmlspecialchars($widget['icon'] ?? 'cube') ?>"></i>
                            <h4><?= htmlspecialchars($widget['title']) ?></h4>
                            <p><?= htmlspecialchars($widget['description']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="config-main">
            <div class="dashboard-preview">
                <h3>Dashboard Preview</h3>
                <div class="dashboard-grid" id="dashboard-grid">
                    <?php if (!empty($dashboard_config['widgets'])): ?>
                        <?php foreach ($dashboard_config['widgets'] as $widget_config): ?>
                            <div class="dashboard-widget" data-widget-id="<?= htmlspecialchars($widget_config['id']) ?>">
                                <div class="widget-header">
                                    <h4><?= htmlspecialchars($widget_config['title']) ?></h4>
                                    <div class="widget-actions">
                                        <button class="btn btn-sm btn-secondary" onclick="configureWidget('<?= htmlspecialchars($widget_config['id']) ?>')">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="removeWidget('<?= htmlspecialchars($widget_config['id']) ?>')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="widget-content">
                                    <!-- Widget content will be dynamically loaded -->
                                    <div class="widget-placeholder">
                                        <i class="fas fa-<?= $widget_config['icon'] ?? 'cube' ?>"></i>
                                        <p><?= htmlspecialchars($widget_config['title']) ?> Widget</p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-dashboard">
                            <i class="fas fa-plus-circle"></i>
                            <p>Drag widgets from the sidebar to start building your dashboard</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="layout-options">
                <h3>Layout Options</h3>
                <div class="form-group">
                    <label>Grid Columns</label>
                    <select name="grid_columns" id="grid-columns" class="form-control">
                        <option value="2" <?= ($dashboard_config['grid_columns'] ?? 3) == 2 ? 'selected' : '' ?>>2 Columns</option>
                        <option value="3" <?= ($dashboard_config['grid_columns'] ?? 3) == 3 ? 'selected' : '' ?>>3 Columns</option>
                        <option value="4" <?= ($dashboard_config['grid_columns'] ?? 3) == 4 ? 'selected' : '' ?>>4 Columns</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Widget Spacing</label>
                    <select name="widget_spacing" id="widget-spacing" class="form-control">
                        <option value="compact" <?= ($dashboard_config['widget_spacing'] ?? 'normal') == 'compact' ? 'selected' : '' ?>>Compact</option>
                        <option value="normal" <?= ($dashboard_config['widget_spacing'] ?? 'normal') == 'normal' ? 'selected' : '' ?>>Normal</option>
                        <option value="spacious" <?= ($dashboard_config['widget_spacing'] ?? 'normal') == 'spacious' ? 'selected' : '' ?>>Spacious</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboardConfigurator();
});

function initializeDashboardConfigurator() {
    // Initialize drag and drop
    const grid = document.getElementById('dashboard-grid');
    const widgets = document.querySelectorAll('.widget-item');
    
    widgets.forEach(widget => {
        widget.draggable = true;
        widget.addEventListener('dragstart', handleDragStart);
    });
    
    grid.addEventListener('dragover', handleDragOver);
    grid.addEventListener('drop', handleDrop);
    
    // Initialize sortable for existing widgets
    if (typeof Sortable !== 'undefined') {
        new Sortable(grid, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            handle: '.widget-header'
        });
    }
}

function handleDragStart(e) {
    e.dataTransfer.setData('widgetId', e.target.dataset.widgetId);
    e.target.classList.add('dragging');
}

function handleDragOver(e) {
    e.preventDefault();
    e.currentTarget.classList.add('drag-over');
}

function handleDrop(e) {
    e.preventDefault();
    const widgetId = e.dataTransfer.getData('widgetId');
    const grid = e.currentTarget;
    
    grid.classList.remove('drag-over');
    document.querySelector('.dragging')?.classList.remove('dragging');
    
    // Add widget to dashboard
    addWidgetToDashboard(widgetId);
}

function addWidgetToDashboard(widgetId) {
    const widget = document.querySelector(`[data-widget-id="${widgetId}"]`);
    if (!widget) return;
    
    const widgetData = {
        id: widgetId,
        title: widget.querySelector('h4').textContent,
        icon: widget.querySelector('i').className.replace('fas fa-', ''),
        description: widget.querySelector('p').textContent
    };
    
    const widgetElement = createWidgetElement(widgetData);
    document.getElementById('dashboard-grid').appendChild(widgetElement);
}

function createWidgetElement(widgetData) {
    const div = document.createElement('div');
    div.className = 'dashboard-widget';
    div.dataset.widgetId = widgetData.id;
    
    div.innerHTML = `
        <div class="widget-header">
            <h4>${widgetData.title}</h4>
            <div class="widget-actions">
                <button class="btn btn-sm btn-secondary" onclick="configureWidget('${widgetData.id}')">
                    <i class="fas fa-cog"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="removeWidget('${widgetData.id}')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="widget-content">
            <div class="widget-placeholder">
                <i class="fas fa-${widgetData.icon}"></i>
                <p>${widgetData.title} Widget</p>
            </div>
        </div>
    `;
    
    return div;
}

function removeWidget(widgetId) {
    const widget = document.querySelector(`.dashboard-widget[data-widget-id="${widgetId}"]`);
    if (widget && confirm('Are you sure you want to remove this widget?')) {
        widget.remove();
    }
}

function configureWidget(widgetId) {
    // Open widget configuration modal
    console.log('Configure widget:', widgetId);
    // Implementation would open a modal with widget-specific options
}

function saveDashboardConfig() {
    const grid = document.getElementById('dashboard-grid');
    const widgets = Array.from(grid.querySelectorAll('.dashboard-widget')).map(widget => ({
        id: widget.dataset.widgetId,
        title: widget.querySelector('h4').textContent,
        position: Array.from(grid.children).indexOf(widget)
    }));
    
    const config = {
        widgets: widgets,
        grid_columns: document.getElementById('grid-columns').value,
        widget_spacing: document.getElementById('widget-spacing').value
    };
    
    fetch('<?= app_base_url('/admin/dashboard/save-config') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= csrf_token() ?>'
        },
        body: JSON.stringify(config)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Dashboard configuration saved successfully', 'success');
        } else {
            showNotification('Failed to save dashboard configuration', 'error');
        }
    })
    .catch(error => {
        showNotification('Error saving configuration', 'error');
    });
}

function resetToDefault() {
    if (confirm('Are you sure you want to reset to the default dashboard configuration?')) {
        fetch('<?= app_base_url('/admin/dashboard/reset-config') ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': '<?= csrf_token() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showNotification('Failed to reset configuration', 'error');
            }
        });
    }
}
</script>

<style>
.dashboard-configurator {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 20px;
    margin-top: 20px;
}

.config-sidebar {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    height: fit-content;
}

.widget-palette {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.widget-item {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    cursor: grab;
    transition: all 0.3s ease;
}

.widget-item:hover {
    border-color: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.widget-item.dragging {
    opacity: 0.5;
    cursor: grabbing;
}

.config-main {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.dashboard-preview {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    min-height: 400px;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 20px;
}

.dashboard-widget {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
}

.widget-header {
    background: #007bff;
    color: white;
    padding: 10px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.widget-header h4 {
    margin: 0;
    font-size: 16px;
}

.widget-actions {
    display: flex;
    gap: 5px;
}

.widget-content {
    padding: 20px;
    min-height: 150px;
}

.widget-placeholder {
    text-align: center;
    color: #6c757d;
}

.widget-placeholder i {
    font-size: 48px;
    margin-bottom: 10px;
    display: block;
}

.empty-dashboard {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-dashboard i {
    font-size: 64px;
    margin-bottom: 20px;
    display: block;
}

.layout-options {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
}

.drag-over {
    background: #e3f2fd;
    border: 2px dashed #007bff;
}

.sortable-ghost {
    opacity: 0.4;
}

@media (max-width: 768px) {
    .dashboard-configurator {
        grid-template-columns: 1fr;
    }
    
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}
</style>
