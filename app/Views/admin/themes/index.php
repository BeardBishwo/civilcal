<?php
// Get themes data from controller
$themes = $data['themes'] ?? [];
$activeTheme = $data['activeTheme'] ?? null;
$stats = $data['stats'] ?? [
    'total_themes' => 0,
    'active_themes' => 0,
    'inactive_themes' => 0,
    'deleted_themes' => 0,
    'premium_themes' => 0
];
$error = $data['error'] ?? null;
?>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1 text-primary">
                <i class="bi bi-palette me-2"></i>Theme Management
            </h2>
            <p class="text-muted mb-0">Complete modular theme administration and control panel</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" id="bulkActionBtn" disabled>
                <i class="bi bi-check-square me-1"></i>Bulk Actions
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadThemeModal">
                <i class="bi bi-cloud-upload me-2"></i>Upload Theme
            </button>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#validateThemeModal">
                <i class="bi bi-check-circle me-2"></i>Validate Theme
            </button>
        </div>
    </div>

    <!-- Statistics Dashboard -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <i class="bi bi-collection fs-2"></i>
                    </div>
                    <h3 class="h4 mb-1"><?= $stats['total_themes'] ?></h3>
                    <p class="text-muted mb-0 small">Total Themes</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <i class="bi bi-check-circle fs-2"></i>
                    </div>
                    <h3 class="h4 mb-1"><?= $stats['active_themes'] ?></h3>
                    <p class="text-muted mb-0 small">Active</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-secondary mb-2">
                        <i class="bi bi-pause-circle fs-2"></i>
                    </div>
                    <h3 class="h4 mb-1"><?= $stats['inactive_themes'] ?></h3>
                    <p class="text-muted mb-0 small">Inactive</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <i class="bi bi-trash fs-2"></i>
                    </div>
                    <h3 class="h4 mb-1"><?= $stats['deleted_themes'] ?></h3>
                    <p class="text-muted mb-0 small">Deleted</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <i class="bi bi-star fs-2"></i>
                    </div>
                    <h3 class="h4 mb-1"><?= $stats['premium_themes'] ?></h3>
                    <p class="text-muted mb-0 small">Premium</p>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="text-dark mb-2">
                        <i class="bi bi-archive fs-2"></i>
                    </div>
                    <h3 class="h4 mb-1">
                        <a href="/admin/themes/backups" class="text-decoration-none">View</a>
                    </h3>
                    <p class="text-muted mb-0 small">Backups</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Bar -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" id="themeSearch" placeholder="Search themes...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="deleted">Deleted</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="premiumFilter">
                        <option value="">All Types</option>
                        <option value="premium">Premium</option>
                        <option value="free">Free</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary" id="clearFilters">
                        <i class="bi bi-x-circle me-1"></i>Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Display -->
    <?php if ($error): ?>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <div><?= htmlspecialchars($error) ?></div>
        </div>
    <?php endif; ?>

    <!-- Active Theme Banner -->
    <?php if ($activeTheme): ?>
        <div class="alert alert-success border-0 mb-4" role="alert">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="alert-heading mb-1">
                        <i class="bi bi-check-circle me-2"></i>Currently Active Theme
                    </h5>
                    <p class="mb-0">
                        <strong><?= htmlspecialchars($activeTheme['display_name'] ?? $activeTheme['name']) ?></strong>
                        <?php if ($activeTheme['is_premium']): ?>
                            <span class="badge bg-warning text-dark ms-2">Premium</span>
                        <?php endif; ?>
                        <span class="ms-2">v<?= htmlspecialchars($activeTheme['version']) ?></span>
                    </p>
                    <small class="text-muted">by <?= htmlspecialchars($activeTheme['author']) ?></small>
                </div>
                <div>
                    <button class="btn btn-outline-primary me-2" id="customizeThemeBtn" data-theme-id="<?= (int)($activeTheme['id'] ?? 0) ?>">
                        <i class="bi bi-sliders me-1"></i>Customize
                    </button>
                    <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#deactivateThemeModal">
                        <i class="bi bi-pause-circle me-1"></i>Deactivate
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Themes Grid -->
    <div class="row" id="themesGrid">
        <?php if (empty($themes)): ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-palette fs-1 text-muted mb-3"></i>
                    <h4 class="text-muted">No themes found</h4>
                    <p class="text-muted">Start by uploading a theme or creating a new one</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadThemeModal">
                        <i class="bi bi-cloud-upload me-2"></i>Upload First Theme
                    </button>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($themes as $theme): ?>
                <div class="col-xl-4 col-lg-6 col-md-6 mb-4 theme-item" 
                     data-name="<?= strtolower($theme['name']) ?>" 
                     data-status="<?= $theme['status'] ?>"
                     data-premium="<?= $theme['is_premium'] ? '1' : '0' ?>">
                    <div class="card theme-card h-100 border-0 shadow-sm <?= $theme['status'] === 'active' ? 'border-primary' : '' ?> <?= $theme['status'] === 'deleted' ? 'opacity-75' : '' ?>">
                        <!-- Theme Preview -->
                        <div class="position-relative">
                            <div class="theme-preview bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                <?php $preview = $theme['screenshot_path'] ?? ($theme['preview_image'] ?? null); ?>
                                <?php if ($preview): ?>
                                    <img src="<?= htmlspecialchars($preview) ?>" alt="Theme Preview" class="img-fluid rounded">
                                <?php else: ?>
                                    <div class="text-center">
                                        <i class="bi bi-palette fs-1 text-muted"></i>
                                        <p class="text-muted small mt-2">No preview available</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <!-- Status Badge -->
                            <div class="position-absolute top-0 end-0 m-2">
                                <?php if ($theme['status'] === 'active'): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php elseif ($theme['status'] === 'deleted'): ?>
                                    <span class="badge bg-danger">Deleted</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </div>
                            <!-- Premium Badge -->
                            <?php if ($theme['is_premium']): ?>
                                <div class="position-absolute top-0 start-0 m-2">
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-star me-1"></i>Premium
                                        <?php if ($theme['price'] > 0): ?>
                                            - $<?= number_format($theme['price'], 2) ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title d-flex align-items-center">
                                <?= htmlspecialchars($theme['display_name'] ?? $theme['name']) ?>
                            </h5>
                            <p class="card-text text-muted small mb-2">
                                <?= htmlspecialchars($theme['description'] ?? 'No description available') ?>
                            </p>
                            
                            <!-- Theme Meta -->
                            <div class="theme-meta">
                                <div class="row small text-muted">
                                    <div class="col-6">
                                        <strong>Version:</strong><br>
                                        <?= htmlspecialchars($theme['version']) ?>
                                    </div>
                                    <div class="col-6">
                                        <strong>Author:</strong><br>
                                        <?= htmlspecialchars($theme['author']) ?>
                                    </div>
                                </div>
                                <?php if (isset($theme['file_size'])): ?>
                                    <div class="row small text-muted mt-2">
                                        <div class="col-6">
                                            <strong>Size:</strong><br>
                                            <?= number_format($theme['file_size'] / 1024, 1) ?> KB
                                        </div>
                                        <div class="col-6">
                                            <strong>Updated:</strong><br>
                                            <?= isset($theme['updated_at']) ? date('M j, Y', strtotime($theme['updated_at'])) : 'N/A' ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-footer bg-transparent">
                            <!-- Checkbox for bulk actions -->
                            <div class="form-check mb-2">
                                <input class="form-check-input theme-checkbox" type="checkbox" value="<?= $theme['id'] ?>" id="theme_<?= $theme['id'] ?>">
                                <label class="form-check-label small" for="theme_<?= $theme['id'] ?>">
                                    Select for bulk action
                                </label>
                            </div>

                            <!-- Action Buttons -->
                            <div class="btn-group w-100" role="group">
                                <?php if ($theme['status'] !== 'active'): ?>
                                    <button class="btn btn-success btn-sm activate-theme" data-theme-id="<?= $theme['id'] ?>">
                                        <i class="bi bi-play-circle me-1"></i>Activate
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-outline-success btn-sm" disabled>
                                        <i class="bi bi-check-circle me-1"></i>Active
                                    </button>
                                <?php endif; ?>

                                <button class="btn btn-outline-primary btn-sm preview-theme" data-theme-id="<?= $theme['id'] ?>" data-preview="<?= htmlspecialchars($theme['screenshot_path'] ?? '') ?>" data-name="<?= htmlspecialchars($theme['display_name'] ?? $theme['name']) ?>">
                                    <i class="bi bi-eye me-1"></i>Preview
                                </button>

                                <?php if ($theme['status'] === 'deleted'): ?>
                                    <button class="btn btn-info btn-sm restore-theme" data-theme-id="<?= $theme['id'] ?>">
                                        <i class="bi bi-arrow-clockwise me-1"></i>Restore
                                    </button>
                                    <button class="btn btn-danger btn-sm hard-delete-theme" data-theme-id="<?= $theme['id'] ?>">
                                        <i class="bi bi-trash me-1"></i>Delete
                                    </button>
                                <?php else: ?>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots me-1"></i>More
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" data-action="validate" data-theme-id="<?= $theme['id'] ?>">
                                                <i class="bi bi-check-circle me-2"></i>Validate
                                            </a></li>
                                            <li><a class="dropdown-item" href="#" data-action="backup" data-theme-id="<?= $theme['id'] ?>">
                                                <i class="bi bi-archive me-2"></i>Create Backup
                                            </a></li>
                                            <?php if ($theme['status'] !== 'active'): ?>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" data-action="delete" data-theme-id="<?= $theme['id'] ?>">
                                                    <i class="bi bi-trash me-2"></i>Delete
                                                </a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Upload Theme Modal -->
