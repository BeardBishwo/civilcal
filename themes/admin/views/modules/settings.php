<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title">Module Settings: <?= htmlspecialchars($moduleName) ?></h1>
                <p class="text-muted">Configure settings for this module.</p>
            </div>
            <a href="<?= app_base_url('/admin/modules') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Modules
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="<?= app_base_url('/admin/modules/settings/update') ?>" method="post">
                    <input type="hidden" name="module" value="<?= htmlspecialchars($moduleName) ?>">

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Settings configuration for this module is not yet implemented.
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" disabled>
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>