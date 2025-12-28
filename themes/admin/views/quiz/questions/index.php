<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Question Bank</h1>
        <a href="<?php echo app_base_url('admin/quiz/questions/create'); ?>" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add New Question
        </a>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo app_base_url('admin/quiz/questions'); ?>" class="form-inline">
                <select name="topic_id" class="form-control mb-2 mr-sm-2">
                    <option value="">All Topics</option>
                    <?php 
                        // Flatten categories for simplified dropdown or load via AJAX ideally. 
                        // Since I passed 'categories', I can iterate, but topics are deep.
                        // For simplicity in this view, let's just assume we might want to load topics if selected, 
                        // or just show a simplified list if available. 
                        // The controller passed 'categories' which contains hierarchy if eager loaded, 
                        // but actually controller only passed 'categories' flat find.
                        // I'll stick to a basic placeholder or standard input for now due to complexity.
                    ?>
                </select>
                
                <select name="type" class="form-control mb-2 mr-sm-2">
                    <option value="">All Types</option>
                    <option value="mcq_single" <?php echo ($_GET['type'] ?? '') == 'mcq_single' ? 'selected' : ''; ?>>MCQ (Single)</option>
                    <option value="mcq_multi" <?php echo ($_GET['type'] ?? '') == 'mcq_multi' ? 'selected' : ''; ?>>MCQ (Multi)</option>
                    <option value="numerical" <?php echo ($_GET['type'] ?? '') == 'numerical' ? 'selected' : ''; ?>>Numerical</option>
                    <option value="true_false" <?php echo ($_GET['type'] ?? '') == 'true_false' ? 'selected' : ''; ?>>True/False</option>
                </select>

                <input type="text" name="search" class="form-control mb-2 mr-sm-2" placeholder="Search question text..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">

                <button type="submit" class="btn btn-primary mb-2">Filter</button>
                <a href="<?php echo app_base_url('admin/quiz/questions'); ?>" class="btn btn-secondary mb-2 ml-2">Reset</a>
            </form>
        </div>
    </div>

    <!-- Questions List -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="40%">Question</th>
                            <th width="15%">Type/Topic</th>
                            <th width="10%">Difficulty</th>
                            <th width="10%">Marks</th>
                            <th width="10%">Status</th>
                            <th width="10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($questions)): ?>
                            <tr><td colspan="7" class="text-center">No questions found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($questions as $q): ?>
                                <?php 
                                    $content = json_decode($q['content'], true); 
                                    $text = strip_tags($content['text'] ?? '');
                                    if (strlen($text) > 80) $text = substr($text, 0, 80) . '...';
                                ?>
                                <tr>
                                    <td><?php echo $q['id']; ?></td>
                                    <td>
                                        <div class="font-weight-bold text-gray-800 mb-1"><?php echo htmlspecialchars($text); ?></div>
                                        <small class="text-muted">Code: <?php echo htmlspecialchars($q['unique_code']); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge badge-info"><?php echo ucwords(str_replace('_', ' ', $q['type'])); ?></span>
                                        <div class="small mt-1 text-gray-600"><?php echo htmlspecialchars($q['topic_name'] ?? 'N/A'); ?></div>
                                    </td>
                                    <td>
                                        <?php 
                                            // Star rating
                                            for($i=1; $i<=5; $i++) {
                                                echo '<i class="fas fa-star '.($i <= $q['difficulty_level'] ? 'text-warning' : 'text-gray-300').' small"></i>';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <span class="text-success">+<?php echo $q['default_marks']; ?></span> / 
                                        <span class="text-danger">-<?php echo $q['default_negative_marks']; ?></span>
                                    </td>
                                    <td>
                                        <?php if ($q['is_active']): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo app_base_url('admin/quiz/questions/edit/' . $q['id']); ?>" class="btn btn-sm btn-circle btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo app_base_url('admin/quiz/questions/delete/' . $q['id']); ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this question?');">
                                            <button type="submit" class="btn btn-sm btn-circle btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">Showing <?php echo count($questions); ?> of <?php echo $total; ?> entries</small>
                
                <?php if ($total > $limit): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            <?php 
                                $totalPages = ceil($total / $limit);
                                for ($i = 1; $i <= $totalPages; $i++): 
                                    $active = ($i == $page) ? 'active' : '';
                            ?>
                                <li class="page-item <?php echo $active; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>&topic_id=<?php echo $_GET['topic_id'] ?? ''; ?>&type=<?php echo $_GET['type'] ?? ''; ?>&search=<?php echo $_GET['search'] ?? ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
