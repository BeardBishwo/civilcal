<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title">Settings</h1>
                <p class="text-muted">Manage application configuration.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="list-group mb-4">
            <a href="/admin/settings" class="list-group-item list-group-item-action active">
                <i class="fas fa-cogs mr-2"></i> General
            </a>
            <a href="/admin/settings/users" class="list-group-item list-group-item-action">
                <i class="fas fa-users-cog mr-2"></i> Users
            </a>
            <a href="/admin/settings/security" class="list-group-item list-group-item-action">
                <i class="fas fa-shield-alt mr-2"></i> Security
            </a>
            <a href="/admin/settings/email" class="list-group-item list-group-item-action">
                <i class="fas fa-envelope mr-2"></i> Email
            </a>
            <a href="/admin/settings/api" class="list-group-item list-group-item-action">
                <i class="fas fa-code mr-2"></i> API
            </a>
            <a href="/admin/settings/performance" class="list-group-item list-group-item-action">
                <i class="fas fa-tachometer-alt mr-2"></i> Performance
            </a>
            <a href="/admin/settings/advanced" class="list-group-item list-group-item-action">
                <i class="fas fa-tools mr-2"></i> Advanced
            </a>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">General Settings</h5>
            </div>
            <div class="card-body">
                <form action="/admin/settings/save" method="post" enctype="multipart/form-data">
                    <?php 
                    // Helper to get setting value safely
                    $getSetting = function($key, $default = '') use ($settingsByGroup) {
                        foreach ($settingsByGroup as $group => $settings) {
                            if (isset($settings[$key])) return $settings[$key];
                        }
                        return $default;
                    };
                    ?>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Site Name</label>
                        <input type="text" name="site_name" class="form-control" value="<?= htmlspecialchars($getSetting('site_name', 'Bishwo Calculator')) ?>">
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Site Description</label>
                        <textarea name="site_description" class="form-control" rows="3"><?= htmlspecialchars($getSetting('site_description', '')) ?></textarea>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Support Email</label>
                        <input type="email" name="support_email" class="form-control" value="<?= htmlspecialchars($getSetting('support_email', '')) ?>">
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">Footer Text</label>
                        <input type="text" name="footer_text" class="form-control" value="<?= htmlspecialchars($getSetting('footer_text', '')) ?>">
                    </div>
                    
                    <hr>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
