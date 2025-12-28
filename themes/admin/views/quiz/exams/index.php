<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Exam & Mock Test Manager</h1>
        <a href="<?php echo app_base_url('admin/quiz/exams/create'); ?>" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Create New Exam
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Mode</th>
                            <th>Duration</th>
                            <th>Marks</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($exams)): ?>
                            <tr><td colspan="7" class="text-center">No exams found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($exams as $exam): ?>
                                <tr>
                                    <td>
                                        <div class="font-weight-bold text-primary"><?php echo htmlspecialchars($exam['title']); ?></div>
                                        <?php if($exam['is_premium']): ?>
                                            <span class="badge badge-warning"><i class="fas fa-crown"></i> Premium</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo ucfirst(str_replace('_', ' ', $exam['type'])); ?></td>
                                    <td><?php echo ucfirst($exam['mode']); ?></td>
                                    <td><?php echo $exam['duration_minutes'] > 0 ? $exam['duration_minutes'] . ' mins' : 'Unlimited'; ?></td>
                                    <td><?php echo $exam['total_marks']; ?></td>
                                    <td>
                                        <?php if ($exam['status'] == 'published'): ?>
                                            <span class="badge badge-success">Published</span>
                                        <?php elseif($exam['status'] == 'archived'): ?>
                                            <span class="badge badge-secondary">Archived</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Draft</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo app_base_url('admin/quiz/exams/builder/' . $exam['id']); ?>" class="btn btn-sm btn-info" title="Manage Questions">
                                            <i class="fas fa-list-ol"></i> Questions
                                        </a>
                                        <a href="<?php echo app_base_url('admin/quiz/exams/edit/' . $exam['id']); ?>" class="btn btn-sm btn-primary" title="Edit Settings">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
             <!-- Pagination Placeholder -->
             <?php if ($total > 20): ?>
                <div class="mt-3">
                    <small>Pagination logic here...</small>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