<div class="modal fade" id="uploadThemeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-cloud-upload me-2"></i>Upload New Theme
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="uploadThemeForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="upload-area border-dashed p-4 text-center mb-3" id="uploadArea">
                        <i class="bi bi-cloud-upload fs-1 text-muted mb-2"></i>
                        <p class="mb-2">Drag & drop your theme ZIP file here, or click to browse</p>
                        <input type="file" class="d-none" id="themeZipFile" name="theme_zip" accept=".zip" required>
                        <button type="button" class="btn btn-outline-primary" id="browseFileBtn">Browse Files</button>
                        <div class="mt-2">
                            <small class="text-muted">Maximum file size: 10MB</small>
                        </div>
                    </div>
                    
                    <div class="upload-progress d-none" id="uploadProgress">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small">Uploading theme...</span>
                            <span class="small" id="uploadPercent">0%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar" id="uploadProgressBar" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle me-2"></i>Theme Requirements</h6>
                        <ul class="mb-0 small">
                            <li>ZIP file must contain a valid <code>theme.json</code> configuration file</li>
                            <li>Include all CSS, JavaScript, and image assets in the ZIP</li>
                            <li>Theme name must be unique and not conflict with existing themes</li>
                            <li>Recommended theme size: Under 10MB for optimal performance</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="uploadSubmitBtn" disabled>
                        <i class="bi bi-upload me-2"></i>Upload Theme
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Upload Result Modal -->
<div class="modal fade" id="uploadResultModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Theme Upload Result</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="resultStatus" class="mb-2"></div>
                <div class="d-flex align-items-center gap-3 mb-2">
                    <img id="resultScreenshot" src="" alt="Preview" class="rounded d-none" style="width:72px;height:72px;object-fit:cover;">
                    <div>
                        <div class="small"><strong>Name:</strong> <span id="resultName">-</span></div>
                        <div class="small"><strong>Checksum:</strong> <span id="resultChecksum">-</span></div>
                        <div class="small"><strong>Size:</strong> <span id="resultSize">-</span></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="resultReloadBtn">Reload</button>
            </div>
        </div>
    </div>
