<?php 
// Get flash messages
$success = isset($_SESSION['flash']['success']) ? $_SESSION['flash']['success'] : '';
$error = isset($_SESSION['flash']['error']) ? $_SESSION['flash']['error'] : '';

// Clear flash messages
if (isset($_SESSION['flash'])) {
    unset($_SESSION['flash']);
}

$pageTitle = $pageTitle ?? 'Calculation History';
$searchTerm = $searchTerm ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Bishwo Calculator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/public/assets/css/history.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="page-title">
                        <i class="fas fa-history me-2"></i><?= htmlspecialchars($pageTitle) ?>
                    </h1>
                    <div class="export-buttons">
                        <a href="/history/export?format=csv" class="btn btn-outline-success btn-sm me-2">
                            <i class="fas fa-file-csv me-1"></i>Export CSV
                        </a>
                        <a href="/history/export?format=json" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-file-code me-1"></i>Export JSON
                        </a>
                    </div>
                </div>
                
                <!-- Flash Messages -->
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Search Box -->
                <div class="search-box mb-4">
                    <form action="/history/search" method="GET" class="d-flex">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" 
                                   placeholder="Search calculations by title, type, or tags..." 
                                   value="<?= htmlspecialchars($searchTerm) ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4" id="stats-cards" style="display: none;">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-calculator fa-2x text-primary mb-2"></i>
                                <h5 class="card-title">Total Calculations</h5>
                                <h3 class="text-primary" id="total-calculations">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-star fa-2x text-warning mb-2"></i>
                                <h5 class="card-title">Favorites</h5>
                                <h3 class="text-warning" id="favorite-calculations">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-calendar-alt fa-2x text-success mb-2"></i>
                                <h5 class="card-title">This Month</h5>
                                <h3 class="text-success" id="month-calculations">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-chart-line fa-2x text-info mb-2"></i>
                                <h5 class="card-title">Most Used</h5>
                                <h6 class="text-info" id="top-calculator">-</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- History List -->
                <?php if (empty($history)): ?>
                    <div class="empty-state text-center py-5">
                        <div class="empty-icon">
                            <i class="fas fa-calculator fa-5x text-muted mb-4"></i>
                        </div>
                        <h3>No calculations found</h3>
                        <p class="text-muted">
                            <?php if (!empty($searchTerm)): ?>
                                No calculations match your search for "<?= htmlspecialchars($searchTerm) ?>".
                            <?php else: ?>
                                Your calculation history will appear here after you perform your first calculation.
                            <?php endif; ?>
                        </p>
                        <a href="/calculators" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>Start Calculating
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Bulk Actions -->
                    <div class="bulk-actions mb-3" style="display: none;">
                        <div class="card">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="selected-count">0 items selected</span>
                                    <div>
                                        <button class="btn btn-sm btn-warning" id="bulk-favorite">
                                            <i class="fas fa-star me-1"></i>Add to Favorites
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" id="bulk-remove-favorite">
                                            <i class="far fa-star me-1"></i>Remove from Favorites
                                        </button>
                                        <button class="btn btn-sm btn-danger" id="bulk-delete">
                                            <i class="fas fa-trash me-1"></i>Delete Selected
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="history-list">
                        <?php foreach ($history as $item): ?>
                            <div class="history-item card mb-3" data-id="<?= $item['id'] ?>">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <input type="checkbox" class="form-check-input me-3 item-checkbox" 
                                                       data-id="<?= $item['id'] ?>">
                                                <h5 class="card-title mb-0">
                                                    <?php if ($item['is_favorite']): ?>
                                                        <i class="fas fa-star text-warning me-2 favorite-star" 
                                                           title="Favorited"></i>
                                                    <?php else: ?>
                                                        <i class="far fa-star text-muted me-2 favorite-star" 
                                                           title="Not favorited"></i>
                                                    <?php endif; ?>
                                                    <?= htmlspecialchars($item['calculation_title']) ?>
                                                </h5>
                                            </div>
                                            
                                            <div class="row mb-2">
                                                <div class="col-md-6">
                                                    <p class="card-text text-muted mb-1">
                                                        <i class="fas fa-calculator me-2"></i>
                                                        <strong>Calculator:</strong> 
                                                        <span class="calculator-type"><?= htmlspecialchars($item['calculator_type']) ?></span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="card-text text-muted mb-1">
                                                        <i class="fas fa-calendar me-2"></i>
                                                        <strong>Date:</strong> 
                                                        <?= date('M j, Y g:i A', strtotime($item['calculation_date'])) ?>
                                                    </p>
                                                </div>
                                            </div>

                                            <?php if (!empty($item['tags'])): ?>
                                                <div class="tags mb-2">
                                                    <small class="text-muted me-2">Tags:</small>
                                                    <?php 
                                                    $tags = explode(',', $item['tags']);
                                                    foreach ($tags as $tag):
                                                        if (!empty(trim($tag))):
                                                    ?>
                                                        <span class="badge bg-light text-dark me-1"><?= htmlspecialchars(trim($tag)) ?></span>
                                                    <?php 
                                                        endif;
                                                    endforeach; 
                                                    ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-outline-warning favorite-btn me-1" 
                                                    data-id="<?= $item['id'] ?>" 
                                                    title="<?= $item['is_favorite'] ? 'Remove from favorites' : 'Add to favorites' ?>">
                                                <i class="fas <?= $item['is_favorite'] ? 'fa-star' : 'fa-star' ?>"></i>
                                            </button>
                                            <a href="/history/view/<?= $item['id'] ?>" 
                                               class="btn btn-sm btn-outline-info me-1" 
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="/history/delete/<?= $item['id'] ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Are you sure you want to delete this calculation?')"
                                               title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <!-- Quick Results Preview -->
                                    <div class="results-preview mt-3">
                                        <button class="btn btn-sm btn-outline-secondary" type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#results-<?= $item['id'] ?>" 
                                                aria-expanded="false">
                                            <i class="fas fa-chevron-down me-1"></i>View Results
                                        </button>
                                        
                                        <div class="collapse mt-2" id="results-<?= $item['id'] ?>">
                                            <div class="card card-body bg-light">
                                                <h6 class="mb-3"><i class="fas fa-chart-bar me-2"></i>Calculation Results:</h6>
                                                <div class="row">
                                                    <?php 
                                                    $results = $item['result_data'];
                                                    $resultCount = 0;
                                                    if (is_array($results)):
                                                        foreach ($results as $key => $value): 
                                                            if ($resultCount < 6): // Show max 6 results in preview
                                                    ?>
                                                        <div class="col-md-6 mb-2">
                                                            <small class="text-muted">
                                                                <strong><?= htmlspecialchars($key) ?>:</strong>
                                                            </small>
                                                            <div class="fw-bold"><?= htmlspecialchars($value) ?></div>
                                                        </div>
                                                    <?php 
                                                            endif;
                                                            $resultCount++;
                                                        endforeach;
                                                    endif; 
                                                    ?>
                                                </div>
                                                
                                                <?php if (count($results ?? []) > 6): ?>
                                                    <div class="text-center mt-2">
                                                        <small class="text-muted">
                                                            +<?= count($results ?? []) - 6 ?> more results...
                                                        </small>
                                                        <a href="/history/view/<?= $item['id'] ?>" class="btn btn-sm btn-primary ms-2">
                                                            View All Results
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination (if needed) -->
                    <?php if (count($history) >= 50): ?>
                        <div class="text-center mt-4">
                            <p class="text-muted">Showing latest 50 calculations. Use search to find specific ones.</p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/assets/js/history.js"></script>
</body>
</html>
