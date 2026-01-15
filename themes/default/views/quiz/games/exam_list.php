<div class="min-vh-100 bg-light" style="font-family: 'Outfit', sans-serif;">

    <!-- Header -->
    <header class="bg-white shadow-sm sticky-top">
        <div class="container py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <a href="<?= app_base_url('/quiz') ?>" class="btn btn-light rounded-circle p-2 shadow-sm">
                    <i class="fas fa-arrow-left text-dark"></i>
                </a>
                <h4 class="mb-0 fw-bold text-dark">Exam Hall</h4>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-primary rounded-pill px-3 py-2">
                    <i class="fas fa-file-alt me-1"></i> <?= count($exams) ?> Exams
                </span>
            </div>
        </div>
    </header>

    <!-- Content -->
    <main class="container py-5">

        <?php if (empty($exams)): ?>
            <div class="text-center py-5">
                <div class="mb-3 display-1 text-gray-300"><i class="fas fa-clipboard-list"></i></div>
                <h3 class="fw-bold text-gray-500">No Exams Available</h3>
                <p class="text-gray-400">Please check back later for new scheduled exams.</p>
            </div>
        <?php else: ?>

            <?php foreach ($grouped_exams as $type => $group): ?>
                <div class="mb-5">
                    <h5 class="fw-bold text-gray-600 mb-3 border-bottom pb-2 d-flex align-items-center">
                        <i class="fas fa-layer-group me-2 text-primary"></i> <?= $type ?>
                    </h5>

                    <div class="row g-4">
                        <?php foreach ($group as $exam): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border-0 shadow-sm hover-shadow transition-all rounded-4 overflow-hidden group">
                                    <div class="card-body p-4 d-flex flex-column">

                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="badge bg-blue-100 text-blue-600 rounded-pill px-3">
                                                <?= ucfirst($exam['mode']) ?>
                                            </div>
                                            <?php if ($exam['price'] > 0): ?>
                                                <div class="badge bg-yellow-100 text-yellow-700 rounded-pill px-3">
                                                    <i class="fas fa-gem me-1"></i> Premium
                                                </div>
                                            <?php else: ?>
                                                <div class="badge bg-green-100 text-green-700 rounded-pill px-3">
                                                    Free
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <h5 class="card-title fw-bold mb-2 text-dark">
                                            <?= htmlspecialchars($exam['title']) ?>
                                        </h5>

                                        <p class="card-text text-gray-500 small mb-4 flex-grow-1 clamp-2">
                                            <?= htmlspecialchars($exam['description'] ?? 'No description available.') ?>
                                        </p>

                                        <div class="d-flex justify-content-between align-items-center text-gray-500 font-sm mb-4">
                                            <span><i class="far fa-clock me-1"></i> <?= $exam['duration_minutes'] ?> min</span>
                                            <span><i class="far fa-question-circle me-1"></i> Questions</span>
                                        </div>

                                        <a href="<?= app_base_url('/quiz/exam/start/' . $exam['slug']) ?>"
                                            class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm hover-move-up">
                                            Start Exam <i class="fas fa-arrow-right ms-2"></i>
                                        </a>

                                    </div>
                                    <div class="card-footer bg-gray-50 border-0 py-2 px-4">
                                        <small class="text-muted">
                                            Available until: <?= $exam['end_datetime'] ? date('M d, Y', strtotime($exam['end_datetime'])) : 'No Limit' ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>

    </main>
</div>

<style>
    .hover-shadow:hover {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
    }

    .transition-all {
        transition: all 0.3s ease;
    }

    .hover-move-up:hover {
        transform: translateY(-2px);
    }

    .clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .bg-blue-100 {
        background-color: #dbeafe;
    }

    .text-blue-600 {
        color: #2563eb;
    }

    .bg-green-100 {
        background-color: #dcfce7;
    }

    .text-green-700 {
        color: #15803d;
    }

    .bg-yellow-100 {
        background-color: #fef9c3;
    }

    .text-yellow-700 {
        color: #a16207;
    }
</style>