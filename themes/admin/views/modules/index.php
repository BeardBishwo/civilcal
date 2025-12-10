<div class="container-fluid p-0">
    <div class="row mb-2 mb-xl-3">
        <div class="col-auto d-none d-sm-block">
            <h3><strong>Modules</strong> Management</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Installed Modules</h5>
                    <div class="alert alert-info mt-3">
                        <i class="align-middle" data-feather="info"></i>
                        Modules allow you to extend the functionality of the calculator platform. Enable or disable modules to control which calculators are available to your users.
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover my-0">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Name</th>
                                    <th class="d-none d-xl-table-cell">Description</th>
                                    <th class="d-none d-xl-table-cell">Category</th>
                                    <th>Calculators</th>
                                    <th class="d-none d-md-table-cell">Version</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($modules)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">No modules found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($modules as $module): ?>
                                        <tr>
                                            <td>
                                                <?php if (($module['status'] ?? 'inactive') === 'active'): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($module['name']); ?></strong>
                                            </td>
                                            <td class="d-none d-xl-table-cell">
                                                <?php echo htmlspecialchars($module['description']); ?>
                                            </td>
                                            <td class="d-none d-xl-table-cell">
                                                <span class="badge bg-info text-dark"><?php echo htmlspecialchars($module['category']); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary"><?php echo $module['calculators_count'] ?? 0; ?> calculators</span>
                                            </td>
                                            <td class="d-none d-md-table-cell"><?php echo htmlspecialchars($module['version']); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <?php if (($module['status'] ?? 'inactive') === 'active'): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="toggleModule('<?php echo $module['name']; ?>', 'deactivate')">
                                                            Deactivate
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="toggleModule('<?php echo $module['name']; ?>', 'activate')">
                                                            Activate
                                                        </button>
                                                    <?php endif; ?>

                                                    <a href="<?php echo get_app_url(); ?>/admin/modules/<?php echo urlencode($module['name']); ?>/settings" class="btn btn-sm btn-outline-primary">
                                                        <i class="align-middle" data-feather="settings"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleModule(moduleName, action) {
        if (!confirm('Are you sure you want to ' + action + ' this module?')) {
            return;
        }

        const formData = new FormData();
        formData.append('module', moduleName);

        fetch('<?php echo get_app_url(); ?>/admin/modules/' + action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while communicating with the server.');
            });
    }
</script>