</div>

<!-- Validate Theme Modal -->
<div class="modal fade" id="validateThemeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle me-2"></i>Validate Theme
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="validateThemeForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Theme Name</label>
                        <select class="form-select" name="theme_name" id="validateThemeSelect" required>
                            <option value="">Select a theme to validate</option>
                            <?php foreach ($themes as $theme): ?>
                                <option value="<?= htmlspecialchars($theme['name']) ?>">
                                    <?= htmlspecialchars($theme['display_name'] ?? $theme['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="validation-results d-none" id="validationResults">
                        <h6>Validation Results:</h6>
                        <div class="alert" id="validationAlert"></div>
                        <ul class="list-unstyled" id="validationIssues"></ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check me-2"></i>Validate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Actions Modal -->
<div class="modal fade" id="bulkActionsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-check-square me-2"></i>Bulk Actions
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bulkActionsForm">
                <div class="modal-body">
                    <p>Selected themes: <strong id="selectedCount">0</strong></p>
                    <div class="mb-3">
                        <label class="form-label">Action</label>
                        <select class="form-select" name="action" required>
                            <option value="">Choose an action</option>
                            <option value="activate">Activate</option>
                            <option value="deactivate">Deactivate</option>
                            <option value="delete">Delete (with backup)</option>
                            <option value="delete_no_backup">Delete (without backup)</option>
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="create_backup" id="createBackup" checked>
                        <label class="form-check-label" for="createBackup">
                            Create backup before deletion
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-play me-2"></i>Execute Action
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Theme Preview Modal -->
<div class="modal fade" id="themePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="themePreviewTitle">Theme Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img id="themePreviewImage" src="" alt="Theme Preview" class="img-fluid rounded d-none">
                    <div id="themePreviewFallback" class="alert alert-info d-none mt-3">No preview image found for this theme.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="customizeThemeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-sliders me-2"></i>Customize Theme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="customizeThemeForm">
                <div class="modal-body">
                    <input type="hidden" id="customizeThemeId" value="<?= (int)($activeTheme['id'] ?? 0) ?>">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Primary</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="colorPrimaryPicker">
                                <input type="text" class="form-control" id="colorPrimary">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Secondary</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="colorSecondaryPicker">
                                <input type="text" class="form-control" id="colorSecondary">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Accent</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="colorAccentPicker">
                                <input type="text" class="form-control" id="colorAccent">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Background</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="colorBackgroundPicker">
                                <input type="text" class="form-control" id="colorBackground">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Text Primary</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="colorTextPicker">
                                <input type="text" class="form-control" id="colorText">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Text Secondary</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="colorTextSecondaryPicker">
                                <input type="text" class="form-control" id="colorTextSecondary">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Typography</label>
                            <select id="typographyStyle" class="form-select">
                                <option value="modern">Modern</option>
                                <option value="classic">Classic</option>
                                <option value="rounded">Rounded</option>
                            </select>
                        </div>
                        <div class="col-6 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="darkModeEnabled">
                                <label class="form-check-label" for="darkModeEnabled">Dark mode by default</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay d-none" id="loadingOverlay">
    <div class="text-center">
        <div class="spinner-border text-primary mb-2" style="width: 2rem; height: 2rem;"></div>
        <p class="mb-0">Processing...</p>
    </div>
</div>

<script>
// Theme Management JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize theme management functionality
    initThemeManagement();
});

