
<?php
/**
 * General Settings Page - Modern UI/UX Design
 * Enhanced with beautiful card layout, visual feedback, and modern styling
 */
?>

<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-cog text-primary me-2"></i>
            General Settings
        </h1>
        <p class="page-description">Configure your website's basic settings and appearance</p>
    </div>

    <!-- Settings Form -->
    <div class="row">
        <div class="col-lg-8 col-xl-9">
            <!-- Site Configuration Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h3 class="card-title mb-0">
                <i class="fas fa-globe text-primary me-2"></i>
                Site Configuration
            </h3>
        </div>
        <div class="card-body">
            <form id="generalSettingsForm" action="<?php echo app_base_url('/admin/settings/save'); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                
                <!-- Site Identity Section -->
                <div class="mb-4">
                    <h5 class="text-primary mb-3">
                    <i class="fas fa-fingerprint me-2"></i>
                    Site Identity
                </h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="site_name" class="form-label fw-semibold">Site Name</label>
                    <input type="text" class="form-control form-control-lg" 
                           id="site_name" 
                           name="site_name" 
                           value="<?= htmlspecialchars($settings['site_name'] ?? 'Bishwo Calculator') ?>" 
                           placeholder="Enter your site name">
                    <div class="form-text text-muted">The name of your website as displayed in the browser title.</div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="site_description" class="form-label fw-semibold">Site Description</div>
                    <textarea class="form-control" 
                              id="site_description" 
                              name="site_description" 
                              rows="3" 
                              placeholder="Describe your website"><?= htmlspecialchars($settings['site_description'] ?? '') ?></textarea>
