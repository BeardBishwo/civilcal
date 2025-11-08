<?php
$content = '
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1">Modules & Categories</h2>
            <p class="text-muted mb-0">Manage calculation modules and categories</p>
        </div>
        <div class="quick-actions">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModuleModal">
                <i class="bi bi-plus-circle me-2"></i>Add Module
            </button>
            <button class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-upload me-2"></i>Import Module
            </button>
        </div>
    </div>

    <!-- Modules Grid -->
    <div class="row">';
    
    foreach ($modules as $module) {
        $content .= '
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card module-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">' . htmlspecialchars($module['name']) . '</h6>
                    <span class="badge bg-info">v' . $module['version'] . '</span>
                </div>
                <div class="card-body">
                    <p class="card-text">' . htmlspecialchars($module['description']) . '</p>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="h5 mb-0 text-primary">' . $module['calculators_count'] . '</div>
                                <small class="text-muted">Calculators</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h5 mb-0 ' . ($module['status'] == 'active' ? 'text-success' : 'text-secondary') . '">
                                ' . ucfirst($module['status']) . '
                            </div>
                            <small class="text-muted">Status</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="btn-group w-100">
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-gear me-1"></i>Settings
                        </button>
                        <button class="btn ' . ($module['status'] == 'active' ? 'btn-outline-warning' : 'btn-outline-success') . ' btn-sm">
                            <i class="bi ' . ($module['status'] == 'active' ? 'bi-pause' : 'bi-play') . ' me-1"></i>
                            ' . ($module['status'] == 'active' ? 'Disable' : 'Enable') . '
                        </button>
                        <button class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash me-1"></i>Remove
                        </button>
                    </div>
                </div>
            </div>
        </div>';
    }
    
    $content .= '
    </div>

    <!-- Categories Management -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Categories Management</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Category Name</th>
                                    <th>Slug</th>
                                    <th>Modules Count</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>';
                            
                            foreach ($categories as $slug => $name) {
                                $count = array_reduce($modules, function($carry, $item) use ($slug) {
                                    return $carry + ($item['category'] === $slug ? 1 : 0);
                                }, 0);
                                
                                $content .= '
                                <tr>
                                    <td>' . htmlspecialchars($name) . '</td>
                                    <td><code>' . $slug . '</code></td>
                                    <td>
                                        <span class="badge bg-primary">' . $count . ' modules</span>
                                    </td>
                                    <td>Automatically created category</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>';
                            }
                            
                            $content .= '
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Add Category Form -->
                    <div class="mt-4 p-3 border rounded bg-light">
                        <h6 class="mb-3">Add New Category</h6>
                        <form class="row g-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control form-control-sm" placeholder="Category Name" required>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control form-control-sm" placeholder="Slug (auto-generated)" required>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control form-control-sm" placeholder="Description">
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Module Modal -->
<div class="modal fade" id="addModuleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Module</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addModuleForm">
                    <div class="mb-3">
                        <label class="form-label">Module Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category" required>';
                        
                        foreach ($categories as $key => $value) {
                            $content .= '<option value="' . $key . '">' . $value . '</option>';
                        }
                        
                        $content .= '
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Version</label>
                        <input type="text" class="form-control" name="version" value="1.0.0" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addModuleForm" class="btn btn-primary">Add Module</button>
            </div>
        </div>
    </div>
</div>
';

include __DIR__ . '/../../layouts/admin.php';
?>