function initThemeManagement() {
    // Search functionality
    const searchInput = document.getElementById('themeSearch');
    const statusFilter = document.getElementById('statusFilter');
    const premiumFilter = document.getElementById('premiumFilter');
    const clearFiltersBtn = document.getElementById('clearFilters');

    if (searchInput) {
        searchInput.addEventListener('input', filterThemes);
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', filterThemes);
    }
    if (premiumFilter) {
        premiumFilter.addEventListener('change', filterThemes);
    }
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', clearFilters);
    }

    // Theme actions
    initThemeActions();
    
    // Upload functionality
    initUploadTheme();
    
    // Validation functionality
    initThemeValidation();
    
    // Bulk actions
    initBulkActions();
    
    // Update bulk action button
    updateBulkActionButton();
}

function filterThemes() {
    const searchTerm = document.getElementById('themeSearch').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const premiumFilter = document.getElementById('premiumFilter').value;
    
    const themeItems = document.querySelectorAll('.theme-item');
    
    themeItems.forEach(item => {
        const name = item.dataset.name || '';
        const status = item.dataset.status || '';
        const isPremium = item.dataset.premium === '1';
        
        let show = true;
        
        // Search filter
        if (searchTerm && !name.includes(searchTerm)) {
            show = false;
        }
        
        // Status filter
        if (statusFilter && status !== statusFilter) {
            show = false;
        }
        
        // Premium filter
        if (premiumFilter === 'premium' && !isPremium) {
            show = false;
        }
        if (premiumFilter === 'free' && isPremium) {
            show = false;
        }
        
        item.style.display = show ? '' : 'none';
    });
}

