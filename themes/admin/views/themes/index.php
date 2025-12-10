<?php
/**
 * PREMIUM THEMES MANAGEMENT INTERFACE
 * Matching the design of Pages Management
 */

// Calculate stats
$totalThemes = isset($stats['total']) ? $stats['total'] : count($themes ?? []);
$activeThemes = isset($stats['active']) ? $stats['active'] : 1;
$inactiveThemes = isset($stats['inactive']) ? $stats['inactive'] : ($totalThemes - $activeThemes);
?>

<!-- Optimized Admin Wrapper Container -->
<div class="admin-wrapper-container">
    <div class="admin-content-wrapper">

        <!-- Compact Page Header -->
        <div class="compact-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="fas fa-palette"></i>
                    <h1>Themes</h1>
                </div>
                <div class="header-subtitle"><?php echo $totalThemes; ?> themes • <?php echo $activeThemes; ?> active</div>
            </div>
            <div class="header-actions">
                <button class="btn btn-primary btn-compact" onclick="document.getElementById('themeUpload').click()">
                    <i class="fas fa-upload"></i>
                    <span>Upload Theme</span>
                </button>
                <input type="file" id="themeUpload" style="display:none" accept=".zip">
            </div>
        </div>

        <!-- Compact Stats Cards -->
        <div class="compact-stats">
            <div class="stat-item">
                <div class="stat-icon primary">
                    <i class="fas fa-palette"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $totalThemes; ?></div>
                    <div class="stat-label">Total</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $activeThemes; ?></div>
                    <div class="stat-label">Active</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon warning">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $inactiveThemes; ?></div>
                    <div class="stat-label">Inactive</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon info">
                    <i class="fas fa-paint-brush"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo isset($activeTheme['name']) ? htmlspecialchars($activeTheme['name']) : 'Default'; ?></div>
                    <div class="stat-label">Current Theme</div>
                </div>
            </div>
        </div>

        <!-- Compact Toolbar -->
        <div class="compact-toolbar">
            <div class="toolbar-left">
                <!-- Client-side Search -->
                <div class="search-compact">
                    <i class="fas fa-search"></i>
                    <input type="text" id="themeSearch" placeholder="Search themes...">
                </div>
            </div>
            <div class="toolbar-right">
                <!-- View Toggle -->
                <div class="view-toggle">
                    <button class="view-btn" data-view="table">
                        <i class="fas fa-list"></i>
                    </button>
                    <button class="view-btn active" data-view="grid">
                        <i class="fas fa-th-large"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Themes Content -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php elseif (empty($themes)): ?>
            <div class="empty-state">
                <i class="fas fa-palette"></i>
                <h3>No themes found</h3>
                <p>Upload a theme to get started.</p>
            </div>
        <?php else: ?>
            <!-- Table View -->
            <div class="table-wrapper" id="tableView" style="display: none;">
                <table class="table-compact" id="themesTable">
                    <thead>
                        <tr>
                            <th class="col-title">Theme Name</th>
                            <th class="col-author">Author</th>
                            <th class="col-version">Version</th>
                            <th class="col-status">Status</th>
                            <th class="col-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($themes as $theme): ?>
                            <tr class="theme-row" data-name="<?php echo strtolower($theme['name'] ?? ''); ?>">
                                <td>
                                    <div class="page-info">
                                        <div class="page-title-compact"><?php echo htmlspecialchars($theme['name'] ?? 'Unknown'); ?></div>
                                        <div class="page-slug-compact"><?php echo htmlspecialchars($theme['description'] ?? ''); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="author-compact"><?php echo htmlspecialchars($theme['author'] ?? 'Unknown'); ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($theme['version'] ?? '1.0.0'); ?></span>
                                </td>
                                <td>
                                    <?php if (($theme['status'] ?? 'inactive') === 'active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions-compact">
                                        <?php if (($theme['status'] ?? 'inactive') !== 'active'): ?>
                                            <button class="action-btn-icon edit-btn activate-theme" 
                                                    data-id="<?php echo $theme['id'] ?? ''; ?>" 
                                                    title="Activate">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        <?php endif; ?>
                                        <a href="<?php echo get_app_url(); ?>/admin/themes/customize/<?php echo urlencode($theme['slug'] ?? $theme['name']); ?>" 
                                           class="action-btn-icon preview-btn" 
                                           title="Customize">
                                            <i class="fas fa-paint-brush"></i>
                                        </a>
                                        <?php if (($theme['status'] ?? 'inactive') !== 'active'): ?>
                                            <button class="action-btn-icon delete-btn delete-theme" 
                                                    data-id="<?php echo $theme['id'] ?? ''; ?>" 
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Grid View -->
            <div class="grid-wrapper" id="gridView">
                <div class="themes-grid">
                    <?php foreach ($themes as $theme): ?>
                        <div class="theme-card" data-name="<?php echo strtolower($theme['name'] ?? ''); ?>">
                            <div class="theme-preview">
                                <?php 
                                // Check for screenshot
                                $hasScreenshot = !empty($theme['screenshot']) && file_exists(BASE_PATH . '/public' . $theme['screenshot']);
                                
                                if ($hasScreenshot): ?>
                                    <img src="<?php echo htmlspecialchars($theme['screenshot']); ?>" alt="<?php echo htmlspecialchars($theme['name']); ?>">
                                <?php else: 
                                    // Show homepage with theme applied
                                    $homeUrl = get_app_url() . '/?preview_theme=' . urlencode($theme['slug'] ?? $theme['name']);
                                ?>
                                    <iframe src="<?php echo $homeUrl; ?>" 
                                            class="theme-preview-iframe" 
                                            scrolling="no"
                                            onload="this.style.opacity='1'"></iframe>
                                    <div class="theme-preview-overlay">
                                        <a href="<?php echo $homeUrl; ?>" target="_blank" class="btn btn-sm btn-light">
                                            <i class="fas fa-external-link-alt"></i> Full Preview
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <?php if (($theme['status'] ?? 'inactive') === 'active'): ?>
                                    <div class="theme-badge">
                                        <span class="badge bg-success">Active</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="theme-info">
                                <h3><?php echo htmlspecialchars($theme['name'] ?? 'Unknown'); ?></h3>
                                <p class="theme-author">By <?php echo htmlspecialchars($theme['author'] ?? 'Unknown'); ?></p>
                                <p class="theme-description"><?php echo htmlspecialchars($theme['description'] ?? ''); ?></p>
                                <div class="theme-meta">
                                    <span class="theme-version">v<?php echo htmlspecialchars($theme['version'] ?? '1.0.0'); ?></span>
                                </div>
                                <div class="theme-actions">
                                    <?php if (($theme['status'] ?? 'inactive') !== 'active'): ?>
                                        <button class="btn btn-sm btn-primary activate-theme" data-id="<?php echo $theme['id'] ?? ''; ?>">
                                            <i class="fas fa-check"></i> Activate
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-success" disabled>
                                            <i class="fas fa-check"></i> Active
                                        </button>
                                    <?php endif; ?>
                                    <a href="<?php echo get_app_url(); ?>/admin/themes/customize/<?php echo urlencode($theme['slug'] ?? $theme['name']); ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-paint-brush"></i> Customize
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
// Theme Search
document.getElementById('themeSearch')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.theme-row, .theme-card');
    
    rows.forEach(row => {
        const name = row.getAttribute('data-name') || '';
        if (name.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// View Toggle
document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const view = this.getAttribute('data-view');
        
        // Update active state
        document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        // Toggle views
        if (view === 'grid') {
            document.getElementById('tableView').style.display = 'none';
            document.getElementById('gridView').style.display = 'block';
        } else {
            document.getElementById('tableView').style.display = 'block';
            document.getElementById('gridView').style.display = 'none';
        }
    });
});

