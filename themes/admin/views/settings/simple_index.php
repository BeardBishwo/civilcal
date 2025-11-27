<div class="page-header">
    <h1 class="page-title"><i class="fas fa-cog"></i> Settings Management</h1>
    <p class="page-description">Configure all aspects of your application from this centralized dashboard</p>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon primary">
                <i class="fas fa-sliders-h"></i>
            </div>
        </div>
        <div class="stat-value">12</div>
        <div class="stat-label">Setting Groups</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-value">142</div>
        <div class="stat-label">Active Settings</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <div class="stat-icon warning">
                <i class="fas fa-sync-alt"></i>
            </div>
        </div>
        <div class="stat-value">24</div>
        <div class="stat-label">Recently Updated</div>
    </div>
</div>

<!-- Settings Navigation -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-th-list"></i>
            Settings Categories
        </h5>
    </div>
    
    <div class="card-content">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-home fa-2x text-primary"></i>
                        </div>
                        <h5 class="card-title">General</h5>
                        <p class="card-text text-muted">Basic site configuration</p>
                        <a href="<?php echo app_base_url('admin/settings/general'); ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-cog"></i> Configure
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-palette fa-2x text-success"></i>
                        </div>
                        <h5 class="card-title">Appearance</h5>
                        <p class="card-text text-muted">Theme and styling options</p>
                        <a href="<?php echo app_base_url('admin/settings/appearance'); ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-cog"></i> Configure
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-shield-alt fa-2x text-warning"></i>
                        </div>
                        <h5 class="card-title">Security</h5>
                        <p class="card-text text-muted">Protection and authentication</p>
                        <a href="<?php echo app_base_url('admin/settings/security'); ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-cog"></i> Configure
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>