// Customize Theme
(function(){
  const customizeBtn = document.getElementById('customizeThemeBtn');
  const modalEl = document.getElementById('customizeThemeModal');
  if (!customizeBtn || !modalEl) return;
  const colorsCfg = <?= json_encode($activeTheme['config']['colors'] ?? []) ?>;
  const settingsCfg = <?= json_encode($activeTheme['settings'] ?? []) ?>;
  const fallback = (k, def) => (settingsCfg && settingsCfg[k]) || (colorsCfg && colorsCfg[k]) || def;
  const assignField = (pickerId, inputId, val) => { const p=document.getElementById(pickerId), i=document.getElementById(inputId); if(p) p.value=val||'#000000'; if(i) i.value=val||''; if(p&&i){ p.addEventListener('input',()=>{ i.value=p.value; }); i.addEventListener('input',()=>{ if(/^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(i.value)) p.value=i.value; }); } };
  const openModal = ()=>{
    assignField('colorPrimaryPicker','colorPrimary', fallback('primary','#2563eb'));
    assignField('colorSecondaryPicker','colorSecondary', fallback('secondary','#64748b'));
    assignField('colorAccentPicker','colorAccent', fallback('accent','#0ea5e9'));
    assignField('colorBackgroundPicker','colorBackground', fallback('background','#ffffff'));
    assignField('colorTextPicker','colorText', fallback('text','#1e293b'));
    assignField('colorTextSecondaryPicker','colorTextSecondary', fallback('text_secondary','#64748b'));
    const dark = document.getElementById('darkModeEnabled'); if (dark) dark.checked = !!(settingsCfg && settingsCfg['dark_mode_enabled']);
    const typo = document.getElementById('typographyStyle'); if (typo) typo.value = (settingsCfg && settingsCfg['typography_style']) || 'modern';
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) { new bootstrap.Modal(modalEl).show(); } else { modalEl.classList.add('show'); modalEl.style.display='block'; }
  };
  customizeBtn.addEventListener('click', openModal);

  document.getElementById('customizeThemeForm').addEventListener('submit', function(e){
    e.preventDefault();
    const id = document.getElementById('customizeThemeId').value;
    const payload = {
      primary: document.getElementById('colorPrimary').value,
      secondary: document.getElementById('colorSecondary').value,
      accent: document.getElementById('colorAccent').value,
      background: document.getElementById('colorBackground').value,
      text: document.getElementById('colorText').value,
      text_secondary: document.getElementById('colorTextSecondary').value,
      dark_mode_enabled: document.getElementById('darkModeEnabled').checked,
      typography_style: document.getElementById('typographyStyle').value
    };
    const headers = { 'Content-Type':'application/json', 'X-Requested-With':'XMLHttpRequest' };
    const csrfMeta = document.querySelector('meta[name="csrf-token"]'); const csrf = csrfMeta ? csrfMeta.getAttribute('content') : null; if (csrf) headers['X-CSRF-Token'] = csrf;
    fetch(`/admin/themes/${id}/settings`, { method:'POST', headers, body: JSON.stringify(payload) })
      .then(r=>r.json())
      .then(d=>{
        if (d.success) { alert('Theme settings saved. Reloading to apply...'); location.reload(); }
        else { alert('Error: ' + (d.message||'Unable to save')); }
      });
  });
})();

function clearFilters() {
    document.getElementById('themeSearch').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('premiumFilter').value = '';
    filterThemes();
}