// Activate Theme
document.querySelectorAll('.activate-theme').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Are you sure you want to activate this theme?')) return;
        
        const id = this.dataset.id;
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Activating...';
        
        const formData = new FormData();
        formData.append('theme_id', id);
        
        fetch('<?php echo app_base_url('/admin/themes/activate'); ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to activate theme'));
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-check"></i> Activate';
            }
        })
        .catch(error => {
            alert('Error activating theme');
            console.error(error);
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-check"></i> Activate';
        });
    });
});

// Delete Theme
document.querySelectorAll('.delete-theme').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('Are you sure you want to delete this theme? This action cannot be undone.')) return;
        
        const id = this.dataset.id;
        this.disabled = true;
        
        const formData = new FormData();
        formData.append('theme_id', id);
        
        fetch('<?php echo app_base_url('/admin/themes/delete'); ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to delete theme'));
                this.disabled = false;
            }
        })
        .catch(error => {
            alert('Error deleting theme');
            console.error(error);
            this.disabled = false;
        });
    });
});

// Upload Theme
document.getElementById('themeUpload')?.addEventListener('change', function(e) {
    if (this.files.length === 0) return;
    
    const formData = new FormData();
    formData.append('theme_zip', this.files[0]);
    
    const btn = document.querySelector('button[onclick*="themeUpload"]');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
    
    fetch('<?php echo app_base_url('/admin/themes/upload'); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        document.getElementById('themeUpload').value = '';
        
        if (data.success) {
            alert('Theme uploaded successfully');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Upload failed'));
        }
    })
    .catch(error => {
        alert('Upload failed');
        console.error(error);
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});
</script>

<style>
/* Theme Grid Styles */
.themes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    padding: 1rem 0;
}

.theme-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}

.theme-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.theme-preview {
    position: relative;
    width: 100%;
    height: 200px;
    background: #f8f9fa;
    overflow: hidden;
}

.theme-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.theme-preview-iframe {
    width: 100%;
    height: 100%;
    border: none;
    transform: scale(0.25);
    transform-origin: top left;
    width: 400%;
    height: 400%;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.3s;
}

.theme-preview-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.2s;
}

.theme-card:hover .theme-preview-overlay {
    opacity: 1;
}

.theme-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    font-size: 3rem;
    color: #dee2e6;
}

.theme-badge {
    position: absolute;
    top: 10px;
    right: 10px;
}

.theme-info {
    padding: 1.25rem;
}

.theme-info h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.125rem;
    font-weight: 600;
}

.theme-author {
    color: #6c757d;
    font-size: 0.875rem;
    margin: 0 0 0.75rem 0;
}

.theme-description {
    color: #495057;
    font-size: 0.875rem;
    margin: 0 0 1rem 0;
    line-height: 1.5;
}

.theme-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-top: 0.75rem;
    border-top: 1px solid #e9ecef;
}

.theme-version {
    font-size: 0.75rem;
    color: #6c757d;
}

.theme-actions {
    display: flex;
    gap: 0.5rem;
}

.theme-actions .btn {
    flex: 1;
}
</style>