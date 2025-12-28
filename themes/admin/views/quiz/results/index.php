<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Results & Analytics</h1>
        <!-- <a href="#" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-download"></i> Export Report</a> -->
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="" class="form-inline justify-content-end">
                <input type="text" name="search" class="form-control mr-2" placeholder="Search user or exam..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All User Attempts</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Exam</th>
                            <th>Score</th>
                            <th>Status</th>
                            <th>Date Started</th>
                            <th>Date Completed</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($attempts)): ?>
                            <tr><td colspan="7" class="text-center">No attempts found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($attempts as $row): ?>
                                <tr>
                                    <td>
                                        <div class="font-weight-bold"><?php echo htmlspecialchars($row['username']); ?></div>
                                        <div class="small text-gray-500"><?php echo htmlspecialchars($row['email']); ?></div>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['exam_title']); ?></td>
                                    <td>
                                        <span class="font-weight-bold"><?php echo number_format($row['score'], 2); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($row['status'] == 'completed'): ?>
                                            <span class="badge badge-success">Completed</span>
                                        <?php elseif($row['status'] == 'in_progress'): ?>
                                            <span class="badge badge-warning">In Progress</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary"><?php echo ucfirst($row['status']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($row['started_at'])); ?></td>
                                    <td><?php echo $row['completed_at'] ? date('Y-m-d H:i', strtotime($row['completed_at'])) : '-'; ?></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info disabled" title="View Details (Coming Soon)">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Simple Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">Showing <?php echo count($attempts); ?> of <?php echo $total; ?> entries</small>
                <?php if ($total > $limit): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                             <?php 
                                $totalPages = ceil($total / $limit);
                                for ($i = 1; $i <= $totalPages; $i++): 
                            ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $_GET['search'] ?? ''; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
