<?php
/**
 * Blueprints List View
 */
$pageTitle = $page_title ?? 'Exam Blueprints';
$blueprints = $blueprints ?? [];
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0"><?= htmlspecialchars($pageTitle) ?></h1>
            <p class="text-muted">Manage exam blueprints and question distribution templates</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= app_base_url('admin/quiz/blueprints/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create Blueprint
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (empty($blueprints)): ?>
                <div class="text-center text-muted py-5">
                    <i class="bi bi-diagram-3 display-1"></i>
                    <p class="mt-3">No blueprints found. Create your first blueprint to get started.</p>
                    <a href="<?= app_base_url('admin/quiz/blueprints/create') ?>" class="btn btn-primary mt-2">
                        Create Blueprint
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Level</th>
                                <th>Questions</th>
                                <th>Duration</th>
                                <th>Wildcard %</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($blueprints as $blueprint): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($blueprint['title']) ?></strong>
                                        <?php if (!empty($blueprint['description'])): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars(substr($blueprint['description'], 0, 60)) ?>...</small>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-info"><?= htmlspecialchars($blueprint['level'] ?? 'N/A') ?></span></td>
                                    <td><?= $blueprint['total_questions'] ?></td>
                                    <td><?= $blueprint['duration_minutes'] ?> min</td>
                                    <td><?= $blueprint['wildcard_percentage'] ?>%</td>
                                    <td>
                                        <?php if ($blueprint['is_active']): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= app_base_url('admin/quiz/blueprints/edit/' . $blueprint['id']) ?>" class="btn btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button class="btn btn-outline-success" onclick="generateFromBlueprint(<?= $blueprint['id'] ?>)" title="Generate Exam">
                                                <i class="bi bi-lightning"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="deleteBlueprint(<?= $blueprint['id'] ?>)" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
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

<script>
const baseUrl = '<?= app_base_url() ?>';

function generateFromBlueprint(blueprintId) {
    if (confirm('Generate a new exam from this blueprint?')) {
        window.location = `${baseUrl}/admin/quiz/blueprints/edit/${blueprintId}#generate`;
    }
}

function deleteBlueprint(blueprintId) {
    if (!confirm('Delete this blueprint? This action cannot be undone.')) return;
    
    fetch(`${baseUrl}/admin/quiz/blueprints/delete/${blueprintId}`, {
        method: 'POST'
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    });
}
</script>
