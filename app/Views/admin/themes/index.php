<?php
$content = '
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Themes Management</h2>
            <p class="text-muted mb-0">Manage website themes and appearance</p>
        </div>
        <div class="quick-actions">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadThemeModal">
                <i class="bi bi-upload me-2"></i>Upload Theme
            </button>
            <button class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-palette me-2"></i>Theme Editor
            </button>
        </div>
    </div>

    <!-- Current Active Theme -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="card-title">Active Theme</h5>
                            <p class="card-text">This theme is currently active and visible to all users.</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-success fs-6">ACTIVE</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Themes -->
    <div class="row">';
    
    // Mock themes data
    $themes = [
        [
            'name' => 'Default Theme',
            'folder' => 'default',
            'description' => 'Clean and professional default theme',
            'version' => '1.0.0',
            'author' => 'Bishwo Calculator Team',
            'status' => 'active'
        ],
        [
            'name' => 'Dark Mode',
            'folder' => 'dark',
            'description' => 'Professional dark theme for night work',
            'version' => '1.2.0',
            'author' => 'Bishwo Calculator Team',
            'status' => 'inactive'
        ],
        [
            'name' => 'Modern Blue',
            'folder' => 'modern-blue',
            'description' => 'Modern blue theme with clean design',
            'version' => '1.1.0',
            'author' => 'Design Team',
            'status' => 'inactive'
        ]
    ];
    
    foreach ($themes as $theme) {
        $isActive = ($theme['folder'] === $activeTheme);
        
        $content .= '
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card theme-card h-100 ' . ($isActive ? 'border-primary' : '') . '">
                <div class="card-header position-relative">
                    <div class="bg-light" style="height: 150px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-palette fs-1 text-muted"></i>
                    </div>
                    ' . ($isActive ? '<div class="position-absolute top-0 end-0 m-2"><span class="badge bg-success">Active</span></div>' : '') . '
                </div>
                <div class="card-body">
                    <h5 class="card-title">' . htmlspecialchars($theme['name']) . '</h5>
                    <p class="card-text text-muted">' . htmlspecialchars($theme['description']) . '</p>
                    <div class="theme-meta">
                        <small class="text-muted">
                            <strong>Version:</strong> ' . ($theme['version']) . '<br>
                            <strong>Author:</strong> ' . ($theme['author']) . '
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="btn-group w-100">';
                    
                    if (!$isActive) {
                        $content .= '
                        <button class="btn btn-primary btn-sm activate-theme" data-theme="' . $theme['folder'] . '">
                            <i class="bi bi-check-circle me-1"></i>Activate
                        </button>';
                    }
                    
                    $content .= '
                        <button class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-eye me-1"></i>Preview
                        </button>
                        <button class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash me-1"></i>Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>';
    }
    
    $content .= '
    </div>

    <!-- Theme Customization -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Theme Customization</h6>
                </div>
                <div class="card-body">
                    <form id="themeCustomizationForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Primary Color</label>
                                    <input type="color" class="form-control form-control-color" name="primary_color" value="#2c3e50" title="Choose primary color">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Secondary Color</label>
                                    <input type="color" class="form-control form-control-color" name="secondary_color" value="#3498db" title="Choose secondary color">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Font Family</label>
                                    <select class="form-select" name="font_family">
                                        <option value="Arial, sans-serif">Arial</option>
                                        <option value="Georgia, serif">Georgia</option>
                                        <option value="\'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif" selected>Segoe UI</option>
                                        <option value="\'Courier New\', monospace">Courier New</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Border Radius</label>
                                    <select class="form-select" name="border_radius">
                                        <option value="0">Sharp (0px)</option>
                                        <option value="4px" selected>Default (4px)</option>
                                        <option value="8px">Rounded (8px)</option>
                                        <option value="12px">Very Rounded (12px)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Custom CSS</label>
                            <textarea class="form-control" name="custom_css" rows="4" placeholder="Enter custom CSS code..."></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary">Reset to Default</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Theme Modal -->
<div class="modal fade" id="uploadThemeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload New Theme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="uploadThemeForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Theme ZIP File</label>
                        <input type="file" class="form-control" name="theme_zip" accept=".zip" required>
                        <small class="form-text text-muted">
                            Upload a ZIP file containing your theme. The ZIP should contain a theme.json file and all theme assets.
                        </small>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Theme Requirements:</strong><br>
                        • Must contain theme.json with name, version, author<br>
                        • Should include all CSS, JS, and image assets<br>
                        • Maximum file size: 10MB
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="uploadThemeForm" class="btn btn-primary">Upload Theme</button>
            </div>
        </div>
    </div>
</div>

<script>
// Activate theme
document.querySelectorAll(".activate-theme").forEach(button => {
    button.addEventListener("click", function() {
        const themeName = this.dataset.theme;
        if (confirm("Are you sure you want to activate this theme?")) {
            const formData = new FormData();
            formData.append("theme_name", themeName);
            
            fetch("/admin/themes/activate", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert("Error: " + data.message);
                }
            });
        }
    });
});

// Upload theme
document.getElementById("uploadThemeForm").addEventListener("submit", function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch("/admin/themes/upload", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert("Error: " + data.message);
        }
    });
});
</script>
';

include __DIR__ . '/../../layouts/admin.php';
?>
