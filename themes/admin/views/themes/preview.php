<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title">Theme Preview</h1>
                <p class="text-muted">Previewing: <?= htmlspecialchars($activeTheme['name'] ?? 'Unknown') ?></p>
            </div>
            <a href="/admin/themes" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Themes
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body text-center p-5">
                <i class="fas fa-desktop fa-4x text-muted mb-3"></i>
                <h3>Preview Mode</h3>
                <p>This is a placeholder for the theme preview functionality.</p>
                <a href="/" target="_blank" class="btn btn-primary mt-3">View Live Site</a>
            </div>
        </div>
    </div>
</div>
