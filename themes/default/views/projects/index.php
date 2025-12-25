<?php require_once dirname(__DIR__) . '/partials/header.php'; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>My Projects</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProjectModal">
            <i class="fas fa-plus"></i> New Project
        </button>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_GET['success'] == 'deleted' ? 'Project deleted successfully.' : 'Project created successfully.'; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php if (empty($projects)): ?>
            <div class="col-12 text-center py-5">
                <div class="text-muted mb-3">
                    <i class="fas fa-folder-open fa-3x"></i>
                </div>
                <h3>No projects yet</h3>
                <p>Create a project to organize your calculations.</p>
                <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#createProjectModal">Create First Project</button>
            </div>
        <?php else: ?>
            <?php foreach ($projects as $project): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0 project-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <h5 class="card-title text-primary">
                                    <a href="<?php echo app_base_url('/projects/view/' . $project['id']); ?>" class="text-decoration-none">
                                        <i class="fas fa-folder me-2"></i><?php echo htmlspecialchars($project['name']); ?>
                                    </a>
                                </h5>
                                <div class="dropdown">
                                    <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <form action="<?php echo app_base_url('/projects/delete/' . $project['id']); ?>" method="POST" onsubmit="return confirm('Are you sure? This will not delete the calculations inside, but unlink them.');">
                                                <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash-alt me-2"></i>Delete</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <p class="card-text text-muted small"><?php echo htmlspecialchars($project['description'] ?: 'No description'); ?></p>
                        </div>
                        <div class="card-footer bg-transparent border-0 d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="fas fa-calculator me-1"></i> <?php echo $project['calculation_count']; ?> items</small>
                            <small class="text-muted">Updated <?php echo date('M d, Y', strtotime($project['updated_at'])); ?></small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Create Project Modal -->
<div class="modal fade" id="createProjectModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo app_base_url('/projects/store'); ?>" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Project Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g. Smith Residence">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description (Optional)</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief details about this project..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Project</button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.project-card { transition: transform 0.2s; }
.project-card:hover { transform: translateY(-5px); }
</style>

<?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>
