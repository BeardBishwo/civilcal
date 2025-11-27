<div class="page-header">
    <h1 class="page-title"><i class="fas fa-list"></i> Calculators List</h1>
    <p class="page-description">View all calculators in the system</p>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-calculator"></i>
            All Calculators
        </h5>
    </div>
    
    <div class="card-content" style="padding: 0;">
        <?php if (!empty($calculators)): ?>
            <div class="table-container">
                <table class="table">
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
                                <td><?php echo htmlspecialchars($calculator['id'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($calculator['name'] ?? ''); ?></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php echo htmlspecialchars($categories[$calculator['category']] ?? $calculator['category'] ?? ''); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($calculator['description'] ?? ''); ?></td>
                                <td>
                                    <span class="badge <?php echo ($calculator['status'] ?? '') == 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?php echo htmlspecialchars($calculator['status'] ?? ''); ?>
                                    </span>
                                </td>
                                <td><?php echo number_format($calculator['usage_count'] ?? 0); ?></td>
                                <td><?php echo htmlspecialchars($calculator['created_at'] ?? ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-calculator fa-3x text-gray-300"></i>
                <h3 class="mt-3">No calculators found</h3>
                <p class="text-gray-500">There are no calculators in the system yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>