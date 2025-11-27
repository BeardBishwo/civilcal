<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><i class="fas fa-exclamation-triangle"></i> Email Manager Error</h1>
            <p class="page-description">An error occurred while processing your request</p>
        </div>
        <div class="page-header-actions">
            <a href="<?php echo app_base_url('/admin/email-manager'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-content text-center py-12">
        <div class="text-red-500 mb-4">
            <i class="fas fa-exclamation-circle fa-3x"></i>
        </div>
        <h3 class="text-xl font-medium text-gray-900 mb-2">Something went wrong</h3>
        <p class="text-gray-600 mb-6">
            <?php echo htmlspecialchars($message ?? 'An unexpected error occurred.'); ?>
        </p>
        <div class="flex justify-center gap-3">
            <a href="<?php echo app_base_url('/admin/email-manager'); ?>" class="btn btn-primary">
                <i class="fas fa-home"></i> Return to Dashboard
            </a>
            <button onclick="window.location.reload()" class="btn btn-outline-secondary">
                <i class="fas fa-sync"></i> Try Again
            </button>
        </div>
    </div>
</div>