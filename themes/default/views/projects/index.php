<?php require_once dirname(__DIR__) . '/partials/header.php'; ?>

<div class="container py-5">
    <div class="row align-items-center mb-5 pb-3 border-bottom border-secondary">
        <div class="col-md-8">
            <h1 class="display-5 fw-bold text-light mb-1">My Projects</h1>
            <p class="text-muted mb-0">Manage and organize your calculation results.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <button class="btn btn-primary btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                <i class="fas fa-plus me-2"></i> New Project
            </button>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show bg-success text-white border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo $_GET['success'] == 'deleted' ? 'Project deleted successfully.' : 'Project created successfully.'; ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <?php if (empty($projects)): ?>
            <div class="col-12">
                <div class="empty-state-card text-center py-5 rounded-3">
                    <div class="mb-4 icon-glow">
                        <i class="fas fa-folder-open fa-4x text-muted"></i>
                    </div>
                    <h3 class="text-light fw-bold">No projects yet</h3>
                    <p class="text-muted mb-4">Create your first project to start organizing your work.</p>
                    <button class="btn btn-outline-primary px-4" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                        Create First Project
                    </button>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($projects as $project): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 project-card bg-dark border-secondary shadow-sm">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="icon-wrapper">
                                    <i class="fas fa-folder fa-lg text-primary"></i>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end bg-dark border-secondary shadow">
                                        <li>
                                            <a class="dropdown-item text-light hover-bg-light" href="<?php echo app_base_url('/projects/view/' . $project['id']); ?>">
                                                <i class="fas fa-eye me-2 text-primary"></i> View Details
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider bg-secondary"></li>
                                        <li>
                                            <form action="<?php echo app_base_url('/projects/delete/' . $project['id']); ?>" method="POST" onsubmit="return confirm('Are you sure? This will not delete the calculations inside, but unlink them.');">
                                                <button type="submit" class="dropdown-item text-danger hover-bg-light"><i class="fas fa-trash-alt me-2"></i>Delete Project</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            <h4 class="card-title text-light mb-2">
                                <a href="<?php echo app_base_url('/projects/view/' . $project['id']); ?>" class="text-decoration-none text-reset stretched-link">
                                    <?php echo htmlspecialchars($project['name']); ?>
                                </a>
                            </h4>
                            <p class="card-text text-muted small mb-4 flex-grow-1">
                                <?php echo htmlspecialchars($project['description'] ?: 'No description provided.'); ?>
                            </p>
                            
                            <div class="project-meta d-flex justify-content-between align-items-center pt-3 border-top border-secondary">
                                <small class="text-muted">
                                    <i class="fas fa-calculator me-1"></i> 
                                    <span class="fw-bold text-light"><?php echo $project['calculation_count']; ?></span> items
                                </small>
                                <small class="text-muted">
                                    <i class="far fa-clock me-1"></i> <?php echo date('M d, Y', strtotime($project['updated_at'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Create Project Modal -->
<div class="modal fade" id="createProjectModal" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?php echo app_base_url('/projects/store'); ?>" method="POST" id="create-project-form">
            <div class="modal-content bg-dark border-secondary text-light">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title"><i class="fas fa-folder-plus me-2 text-primary"></i>Create New Project</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted">Project Name</label>
                        <input type="text" name="name" class="form-control bg-dark text-light border-secondary" required placeholder="e.g. Smith Residence">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Description (Optional)</label>
                        <textarea name="description" class="form-control bg-dark text-light border-secondary" rows="3" placeholder="Brief details about this project..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Create Project</button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.project-card { 
    transition: all 0.3s ease; 
    background: #1e1e1e;
    border-radius: 12px;
    overflow: hidden;
}
.project-card:hover { 
    transform: translateY(-5px); 
    box-shadow: 0 10px 20px rgba(0,0,0,0.3) !important;
    border-color: #4ecdc4 !important;
}
.empty-state-card {
    background: rgba(255,255,255,0.02);
    border: 1px dashed rgba(255,255,255,0.1);
}
.icon-wrapper {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(78, 205, 196, 0.1);
    border-radius: 50%;
}
.hover-bg-light:hover {
    background-color: rgba(255,255,255,0.1);
}
.form-control:focus {
    background-color: #1a1a1a;
    color: #fff;
    border-color: #4ecdc4;
    box-shadow: 0 0 0 0.25rem rgba(78, 205, 196, 0.25);
}
</style>

<?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>
