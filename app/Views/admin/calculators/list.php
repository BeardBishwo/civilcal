<?php
// Calculators List View
$this->view('admin/layout/header', $data);
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1>Calculators List</h1>
            <p>This is a list of all calculators in the system.</p>
            
            <?php if (!empty($calculators)): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Usage Count</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($calculators as $calculator): ?>
                                <tr>
                                    <td><?= htmlspecialchars($calculator['id'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($calculator['name'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($calculator['category'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($calculator['description'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($calculator['status'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($calculator['usage_count'] ?? 0) ?></td>
                                    <td><?= htmlspecialchars($calculator['created_at'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <p>No calculators found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$this->view('admin/layout/footer', $data);
?>