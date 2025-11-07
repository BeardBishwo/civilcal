<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Management - Bishwo Calculator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/assets/css/admin.css" rel="stylesheet">
    <style>
        .theme-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .theme-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .theme-card.active {
            border-color: #28a745;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        }
        .theme-preview {
            width: 100%;
            height: 150px;
            background: linear-gradient(45deg, #f8f9fa 25%, transparent 25%), 
                        linear-gradient(-45deg, #f8f9fa 25%, transparent 25%), 
                        linear-gradient(45deg, transparent 75%, #f8f9fa 75%), 
                        linear-gradient(-45deg, transparent 75%, #f8f9fa 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            margin-bottom: 15px;
            position: relative;
            overflow: hidden;
        }
        .theme-preview.professional {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .theme-preview.default {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .theme-status {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .upload-zone {
            border: 2px dashed #6c757d;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        .upload-zone:hover {
            border-color: #007bff;
            background: #e9ecef;
        }
        .upload-zone.dragover {
            border-color: #28a745;
            background: #d4edda;
        }
        .loading-spinner {
            display: none;
        }
        .btn-group-custom .btn {
            margin-right: 5px;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 15px;
        }
        .theme-screenshot {
            width: 100%;
            height: 200px;
            background-size: cover;
            background-position: center;
            border-radius: 8px 8px 0 0;
        }
        .theme-info {
            padding: 15px;
        }
        .theme-name {
            font-weight: bold;
            font-size: 1.1em;
            margin-bottom: 5px;
        }
        .theme-author {
            color: #6c757d;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
        .theme-description {
            color: #495057;
            font-size: 0.9em;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-palette me-2"></i>Theme Management</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
                    <li class="breadcrumb-item active">Themes</li>
                </ol>
            </nav>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-palette fa-2x mb-2"></i>
                        <h3><?= $themeStats['total'] ?? 0 ?></h3>
                        <p class="mb-0">Total Themes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h3><?= $themeStats['active'] ?? 0 ?></h3>
                        <p class="mb-0">Active Themes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-download fa-2x mb-2"></i>
                        <h3><?= $themeStats['installed'] ?? 0 ?></h3>
                        <p class="mb-0">Installed Themes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="fas fa-heart fa-2x mb-2"></i>
                        <h3><?= $themeStats['custom'] ?? 0 ?></h3>
                        <p class="mb-0">Custom Themes</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-upload me-2"></i>Upload New Theme</h5>
            </div>
            <div class="card-body">
                <form id="themeUploadForm" enctype="multipart/form-data">
                    <div class="upload-zone" id="uploadZone">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                        <h5>Drop theme ZIP file here or click to browse</h5>
                        <p class="text-muted">Supports ZIP files up to 10MB</p>
                        <input type="file" id="themeFile" name="theme_file" accept=".zip" style="display: none;">
                        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('themeFile').click()">
                            <i class="fas fa-folder-open me-1"></i>Choose File
                        </button>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary" id="uploadBtn">
                            <i class="fas fa-upload me-1"></i>Upload Theme
                        </button>
                        <div class="loading-spinner d-inline-block ms-2" id="uploadSpinner">
                            <span class="spinner-border spinner-border-sm"></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Themes Grid -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-th-large me-2"></i>Installed Themes</h5>
                <button class="btn btn-outline-secondary btn-sm" onclick="refreshThemes()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
            </div>
            <div class="card-body">
                <?php if (empty($themes)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-palette fa-3x text-muted mb-3"></i>
                        <h5>No themes installed</h5>
                        <p class="text-muted">Upload a theme to get started</p>
                    </div>
                <?php else: ?>
                    <div class="row" id="themesGrid">
                        <?php foreach ($themes as $theme): ?>
                            <div class="col-md-4 col-lg-3 mb-4">
                                <div class="card theme-card <?= $theme['is_active'] ? 'active' : '' ?>" data-theme="<?= htmlspecialchars($theme['slug']) ?>">
                                    <div class="theme-screenshot" 
                                         style="background: linear-gradient(135deg, <?= htmlspecialchars($theme['colors']['primary'] ?? '#667eea') ?> 0%, <?= htmlspecialchars($theme['colors']['secondary'] ?? '#764ba2') ?> 100%);">
                                        <div class="theme-status">
                                            <?php if ($theme['is_active']): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>Active
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-pause me-1"></i>Inactive
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="theme-info">
                                        <div class="theme-name"><?= htmlspecialchars($theme['name']) ?></div>
                                        <div class="theme-author">by <?= htmlspecialchars($theme['author'] ?? 'Unknown') ?></div>
                                        <div class="theme-description"><?= htmlspecialchars($theme['description'] ?? 'No description available') ?></div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <small class="text-muted">v<?= htmlspecialchars($theme['version'] ?? '1.0.0') ?></small>
                                            <small class="text-muted"><?= htmlspecialchars($theme['type'] ?? 'custom') ?></small>
                                        </div>
                                        <div class="btn-group-custom">
                                            <?php if (!$theme['is_active']): ?>
                                                <button class="btn btn-success btn-sm" 
                                                        onclick="activateTheme('<?= htmlspecialchars($theme['slug']) ?>')"
                                                        <?= $theme['is_core'] ? 'disabled' : '' ?>>
                                                    <i class="fas fa-play me-1"></i>Activate
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn btn-info btn-sm" 
                                                    onclick="viewThemeDetails('<?= htmlspecialchars($theme['slug']) ?>')">
                                                <i class="fas fa-eye me-1"></i>Details
                                            </button>
                                            <?php if (!$theme['is_core']): ?>
                                                <button class="btn btn-danger btn-sm" 
                                                        onclick="deleteTheme('<?= htmlspecialchars($theme['slug']) ?>')">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Theme Details Modal -->
    <div class="modal fade" id="themeDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Theme Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="themeDetailsContent">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Upload functionality
        const uploadZone = document.getElementById('uploadZone');
        const themeFile = document.getElementById('themeFile');

        uploadZone.addEventListener('click', () => themeFile.click());
        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.classList.add('dragover');
        });
        uploadZone.addEventListener('dragleave', () => {
            uploadZone.classList.remove('dragover');
        });
        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                themeFile.files = files;
            }
        });

        // Upload form submission
        document.getElementById('themeUploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const uploadBtn = document.getElementById('uploadBtn');
            const uploadSpinner = document.getElementById('uploadSpinner');
            
            uploadBtn.disabled = true;
            uploadSpinner.style.display = 'inline-block';
            
            fetch('/admin/themes/upload', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Theme uploaded successfully!');
                    location.reload();
                } else {
                    alert('Upload failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Upload failed. Please try again.');
            })
            .finally(() => {
                uploadBtn.disabled = false;
                uploadSpinner.style.display = 'none';
            });
        });

        // Theme management functions
        function activateTheme(slug) {
            if (confirm('Are you sure you want to activate this theme?')) {
                fetch(`/admin/themes/activate/${slug}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Theme activated successfully!');
                        location.reload();
                    } else {
                        alert('Activation failed: ' + data.message);
                    }
                });
            }
        }

        function deleteTheme(slug) {
            if (confirm('Are you sure you want to delete this theme? This action cannot be undone.')) {
                fetch(`/admin/themes/delete/${slug}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Theme deleted successfully!');
                        location.reload();
                    } else {
                        alert('Deletion failed: ' + data.message);
                    }
                });
            }
        }

        function viewThemeDetails(slug) {
            fetch(`/admin/themes/details/${slug}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('themeDetailsContent').innerHTML = html;
                new bootstrap.Modal(document.getElementById('themeDetailsModal')).show();
            });
        }

        function refreshThemes() {
            location.reload();
        }
    </script>
</body>
</html>