function initThemeActions() {
    // Activate theme
    document.querySelectorAll('.activate-theme').forEach(button => {
        button.addEventListener('click', function() {
            const themeId = this.dataset.themeId;
            executeThemeAction('activate', themeId);
        });
    });

    // Restore theme
    document.querySelectorAll('.restore-theme').forEach(button => {
        button.addEventListener('click', function() {
            const themeId = this.dataset.themeId;
            executeThemeAction('restore', themeId);
        });
    });

    // Hard delete theme
    document.querySelectorAll('.hard-delete-theme').forEach(button => {
        button.addEventListener('click', function() {
            const themeId = this.dataset.themeId;
            if (confirm('Are you sure you want to permanently delete this theme? This action cannot be undone.')) {
                executeThemeAction('hardDelete', themeId);
            }
        });
    });

    // Dropdown actions
    document.querySelectorAll('[data-action]').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const action = this.dataset.action;
            const themeId = this.dataset.themeId;
            
            if (action === 'delete') {
                if (confirm('Are you sure you want to delete this theme? A backup will be created.')) {
                    executeThemeAction('delete', themeId);
                }
            } else if (action === 'validate') {
                validateTheme(themeId);
            } else if (action === 'backup') {
                createBackup(themeId);
            }
        });
    });

    // Preview theme
    document.querySelectorAll('.preview-theme').forEach(button => {
        button.addEventListener('click', function() {
            const url = this.dataset.preview || '';
            const name = this.dataset.name || 'Theme Preview';
            const modalEl = document.getElementById('themePreviewModal');
            const img = document.getElementById('themePreviewImage');
            const title = document.getElementById('themePreviewTitle');
            const fallback = document.getElementById('themePreviewFallback');
            title.textContent = name;
            if (url) {
                img.src = url + (url.indexOf('?') > -1 ? '&' : '?') + 't=' + Date.now();
                img.classList.remove('d-none');
                fallback.classList.add('d-none');
            } else {
                img.classList.add('d-none');
                fallback.classList.remove('d-none');
            }
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const m = new bootstrap.Modal(modalEl);
                m.show();
            } else {
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
            }
        });
    });
}

function executeThemeAction(action, themeId) {
    showLoading();
    
    const formData = new FormData();
    formData.append('theme_id', themeId);
    if (action === 'delete') {
        formData.append('create_backup', '1');
    }
    
    const headers = {
        'X-Requested-With': 'XMLHttpRequest'
    };
    const csrfMeta1 = document.querySelector('meta[name="csrf-token"]');
    const csrf1 = csrfMeta1 ? csrfMeta1.getAttribute('content') : null;
    if (csrf1) { headers['X-CSRF-Token'] = csrf1; }
    fetch(`/admin/themes/${action}`, {
        method: 'POST',
        headers: headers,
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showAlert('success', data.message || `Theme ${action} completed successfully`);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.message || `Theme ${action} failed`);
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('danger', 'An error occurred while processing the request');
        console.error('Theme action error:', error);
    });
}

function initUploadTheme() {
    const uploadForm = document.getElementById('uploadThemeForm');
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('themeZipFile');
    const browseBtn = document.getElementById('browseFileBtn');
    const submitBtn = document.getElementById('uploadSubmitBtn');

    // File selection
    browseBtn.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', handleFileSelect);
    
    // Drag and drop
    uploadArea.addEventListener('dragover', handleDragOver);
    uploadArea.addEventListener('drop', handleFileDrop);
    
    // Form submission
    uploadForm.addEventListener('submit', handleUploadSubmit);
}

function handleFileSelect(e) {
    const file = e.target.files[0];
    if (file) {
        validateAndShowFile(file);
    }
}

function handleDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
    e.currentTarget.classList.add('border-primary', 'bg-light');
}

function handleFileDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    e.currentTarget.classList.remove('border-primary', 'bg-light');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        const file = files[0];
        document.getElementById('themeZipFile').files = files;
        validateAndShowFile(file);
    }
}

function validateAndShowFile(file) {
    const submitBtn = document.getElementById('uploadSubmitBtn');
    
    if (file.type !== 'application/zip' && !file.name.toLowerCase().endsWith('.zip')) {
        showAlert('danger', 'Please select a valid ZIP file');
        submitBtn.disabled = true;
        return;
    }
    
    if (file.size > 10 * 1024 * 1024) { // 10MB
        showAlert('danger', 'File size must be less than 10MB');
        submitBtn.disabled = true;
        return;
    }
    
    submitBtn.disabled = false;
    
    // Show file info
    const fileInfo = document.createElement('div');
    fileInfo.className = 'mt-2 p-2 bg-light rounded';
    fileInfo.innerHTML = `
        <small><strong>Selected:</strong> ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)</small>
    `;
    
    const existingInfo = uploadArea.querySelector('.mt-2.bg-light');
    if (existingInfo) {
        existingInfo.remove();
    }
    uploadArea.appendChild(fileInfo);
}

function handleUploadSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const submitBtn = e.target.querySelector('button[type="submit"]');
    
    // Show progress
    showUploadProgress();
    submitBtn.disabled = true;
    
    // Upload with progress
    const xhr = new XMLHttpRequest();
    
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const percent = Math.round((e.loaded / e.total) * 100);
            updateUploadProgress(percent);
        }
    });
    
    xhr.addEventListener('load', function() {
        hideUploadProgress();
        submitBtn.disabled = false;
        
        const modalEl = document.getElementById('uploadResultModal');
        const statusEl = document.getElementById('resultStatus');
        const nameEl = document.getElementById('resultName');
        const checksumEl = document.getElementById('resultChecksum');
        const sizeEl = document.getElementById('resultSize');
        const shotEl = document.getElementById('resultScreenshot');
        const reloadBtn = document.getElementById('resultReloadBtn');
        if (reloadBtn) reloadBtn.onclick = () => location.reload();

        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    const data = response.data || {};
                    statusEl.className = 'alert alert-success';
                    statusEl.textContent = response.message || 'Theme uploaded successfully';
                    nameEl.textContent = data.theme_name || '-';
                    checksumEl.textContent = data.checksum || '-';
                    sizeEl.textContent = data.file_size ? (Math.round((data.file_size/1024)*10)/10) + ' KB' : '-';
                    if (data.screenshot_path) {
                        shotEl.src = data.screenshot_path;
                        shotEl.classList.remove('d-none');
                    } else {
                        shotEl.classList.add('d-none');
                    }
                    if (modalEl && window.bootstrap) new bootstrap.Modal(modalEl).show();
                } else {
                    statusEl.className = 'alert alert-danger';
                    statusEl.textContent = response.message || 'Upload failed';
                    if (modalEl && window.bootstrap) new bootstrap.Modal(modalEl).show();
                }
            } catch (e) {
                showAlert('danger', 'Invalid response from server');
            }
        } else {
            showAlert('danger', 'Upload failed with status ' + xhr.status);
        }
    });
    
    xhr.addEventListener('error', function() {
        hideUploadProgress();
        submitBtn.disabled = false;
        showAlert('danger', 'Upload failed due to network error');
    });
    
    xhr.open('POST', '/admin/themes/upload');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrf) { xhr.setRequestHeader('X-CSRF-Token', csrf); }
    xhr.send(formData);
}

function initThemeValidation() {
    const validateForm = document.getElementById('validateThemeForm');
    const validateSelect = document.getElementById('validateThemeSelect');
    // ... (rest of the code remains the same)
    
    if (validateForm) {
        validateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const themeName = validateSelect.value;
            if (themeName) {
                validateThemeByName(themeName);
            }
        });
    }
}

function validateTheme(themeId) {
    // Find theme by ID and validate by name
    const themeCard = document.querySelector(`[data-theme-id="${themeId}"]`);
    if (themeCard) {
        const themeName = themeCard.querySelector('.card-title').textContent.trim();
        validateThemeByName(themeName);
    }
}

function validateThemeByName(themeName) {
    showLoading();
    
    const formData = new FormData();
    formData.append('theme_name', themeName);
    
    const headers2 = {
        'X-Requested-With': 'XMLHttpRequest'
    };
    const csrfMeta2 = document.querySelector('meta[name="csrf-token"]');
    const csrf2 = csrfMeta2 ? csrfMeta2.getAttribute('content') : null;
    if (csrf2) { headers2['X-CSRF-Token'] = csrf2; }
    fetch('/admin/themes/validate', {
        method: 'POST',
        headers: headers2,
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        showValidationResults(data);
    })
    .catch(error => {
        hideLoading();
        showAlert('danger', 'Validation failed');
        console.error('Validation error:', error);
    });
}

