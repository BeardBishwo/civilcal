<div class="container-fluid p-0">
    <div class="row mb-2 mb-xl-3">
        <div class="col-auto d-none d-sm-block">
            <h3>Module Settings: <strong><?php echo htmlspecialchars($data['module']['name']); ?></strong></h3>
        </div>
        <div class="col-auto ms-auto text-end mt-n1">
            <a href="<?php echo get_app_url(); ?>/admin/modules" class="btn btn-secondary">Back to Modules</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Configuration</h5>
                </div>
                <div class="card-body">
                    <form id="moduleSettingsForm">
                        <input type="hidden" name="module" value="<?php echo htmlspecialchars($data['module']['name']); ?>">

                        <div class="mb-3">
                            <label class="form-label">Display Name</label>
                            <input type="text" class="form-control" name="settings[display_name]" value="<?php echo htmlspecialchars($data['module']['display_name'] ?? $data['module']['name']); ?>">
                            <div class="form-text">The name displayed to users in the frontend menu.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="settings[description]" rows="3"><?php echo htmlspecialchars($data['module']['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="settings[status]">
                                <option value="active" <?php echo ($data['module']['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($data['module']['status'] ?? '') !== 'active' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>

                        <div class="alert alert-warning">
                            Note: Detailed configuration options for this module are not yet implemented in the schema. These settings are for demonstration purposes.
                        </div>

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Module Info</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Version</dt>
                        <dd class="col-sm-8"><?php echo htmlspecialchars($data['module']['version'] ?? '1.0.0'); ?></dd>

                        <dt class="col-sm-4">Date Added</dt>
                        <dd class="col-sm-8"><?php echo htmlspecialchars($data['module']['created_at'] ?? date('Y-m-d')); ?></dd>

                        <dt class="col-sm-4">Calculators</dt>
                        <dd class="col-sm-8"><?php echo $module['calculators_count'] ?? 0; ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('moduleSettingsForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('<?php echo get_app_url(); ?>/admin/modules/settings/update', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Settings saved successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving settings.');
            });
    });
</script>