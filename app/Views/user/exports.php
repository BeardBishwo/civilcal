<?php 
// Get flash messages
$success = isset($_SESSION['flash_messages']['success']) ? $_SESSION['flash_messages']['success'] : '';
$error = isset($_SESSION['flash_messages']['error']) ? $_SESSION['flash_messages']['error'] : '';

// Clear flash messages
if (isset($_SESSION['flash_messages'])) {
    unset($_SESSION['flash_messages']);
}

$pageTitle = $pageTitle ?? 'Export Templates';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Bishwo Calculator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/public/assets/css/exports.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="page-title">
                        <i class="fas fa-file-export me-2"></i><?= htmlspecialchars($pageTitle) ?>
                    </h1>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTemplateModal">
                            <i class="fas fa-plus me-2"></i>Create Template
                        </button>
                        <a href="/user/history" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to History
                        </a>
                    </div>
                </div>
                
                <!-- Flash Messages -->
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-file-alt fa-2x text-primary mb-2"></i>
                                <h5 class="card-title">Total Templates</h5>
                                <h3 class="text-primary"><?= $stats['total'] ?? 0 ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-user fa-2x text-success mb-2"></i>
                                <h5 class="card-title">User Templates</h5>
                                <h3 class="text-success"><?= $stats['user_templates'] ?? 0 ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-cog fa-2x text-warning mb-2"></i>
                                <h5 class="card-title">Default Templates</h5>
                                <h3 class="text-warning"><?= $stats['default'] ?? 0 ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-globe fa-2x text-info mb-2"></i>
                                <h5 class="card-title">Public Templates</h5>
                                <h3 class="text-info"><?= $stats['public'] ?? 0 ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Templates -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-user-cog me-2"></i>My Templates
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($user_templates)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No custom templates yet</p>
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTemplateModal">
                                            Create Your First Template
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <div class="template-list">
                                        <?php foreach ($user_templates as $template): ?>
                                            <div class="template-item border rounded p-3 mb-3" data-template-id="<?= $template['id'] ?>">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">
                                                            <?php 
                                                            $formatIcons = [
                                                                'pdf' => 'fas fa-file-pdf text-danger',
                                                                'excel' => 'fas fa-file-excel text-success',
                                                                'csv' => 'fas fa-file-csv text-info',
                                                                'json' => 'fas fa-file-code text-warning'
                                                            ];
                                                            ?>
                                                            <i class="<?= $formatIcons[$template['template_type']] ?? 'fas fa-file' ?> me-2"></i>
                                                            <?= htmlspecialchars($template['template_name']) ?>
                                                        </h6>
                                                        <p class="text-muted small mb-2">
                                                            Type: <?= strtoupper($template['template_type']) ?> | 
                                                            Created: <?= date('M j, Y', strtotime($template['created_at'])) ?>
                                                        </p>
                                                        <?php if ($template['is_public']): ?>
                                                            <span class="badge bg-info">Public</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="template-actions">
                                                        <button class="btn btn-sm btn-outline-primary me-1 edit-template-btn" 
                                                                data-template-id="<?= $template['id'] ?>" 
                                                                title="Edit Template">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-success me-1 duplicate-template-btn" 
                                                                data-template-id="<?= $template['id'] ?>" 
                                                                title="Duplicate Template">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger delete-template-btn" 
                                                                data-template-id="<?= $template['id'] ?>" 
                                                                title="Delete Template">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Default Templates -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-star me-2"></i>System Templates
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($default_templates)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-cog fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No system templates available</p>
                                    </div>
                                <?php else: ?>
                                    <div class="template-list">
                                        <?php foreach ($default_templates as $template): ?>
                                            <div class="template-item border rounded p-3 mb-3">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">
                                                            <i class="<?= $formatIcons[$template['template_type']] ?? 'fas fa-file' ?> me-2"></i>
                                                            <?= htmlspecialchars($template['template_name']) ?>
                                                        </h6>
                                                        <p class="text-muted small mb-2">
                                                            Type: <?= strtoupper($template['template_type']) ?> | 
                                                            Built-in System Template
                                                        </p>
                                                        <span class="badge bg-warning">Default</span>
                                                    </div>
                                                    <div class="template-actions">
                                                        <button class="btn btn-sm btn-outline-primary use-template-btn" 
                                                                data-template-id="<?= $template['id'] ?>" 
                                                                title="Use Template">
                                                            <i class="fas fa-download"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Template Modal -->
    <div class="modal fade" id="createTemplateModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>Create Export Template
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createTemplateForm" method="POST" action="/user/exports/create-template">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="template_name" class="form-label">Template Name *</label>
                                    <input type="text" class="form-control" id="template_name" name="template_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="template_type" class="form-label">Export Format *</label>
                                    <select class="form-select" id="template_type" name="template_type" required>
                                        <option value="">Select Format</option>
                                        <option value="pdf">PDF Report</option>
                                        <option value="excel">Excel Spreadsheet</option>
                                        <option value="csv">CSV Data</option>
                                        <option value="json">JSON Data</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="template_public" name="is_public">
                            <label class="form-check-label" for="template_public">
                                Make template public (share with other users)
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/assets/js/exports.js"></script>
</body>
</html>