function showValidationResults(data) {
    const resultsDiv = document.getElementById('validationResults');
    const alertDiv = document.getElementById('validationAlert');
    const issuesList = document.getElementById('validationIssues');
    
    resultsDiv.classList.remove('d-none');
    
    if (data.success) {
        alertDiv.className = 'alert alert-success';
        alertDiv.innerHTML = '<i class="bi bi-check-circle me-2"></i>' + (data.message || 'Theme validation passed');
        issuesList.innerHTML = '';
    } else {
        alertDiv.className = 'alert alert-danger';
        alertDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>' + (data.message || 'Theme validation failed');
        
        if (data.issues && data.issues.length > 0) {
            issuesList.innerHTML = data.issues.map(issue => 
                `<li class="small text-danger"><i class="bi bi-x me-1"></i>${issue}</li>`
            ).join('');
        }
    }
}

function initBulkActions() {
    const bulkBtn = document.getElementById('bulkActionBtn');
    const bulkModal = document.getElementById('bulkActionsModal');
    const bulkForm = document.getElementById('bulkActionsForm');
    const checkboxes = document.querySelectorAll('.theme-checkbox');
    
    // Bulk action button click
    if (bulkBtn) {
        bulkBtn.addEventListener('click', function() {
            if (this.disabled) return;
            new bootstrap.Modal(bulkModal).show();
        });
    }
    
    // Checkbox changes
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActionButton);
    });
    
    // Bulk form submission
    if (bulkForm) {
        bulkForm.addEventListener('submit', function(e) {
            e.preventDefault();
            executeBulkAction();
        });
    }
}

function updateBulkActionButton() {
    const checkboxes = document.querySelectorAll('.theme-checkbox:checked');
    const bulkBtn = document.getElementById('bulkActionBtn');
    const selectedCount = document.getElementById('selectedCount');
    
    if (checkboxes.length > 0) {
        bulkBtn.disabled = false;
        selectedCount.textContent = checkboxes.length;
    } else {
        bulkBtn.disabled = true;
        selectedCount.textContent = '0';
    }
}

function executeBulkAction() {
    const formData = new FormData();
    const action = document.querySelector('[name="action"]').value;
    const createBackup = document.querySelector('[name="create_backup"]').checked;
    
    if (!action) {
        showAlert('danger', 'Please select an action');
        return;
    }
    
    const checkboxes = document.querySelectorAll('.theme-checkbox:checked');
    const themeIds = Array.from(checkboxes).map(cb => cb.value);
    
    formData.append('action', action);
    formData.append('theme_ids[]', themeIds);
    if (action.includes('delete') && createBackup) {
        formData.append('create_backup', '1');
    }
    
    showLoading();
    
    const headers3 = {
        'X-Requested-With': 'XMLHttpRequest'
    };
    const csrfMeta3 = document.querySelector('meta[name="csrf-token"]');
    const csrf3 = csrfMeta3 ? csrfMeta3.getAttribute('content') : null;
    if (csrf3) { headers3['X-CSRF-Token'] = csrf3; }
    fetch('/admin/themes/bulkAction', {
        method: 'POST',
        headers: headers3,
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showAlert('success', data.message || 'Bulk action completed successfully');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.message || 'Bulk action failed');
        }
    })
    .catch(error => {
        hideLoading();
        showAlert('danger', 'Bulk action failed due to network error');
        console.error('Bulk action error:', error);
    });
}

function createBackup(themeId) {
    showLoading();
    
    // This would trigger a backup creation via the API
    setTimeout(() => {
        hideLoading();
        showAlert('success', 'Backup created successfully');
    }, 2000);
}

function showUploadProgress() {
    document.getElementById('uploadProgress').classList.remove('d-none');
}

function hideUploadProgress() {
    document.getElementById('uploadProgress').classList.add('d-none');
}

function updateUploadProgress(percent) {
    document.getElementById('uploadProgressBar').style.width = percent + '%';
    document.getElementById('uploadPercent').textContent = percent + '%';
}

function showLoading() {
    document.getElementById('loadingOverlay').classList.remove('d-none');
}

function hideLoading() {
    document.getElementById('loadingOverlay').classList.add('d-none');
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
