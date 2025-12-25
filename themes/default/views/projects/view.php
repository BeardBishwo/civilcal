<?php require_once dirname(__DIR__) . '/partials/header.php'; ?>

<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo app_base_url('/projects'); ?>">Projects</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($project['name']); ?></li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-folder-open text-primary me-2"></i><?php echo htmlspecialchars($project['name']); ?></h1>
            <p class="text-muted mb-0"><?php echo htmlspecialchars($project['description']); ?></p>
        </div>
        <div>
             
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Saved Calculations</h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($calculations)): ?>
                <div class="text-center py-5">
                    <div class="text-muted mb-3"><i class="fas fa-file-alt fa-2x"></i></div>
                    <p>No calculations saved in this project yet.</p>
                    <a href="<?php echo app_base_url('/'); ?>" class="btn btn-outline-primary btn-sm">Go to Calculators</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Calculation</th>
                                <th>Date</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($calculations as $calc): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold"><?php echo htmlspecialchars(ucwords(str_replace('-', ' ', $calc['calculator_type']))); ?></div>
                                        <div class="small text-muted text-truncate" style="max-width: 300px;">
                                            <?php 
                                            // Ideally parse inputs to show summary
                                            echo "ID: " . $calc['id']; 
                                            ?>
                                        </div>
                                    </td>
                                    <td><?php echo date('M d, Y h:i A', strtotime($calc['created_at'])); ?></td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                             <!-- View/Load would go here if implemented -->
                                            <button class="btn btn-sm btn-outline-secondary" onclick="alert('Load functionality coming soon!')"><i class="fas fa-eye"></i> View</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/partials/footer.php'; ?>
