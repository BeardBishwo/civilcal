<?php
/**
 * Import Questions View
 */
$pageTitle = $page_title ?? 'Import Questions';
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0"><?= htmlspecialchars($pageTitle) ?></h1>
            <p class="text-muted">Bulk import questions using CSV with multi-context support</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= app_base_url('docs/CSV_IMPORT_TEMPLATE.md') ?>" target="_blank" class="btn btn-outline-primary">
                <i class="bi bi-file-earmark-text"></i> View Template Docs
            </a>
            <a href="<?= app_base_url('admin/quiz/questions') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Questions
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['warning'])): ?>
        <div class="alert alert-warning alert-dismissible fade show">
            <?= $_SESSION['warning'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <?php unset($_SESSION['warning']); ?>
        </div>
    <?php endif; ?>

    <!-- Import Logic -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Upload CSV</h5>
                </div>
                <div class="card-body">
                    <form action="<?= app_base_url('admin/quiz/import/upload') ?>" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        
                        <div class="mb-4">
                            <label class="form-label">Select CSV File</label>
                            <input type="file" name="file" class="form-control" accept=".csv" required>
                            <small class="text-muted">Max file size: 5MB</small>
                        </div>

                        <div class="alert alert-info">
                            <h5><i class="bi bi-info-circle"></i> CSV Format Guide</h5>
                            <p class="mb-2 small">Your CSV must follow the standard template structure:</p>
                            <code class="d-block bg-light p-2 rounded mb-2 small">
                                Question Text, Option A, Option B, Option C, Option D, Correct Answer, Is Practical, Global Tags, Level Map Syntax, Explanation
                            </code>
                            <p class="mb-0 small"><strong>Level Map Syntax:</strong> <br> Use format <code>L4:Hard|L5:Medium</code> to map questions to levels.</p>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-cloud-upload"></i> Import Questions
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Import Errors -->
        <?php if (!empty($_SESSION['import_errors'])): ?>
        <div class="col-md-6">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Import Errors</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($_SESSION['import_errors'] as $error): ?>
                            <li class="list-group-item text-danger small">
                                <i class="bi bi-exclamation-circle me-2"></i> <?= htmlspecialchars($error) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="card-footer text-muted small">
                    Please correct these rows and upload again.
                </div>
            </div>
            <?php unset($_SESSION['import_errors']); ?>
        </div>
        <?php endif; ?>
    </div>
</div>
