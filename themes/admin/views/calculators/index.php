<div class="container-fluid p-0">
    <div class="row mb-2 mb-xl-3">
        <div class="col-auto d-none d-sm-block">
            <h3><strong>Calculators</strong> Management</h3>
        </div>
    </div>

    <!-- Filters Toolbar -->
    <div class="card mb-3">
        <div class="card-body p-3">
            <form action="" method="GET" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="align-middle" data-feather="search"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Search calculators..." value="<?php echo htmlspecialchars($filters['search']); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="module">
                        <option value="">All Modules</option>
                        <?php foreach ($modules as $modName): ?>
                            <!-- Simple slug generation for filter match -->
                            <?php $modSlug = strtolower(str_replace(' ', '-', $modName)); ?>
                            <option value="<?php echo $modSlug; ?>" <?php echo ($filters['module'] === $modSlug) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($modName); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="<?php echo get_app_url(); ?>/admin/calculators" class="btn btn-outline-secondary w-100">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover my-0">
                            <thead>
                                <tr>
                                    <th>Calculator Name</th>
                                    <th>Module</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($calculators)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <div class="text-muted">No calculators found matching your criteria.</div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($calculators as $calc): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($calc['name']); ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($calc['module_name']); ?></span>
                                            </td>
                                            <td>
                                                <?php if ($calc['status'] === 'active'): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <!-- Toggle Button -->
                                                    <?php if ($calc['status'] === 'active'): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="toggleCalculator('<?php echo $calc['unique_id']; ?>', 'deactivate')" 
                                                                title="Deactivate">
                                                            <i class="align-middle" data-feather="power"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                                onclick="toggleCalculator('<?php echo $calc['unique_id']; ?>', 'activate')" 
                                                                title="Activate">
                                                            <i class="align-middle" data-feather="power"></i>
                                                        </button>
                                                    <?php endif; ?>

                                                    <!-- Test Button -->
                                                    <a href="<?php echo get_app_url() . $calc['url']; ?>" target="_blank" class="btn btn-sm btn-outline-primary ms-2" title="Test Calculator">
                                                        <i class="align-middle" data-feather="external-link"></i> Test
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
    function toggleCalculator(id, action) {
        // Optimistic UI update or reload? Reload is safer for verify
        const formData = new FormData();
        formData.append('id', id);
        formData.append('action', action);

        fetch('<?php echo get_app_url(); ?>/admin/calculators/toggle', {
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
            alert('Request failed');
        });
    }
</script>